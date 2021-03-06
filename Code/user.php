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
$username = $password = $confirm_password = $fname = $lname = $promotion = $email = $is_admin_post = "";
$username_err = $password_err = $confirm_password_err = $fname_err = $lname_err = $promo_err = $email_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty($_POST["username"])) {
        $username_err = "Veuillez entrer un nom d'utilisateur.";
    } else if (isset($_POST['btn_create'])) {
        //check if username exists when creating new user

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
                    $username_err = "Ce nom d'utilisateur est déjà pris.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Quelque chose cloche. Veuillez réessayer plus tard.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    } else if (isset($_POST['btn_edit'])){
        $username = trim($_POST["username"]);
    }

    // Do not validate password in edit mode, check if an id to edit has been passed in POST
    if (!isset($_POST["id_edit_post"])) {
        // Validate password
        if (empty($_POST["password"])) {
            $password_err = "Veuillez entrer un mot de passe.";
        } elseif (strlen(trim($_POST["password"])) < 6) {
            $password_err = "Le mot de passe doit comprendre au moins 6 charactères.";
        } else {
            $password = trim($_POST["password"]);
        }

        // Validate confirm password
        if (empty($_POST["confirm_password"])) {
            $confirm_password_err = "Veuillez confirmer le mot de passe.";
        } else {
            $confirm_password = trim($_POST["confirm_password"]);
            if (empty($password_err) && ($password != $confirm_password)) {
                $confirm_password_err = "Les mots de passe ne correspondent pas.";
            }
        }
    } else {
        $is_admin_post = isset($_POST['is_admin']);
    }

    // validate promotion
    if (empty($_POST["promotion"])) {
        $promo_err = "Veuillez renseigner une promotion.";
    } else {
        $promotion = trim($_POST["promotion"]);
    }

    // validate email
    if (empty($_POST["email"])) {
        $email_err = "Veuillez renseigner une adresse mail.";
    } elseif (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $email = trim($_POST["email"]);
    } else {
        $email_err = "Cet email n'est pas valide.";
    }

    // validate first and last name
    if (empty($_POST["lastname"])) {
        $lname_err = "Veuillez renseigner un nom.";
    } else {
        $lname = trim($_POST["lastname"]);
    }
    if (empty($_POST["firstname"])) {
        $fname_err = "Veuillez renseigner un prénom.";
    } else {
        $fname = trim($_POST["firstname"]);
    }

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($first_name_err) && empty($last_name_err) && empty($promo_err) && empty($email_err)) {
        if (isset($_POST['btn_create'])) {
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
                if (!mysqli_stmt_execute($stmt)) {
                    die("ERROR PROCESSING UPDATE QUERY : \n" . mysqli_stmt_errno($stmt) . "\n" . mysqli_stmt_error($stmt));
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        } else if (isset($_POST['btn_delete'])) {
            if (isset($_POST["id_edit_post"])) { // check if an edit id is specified
                $temp = (int) $_POST["id_edit_post"];
                $sql = "DELETE FROM users WHERE (user_id = $temp);";
                $deleted_row =  mysqli_query($link, $sql);
            }
            header("location: /");
            exit;
        } else if (isset($_POST['btn_edit'])) {
            // TODO : change password and comment
            if (isset($_POST["id_edit_post"])) {
                $sql = "UPDATE users SET username=?, admin=?, promotion=?, firstname=?, lastname=?, email=?, comment=? WHERE (user_id=?);";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "sisssssi", $p_usrname, $p_admin, $p_promo, $p_fname, $p_lname, $p_mail, $p_comment, $p_edit_id );

                    // set parameters
                    $p_usrname = $username;
                    $p_admin = $is_admin_post? 1: 0;
                    $p_promo = $promotion;
                    $p_fname = $fname;
                    $p_lname = $lname;
                    $p_mail = $email;
                    $p_comment = $_POST["comment"];
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
$fname_edit = $lname_edit = $mail_edit = $promotion_edit = $password_edit = $admin_edit = NULL;
if (isset($_GET["id_edit"]) && $_SESSION["is_admin"]) {
    $id_edit = $_GET['id_edit'];
    $query_edit = "SELECT * FROM users WHERE user_id = $id_edit;";
    $result_edit =  mysqli_query($link, $query_edit);
    $row_edit = mysqli_fetch_array($result_edit);

    $fname_edit = $row_edit["firstname"];
    $lname_edit = $row_edit["lastname"];
    $mail_edit = $row_edit["email"];
    $promotion_edit = $row_edit["promotion"];
    $password_edit = $row_edit["password"];
    $admin_edit = $row_edit["admin"];
    $comment_edit = $row_edit["comment"];

    mysqli_free_result($result_edit);
} else if (!$_SESSION["is_admin"]) {
    //echo '<pre>' . var_export($_POST, true) . '</pre>';
    //echo '<pre>' . var_export($_SESSION, true) . '</pre>';
    //reject attempt if user is not admin and tries to edit an user
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
    <title><?php echo isset($_GET["id_edit"]) ? "Modifier l'utilisateur n_" . $_GET['id_edit'] : 'Nouvel Utilisateur' ?></title>
    <link href="form.css" rel="stylesheet" type="text/css">
</head>

<body onLoad="update_username()">
    <div class="wrapper fadeInDown user" id="formContent">
        <h2 class="active"><?php echo isset($_GET["id_edit"]) ? "Modifier l'utilisateur n_" . $_GET['id_edit'] : 'Nouvel Utilisateur' ?></h2>
        <p><?php echo isset($_GET["id_edit"]) ? 'Veuillez modifier ce formulaire pour modifier un utilisateur existant.' : 'Veuillez modifier ce formulaire pour créer un nouvel utilisateur.' ?></p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="fadeIn first form-group <?php echo (!empty($fname_err)) ? 'has-error' : ''; ?>">
                <div><label>Prénom</label></div>
                <input type="text" name="firstname" id="fname" class="form-control" value=<?php echo is_null($fname_edit) ? "" : "$fname_edit"; ?>>
                <span class="help-block"><?php echo $fname_err; ?></span>
            </div>
            <div class="fadeIn second form-group <?php echo (!empty($lname_err)) ? 'has-error' : ''; ?>">
                <div><label>Nom</label></div>
                <input type="text" name="lastname" id="lname" class="form-control" value=<?php echo is_null($lname_edit) ? "" : "$lname_edit"; ?>>
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
            <div class="fadeIn third form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <div><label>Nom d'utilisateur</label></div>
                <input type="text" name="username" id="usrname" class="form-control" value="<?php echo $username; ?>" readonly="readonly">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="fadeIn fourth form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <div><label>Email</label></div>
                <input type="mail" name="email" class="form-control" value=<?php echo is_null($mail_edit) ? "" : "$mail_edit"; ?>>
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
            <div class="fadeIn fifth form-group <?php echo (!empty($promo_err)) ? 'has-error' : ''; ?>">
                <div><label>Promotion</label></div>
                <select name="promotion" class="form-control" value="<?php echo $promotion; ?>">
                    <?php
                    foreach (array("Autre", "FISE1", "FISE2", "FISE3", "FISA-DE1", "FISA-DE2", "FISA-DE3", "FISA-IPSI1", "FISA-IPSI2", "FISA-IPSI3", "CITISE1", "CITISE2", "SMW", "Info-Com", "DCIMN1", "DCIMN2", "DTA", "Administration", "Alumni") as $promo_name) {
                        echo "<option ". ($promotion_edit==$promo_name? 'selected ' : '') . "value=\"$promo_name\">$promo_name</option>";
                    }
                    ?>
                </select>
                <span class="help-block"><?php echo $promo_err; ?></span>
            </div>
            <div class="fadeIn sixth form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>" <?php echo isset($_GET['id_edit']) ? 'style="display: none;"' : '' ?>>
                <div><label>Mot de passe</label></div>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="fadeIn seventh form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>" <?php echo isset($_GET['id_edit']) ? 'style="display: none;"' : '' ?>>
                <div><label>Confirmer mot de passe</label></div>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="fadeIn eighth form-group" <?php echo isset($_GET['id_edit']) ? '' : 'style="display: none;"' ?>>
                <div><label>Droits d'administrateur</label></div>
                <input type="checkbox" name="is_admin" class="form-control" <?php echo $admin_edit ? 'checked' : '' ?>>
            </div>
            <div class="fadeIn nineth form-group" <?php echo isset($_GET['id_edit']) ? '' : 'style="display: none;"' ?>>
                <div><label>Commentaire</label></div>
                <input type="text" name="comment" class="form-control" <?php echo "value='$comment_edit'"?>>
            </div>
            <div class="fadeIn tenth form-group">
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
            <p class="message underlineHover">Vous avez changé d'avis ? <a href="/">Retour</a>.</p>
            <!-- hidden input to pass the mobility ID from GET to POST -->
            <input type="hidden" name="id_edit_post" value="<?php echo $_GET["id_edit"]; ?>" />
        </form>
    </div>
</body>

</html>