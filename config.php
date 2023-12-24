<?php
// Database configuration
//phpinfo();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'hex');
define('DB_PASSWORD', '123');
define('DB_NAME', 'smartestate');

// Attempt to connect to MySQL database
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check the connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
