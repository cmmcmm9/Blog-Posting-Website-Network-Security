<?php
include_once 'db.php';
/**
 * Will display a welcome message if a valid user is logged in.
 * Note: if the user is an admin, it will redirect them to
 * admin.php.
 * Otherwise if not valid, will be redirected to register.php.
 */
try{
    $isUserAdmin = isUserAdmin();
    if($isUserAdmin){
        $username = getUsernameByID(getCookieContentsAsArray()["userID"]);
        echo '<script>window.alert("You are an admin '.$username.'.. perhaps you meant to go here.. ");window.location.replace("http://weblab.salemstate.edu/~csc435Fall2020Group4/admin.php");</script>';
        header("Location: admin.php");
    }
    else{
        $username = getUsernameByID(getCookieContentsAsArray()["userID"]);
        echo '<h1>How is the user life, '.$username.' ?</h1>';

    }

}
catch (Exception $exception){
    echo '<script>window.alert("You are anonymous.. and now you want to be an user!?");window.location.replace("http://weblab.salemstate.edu/~csc435Fall2020Group4/register.php");</script>';
//    header("Location: register.php");
}