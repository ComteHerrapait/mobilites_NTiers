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

// Define variables and initialize with empty values
$name = $country = $city = $location1 = $location2 = "";
$name_err = $country_err = $city_err = $location1_err = $location2_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // validate name and city and check if unique combinaison
    if (empty($_POST["name"]) or empty($_POST["city"])) {
        if (empty($_POST["name"])) {
            $name_err = "Veuillez renseigner un nom.";
        } else {
            $city_err = "Veuillez renseigner une ville.";
        }
    } else {
        $name = trim($_POST["name"]);
        // Prepare a select statement
        $sql = "SELECT partner_id FROM partners WHERE (LOWER(name) = ? AND LOWER(city) = ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_name, $param_city);

            // Set parameters
            $param_name = strtolower(trim($_POST["name"]));
            $param_city = strtolower(trim($_POST["city"]));

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $name_err = "Cette entrée existe déjà.";
                } else {
                    $name = trim($_POST["name"]);
                    $city = trim($_POST["city"]);
                }
            } else {
                echo "Oops! Quelque chose cloche. Veuillez réessayer plus tard.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // validate country
    if (empty($_POST["country"])) {
        $country_err = "Veuillez renseigner un pays.";
    } else {
        $country = trim($_POST["country"]);
    }

    //get location
    if (empty($_POST["location1"]) or (empty($_POST["location2"]))) {
        $location_err = "Cliquez sur \"Trouver le lieu\" pour générer la latitude et la longitude de la ville";
    } else {
        $location1 = trim($_POST["location1"]);
        $location2 = trim($_POST["location2"]);
    }

    // Check input errors before inserting in database
    if (empty($name_err) && empty($country_err) && empty($city_err) && empty($location_err)) {
        if (isset($_POST['btn_create'])) {

            // Prepare an insert statement
            $sql = "INSERT INTO partners (name, location1, location2, country, city) VALUES (?, ?, ?, ?, ?)";

            if ($stmt = mysqli_prepare($link, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "sddss", $p_name, $p_location1, $p_location2, $p_country, $p_city);

                // set parameters
                $p_name = $name;
                $p_location1 = floatval($location1);
                $p_location2 = floatval($location2);
                $p_country = $country;
                $p_city = $city;

                // Attempt to execute the prepared statement
                if (!mysqli_stmt_execute($stmt)) {
                    die("ERROR PROCESSING UPDATE QUERY : \n" . mysqli_stmt_errno($stmt) . "\n" . mysqli_stmt_error($stmt));
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        } else if (isset($_POST['btn_delete'])) {
            if (isset($_POST["id_edit_post"])) { // check if an edit id is specified
                $temp = (int) $_POST["id_edit_post"];
                $sql = "DELETE FROM partners WHERE (partner_id = $temp);";
                $deleted_row =  mysqli_query($link, $sql);
            }
            header("location: /");
            exit;
        } else if (isset($_POST['btn_edit'])) {
            if (isset($_POST["id_edit_post"])) {
                $sql = "UPDATE partners SET name=?, location1=?, location2=?, country=?, city=? WHERE (partner_id=?);";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "sddssi", $p_name, $p_loc1, $p_loc2, $p_country, $p_city, $p_edit_id);

                    // set parameters
                    $p_name = $name;
                    $p_loc1 = (float) $location1;
                    $p_loc2 = (float) $location2;
                    $p_country = $country;
                    $p_city = $city;
                    $p_edit_id = (int) $_POST["id_edit_post"];

                    // Attempt to execute the prepared statement
                    if (!mysqli_stmt_execute($stmt)) {
                        die("ERROR PROCESSING UPDATE QUERY : \n" . mysqli_stmt_errno($stmt) . "\n" . mysqli_stmt_error($stmt));
                    }
                }
            }
            mysqli_stmt_close($stmt);
            header("location: /");
            exit;
        } else {
            // invalid
            die('invalid button');
        }
    }

    // Close connection
    mysqli_close($link);
}
$name_edit = $country_edit = $city_edit = $loc1_edit = $loc2_edit = NULL;
if (isset($_GET["id_edit"]) && $_SESSION["is_admin"]) {
    $id_edit = $_GET['id_edit'];
    $query_edit = "SELECT * FROM partners WHERE partner_id = $id_edit;";
    $result_edit =  mysqli_query($link, $query_edit);
    $row_edit = mysqli_fetch_array($result_edit);

    $name_edit =  $row_edit["name"];
    $country_edit =  $row_edit["country"];
    $city_edit =  $row_edit["city"];
    $loc1_edit =  $row_edit["location1"];
    $loc2_edit = $row_edit["location2"];

    mysqli_free_result($result_edit);
} else if (!$_SESSION["is_admin"]) {
    //echo '<pre>' . var_export($_POST, true) . '</pre>';
    //echo '<pre>' . var_export($_SESSION, true) . '</pre>';
    //reject attempt if user is not admin and tries to edit a partner
    echo "<script>alert(\"VOUS N'ÊTES PAS ADMIN.\nContactez un administrateur pour plus d'informations.\")</script>";
    header("location: login.php");
    die("not authorised");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,height=device-height initial-scale=1">
    <title><?php echo isset($_GET["id_edit"]) ? "Modifier partenaire n_" . $_GET['id_edit'] : 'Nouveau Partneraire' ?></title>
    <link href="form.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="wrapper fadeInDown" id="formContent">
        <h2 class="active"><?php echo isset($_GET["id_edit"]) ? "Modifier partenaire n_" . $_GET['id_edit'] : 'Nouveau Partneraire' ?></h2>
        <p><?php echo isset($_GET["id_edit"]) ? 'Veuillez modifier ce formulaire pour modifier un partenaire existant.' : 'Veuillez modifier ce formulaire pour créer un nouveau partenaire.' ?></p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

            <div class="fadeIn first form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                <div><label>Nom</label></div>
                <input type="text" name="name" class="form-control" value="<?php echo isset($_GET["id_edit"]) ? $name_edit : $name; ?>">
                <span class="help-block"><?php echo $name_err; ?></span>
            </div>
            <div class="fadeIn second form-group <?php echo (!empty($country_err)) ? 'has-error' : ''; ?>">
                <div><label>Pays</label></div>
                <input type="text" name="country" id="country" class="form-control" value="<?php echo isset($_GET["id_edit"]) ? $country_edit : $country; ?>">
                <span class="help-block"><?php echo $country_err; ?></span>
            </div>
            <div class="fadeIn third form-group <?php echo (!empty($city_err)) ? 'has-error' : ''; ?>">
                <div><label>Ville</label></div>
                <input type="text" name="city" id="city" class="form-control" value="<?php echo isset($_GET["id_edit"]) ? $city_edit : $city; ?>">
                <span class="help-block"><?php echo $city_err; ?></span>
            </div>
            <div class="fadeIn fourth form-group <?php echo (!empty($location_err)) ? 'has-error' : ''; ?>">
                <div><label>Lieu</label></div>
                <input type="text" name="location1" id="latitude" class="form-control" value="<?php echo isset($_GET["id_edit"]) ? $loc1_edit : $location1; ?>" <?php echo isset($_GET['id_edit']) ? '' : 'readonly="readonly"' ?>>
                <input type="text" name="location2" id="longitude" class="form-control" value="<?php echo isset($_GET["id_edit"]) ? $loc2_edit : $location2; ?>" <?php echo isset($_GET['id_edit']) ? '' : 'readonly="readonly"' ?>>
                <button type="button" class="btn btn-info view-map" id="btn-map" onclick="findLocation()">Trouver le lieu</button>
                <script src="https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.js"></script>
                <link type="text/css" rel="stylesheet" href="https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.css" />
                <script>
                    function findLocation() {
                        //Latitude and Longitude are determined by Mapquest
                        //https://developer.mapquest.com/documentation/mapquest-js/v1.3/examples/geocoding-with-a-single-line-address
                        L.mapquest.key = 'brtPFIZQrVIdU4AeUYc6GrFeVLmOEyg0'; //Leon's API key
                        var query = city.value + ", " + country.value
                        L.mapquest.geocoding().geocode(query, response);

                        function response(error, content) {
                            var location = content.results[0].locations[0];
                            var latLng = location.displayLatLng;
                            latitude.value = latLng.lat;
                            longitude.value = latLng.lng;
                        }
                    }
                </script>
                <span class="help-block"><?php echo $location_err; ?></span>
            </div>
            <div class="fadeIn fifth form-group">
                <?php
                if (isset($_GET["id_edit"])) {
                    echo "<input type=\"submit\" class=\"btn btn-primary\" name=\"btn_edit\" value=\"Modifier\" />";
                } else {
                    echo "<input type=\"submit\" class=\"btn btn-primary\" name=\"btn_create\" value=\"Créer\" />";
                }
                ?>
                <input type="submit" class="btn btn-primary" name="btn_delete" value="Supprimer" />
                <input type="reset" class="btn btn-default" name="btn_reset" value="Reset" />
            </div>
            <p class="message underlineHover">Vous avez changé d'avis ? <a href="login.php">Retour</a>.</p>
            <!-- hidden input to pass the mobility ID from GET to POST -->
            <input type="hidden" name="id_edit_post" value="<?php echo $_GET["id_edit"]; ?>" />
        </form>
    </div>
</body>

</html>