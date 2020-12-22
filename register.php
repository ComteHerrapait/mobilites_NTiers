<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$username = $password = $confirm_password = $fname = $lname = $promotion = $email = "";
$username_err = $password_err = $confirm_password_err = $fname_err = $lname_err = $promo_err = $email_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty($_POST["username"])) {
        $username_err = "Please enter a username.";
    } else {
        // Prepare a select statement
        $sql = "SELECT user_id FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if (empty($_POST["password"])) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty($_POST["confirm_password"])) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // validate promotion
    if (empty($_POST["promotion"])) {
        $promo_err = "Please enter a promotion.";
    } else {
        $promotion = trim($_POST["promotion"]);
    }

    // validate email
    if (empty($_POST["email"])) {
        $email_err = "Please enter a email.";
    } elseif (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $email = trim($_POST["email"]);
    } else {
        $email_err = "This email is invalid.";
    }

    // validate first and last name
    if (empty($_POST["lastname"])) {
        $lname_err = "Please enter a last name.";
    } else {
        $lname = trim($_POST["lastname"]);
    }
    if (empty($_POST["firstname"])) {
        $fname_err = "Please enter a first name.";
    } else {
        $fname = trim($_POST["firstname"]);
    }

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($first_name_err) && empty($last_name_err) && empty($promo_err) && empty($email_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, promotion, firstname, lastname, email) VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssss", $param_username, $param_password, $param_promo, $param_fname, $param_lname, $param_email);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_promo = $promotion;
            $param_fname = $fname;
            $param_lname = $lname;
            $param_email = $email;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                header("location: login.php");
            } else {
                echo "Something went wrong. Please try again later.";
                echo "ERROR:\n";
                echo mysqli_stmt_errno($stmt);
                echo "ERROR:\n";
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link href="register.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($fname_err)) ? 'has-error' : ''; ?>">
                <label>Firstname</label>
                <input type="text" name="firstname" id="fname" class="form-control" value="<?php echo $fname; ?>">
                <span class="help-block"><?php echo $fname_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($lname_err)) ? 'has-error' : ''; ?>">
                <label>Lastname</label>
                <input type="text" name="lastname" id="lname" class="form-control" value="<?php echo $lname; ?>">
                <span class="help-block"><?php echo $lname_err; ?></span>
            </div>
            <script>
                //creates a username using first and lastname of the user
                function update_username() {
                    //use temp variables because the replacing function removes the "."
                    var lname_temp = lname.value.normalize('NFKD').replace(/[^\w]/g, ''); //remove accents;
                    var fname_temp = fname.value.normalize('NFKD').replace(/[^\w]/g, '');
                    usrname.value = lname_temp.toLowerCase() + "." + fname_temp.toLowerCase();
                }
                fname.addEventListener('input', update_username);
                lname.addEventListener('input', update_username);
            </script>
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" id="usrname" class="form-control" value="<?php echo $username; ?>" readonly="readonly">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Mail</label>
                <input type="mail" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($promo_err)) ? 'has-error' : ''; ?>">
                <label>Promotion</label>
                <select name="promotion" class="form-control" value="<?php echo $promotion; ?>">
                    <?php
                    foreach (array("Other", "FISE1", "FISE2", "FISE3", "FISA-DE1", "FISA-DE2", "FISA-DE3", "FISA-IPSI1", "FISA-IPSI2", "FISA-IPSI3", "CITISE1", "CITISE2", "SMW", "Info-Com", "DCIMN1", "DCIMN2", "DTA", "Administration", "Alumni") as $promo_name) {
                        echo "<option value=\"$promo_name\">$promo_name</option>";
                    }
                    ?>

                </select>
                <span class="help-block"><?php echo $promo_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p class ="message">Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>
</body>

</html>