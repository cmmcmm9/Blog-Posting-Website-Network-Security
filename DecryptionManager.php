<?php

/**
 * Class DecryptionManager
 * Class to handle the decryption process for the encrypted credentials array.
 * Array contains "username" and "password" to the database.
 * Must have the 'Key', 'IV', and arrayEncrypt.txt created, and must be place in the parent directory
 * of this PHP file.
 * @see [encrypt.php]
 *
 */
class DecryptionManager {

    /**
     * Function to get the Key Value stored in the 'Key' file.
     * Must be in parent directory.
     * @return false|string
     */

    private function getKey(){
        $keyFile = fopen("../Key", "r") or die("Could not open key file");
        $key = fgets($keyFile);
        fclose($keyFile);
        return $key;
    }

    /**
     * Function to get the IV Value stored in the 'IV' file.
     * Must be in parent directory.
     * @return false|string
     */

    private function getIV(){
        $ivFile = fopen("../IV", "r") or die("Could not open IV file");
        $iv = fgets($ivFile);
        fclose($ivFile);
        return $iv;
    }

    /**
     * Function that will decrypt the serialized credential array in
     * arrayEncrypt.txt and deserialize it so it can be used in
     * @see getDB()
     * @return array : credential array containing the credentials for the database.
     * @example credentialArray["username"] = user name for the database.
     * @example credentialArray["password"] = password for the database.
     */
    //TODO if not up on directory, get rid of ../
    function getCredentialArray(): array
    {
        $myFile = fopen("../arrayEncrypt.txt", "r") or die("Cannot get credential array file");
        $decrypt = fgets($myFile);

        fclose($myFile);
        $cipher = "aes-128-ctr";

        // Use OpenSSl Encryption method
        $options = 0;

        // Non-NULL Initialization Vector for encryption
        $encryption_iv = $this->getIV();

        // Store the encryption key
        $encryption_key = $this->getKey();

        return unserialize(openssl_decrypt($decrypt, $cipher, $encryption_key, $options, $encryption_iv));
    }
}






