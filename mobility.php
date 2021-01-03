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

$student = $destination = $date_start = $date_stop = "";
$student_err = $destination_err = $date_start_err = $date_stop_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // validate student
    if (empty($_POST["student"])) {
        $student_err = "Please enter a student.";
    } else {
        $student = trim($_POST["student"]);
    }

    // validate destination
    if (empty($_POST["destination"])) {
        $destination_err = "Please enter a destination.";
    } else {
        $destination = trim($_POST["destination"]);
    }

    // validate dates
    //TODO : check if dates are in right order
    if (empty($_POST["date_start"])) {
        $date_start_err = "Please enter a date.";
    } else {
        $date_start = trim($_POST["date_start"]);
    }
    if (empty($_POST["date_stop"])) {
        $date_stop_err = "Please enter a date.";
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
                    echo "Something went wrong. Please try again later.";
                    echo "ERROR:\n";
                    echo mysqli_stmt_errno($stmt);
                    echo "ERROR:\n";
                    echo mysqli_stmt_error($stmt);
                }
            }
            mysqli_stmt_close($stmt);
            header("location: /");
            exit;
        } else if (isset($_POST['btn_delete'])) {
            if ($_POST["id_edit_post"]) { // check if an edit id is specified
                $temp = (int) $_POST["id_edit_post"];
                $sql = "DELETE FROM mobilities WHERE (mobility_id = $temp);";
                $deleted_row =  mysqli_query($link, $sql);
            }
            header("location: /");
            exit;
        } else if (isset($_POST['btn_edit'])) {
            // TODO
            error_log("button edit clicked\n", 3, "/var/www/html/logs/php_errors.log");
            header("location : /");
            exit;
        } else {
            // invalid
            die('invalid button');
        }
    }
    // Close connection
    mysqli_close($link);
}

$user_id_edit = $partner_id_edit = $date_stop_edit = $date_start_edit = NULL;
if ($_GET["id_edit"]) {
    //TODO : reject if user is not admin
    $id_edit = $_GET['id_edit'];
    $query_edit = "SELECT user_id, partner_id, date_start, date_stop FROM mobilities JOIN users USING(user_id) JOIN partners USING(partner_id) WHERE mobility_id = $id_edit;";
    $result_edit =  mysqli_query($link, $query_edit);
    $row_edit = mysqli_fetch_array($result_edit);

    $user_id_edit = $row_edit['user_id'];
    $partner_id_edit = $row_edit['partner_id'];
    $date_stop_edit = $row_edit['date_stop'];
    $date_start_edit = $row_edit['date_start'];

    mysqli_free_result($result_edit);
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $_GET["id_edit"] ? 'Edit Mobility' : 'New Mobility' ?></title>
    <link href="forms.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 350px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2><?php echo $_GET["id_edit"] ? "Edit Mobility n_" . $_GET['id_edit'] : 'New Mobility' ?></h2>
        <p><?php echo $_GET["id_edit"] ? 'Please edit this form to edit an existing mobility.' : 'Please fill this form to create a new mobility.' ?></p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($student_err)) ? 'has-error' : ''; ?>">
                <label>Student</label>
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
            <div class="form-group <?php echo (!empty($student_err)) ? 'has-error' : ''; ?>">
                <label>Destination</label>
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
            <div class="form-group <?php echo (!empty($date_start_err)) ? 'has-error' : ''; ?>">
                <label>Starting Date</label>
                <input type="date" name="date_start" value=<?php echo is_null($date_start_edit) ? date("Y-m-d") : date($date_start_edit); ?>>
                <span class="help-block"><?php echo $date_start_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($date_stop_err)) ? 'has-error' : ''; ?>">
                <label>Ending Date</label>
                <input type="date" name="date_stop" value=<?php echo is_null($date_stop_edit) ? date("Y-m-d") : date($date_stop_edit); ?>>
                <span class="help-block"><?php echo $date_stop_err; ?></span>
            </div>
            <div class="form-group">
                <?php 
                if ($_GET["id_edit"]){
                    echo "<input type=\"submit\" class=\"btn btn-primary\" name=\"btn_edit\" value=\"Edit (WIP)\" />";
                } else {
                    echo "<input type=\"submit\" class=\"btn btn-primary\" name=\"btn_create\" value=\"Create\" />";
                }
                ?>
                <input type="submit" class="btn btn-primary" name="btn_delete" value="Delete" />
                <input type="reset" class="btn btn-default" name="btn_reset" value="Reset" />
            </div>
            <p class="message">Changed your mind? <a href="login.php">go back</a>.</p>
            <input type="hidden" name="id_edit_post" value="<?php echo $_GET["id_edit"];?>" />
        </form>
    </div>
</body>

</html>