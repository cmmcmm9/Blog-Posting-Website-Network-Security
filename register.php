<?php
require('db.php');
require 'testPass.php';
//this php files is responsible for the registration process
//it is called by the form in register.html
if (isset($_REQUEST['username']))
{
    $db = getDB();
    $username = stripcslashes($_REQUEST['username']);
    $username = mysqli_real_escape_string($db, $username);

    //make sure the username is not already taken.
    $checkIfUserExists = getUserID($username);



    //format username and password
    //more of a paranoia check

    $email = stripcslashes($_REQUEST['email']);
    $email = mysqli_real_escape_string($db, $email);
    $password = stripcslashes($_REQUEST['password']);
    $password = mysqli_real_escape_string($db, $password);

    $options = [
        'cost' => 11
    ];

    //use Bcrypt to hash password
    $passwordSHA256 = password_hash($password, PASSWORD_BCRYPT, $options);

    if(!$checkIfUserExists){
        if(!testPass($password)){
            echo '<script>window.alert("Password does not meet requirements!")</script>';
        }
        else {
            $result = insertNewUser($username, $email, $passwordSHA256);

            //check the queries success.
            //if created, it will redirect to the login.php
            //else it will display an error
            if($result != false)
            {
                print "An error occurred! Please try again!";

            }
            else
            {
                print "<div class='form'><h3>Congratulations..You are Registered!</h3><br/> Not yet a member? <a href='login.php'>Sign Up Here</a></div>";
            header("Location: login.php");
            }
        }
    }
    else echo '<script>window.alert("Username Already Taken")</script>';




}

?>
<!doctype html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register with URFILED</title>
    <link href="css/singlePageTemplate.css" rel="stylesheet" type="text/css">
    <!--The following script tag downloads a font from the Adobe Edge Web Fonts server for use within the web page. We recommend that you do not modify it.-->
    <script>var __adobewebfontsappname__="dreamweaver"</script>
    <script src="http://use.edgefonts.net/source-sans-pro:n2:default.js" type="text/javascript"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<!-- Main Container -->
<div class="container">
    <!-- Navigation -->
    <header> <a href="">
            <h4 class="logo">Blog Website</h4>
        </a>
        <nav>
            <ul>
                <li><a href="home.php">HOME</a></li>
                <li><a href="home.php#about">ABOUT</a></li>
                <li> <a href="register.php">REGISTER</a></li>
                <li> <a href="login.php">SIGN IN</a></li>
            </ul>
        </nav>
    </header>
    <section class="banner_register">
        <div class="header">
            <h1 class="hero_mod_header">Registration<span class="light"></span></h1>
        </div>

        <div class="form_contan">
            <form id="registerForm" class="myform" method="post" action="register.php">
                <br>
                <label>Username</label>
                <input type="text" name="username" value="" required><br>
                <label>Email</label>
                <input type="email" name="email" value="" required><br>
                <label>Password</label>
                <input id= "password" type="password" name="password" required ><br>
                <label>Confirm Password</label>
                <input id = "confirm_password" type="password" name="password_2" required><br>


                <button type="submit" class="button_log_reg" name="register_btn">Register</button>
                <p>
                    Already A Member? <a href="login.php">Sign in</a> </p>
            </form>
        </div>
    </section>
</div>
<script>


    const passwordRegex = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,100}$/;
    const password = document.getElementById("password")
        , confirm_password = document.getElementById("confirm_password");

    /**
     * Function to validate the password meets our requirements
     * and the password and confirm-password are matching.
     * Will prevent the user from submitting the registration form
     * if it is not correct.
     */
    function validatePassword(){
        if(password.value !== confirm_password.value) {
            confirm_password.setCustomValidity("Passwords Don't Match");
        }
        else if(!password.value.match(passwordRegex)){
            console.log("In this if")
            password.setCustomValidity("Passwords Must be At least 8 Characters, Contain 1 Upper Case & a Special Character ");
        }
        else {
            confirm_password.setCustomValidity('');
        }
    }

    password.onchange = validatePassword;
    confirm_password.onkeyup = validatePassword;



</script>

</body>
</html>
