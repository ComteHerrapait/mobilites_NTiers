<!DOCTYPE html>
<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$name = $country = $city = $location1 = $location2 = "";
$name_err = $country_err = $city_err = $location1_err = $location2_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // validate name
    if(empty($_POST["name"])){
        $name_err = "Please enter a name.";     
    } else{
        $name = trim($_POST["name"]);
    }

    // validate country
    if(empty($_POST["country"])){
        $country_err = "Please enter a country name.";     
    } else{
        $country = trim($_POST["country"]);
    }
    
    // validate city
    if(empty($_POST["city"])){
        $city_err = "Please enter a city name.";     
    } else{
        $city = trim($_POST["city"]);
    }

    //get location
    if (empty($_POST["location1"]) or (empty($_POST["location2"]))){
        $location_err = "Click on Find Location to generate Latitude and Longitude for your city";
    } else {
        $location1 = trim($_POST["location1"]);
        $location2 = trim($_POST["location2"]);
    }
    

    // Check input errors before inserting in database
    if(empty($name_err) && empty($country_err) && empty($city_err) && empty($location_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO partners (name, location1, location2, country, city) VALUES (?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sddss", $p_name, $p_location1, $p_location2, $p_country, $p_city);
            
            // set parameters
            $p_name = $name;
            $p_location1 = floatval($location1);
            $p_location2 = floatval($location2);
            $p_country = $country;
            $p_city= $city;

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
        <h2>New Partner</h2>
        <p>Please fill this form to create a new partner.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" >

            <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                <label>Name</label>
                <input type="text" name="name"  class="form-control" value="<?php echo $name; ?>">
                <span class="help-block"><?php echo $name_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($country_err)) ? 'has-error' : ''; ?>">
                <label>Country</label>
                <input type="text" name="country" id="country" class="form-control" value="<?php echo $country; ?>">
                <span class="help-block"><?php echo $country_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($city_err)) ? 'has-error' : ''; ?>">
                <label>City</label>
                <input type="text" name="city" id="city" class="form-control" value="<?php echo $city; ?>">
                <span class="help-block"><?php echo $city_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($location_err)) ? 'has-error' : ''; ?>">
                <label>Location</label>
                <input type="text" name="location1" id="latitude" class="form-control" value="<?php echo $location1; ?>" readonly="readonly">
                <input type="text" name="location2" id="longitude" class="form-control" value="<?php echo $location2; ?>" readonly="readonly">
                <button type="button" class="btn btn-info view-map" id="btn-map" onclick="findLocation()">Find Location</button>
                <script src="https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.js"></script>
                <link type="text/css" rel="stylesheet" href="https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.css"/>
                <script>
                    function findLocation(){
                        //Latitude and Longitude are determined by Mapquest
                        //https://developer.mapquest.com/documentation/mapquest-js/v1.3/examples/geocoding-with-a-single-line-address
                        L.mapquest.key = 'brtPFIZQrVIdU4AeUYc6GrFeVLmOEyg0';//Leon's API key
                        var query = country.value +", "+ city.value
                        L.mapquest.geocoding().geocode(query,response);
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
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Changed your mind? <a href="login.php">go back</a>.</p>
        </form>
    </div>    
</body>
</html>