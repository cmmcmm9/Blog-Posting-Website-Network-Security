<?php

include_once('db.php');
include_once('HMACHashManager.php');

/**
 * Will generate a new cookie and update the values in the Database for the user.
 * The cookie is stored on the user's machine as a json stringified PHP array.
 * cookie has the structure:
 *     $cookieContentsArray = [
            "userID" => $userID,
            "time" => $time,
            "sessionID" => $sessionID,
            "ip" => $ip];
 *
 * Will generate a unique session ID to be stored in the Users table to uniquely identify each user session
 * along with the public IP4 address associated with this login request in the "address" filed in the user's table.
 * The cookie contents are then signed with the HMACHashManager and stored in the database to verify a valid cookie.
 * @param $userID : userID of the user to update
 * @param $ip : public IP4 address of the requesting device
 */
function __createCookieIfNotExists($userID, $ip) {
    $time = time()+2*24*60*60;
    try {
        $sessionID = random_int(0, PHP_INT_MAX);
    } catch (Exception $e) {
        $sessionID = genRandomNumber();
    }

    $db = getDB();
    $cookieName = 'compsecp1';

    //prevent use on different domain and different path on same domain
    $path = "/~csc435Fall2020Group4/";
    $domain = "salemstate.edu";

    $hashManager = new HMACHashManager();

    $cookieContent = jsonStringifyCookieContents($userID, $time, $sessionID, $ip);
    setcookie($cookieName, $cookieContent , $time, $path, $domain);
    $hash = $hashManager->generateHmac($cookieContent);
//    echo "UPDATE Users SET cookie = $hash, cookieTime = $time, sessionID = $sessionID WHERE id = $userID";

    $statementToInsertCookieParameters = $db->prepare("UPDATE Users SET cookie = ?, cookieTime = ?, sessionID = ?, address = ?  WHERE id = ?");
    $statementToInsertCookieParameters->bind_param("sssss", $hash,$time,$sessionID, $ip, $userID);
    $statementToInsertCookieParameters->execute(); //Inserts the hash of "$userID"."-"."$time" into the database

    $statementToInsertCookieParameters->close();
    $db->close();
}

/**
 * Function to return the contents in the cookies (JSON encoded @see [getCookieContentsAsJson]
 * as an associated array. Will return false if the cookies are not present.
 * @return false|mixed : false if no cookies, else the cookies contents as an associated array.
 */
function getCookieContentsAsArray()
{
    $cookieName = 'compsecp1';
    if(!isset($_COOKIE[$cookieName])){
        echo 'COOKIE IS NOT SET ';
        echo '</br>';
    }
    $cookieContents = json_decode($_COOKIE[$cookieName], true);

    if(!isset($cookieContents)){
        return false;
    }
    return $cookieContents;
}

/**
 * Function to encode the cookies content array as a JSON so that it may
 * be stored in cookies. Not using serialize as that can execute PHP code.
 * @param $userID : the userID to store in the cookie
 * @param $time :the time to store in the cookie
 * @param $sessionID : the sessionID to store in the cookie.
 * @param $ip : public IP4 for the user
 * @return false|string
 */
function jsonStringifyCookieContents($userID, $time, $sessionID, $ip){

    $cookieContentsArray = [
        "userID" => $userID,
        "time" => $time,
        "sessionID" => $sessionID,
        "ip" => $ip
    ];

    return json_encode($cookieContentsArray);
}

/**
 * Function to delete the cookie.
 */
function deleteCookie(){
    //set to anynomous
    $cookieContents = [
        "userID" => 24,
        "time" => time()-3600,
        "sessionID" => -1
    ];
    $cookieName = 'compsecp1';
    setcookie($cookieName, json_encode($cookieContents), time()-3600);
}

/**
 * Function to verify the cookies contents with the current contents in the Database.
 * Will first verify the current cookies HMAC value with that of the HMAC stored in the database (@see HMACHashManager)
 * Next it will make sure that the sessionID in the cookie matches the session ID for the
 * corresponding user in the database.
 * After, it will ensure the same public IP4 address stored in the database from the last successsful
 * login for this user matches the IP4 address of the current request.
 * Lastly, it will ensure if the cookie has expired.
 * Will return true if valid, and the user is authenticated or false if not. If a user
 * is not authenticated but has a cookie, that cookie will be destroyed.
 * @return bool : true if the user is authenticated, else false.
 */
function validateSession()
{
    $ip = $_SERVER["REMOTE_ADDR"];

    $db = getDB();
    $hashManager = new HMACHashManager();
    $cookieName = 'compsecp1';
    if(isset($_COOKIE[$cookieName])){
        $cookieContentsAsArray = getCookieContentsAsArray();
        //Takes the cookies contents and separates them into an array

        $statementToGetCookieParameters = $db->prepare("SELECT cookie, sessionID, cookieTime, address FROM Users WHERE id = ? AND sessionID = ?");
        $statementToGetCookieParameters->bind_param("ss", $cookieContentsAsArray["userID"], $cookieContentsAsArray["sessionID"]);
        $statementToGetCookieParameters->execute();
        $cookieParametersInDatabase = mysqli_fetch_array($statementToGetCookieParameters->get_result(), MYSQLI_NUM);

        $statementToGetCookieParameters->close();
        /**
         * Testing below
         */
//        echo '</br>';
//        echo '</br>';
//        //Retrieves the users cookieTime value from the database
//        if($hashManager->verifyHashValues($cookieContentsAsArray, $cookieParametersInDatabase[0])){
//
//        }
//        else{
//            echo "Hash manager failed";
//            echo '</br>';
//        }
//
//        if($cookieParametersInDatabase >= time()){
//
//        }
//        else{
//            echo "Time in database failed";
//            echo '</br>';
//        }
//
//        if($cookieParametersInDatabase[1] !== NULL){
//
//        }
//        else{
//            echo "Session ID was null in database";
//            echo '</br>';
//        }

        if($hashManager->verifyHashValues($cookieContentsAsArray, $cookieParametersInDatabase[0])
            && $cookieParametersInDatabase[2] >= time() && $cookieParametersInDatabase[1] !== NULL && $ip == $cookieParametersInDatabase[3]){

            $db->close();
            return true;

        }
        else{
            $db ->close();
            deleteCookie();
            return false;
        }
    }
    //if here, cookie is NULL
    $db->close();
    return false;

}

/**
 * Function to create a random big integer in case random_int(0, PHP_INT_MAX)
 * fails in @see[__createCookieIfNotExist]
 * @return string : Random number with length of 100
 */
function genRandomNumber() {
    $length = 100;
    $nums = '0123456789';

    // First number shouldn't be zero
    $out = $nums[mt_rand( 1, strlen($nums)-1 )];

    // Add random numbers to your string
    for ($p = 0; $p < $length-1; $p++)
        $out .= $nums[mt_rand( 0, strlen($nums)-1 )];

    return $out;
}
?>