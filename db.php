<?php

/**
 * This file is responsible for all of the database queries and the database connection.
 */

include("DecryptionManager.php");
include_once 'setCookie.php';

/**
 * Function to insert a new user.
 * Will return false if the user was not inserted.
 * @param $username
 * @param $email
 * @param $hashedPassword
 * @return false|mysqli_result
 */
function insertNewUser($username, $email, $hashedPassword){
    $username = strip_tags(htmlspecialchars($username));
    $email = strip_tags(htmlspecialchars(($email)));
    $db = getDB();
    $statement = $db->prepare("INSERT INTO Users (username, email, password) values (?, ?, ?)");
    $statement->bind_param("sss", $username, $email, $hashedPassword);
    $statement->execute();
//    echo "Error inserting user is: " . $statement->error;

    //the result of the query
    $result = $statement->get_result();
    $statement->close();
    $db->close();

    return $result;

}



/**
 * Function to determine if the user is authenticated given their credentials.
 * Password is retrieved from database and using the @see[password_verify()]
 * @param $username
 * @param $password
 * @return bool whether the user is authenticated
 */
function isUserAuthenticated($username, $password)
{
    $db = getDB();
    //query to check if the username and hashed password are in the db
    $statement = $db->prepare("SELECT password from Users where username = ? LIMIT 1");
    $statement->bind_param("s", $username );
    $statement->execute();
    //result of the query
    $result = $statement->get_result()->fetch_assoc();
    $passwordInDatabase = $result["password"];

    $statement->close();
    $db->close();

//    if(password_verify($password, $passwordInDatabase)){
//        echo "<br>";
//        echo "password verify was true";
//        return true;
//    }
//    else {
//        echo "<br>";
//        echo "password verify was false";
//        return false;
//    }
    return password_verify($password, $passwordInDatabase);

}

/**
 * Function to return the User ID of a user by their username.
 * @param $username : username to query for
 * @return false|int :false if no user by this name, userID if success
 */
function getUserID($username){
    $db = getDB();
    $statementToGetUserID = $db->prepare("SELECT id FROM Users WHERE username = ?");
    $statementToGetUserID->bind_param("s", $username);
    $statementToGetUserID->execute();
    print $statementToGetUserID->error;
    $result = $statementToGetUserID->get_result();
    if(!$result){
        print "Result is false, no user with this ID";
        return false;
    }
    else print "Result is not false";

    $row = $result->fetch_array( MYSQLI_NUM);
    $currentUser_id = (int)$row[0];

    $statementToGetUserID->close();
    $db->close();

    return $currentUser_id;

}

/**
 * Function to get a Post by its Post ID.
 * @param $postID
 * @return false|mysqli_result
 */
function getSinglePostByID($postID){
    $db = getDB();

    //query the post data from the db using the post_id
    $statement= $db->prepare("SELECT title, description, Users.username, body from posts, Users where posts.user_id = Users.id and posts.id = ? ");
    $statement->bind_param("s", $postID);
    $statement->execute();

    $result = $statement->get_result();

    $statement->close();
    $db->close();

    return $result;
}

/**
 * Function to retrieve all of the posts from the 'posts' table in the database
 * @return bool|mysqli_result
 */
function getAllPosts() {
    //no need to do prepare statement as we are not taking user input?
    $db = getDB();
    $query="SELECT title, description, username, posts.id from posts JOIN Users on Users.id = posts.user_id";
    $result = mysqli_query($db, $query) or die(mysqli_error($db));
    $db->close();
    return $result;
}


/**
 * function to insert a post into the posts table.
 * @param $post_title : Post Title
 * @param $post_description : Post Description
 * @param $post_body : Post Body
 */
function insertNewPost($post_title, $post_description, $post_body){

    //encode and remove html characters
    $post_title = strip_tags(htmlspecialchars($post_title));
    $post_description = strip_tags(htmlspecialchars($post_description));
    $post_body = strip_tags(htmlspecialchars($post_body));

    $db = getDB();

    if(!validateSession()){
        //Anonymous Post User ID
        $currentUser_id = 25;
    }
    else{
        $currentUser_id = getCookieContentsAsArray()["userID"];
    }

    $statement= $db->prepare("INSERT INTO posts (user_id, title, description, body) VALUES (?,?,?,?)");
    $statement->bind_param("ssss", $currentUser_id , $post_title, $post_description, $post_body);
    $statement->execute();
    $statement->close();
    $db ->close();
}

/**
 * Function to insert a new comment under a post. If the
 * currentUser is null, will comment as Anonymous.
 * @param $postID : Post ID of the post to comment under (int)
 * @param $commentBody : The body of the comment to post
 */
function insertNewComment($postID, $commentBody) {

    //encode and remove html characters
    $postID = strip_tags(htmlspecialchars($postID));
    $commentBody = strip_tags(htmlspecialchars($commentBody));

    $db = getDB();

    if(!validateSession()){
        //Anonymous Post User ID
        $currentUser_id = 25;
    }
    else{
        $currentUser_id = getCookieContentsAsArray()["userID"];
    }

    $statement= $db->prepare("INSERT INTO comments (postID, userID, commentBody ) VALUES (?,?,?)");
    $statement->bind_param("sss", $postID, $currentUser_id, $commentBody);
    $statement->execute();
    $statement->close();
    $db ->close();
}

/**
 * Function to return all of the comments correspond to a post.
 * Will return in order [commentBody, Users.username, timeStamp]
 * TODO do we want to do ACS or DESC by timestamp?
 * @param $postID : Post ID of the post to retrieve the comments for
 * @return false|mysqli_result : Will return the result of the query or false if it failed.
 */
function getAllComments($postID) {
    $db = getDB();
    $statement = $db->prepare("SELECT commentBody, Users.username, timeStamp FROM comments JOIN Users ON Users.id = comments.userID WHERE postID = ?");
    $statement->bind_param("s", $postID);
    $statement->execute();
    $result = $statement->get_result();
    $statement->close();
    $db->close();

    return $result;
}

/**
 * Funciton to get a user's username given the userID.
 * @param $userID : userID of the user
 * @return mixed : String of username or false of failed.
 */
function getUsernameByID($userID){
    $db = getDB();
    $statement = $db->prepare("SELECT username FROM Users WHERE id = ?");
    $statement->bind_param("s", $userID);
    $statement->execute();
    $result = $statement->get_result()->fetch_array(MYSQLI_NUM)[0];
    $statement->close();
    $db->close();

    return $result;
}

/**
 * Function called when the user initiates a "Log out"
 * @param $userID : user ID of the user trying to logout
 */
function deleteSessionID($userID){
    $db = getDB();
    $statement = $db->prepare("UPDATE Users set sessionID = null WHERE id = ?");
    $statement->bind_param("s", $userID);
    $statement->execute();
    $statement->close();
    $db->close();
}

/**
 * Function to determine if the current user is an Admin.
 * Will return true if the user is an Admin, or false if not.
 * If the user is not logged, it will throw an exception.
 * @return bool : true = user is an Admin, false = not and Admin;
 * @throws Exception : if the user is not logged in.
 */
function isUserAdmin()
{
    if(validateSession()){
        $userID = getCookieContentsAsArray()["userID"];
        $db = getDB();
        $statement = $db->prepare("SELECT isAdmin FROM Users WHERE id = ?");
        $statement->bind_param("s", $userID);
        $statement->execute();
        $result = (int)$statement->get_result()->fetch_array(MYSQLI_NUM)[0];
        $statement->close();
        $db->close();

        if($result !== 1){
            return false;
        }
        return true;


    }
    throw new Exception("User not logged in");
}

/**
 * Function to determine whether or not the given IP (public ip4)
 * has reached its login limit attempts (3 per 10 minutes).
 * Will return true if the max attempts has been reached.
 * @param $ip : public IP4 address of the login request.
 * @return bool : true if the the max attempts has been reached, otherwise false
 */
function isAtMaxAllowedAttempts($ip)
{
    echo "Max attempts called";
    $db = getDB() or die("Cannot get database");
    $statementToInsertIP = $db->prepare("INSERT INTO ip (address ,timestamp)VALUES (? ,CURRENT_TIMESTAMP)");
    $statementToInsertIP->bind_param("s", $ip);
    $statementToInsertIP->execute();
    $statementToInsertIP->close();
    echo "after insert statement";

    $statementToGetIPAttempts = $db->prepare("SELECT COUNT(address) FROM ip WHERE address = ? AND timestamp > (NOW() - interval 10 minute)");
    $statementToGetIPAttempts->bind_param("s", $ip );
    $statementToGetIPAttempts->execute();
    $count = $statementToGetIPAttempts->get_result()->fetch_array(MYSQLI_NUM);

    $statementToGetIPAttempts->close();

    $db->close();
    if($count[0] > 3){
        echo "<script>window.alert('You are only allowed 3 login attempts per 10 minutes')</script>";
        return true;
    }
    else {
        return false;
    }
}

/**
 * Function to retrieve the database connection.
 * Connection will be closed if you make a weak reference to the database
 * and the query will fail (mostly for prepared statements).
 * @example DO: $db = getDB(); mysqli_query($db, $query);
 * @example DO NOT DO: mysqli_query(getDB(), $query);
 * @return false|mysqli
 */
function getDB(){
    $decryptionManager = new DecryptionManager();
    $credentialArray = $decryptionManager -> getCredentialArray();
    $host = 'weblab.salemstate.edu';
    $dbaseName = 'csc435Fall2020Group4';
    $dbaseUser = $credentialArray["username"];
    $pass = $credentialArray["password"];

    $db = mysqli_connect($host, $dbaseUser, $pass, $dbaseName) or die("cannot connect");

    if (mysqli_connect_errno()){
        print "Failed to connect to MySQL";
    }
    return $db;
}

?>
