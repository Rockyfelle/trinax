<?php
include 'controller.php';
?>
<html>

<head>
	<title>PHP Test</title>
	<link rel="stylesheet" href="index.css" />
</head>

<body>
	<div id="main">
		<div id="left">
			<form>
				<label for="workplace">Workplace</label>
				<select id="workplace" name="workplace">
					<option value='all'>-- All --</option>
					<?php
					$workplaceFromUrl = $_GET['workplace'];
					foreach ($controller->workplaces as $workplace) {
						echo '<option '
							. ($workplace->id == $workplaceFromUrl ? 'selected' : '')
							. ' value='
							. $workplace->id
							. '>'
							. $workplace->name
							. '</option>';
					}
					?>
				</select>
				<label for="date-from">From</label>
				<input type="date" id="date-from" name="from" value="<?php echo $_GET['from'] ?? '' ?>">
				<label for="date-to">To</label>
				<input type="date" id="date-to" name="to" value="<?php echo $_GET['to'] ?? '' ?>">
				<button type="submit">Update Results</button>
			</form>
			<table id="timereports">
				<thead>
					<tr>
						<th>Workplace</th>
						<th>Date</th>
						<th>Hours</th>
						<th>Info</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($controller->timereports() as $timereport) {
						echo '<tr>';
						echo '<td>' . $timereport->workplace->name . '</td>';
						echo '<td>' . $timereport->date . '</td>';
						echo '<td>' . $timereport->hours . '</td>';
						echo '<td>' . $timereport->info . '</td>';
						echo '</tr>';
					}
					?>
				</tbody>
			</table>
		</div>
		<div id="right">
			<h1>New Timereport</h1>
			<form>
				<label for="new-workplace">Workplace</label>
				<select id="new-workplace" name="workplace">
					<?php
					foreach ($controller->workplaces as $workplace) {
						echo '<option'
							. ' value='
							. $workplace->id
							. '>'
							. $workplace->name
							. '</option>';
					}
					?>
				</select>
				<label for="new-date">Date</label>
				<input type="date" id="new-date" name="date">
				<label for="new-hours">Hours</label>
				<input type="input" id="new-hours" name="new-hours">
				<label for="new-misc">Info</label>
				<textarea type="input" id="new-info" name="new-info" rows="4" ></textarea>
				<label for="new-image">Image</label>
				<input type="file" id="new-image" name="new-image">
				<button type="submit" id="sendreport">Send In Report</button>
			</form>
		</div>
	</div>
</body>
<script src="index.js"></script>

</html>