<?php
    require_once 'GoogleAuth/GoogleAuthenticator.php';
    $secret = 'H7SGXIVFIVQXHNTK';
 
    if (isset($_POST['code'])) {
        $code = $_POST['code'];
 
        $ga = new PHPGangsta_GoogleAuthenticator();
        $result = $ga->verifyCode($secret, $code);
 
        if ($result == 1) {
            echo $result;
            header("Refresh:0; url = user-profile.php");
        } else {
            echo "<script language='javascript'>";
            echo "alert('Login Failed.')";
            echo "</script>";
            header("Refresh:0; url = index.php");
        }
    }
?>