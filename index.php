<?php
session_start();
define('MYDIR','../');
require_once(MYDIR."../vendor/autoload.php");

$redirect_uri = 'https://wt32.fei.stuba.sk/zadanie3/';

$client = new Google_Client();
$client->setAuthConfig('../../configs/credentials.json');
$client->setRedirectUri($redirect_uri);
$client->addScope("email");
$client->addScope("profile");

$service = new Google_Service_Oauth2($client);

if(isset($_GET['code'])){
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);
    $_SESSION['upload_token'] = $token;

    // redirect back to the example
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

// set the access token as part of the client
if (!empty($_SESSION['upload_token'])) {
    $client->setAccessToken($_SESSION['upload_token']);
    if ($client->isAccessTokenExpired()) {
        unset($_SESSION['upload_token']);
    }
} else {
    $authUrl = $client->createAuthUrl();
}

if ($client->getAccessToken()) {
    //Get user profile data from google
    $UserProfile = $service->userinfo->get();
    if(!empty($UserProfile)){
        $_SESSION['picture'] = $UserProfile['picture'];
        $_SESSION['full_name'] = $UserProfile['given_name'].' '.$UserProfile['family_name'];
        $_SESSION['google_id'] = $UserProfile['id'];
        $_SESSION['email'] = $UserProfile['email'];

        header("Refresh:0; url = user-profile.php");
    }else{
        $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
    }
} else {
    $authUrl = $client->createAuthUrl();
    $output = '<a href="'.filter_var($authUrl, FILTER_SANITIZE_URL).'"><img src="assets/sign-with-google.png" alt=""/></a>';
}

if(isset($_POST['username'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $_SESSION['ldap-password'] = $password;

    $ldap['host'] = 'ldap.stuba.sk';
    $ldap['port'] = '389';
    $ldap['basedn'] = 'ou=People, DC=stuba, DC=sk';
    $ldap['usersdn'] = 'cn=users';
    $ds=ldap_connect($ldap['host'], $ldap['port']);

    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
    ldap_set_option($ds, LDAP_OPT_NETWORK_TIMEOUT, 10);

    $dn="uid=".$username.",".$ldap['basedn'];
    if(isset($_POST['username'])){
        if ($bind=ldap_bind($ds, $dn, $password)) {
            echo("Login correct");
            $sr = ldap_search($ds, 'ou=People, DC=stuba, DC=sk', 'uid='.$username, ['givenname','surname', 'mail', 'uisid']);
            $_SESSION['ldap'] = ldap_get_entries($ds, $sr);
            header("Refresh:0; url = user-profile.php");
        } else {
            echo "<script language='javascript'>";
            echo "alert('Login Failed: Please check your username or password')";
            echo "</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Three ways how to sign</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
    <section class="text-center">
        <div class="container">
            <h3>Three ways how to Sign!</h3><hr><h3>Choose one!</h3>
            <div class="row justify-content-center">
                <?php echo $output; ?>
            </div>
            <div class="row justify-content-center p-3">
                <button data-toggle='modal' data-target='#ldap'><img src='assets/sign-with-stuba.png'></button>
            </div>
            <h3>Not registered yet?</h3>
            <div class="row justify-content-center p-3">
                <button data-toggle='modal' data-target='#registration'><img src='assets/sign-with-registration.png'></button>
            </div>
        </div>
        <div class="modal fade" id="ldap" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Use stuba login details.</h4>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post">
                            <div class="form-group">
                                <input type="text" class="form-control" name="username" placeholder="Stuba email">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" name="password" placeholder="Password">
                            </div>
                            <input type="submit" value="Submit" class="btn btn-success btn-lg">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="registration" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Registration.</h4>
                    </div>
                    <div class="modal-body">
                        <form action="login.php" method="post">
                            <div class="form-group">
                                <input type="text" class="form-control" name="register-name" placeholder="Name">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="register-email" placeholder="Private email">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" name="register-password" placeholder="Password">
                            </div>
                            <input type="submit" value="Submit" class="btn btn-success btn-lg">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>
</html>
