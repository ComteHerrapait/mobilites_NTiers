<!DOCTYPE html>
<?php
// Include config file
require_once "config.php";

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
	header("location: login.php");
	exit;
}
?>

<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>TSE Mobility</title>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round|Open+Sans">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="style.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />
	<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="script.js"></script>
	<!-- <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="crossorigin=""></script> -->
	<script language="javascript">
		function viewMap() {

			if (document.getElementsByClassName("table-bordered")[0].style.display != "none") {
				document.getElementsByClassName("div-map")[0].style.display = "block";
				setTimeout(function() {
					mymap.invalidateSize()
				}, 500);
				document.getElementsByClassName("btn btn-info add-new")[0].style.display = "none";
				document.getElementsByClassName("table-bordered")[0].style.display = "none";
				document.getElementById("btn-map").innerHTML = "View Table";
			} else if (document.getElementsByClassName("div-map")[0].style.display != "none") {
				document.getElementsByClassName("div-map")[0].style.display = "none";
				document.getElementsByClassName("btn btn-info add-new")[0].style.display = "block";
				document.getElementsByClassName("table-bordered")[0].style.display = "block";
				document.getElementById("btn-map").innerHTML = "View Map";
			}
		};
	</script>
</head>

<body>
	<button type="button" class="btn btn-info" onclick="window.location.href='logout.php'">Logout</button>
	<!--These buttons are a temporary solution TODO:make something definitive-->
	<button type="button" class="btn btn-info" onclick="window.location.href='new_mobility.php'">New Mobility</button>
	<button type="button" class="btn btn-info" onclick="window.location.href='new_partner.php'">New Partner</button>

	<?php
	echo "session : " . $_SESSION["username"];
	if ($_SESSION["is_admin"]) {
		echo " (admin) ";
	}
	?>
	<div class="container-lg">
		<div class="table-responsive">
			<div class="table-wrapper">
				<div class="table-title">
					<div class="row">
						<div class="col-sm-8">
							<h2>Student <b>Mobility</b></h2>
						</div>
						<div class="col-sm-4">
							<button type="button" class="btn btn-info add-new"><i class="fa fa-plus"></i> Add New</button>
							<button type="button" class="btn btn-info view-map" id="btn-map" onclick="viewMap()">View Map</button>
						</div>
					</div>
				</div>
				<table class="table table-bordered">
					<thead>
						<!-- header for the table -->
						<tr>
							<th>First Name</th>
							<th>Last Name</th>
							<th>Promotion</th>
							<th>Country</th>
							<th>City</th>
							<th>Start Date</th>
							<th>End Date</th>
							<?php if ($_SESSION["is_admin"]) {
								echo "<th>Actions</th>";
							} ?>
						</tr>
					</thead>
					<tbody>
						<!-- get mobilities from Database -->
						<?php
						//query
						$query_mobilities = "SELECT firstname, lastname, promotion, country, city, date_start, date_stop, location1, location2, name FROM mobilities JOIN users USING(user_id) JOIN partners USING(partner_id);";
						$result_mobilities =  mysqli_query($link, $query_mobilities);
						//$num = mysqli_num_rows($result_mobilities);//number of rows from the query

						//display in table
						while ($row = mysqli_fetch_array($result_mobilities)) {
							echo "<tr>";
							echo "<td>" . $row['firstname'] . "</td>";
							echo "<td>" . $row['lastname'] . "</td>";
							echo "<td>" . $row['promotion'] . "</td>";
							echo "<td>" . $row['country'] . "</td>";
							echo "<td>" . $row['city'] . "</td>";
							echo "<td>" . $row['date_start'] . "</td>";
							echo "<td>" . $row['date_stop'] . "</td>";
							if ($_SESSION["is_admin"]) {
								echo "<td>";
								echo "<a class=\"add\" title=\"Add\" data-toggle=\"tooltip\"><i class=\"material-icons\">&#xE03B;</i></a>";
								echo "<a class=\"edit\" title=\"Edit\" data-toggle=\"tooltip\"><i class=\"material-icons\">&#xE254;</i></a>";
								echo "<a class=\"delete\" title=\"Delete\" data-toggle=\"tooltip\"><i class=\"material-icons\">&#xE872;</i></a>";
								echo "</td>";
							}
							echo "</tr>";
						};
						?>
					</tbody>
				</table>
				<div class="div-map" style="display: none">
					<div id="mapID" style="height: 500px" style="width: 100%"></div>
					<script type='text/javascript'>
						<?php
						//this code passes the result of the mysql request in php to a javascript array
						$php_array = [];
						$query_mobilities = "SELECT location1, location2, name FROM mobilities JOIN users USING(user_id) JOIN partners USING(partner_id);";
						$result_mobilities =  mysqli_query($link, $query_mobilities);
						while ($row = mysqli_fetch_array($result_mobilities)) {
							array_push($php_array, [$row['name'], floatval($row['location1']), floatval($row['location2'])]);
						};
						$js_array = json_encode($php_array);
						echo "var locations = " . $js_array . ";\n";
						?>

						var mymap = L.map('mapID').setView([45.452, 4.381], 2);

						L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
							maxZoom: 18,
							attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
								'<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
								'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
							id: 'mapbox/streets-v11',
							tileSize: 512,
							zoomOffset: -1
						}).addTo(mymap);


						for (var i = 0; i < locations.length; i++) {
							marker = new L.marker([locations[i][1], locations[i][2]])
								.bindPopup(locations[i][0])
								.addTo(mymap);
						}
					</script>
				</div>
			</div>
		</div>
	</div>
	<?php mysqli_close($link); ?>
</body>

</html>