<?php
include_once 'setCookie.php';


//set the session IF of the user to NULL in the database, prevent replay attack (as best we can)
deleteSessionID(getCookieContentsAsArray()["userID"]);

//delete the cookie
deleteCookie();

header("Location: home.php");

?>