<!DOCTYPE html>
<?php
// Include config file
require_once "config.php";

$student = $destination = $date_start = $date_stop = "";
$student_err = $destination_err = $date_start_err = $date_stop_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // validate student
    if(empty($_POST["student"])){
        $student_err = "Please enter a student.";     
    } else{
        $student = trim($_POST["student"]);
    }

    // validate destination
    if(empty($_POST["destination"])){
        $destination_err = "Please enter a destination.";     
    } else{
        $destination = trim($_POST["destination"]);
    }
    
    // validate dates
    if(empty($_POST["date_start"])){
        $date_start_err = "Please enter a date.";     
    } else{
        $date_start = trim($_POST["date_start"]);
    }
    if(empty($_POST["date_stop"])){
        $date_stop_err = "Please enter a date.";     
    } else{
        $date_stop = trim($_POST["date_stop"]);
    }
    

    // Check input errors before inserting in database
    if(empty($student_err) && empty($destination_err) && empty($date_start_err) && empty($date_stop_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO mobilities (date_start, date_stop, partner_id, user_id) VALUES (?, ?, ?, ?)";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $p_dstart, $p_dstop, $p_partner, $p_user);
            
            // set parameters
            $p_dstart = $date_start;
            $p_dstop = $date_stop;
            $p_partner = floatval($destination);
            $p_user = floatval($student);

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Something went wrong. Please try again later.";
                echo "ERROR:\n" ;
                echo mysqli_stmt_errno($stmt);
                echo "ERROR:\n" ;
                echo mysqli_stmt_error($stmt);
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } 
    }
    
    // Close connection
    mysqli_close($link);
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper" >
        <h2>New Mobility</h2>
        <p>Please fill this form to create a new mobility.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" >
            <div class="form-group <?php echo (!empty($student_err)) ? 'has-error' : ''; ?>">
                <label>Student</label>
                <select name="student" class="form-control" value="<?php echo $student; ?>">
                    <?php
                        $query = "select user_id, username from users";
                        if ($stmt = $link->prepare($query)) {
                            $stmt->execute();
                            $stmt->bind_result($user_id, $username);
                            while ($stmt->fetch()) {
                                echo "<option value=\"$user_id\">$username</option>";
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
                                echo "<option value=\"$partner_id\">$name ($city,$country)</option>";
                            }
                            $stmt->close();
                        }
                    ?>
                </select>
                <span class="help-block"><?php echo $destination_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($date_start_err)) ? 'has-error' : ''; ?>">
                <label>Starting Date</label>
                <input type="date" name="date_start" value=<?php echo date("Y-m-d");?>>
                <span class="help-block"><?php echo $date_start_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($date_stop_err)) ? 'has-error' : ''; ?>">
                <label>Ending Date</label>
                <input type="date" name="date_stop" value=<?php echo date("Y-m-d");?>>
                <span class="help-block"><?php echo $date_stop_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Changed your mind? <a href="login.php">go back</a>.</p>
        </form>
    </div>    
</body>
</html>
