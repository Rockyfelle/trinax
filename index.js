let base = '';

document.getElementById("new-image").addEventListener("change", function (event) {
	const file = document.getElementById("new-image").files[0];
	getBase64(file);
});

document.getElementById("sendreport").addEventListener("click", function (event) {
	event.preventDefault();

	const data = {
		workplace: document.getElementById("new-workplace").value,
		date: document.getElementById("new-date").value,
		hours: parseInt(document.getElementById("new-hours").value),
		info: document.getElementById("new-info").value,
		image: base,
	};

	verified = verify(data);
	if (!verified)
		return;

	toggleDisabled(true);

	fetch("/report.php", {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
		},
		body: JSON.stringify(data),
	})
		.then((response) => response.json())
		.then((data) => {
			if (!data.ok || data.ok === true) {
				alert("Report sent");
				clearAll();
				toggleDisabled(false);
			} else {
				alert("Error:", data.error);
				toggleDisabled(false);
			}
		})
		.catch((error) => {
			alert("Error:", error);
			toggleDisabled(false);
		});
});

document.getElementById("new-hours").addEventListener("change", function (event) {
	const hours = parseInt(document.getElementById("new-hours").value);
	document.getElementById("new-hours").value = isNaN(hours) ? "" : hours;
});

function verify(data) {
	if (data.workplace === "" || data.date === "" || data.hours === "" || isNaN(data.hours) || data.info === "") {
		alert("All fields must be filled out");
		return false;
	}

	if (data.hours < 1 || data.hours > 24) {
		alert("Hours must be between 1 and 24");
		return false;
	}

	return true;
}

function toggleDisabled(disabled) {
	document.getElementById("new-workplace").disabled = disabled;
	document.getElementById("new-date").disabled = disabled;
	document.getElementById("new-hours").disabled = disabled;
	document.getElementById("new-info").disabled = disabled;
	document.getElementById("new-image").disabled = disabled;
	document.getElementById("sendreport").disabled = disabled;
}

function clearAll() {
	document.getElementById("new-date").value = "";
	document.getElementById("new-hours").value = "";
	document.getElementById("new-info").value = "";
	document.getElementById("new-image").value = "";
}

function getBase64(file) {
	var reader = new FileReader();
	reader.readAsDataURL(file);
	reader.onload = function () {
	  base = reader.result;
	};
	reader.onerror = function (error) {
	  console.log('Error: ', error);
	};
 }
