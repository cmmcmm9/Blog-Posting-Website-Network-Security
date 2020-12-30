<?php


require ('db.php');

//Check if the user is logged in and that none of the values are empty.
if(isset($_POST['submit']) && !empty($_POST['old_password'])
               && !empty($_POST['new_password']) && !empty($_POST['confirm_password']) && !validateSession())
{
    $db = getDB();
	$username = mysqli_real_escape_string($db, $_POST['username']);
	$old_password = mysqli_real_escape_string($db, $_POST['old_password']);
	$new_password = mysqli_real_escape_string($db, $_POST['new_password']);
	$confirm_new_password = mysqli_real_escape_string($db, $_POST['confirm_password']);
	$hash_of_old_password = hash('sha256', $old_password);
	
	//Check to see if the username exists in order to continue.
	if(isUserAuthenticated($username, $hash_of_old_password))
	{
		//Check to see if the new passwords match in order to continue. 
		if($new_password == $confirm_new_password)
		{
			$hash_of_new_password = hash('sha256', $new_password);
            $userID = getCookieContentsAsArray()["userID"];
            $statement = $db->prepare("UPDATE Users SET password = ? WHERE id = ?");
			$statement->bind_param("ss", $hash_of_new_password, $userID);
			$statement->execute();
			$result = $statement->get_result();
			$statement->close();
            $db->close();

            if($result)
			{
				echo "<script>alert('Password has been changed. Close alert window to log in.');</script>";
				header("Location: login.php");
			}
			else
				echo "<script>alert('Password was updated.');</script>";
		}
		else
			echo "<script>alert('Passwords do not match.');</script>";
	}
	else 
		echo "<script>alert('Account appears to not exist, please try again.');</script>";

	$db->close();
	
}
else if(validateSession()){
    $currentUser = true;
}
else{
    echo "<script>alert('Please log in.');</script>";
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
                <?php
                //if the user is logged in, display the logout button on the nav bar
                if(isset($currentUser)){
                    echo '<li> <a href="logout.php">LOG OUT</a></li>';
                    echo '<li> <a href="change_password.php">CHANGE PASSWORD</a></li>';
                }else{
                    echo '<li> <a href="register.php">REGISTER</a></li>';
                    echo '<li> <a href="login.php">SIGN IN</a></li>';

                }
                ?>
            </ul>
        </nav>
    </header>
    <section class="banner_register">
        <div class="header">
            <h1 class="hero_mod_header">Change Password<span class="light"></span></h1>
        </div>

        <div class="form_contan">
            <form class="myform" method="post" action="change_password.php">
                <br>
                <label>Username</label>
                <input type="text" name="username" value="" required><br>
                <label>Old password</label>
                <input type="password" name="old_password" value="" required><br>
                <label>New password</label>
                <input id= "password" type="password" name="new_password" required ><br>
                <label>Confirm password</label>
                <input id = "confirm_password" type="password" name="confirm_password" required><br>


                <button type="submit" class="button_log_reg" name="submit">Change</button>
            </form>
        </div>
    </section>
</div>
</body>
</html>
