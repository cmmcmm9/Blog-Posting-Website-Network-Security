<?php

/**
 * Class HMACHashManager: class to generate an HMAC, and verify an attempt (plain text) with given hash.
 * Note that the hmackey.txt must be place in the parent directory of this file.
 * This is used to generate an HMAC for a valid cookie upon creation, which is then stored in the
 * database. When a cookie must be validated, the cookie (as a JSON encoded PHP array)
 * is passed to the @see HMACHashManager->verifyHashValues() function.
 */
class HMACHashManager
{
    /**
     * Function to get the Key Value stored in the 'Key' file.
     * @return false|string
     */
    private function getHmacKey(){
        $keyFile = fopen("../hmackey", "r") or die("Cannot open HMAC key file");
        $key = fgets($keyFile);
        fclose($keyFile);
        return $key;
    }

    /**
     * Function to generate an HMAC of the cookies contents.
     * @param $jsonEncodedArray : cookie contents as a JSON encoded array
     * @return string : HMAC string of cookie contents
     */
    function generateHmac($jsonEncodedArray) {
        return hash_hmac("sha256", $jsonEncodedArray, $this->getHmacKey(), false);
    }

    /**
     * @param $cookiesContentArray : UN-HASHED attempt as an array
     * @param $hashInDatabase : the cookie has stored in the database
     * @return bool : true if the HMAC is verified
     */
    function verifyHashValues($cookiesContentArray, $hashInDatabase): bool
    {
        $hashAttempt = $this->generateHmac(json_encode($cookiesContentArray));
        if($hashAttempt != $hashInDatabase){
            return false;
        }
        return true;
    }

}