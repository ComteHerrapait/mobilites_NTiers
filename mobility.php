<!DOCTYPE html>
<?php
error_log("start\n", 3, "/var/www/html/logs/php_errors.log");
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
$student = $destination = $date_start = $date_stop = "";
$student_err = $destination_err = $date_start_err = $date_stop_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // validate student
    if (empty($_POST["student"])) {
        $student_err = "Veuillez renseigner un étudiant.";
    } else {
        $student = trim($_POST["student"]);
    }

    // validate destination
    if (empty($_POST["destination"])) {
        $destination_err = "Veuillez renseigner une destination.";
    } else {
        $destination = trim($_POST["destination"]);
    }

    // validate dates
    //TODO : check if dates are in right order
    if (empty($_POST["date_start"])) {
        $date_start_err = "Veuillez renseigner une date.";
    } else {
        $date_start = trim($_POST["date_start"]);
    }
    if (empty($_POST["date_stop"])) {
        $date_stop_err = "Veuillez renseigner une date.";
    } else {
        $date_stop = trim($_POST["date_stop"]);
    }


    // Check input errors before inserting in database
    if (empty($student_err) && empty($destination_err) && empty($date_start_err) && empty($date_stop_err)) {
        if (isset($_POST['btn_create'])) {
            // Prepare an insert statement
            $sql = "INSERT INTO mobilities (date_start, date_stop, partner_id, user_id) VALUES (?, ?, ?, ?)";

            if ($stmt = mysqli_prepare($link, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ssss", $p_dstart, $p_dstop, $p_partner, $p_user);

                // set parameters
                $p_dstart = $date_start;
                $p_dstop = $date_stop;
                $p_partner = floatval($destination);
                $p_user = floatval($student);

                // Attempt to execute the prepared statement
                if (!mysqli_stmt_execute($stmt)) {
                    die("ERROR PROCESSING UPDATE QUERY : \n" . mysqli_stmt_errno($stmt) . "\n" . mysqli_stmt_error($stmt));
                }
            }
            mysqli_stmt_close($stmt);
            header("location: /");
            exit;
        } else if (isset($_POST['btn_delete'])) {
            if (isset($_POST["id_edit_post"])) { // check if an edit id is specified
                $temp = (int) $_POST["id_edit_post"];
                $sql = "DELETE FROM mobilities WHERE (mobility_id = $temp);";
                $deleted_row =  mysqli_query($link, $sql);
            }
            header("location: /");
            exit;
        } else if (isset($_POST['btn_edit'])) {
            if (isset($_POST["id_edit_post"])) { // check if an edit id is specified
                $sql = "UPDATE mobilities SET date_start=?, date_stop=?, partner_id=?, user_id=? WHERE (mobility_id = ?);";
                //$deleted_row =  mysqli_query($link, $sql);
                if ($stmt = mysqli_prepare($link, $sql)) {
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "ssiii", $p_dstart, $p_dstop, $p_partner, $p_user, $p_edit_id);

                    // set parameters
                    $p_dstart = $date_start;
                    $p_dstop = $date_stop;
                    $p_partner = intval($destination);
                    $p_user = intval($student);
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
    die("CLOSE CON");
    mysqli_close($link);
}

$user_id_edit = $partner_id_edit = $date_stop_edit = $date_start_edit = NULL;
if (isset($_GET["id_edit"]) && $_SESSION["is_admin"]) {
    $id_edit = $_GET['id_edit'];
    $query_edit = "SELECT user_id, partner_id, date_start, date_stop FROM mobilities JOIN users USING(user_id) JOIN partners USING(partner_id) WHERE mobility_id = $id_edit;";
    $result_edit =  mysqli_query($link, $query_edit);
    $row_edit = mysqli_fetch_array($result_edit);

    $user_id_edit = $row_edit['user_id'];
    $partner_id_edit = $row_edit['partner_id'];
    $date_stop_edit = $row_edit['date_stop'];
    $date_start_edit = $row_edit['date_start'];

    mysqli_free_result($result_edit);
} else if (!$_SESSION["is_admin"]) {
    //echo '<pre>' . var_export($_POST, true) . '</pre>';
    //echo '<pre>' . var_export($_SESSION, true) . '</pre>';
    //reject attempt if user is not admin and tries to edit a mobility
    echo "<script>alert(\"VOUS N'ÊTES PAS ADMIN.\nContactez un administrateur pour plus d'informations.\")</script>";
    header("location: login.php");
    die("not authorised");
    exit;
}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,height=device-height initial-scale=1">
    <title><?php echo isset($_GET["id_edit"]) ? "Éditer la Mobilité n_" . $_GET['id_edit'] : 'Nouvelle Mobilité' ?></title>
    <link href="form.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="wrapper fadeInDown" id="formContent">
        <h2 class="active"><?php echo isset($_GET["id_edit"]) ? "Éditer la Mobilité n_" . $_GET['id_edit'] : 'Nouvelle Mobilité' ?></h2>
        <p><?php echo isset($_GET["id_edit"]) ? 'Veuillez modifier ce formulaire pour modifier la mobilité.' : 'Veuillez remplir ce formulaire pour créer une nouvelle mobilité.' ?></p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($student_err)) ? 'has-error' : ''; ?>">
                <div><label>Étudiant</label></div>
                <select name="student" class="form-control" id="student" value="<?php echo $student; ?>">
                    <?php
                    $query = "select user_id, username from users";
                    if (!$_SESSION["is_admin"]) {
                        $current_id = $_SESSION["id"];
                        $query = $query . " where user_id = " . $current_id;
                    }
                    if ($stmt = $link->prepare($query)) {
                        $stmt->execute();
                        $stmt->bind_result($user_id, $username);
                        while ($stmt->fetch()) {
                            echo "<option " . ($user_id == $user_id_edit ? 'selected' : '') . " value=\"$user_id\">$username</option>\n";
                        }
                        $stmt->close();
                    }
                    ?>
                </select>
                <span class="help-block"><?php echo $student_err; ?></span>
            </div>
            <div class="fadeIn first form-group <?php echo (!empty($student_err)) ? 'has-error' : ''; ?>">
                <div><label>Destination</label></div>
                <select name="destination" class="form-control" value="<?php echo $destination; ?>">
                    <?php
                    $query = "select partner_id,name,city,country from partners";
                    if ($stmt = $link->prepare($query)) {
                        $stmt->execute();
                        $stmt->bind_result($partner_id, $name, $city, $country);
                        while ($stmt->fetch()) {
                            echo "<option " . ($partner_id == $partner_id_edit ? 'selected' : '') . " value=\"$partner_id\">$name ($city,$country)</option>\n";
                        }
                        $stmt->close();
                    }
                    ?>
                </select>
                <span class="help-block"><?php echo $destination_err; ?></span>
            </div>
            <div class="fadeIn second form-group <?php echo (!empty($date_start_err)) ? 'has-error' : ''; ?>">
                <div><label>Date de début</label></div>
                <input type="date" name="date_start" class="form-control" value=<?php echo is_null($date_start_edit) ? date("Y-m-d") : date($date_start_edit); ?>>
                <span class="help-block"><?php echo $date_start_err; ?></span>
            </div>
            <div class="fadeIn third form-group <?php echo (!empty($date_stop_err)) ? 'has-error' : ''; ?>">
                <div><label>Date de fin</label></div>
                <input type="date" name="date_stop" class="form-control" value=<?php echo is_null($date_stop_edit) ? date("Y-m-d") : date($date_stop_edit); ?>>
                <span class="help-block"><?php echo $date_stop_err; ?></span>
            </div>
            <div class="fadeIn fourth form-group">
                <?php
                if ($_GET["id_edit"]) {
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