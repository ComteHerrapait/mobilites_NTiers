<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty($_POST["username"])) {
        $username_err = "Veuillez renseigner votre nom d'utilisateur.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty($_POST["password"])) {
        $password_err = "Veuillez renseigner votre mot de passe.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT user_id, username, password, admin FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $is_admin);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["is_admin"] = $is_admin == "1";

                            // Redirect user to welcome page
                            header("location: /");
                        } else {
                            // Display an error message if password is not valid
                            $password_err = "Le mot de passe renseigné n'est pas valide.";
                        }
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $username_err = "Il n'y pas de compter avec ce nom d'utilisateur.";
                }
            } else {
                echo "Oops! Quelque chose cloche. Veuillez réessayer plus tard.";
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
    <meta name="viewport" content="width=device-width,height=device-height initial-scale=1">
    <title>Connexion</title>
    <link href="form.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="wrapper fadeInDown" id="formContent" >
        <h2 class="active">SE CONNECTER</h2>
        <p>Veuillez renseigner vos informations pour vous connecter</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="fadeIn first form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Nom d'utilisateur</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="fadeIn second form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Mot de passe</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group fadeIn third">
                <input type="submit" class="btn btn-primary" value="SE CONNECTER">
            </div>
            <p class ="message underlineHover">Vous n'avez pas encore de compte ? 
            <a href="register.php">Enregistre-vous maintenant.</a>.</p>
        </form>
    </div>
</body>

</html>