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
	<!-- imports -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round|Open+Sans">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />
	<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
	<!-- <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="crossorigin=""></script> -->

	<!-- customs -->
	<link href="table.css" rel="stylesheet" type="text/css">
	<link href="index.css" rel="stylesheet" type="text/css">
	<link href="map.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="script.js"></script>
	<script type="text/javascript" src="map.js"></script>
	<script type="text/javascript" src="search.js"></script>
	<script language="javascript">
		function viewMap() {
			//TODO : select which table to show
			if (document.getElementById("table").style.display != "none") {
				document.getElementById("map").style.display = "block";
				setTimeout(function() {
					mymap.invalidateSize()
				}, 500);
				document.getElementById("table").style.display = "none"
				document.getElementById("btn-map").innerHTML = "View Table";
				document.getElementById("label").style.display = "none";
				
			} else if (document.getElementById("map").style.display != "none") {
				document.getElementById("map").style.display = "none"
				document.getElementById("table").style.display = "block"
				document.getElementById("btn-map").innerHTML = "View Map";
				document.getElementById("label").style.display = "block";
			}
		};

		function viewMobilities() {
			//TODO : select which table to show
			if ((document.getElementById("table-users").style.display != "none") || (document.getElementById("table-partners").style.display != "none")) {
				document.getElementById("table-mobilities").style.display = "block";
				document.getElementById("table-users").style.display = "none";
				document.getElementById("table-partners").style.display = "none";
			}
		};

		function viewUsers() {
			//TODO : select which table to show
			if ((document.getElementById("table-mobilities").style.display != "none") || (document.getElementById("table-partners").style.display != "none")) {
				document.getElementById("table-mobilities").style.display = "none";
				document.getElementById("table-users").style.display = "block";
				document.getElementById("table-partners").style.display = "none";
			}
		};

		function viewPartners() {
			//TODO : select which table to show
			if ((document.getElementById("table-users").style.display != "none") || (document.getElementById("table-mobilities").style.display != "none")) {
				document.getElementById("table-partners").style.display = "block";
				document.getElementById("table-users").style.display = "none";
				document.getElementById("table-mobilities").style.display = "none";
			}
		};
	</script>

</head>

<body>
	<div class="wrap-all">
		<nav class="navbar navbar-expand-lg navbar-light bg-primary">
			<strong style="color:white; font-style: italic"><?php echo "Bienvenue " . $_SESSION["username"] . " ";
			if ($_SESSION["is_admin"]) {
				echo "(admin) ";
			}
			?></strong>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" id="toggler" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarTogglerDemo02">
				<ul class="navbar-nav mr-auto mt-2 mt-lg-0">
					<button class="btn" id="btn-map" onclick="viewMap()" data-toggle="collapse" data-target=".navbar-collapse.show">View Map <span class="sr-only">(current)</span></button>
					<button class="btn" onclick="window.location.href='mobility.php'" data-toggle="collapse" data-target=".navbar-collapse.show">New Mobility</button>
					<button class="btn" onclick="window.location.href='partner.php'" data-toggle="collapse" data-target=".navbar-collapse.show">New Partner</button>
					<?php
					if ($_SESSION['is_admin']) {
						echo "<button type=\"button\" class=\"btn\" onclick=\"window.location.href='user.php'\" data-toggle=\"collapse\" data-target=\".navbar-collapse.show\">New User</button>";
					}
					?>
					<button class="btn" onclick="window.location.href='logout.php'" data-toggle="collapse" data-target=".navbar-collapse.show">Logout</button>
				</ul>
				<form class="form-inline my-2 my-lg-0">
					<input class="form-control mr-sm-2" type="search" id="search-bar" placeholder="Filter" onkeyup="simpleSearch()">
				</form>
			</div>
		</nav>
	</div>
	<div class="bartop" id="label">
		<h1>Student Mobilities in Telecom Saint Etienne</h1>
	</div>
	<div class="main">
		<div>
			<div id="table" style="display: block">
				<div class="btn-group-wrap btn-sm" role="group" style="text-align: center;">
					<button class="btn" id="btn-mobilities" onclick="viewMobilities()">Mobilities</button>
					<button class="btn" id="btn-users" onclick="viewUsers()">Users</button>
					<button class="btn" id="btn-partners" onclick="viewPartners()">Partners</button>
				</div>
				<div class="container-lg">
					<div id="table-mobilities" style="display: block;">
						<table class="list list-mobility">
							<tbody class="mobility-tbody">
								<?php
								//query
								$query_mobilities = "SELECT * FROM mobilities JOIN users USING(user_id) JOIN partners USING(partner_id);";
								$result_mobilities =  mysqli_query($link, $query_mobilities);
								//$num = mysqli_num_rows($result_mobilities);//number of rows from the query

								//display in table
								while ($row = mysqli_fetch_array($result_mobilities)) {
									echo "<tr class=\"list-mobility\">";
									echo "<td class=\"list-name\">";
									echo "<span><strong>" . $row['firstname'] . " " . $row['lastname'] . "</strong></span>";
									echo "<span>" . $row['promotion'] .  "</span></td>";
									echo "<td class=\"list-meta\">";
									echo "<span>" . $row['country'] . ", " . $row['city'] . "</span>";
									echo "<span>" . $row['date_start'] . " / " . $row['date_stop'] . "</span></td>";
									if ($_SESSION["is_admin"]) {
										echo "<td class=\"list-edit\">";
										$temp = $row['mobility_id'];
										echo "<a data-toggle=\"tooltip\" href=\"/mobility.php?id_edit=$temp\"><i class=\"material-icons\">&#xE254;</i></a>";
										echo "</td>";
									};
									echo "</tr>";
								};
								?>
							</tbody>
						</table>
					</div>
					<div id="table-users" style="display: none;">
						<table class="list list-users">

							<tbody>
								<!-- get users from Database -->
								<?php
								//query
								$query_users = "SELECT user_id, firstname, lastname, promotion, email, comment, admin, created_at FROM users;";
								$result_users =  mysqli_query($link, $query_users);

								//display in table
								while ($row = mysqli_fetch_array($result_users)) {
									echo "<tr class=\"list-users\">";
									echo "<td class=\"list-name\">";
									$isAdmin = "";
									if ($row['admin'] == '1')
										$isAdmin = "ADMIN";
									echo "<span><strong>" . $row['firstname'] . " " . $row['lastname'] . "</strong><strong class=\"admin\"
									style=\"color: red\"> " . $isAdmin . "</strong></span>";
									echo "<span>" . $row['promotion'] . "</span></td>";
									echo "<td class=\"list-comment\">" . $row['comment'] . "</td>";
									echo "<td class=\"list-meta\">";
									echo "<span" . $row['email'] . "</span>";
									echo "<span>" . $row['created_at'] . "</span></td>";
									if ($_SESSION["is_admin"]) {
										echo "<td class=\"list-edit\">";
										$temp = $row['user_id'];
										echo "<a data-toggle=\"tooltip\" href=\"/user.php?id_edit=$temp\"><i class=\"material-icons\">&#xE254;</i></a>";
										echo "</td>";
									};
									echo "</tr>";
								};
								?>
							</tbody>
						</table>
					</div>
					<div id="table-partners" style="display: none;">
						<table class="list list-partners">

							<tbody>
								<!-- get partners from Database -->
								<?php
								//query
								$query_partners = "SELECT * FROM partners;";
								$result_partners =  mysqli_query($link, $query_partners);

								//display in table
								while ($row = mysqli_fetch_array($result_partners)) {
									echo "<tr class=\"list-partners\">";
									echo "<td class=\"list-name\">";
									echo "<span><strong>" . $row['name'] . "</span></strong>";
									echo "<span>" . $row['country'] . ", " . $row['city'] . "</span></td>";
									echo "<td class=\"list-meta\">";
									echo "<span>" . $row['location1'] . "</span>";
									echo "<span>" . $row['location2'] . "</span></td>";
									if ($_SESSION["is_admin"]) {
										echo "<td class=\"list-edit\">";
										$temp = $row['partner_id'];
										echo "<a data-toggle=\"tooltip\" href=\"/partner.php?id_edit=$temp\"><i class=\"material-icons\">&#xE254;</i></a>";
										echo "</td>";
									};
									echo "</tr>";
								};
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="div-map" id="map" style="display: none">
				<div id="mapID" style="position: absolute; top: 6em; width: 98%; bottom: 2em;
				margin-left: 1%; z-index: -1;">
					<!--map is here-->
				</div>
				<script type='text/javascript'>
					<?php
					//this code passes the result of the mysql request in php to a javascript array
					$php_array = [];
					$query_mobilities = "SELECT location1, location2, name, firstname, lastname, partner_id, city, country FROM mobilities JOIN users USING(user_id) JOIN partners USING(partner_id);";
					$result_mobilities =  mysqli_query($link, $query_mobilities);
					while ($row = mysqli_fetch_array($result_mobilities)) {
						$loc = [
							$row['name'], //0
							floatval($row['location1']), //1
							floatval($row['location2']), //2
							$row['firstname'], //3
							$row['lastname'], //4
							$row['partner_id'], //5
							$row['city'], //6
							$row['country'] //7
						];
						array_push($php_array, $loc);
					};
					$js_array = json_encode($php_array);
					echo "var mobilities = " . $js_array . ";\n";
					?>
					var mymap = L.map('mapID')
					mymap.setView([0, 0], 3);
					mymap.setMaxBounds([
						[-60, -180],
						[80, 180]
					]);

					L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
						maxZoom: 18,
						attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
							'<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
							'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
						id: 'mapbox/streets-v11',
						tileSize: 512,
						zoomOffset: -1
					}).addTo(mymap);

					mymap.options.minZoom = 3;
					mymap.options.maxZoom = 12;

					var map_markers = new MapMarkers(mobilities);
					map_markers.partners.forEach(function(p) {
						marker = new L.marker(p.getLngLat())
							.bindPopup(p.getPopupText())
							.addTo(mymap);
					});
				</script>
			</div>
		</div>
	</div>
	</div>
	<?php mysqli_close($link); ?>
</body>

</html>

<style>
	.main {
		margin-left: 0;
		z-index: auto;
	}

	div {
		z-index: auto;
	}
	.list-name span {
		display: block;
		text-align: left;
	}

	.list-meta span {
		display: block;
		text-align: right;
	}

	.list tr td {
		padding: 8px 0;
		border-bottom: 1px solid #ddd;
	}

	.list {
		width: 100%;
	}

	.list-edit {
		text-align: right;
	}

	.btn{
		border-color: #007BFF;
		background-color: #007BFF;
		color: white;
		white-space: nowrap;
		text-align: center;
	}

	.btn:hover{
		background-color: #045cb9;
		color: white;
	}

	.btn:active {
		background: #045cb9;
		color: white;
	}

	.btn:focus {
		background: #045cb9;
		text-emphasis-color: white;
	}

</style>