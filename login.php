
<?php
require('db.php');

//this php file is responsible for logging the user in
if (isset($_REQUEST['username']))
{
    $db = getDB();

    //format the username and password
    //more of a paranoia check because we are using prepared statements
    $username = stripcslashes($_REQUEST['username']);
    $username = mysqli_real_escape_string($db, $username);
    $password = stripcslashes($_REQUEST['password']);
    $password = mysqli_real_escape_string($db, $password);


    $ip = $_SERVER["REMOTE_ADDR"];


    //make sure user is authenticated and this public IP4 address has not reached its max login attempts
    if(isUserAuthenticated($username, $password) && !isAtMaxAllowedAttempts($ip))
    {
        $cookieName = 'compsecp1';
        $userID = getUserID($username);
//        echo "User id is $userID";
        __createCookieIfNotExists($userID, $ip);
        //variables used in home.php to check if the user is logged in, and thier username
//        if(!isset($_COOKIE[$cookieName])) {
//            echo "Cookie named '" . $cookieName . "' is not set!";
//        } else {
//            echo "Cookie '" . $cookieName . "' is set!<br>";
//            echo "Value is: " . $_COOKIE[$cookieName];
//        }
        header("Location: home.php");
    }
    else
    {
        //show an alert about failure to authenticate
        echo "<script>";
        echo 'alert("Sorry we do not recognize this username or password.")';
        echo "</script>";
    }
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
    <section class="banner">
        <div class="header">
            <h1 class="hero_mod_header">Sign In<span class="light"></span></h1>
        </div>
        <form method="post" action="login.php">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username">
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password">
            </div>

            <div class="input-group">
                <button type="submit" class="button_log_reg" name="login_user">Login</button>
            </div>
            <p>Not yet a member? <a href="register.php">Sign up!</a></p>
        </form>
    </section>
</div>
</body>
</html>
