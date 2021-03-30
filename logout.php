<?php
session_start();
define('MYDIR','../');
require_once(MYDIR."../vendor/autoload.php");

$client = new Google_Client();
$client->setAuthConfig('../../configs/credentials.json');

//Unset token from session
unset($_SESSION['upload_token']);

// Reset OAuth access token
$client->revokeToken();

//Destroy entire session
session_destroy();

//Redirect to homepage
header("Location:index.php");

