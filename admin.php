<?php

include_once 'db.php';
/**
 * If the user is not an admin (determined in the database)
 * then it will redirect the user.Otherwise display a message.
 */
try{
    $isUserAdmin = isUserAdmin();
    if($isUserAdmin){
        $username = getUsernameByID(getCookieContentsAsArray()["userID"]);
        echo '<h1>How is the admin life, '.$username.'?</h1>';
    }
    else{
        $username = getUsernameByID(getCookieContentsAsArray()["userID"]);
        echo '<script>window.alert("You are a regular user.. perhaps you meant to go here '.$username.'.. ");window.location.replace("http://weblab.salemstate.edu/~csc435Fall2020Group4/user.php");</script>';
        //header("Location: user.php");
        echo '<h1>You are but a mere simpleton.. a regular user.</h1>';
    }

}
catch (Exception $exception){
    echo '<script>window.alert("You are anonymous.. and now you want to be an admin!?");window.location.replace("http://weblab.salemstate.edu/~csc435Fall2020Group4/register.php")</script>';
//    header("Location: register.php");
    echo '<h1>Kinda Hard to tell if you are not logged in..</h1>';
}


