<?php

class Controller
{
	public $workplaces = array();
	//public $conn = null;

	public function __construct()
	{
		$url = '/workplace';
		$method = 'GET';
		$this->workplaces = $this->api($url, $method);

		$this->connectMySql()->close();
	}

	public function connectMySql()
	{
		$servername = 'localhost';
		$username = 'root';
		$password = '';

		$conn = new mysqli($servername, $username, $password, 'trinax');

		if ($conn->connect_error) {
			die('Connection failed: ' . $conn->connect_error);
		}

		return $conn;
	}

	public function api($url, $method, $data = null)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://arbetsprov.trinax.se/api/v1' . $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		$headers = array();
		$headers[] = 'Accept: application/json';
		$headers[] = 'Authorization: bearer ';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		if ($data)
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

		$result = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($httpcode != 200 && $httpcode != 201) {
			//If http code is not 200, then it is an error
			echo 'API Error: ' . $httpcode . ' ' . $result;
			die();
		}
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
			die();
		}
		curl_close($ch);
		return json_decode($result);
	}

	public function timereports()
	{
		$url = '/timereport';
		$method = 'GET';
		$from = $_GET['from'] ?? '';
		$to = $_GET['to'] ?? '';
		$workplace = $_GET['workplace'] ?? '';
		$query = array();
		if ($from) {
			$query[] = 'from_date=' . $from;
		}
		if ($to) {
			$query[] = 'to_date=' . $to;
		}
		if ($workplace && $workplace != 'all') {
			$query[] = 'workplace=' . $workplace;
		}
		if ($query) {
			$url .= '?' . implode('&', $query);
		}

		$raw = $this->api($url, $method);
		foreach ($raw as $timereport) {
			$timereport->workplace = $this->getWorkplaceById($timereport->workplace_id);
		}

		return $raw;
	}

	public function getWorkplaceById($id)
	{
		foreach ($this->workplaces as $workplace) {
			if ($workplace->id == $id) {
				return $workplace;
			}
		}
	}

	public function sendReport()
	{
		try {
			$input = json_decode(file_get_contents('php://input'), true);
			$workplace = $input['workplace'] ?? '';
			$date = $input['date'] ?? '';
			$hours = $input['hours'] ?? '';
			$info = $input['info'] ?? '';
			$image = $input['image'] ?? '';
			$url = '/timereport';
			$method = 'POST';
			$body = array(
				'workplace_id' => $workplace,
				'date' => $date,
				'hours' => $hours,
				'info' => $info,
			);
			$reply = $this->api($url, $method, $body);

			$conn = $this->connectMySql();
			$stmt = $conn->prepare('INSERT into images (data, trinax_id) VALUES (?, ?)');
			$stmt->bind_param('si', $image, $reply->id);
			$stmt->execute();
			$result = $stmt->get_result();

			$conn->close();
		} catch (Exception $e) {
			return [
				'ok' => false,
				'error' => $e->getMessage(),
			];
		}

		return [
			'ok' => true,
		];
	}
}

$controller = new Controller();
