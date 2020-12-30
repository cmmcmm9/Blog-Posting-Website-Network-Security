<?php
/**
 * Function to determine if the password meets our requirements
 * of one Uppercase, one LowerCase, and one numeric character.
 * This is also done before the form is submitted in @see register.php
 * via JavaScript, but is used as more of a back-up.
 * @param $password : password attempt
 * @return bool : true if the password meets our requirement, otherwise false
 */
function testPass($password): bool
{
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);

    if(!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
        return false;
    }
    else return true;
}
?>