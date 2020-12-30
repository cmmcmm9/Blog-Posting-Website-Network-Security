<?php
include_once('db.php');
include_once('setcookie.php');

/**
 * Show a welcome message to the user is they are logged in (valid cookie)
 * otherwise tell the user they are not logged in.
 */

if(validateSession()){
    $userName = getUsernameByID(getCookieContentsAsArray()["userID"]);
    echo '<script defer>window.alert("Welome back '.$userName.'!"); </script>';
}
else{
    echo '<script defer>window.alert("You are not logged in.");</script>';

}
?>