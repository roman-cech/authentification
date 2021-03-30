<?php
session_start();
require_once "classes/User.php";
require_once "classes/Account.php";
require_once "classes/Users.php";
require_once "classes/helper/Database.php";

$history = $_GET['showAll'];
$users = 'usersHistory';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Three ways how to sign</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body class="bg-light">
    <section class="p-3">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="text-center text-success">Login Successful!</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-5 p-3">
                    <?php
                    if(isset($_SESSION['ldap'])){
                        $ldap = $_SESSION['ldap'];
                        $password = $_SESSION['ldap-password'];
                        echo "<h1>LDAP details</h1>";
                        echo "<h4><strong>Name:</strong> ".$ldap[0]['givenname'][0].' '.$ldap[0]['sn'][0]."</h4>";
                        echo "<h4><strong>Email:</strong> ".$ldap[0]['mail'][0]."</h4>";
                        echo "<h4><strong>AIS Id:</strong> ".$ldap[0]['uisid'][0]."</h4>";

                        $ldap_name = $ldap[0]['givenname'][0].' '.$ldap[0]['sn'][0];
                        $ldap_email = $ldap[0]['mail'][0];
                        $ldap_type = "LDAP";
                        $ldap_password= password_hash($password, PASSWORD_DEFAULT);
                        try{
                            $db = new Database();
                            $db->getConnection()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $stmt = $db->getConnection()->prepare("INSERT INTO users.user (name, email) VALUES (:name, :email)");

                            $stmt->bindParam(":name", $ldap_name, PDO::PARAM_STR);
                            $stmt->bindParam(":email", $ldap_email, PDO::PARAM_STR);
                            $stmt->execute();

                            $stmt = $db->getConnection()->prepare("SELECT * FROM users.user");
                            $stmt->execute();
                            $user_ldap = $stmt->fetchAll(PDO::FETCH_CLASS, "User");
                            foreach ($user_ldap as $value)
                                $ldap_user_id = $value->getId();

                            $db->getConnection()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $stmt = $db->getConnection()->prepare("INSERT INTO users.account (user_id, type, password) VALUES (:user_id, :type, :password)");

                            $stmt->bindParam(":user_id", $ldap_user_id, PDO::PARAM_STR);
                            $stmt->bindParam(":type", $ldap_type, PDO::PARAM_STR);
                            $stmt->bindParam(":password", $ldap_password, PDO::PARAM_STR);
                            $stmt->execute();

                            $stmt = $db->getConnection()->prepare("SELECT * FROM users.account");
                            $stmt->execute();
                            $account_ldap = $stmt->fetchAll(PDO::FETCH_CLASS, "Account");
                            foreach ($account_ldap as $value)
                                $ldap_account_id = $value->getId();

                            $db->getConnection()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $stmt = $db->getConnection()->prepare("INSERT INTO users.access (account_id, timestamp ) VALUES (:account_id, NOW() + INTERVAL 2 HOUR )");

                            $stmt->bindParam(":account_id", $ldap_account_id, PDO::PARAM_STR);
                            $stmt->execute();
                        }
                        catch (PDOException $e){}
                        echo "<button type='submit' class='btn btn-danger btn-lg' onClick=\"document.location.href='logout.php'\">Logout from Ldap</button>";
                    }
                    ?>

                    <?php
                    if(isset($_SESSION['full_name'])){
                        if(isset($_SESSION['email'])){
                            if(isset($_SESSION['google_id'])){
                                $google_name = $_SESSION['full_name'];
                                $google_email = $_SESSION['email'];
                                $google_picture = $_SESSION['picture'];
                                $google_id = $_SESSION['google_id'];
                                echo "<h1>Google details</h1>";
                                echo "<img src='$google_picture'>";
                                echo "<h4><strong>Name:</strong> ".$google_name."</h4>";
                                echo "<h4><strong>Email:</strong> ".$google_email."</h4>";
                                echo "<h4><strong>Google Id:</strong> ".$google_id."</h4>";

                                $google_type = "OAUTH";
                                try{
                                    $db = new Database();
                                    $db->getConnection()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                    $stmt = $db->getConnection()->prepare("INSERT INTO users.user (name, email) VALUES (:name, :email)");

                                    $stmt->bindParam(":name", $google_name, PDO::PARAM_STR);
                                    $stmt->bindParam(":email", $google_email, PDO::PARAM_STR);
                                    $stmt->execute();

                                    $stmt = $db->getConnection()->prepare("SELECT * FROM users.user");
                                    $stmt->execute();
                                    $user_google = $stmt->fetchAll(PDO::FETCH_CLASS, "User");
                                    foreach ($user_google as $value)
                                        $google_user_id = $value->getId();

                                    $db->getConnection()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                    $stmt = $db->getConnection()->prepare("INSERT INTO users.account (user_id, type, google_id) VALUES (:user_id, :type, :google_id)");

                                    $stmt->bindParam(":user_id", $google_user_id, PDO::PARAM_STR);
                                    $stmt->bindParam(":type", $google_type, PDO::PARAM_STR);
                                    $stmt->bindParam(":google_id", $google_id, PDO::PARAM_INT);
                                    $stmt->execute();

                                    $stmt = $db->getConnection()->prepare("SELECT * FROM users.account");
                                    $stmt->execute();
                                    $account_google = $stmt->fetchAll(PDO::FETCH_CLASS, "Account");
                                    foreach ($account_google as $value)
                                        $google_account_id = $value->getId();

                                    $db->getConnection()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                    $stmt = $db->getConnection()->prepare("INSERT INTO users.access (account_id, timestamp ) VALUES (:account_id, NOW() + INTERVAL 2 HOUR )");

                                    $stmt->bindParam(":account_id", $google_account_id, PDO::PARAM_STR);
                                    $stmt->execute();
                                }
                                catch (PDOException $e){}
                                echo "<button type='submit' class='btn btn-danger btn-lg' onClick=\"document.location.href='logout.php'\">Logout from Google</button>";
                            }
                        }
                    }
                    ?>

                    <?php
                        if(isset($_COOKIE['register-email']) && !isset($_SESSION['ldap']) && !isset($_SESSION['google_id'])){
                            if(isset($_COOKIE['register-name'])){
                                if(isset($_COOKIE['register-password'])){
                                    $registration_name = $_COOKIE['register-name'];
                                    $registration_email = $_COOKIE['register-email'];
                                    $registration_password = $_COOKIE['register-password'];
                                    $hash_registration_password = password_hash($registration_password, PASSWORD_DEFAULT);

                                    echo "<h1>Registration details</h1>";
                                    echo "<h4><strong>Name:</strong> ".$registration_name."</h4>";
                                    echo "<h4><strong>Email:</strong> ".$registration_email."</h4>";

                                    $registration_type = "REGISTRATION";
                                    try{
                                        $db = new Database();
                                        $db->getConnection()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                        $stmt = $db->getConnection()->prepare("INSERT INTO users.user (name, email) VALUES (:name, :email)");

                                        $stmt->bindParam(":name", $registration_name, PDO::PARAM_STR);
                                        $stmt->bindParam(":email", $registration_email, PDO::PARAM_STR);
                                        $stmt->execute();

                                        $stmt = $db->getConnection()->prepare("SELECT * FROM users.user");
                                        $stmt->execute();
                                        $user_registration = $stmt->fetchAll(PDO::FETCH_CLASS, "User");
                                        foreach ($user_registration as $value)
                                            $registration_user_id = $value->getId();

                                        $db->getConnection()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                        $stmt = $db->getConnection()->prepare("INSERT INTO users.account (user_id, type, password) VALUES (:user_id, :type, :password)");

                                        $stmt->bindParam(":user_id", $registration_user_id, PDO::PARAM_STR);
                                        $stmt->bindParam(":type", $registration_type, PDO::PARAM_STR);
                                        $stmt->bindParam(":password", $hash_registration_password, PDO::PARAM_STR);
                                        $stmt->execute();

                                        $stmt = $db->getConnection()->prepare("SELECT * FROM users.account");
                                        $stmt->execute();
                                        $account_registration = $stmt->fetchAll(PDO::FETCH_CLASS, "Account");
                                        foreach ($account_registration as $value)
                                            $registration_account_id = $value->getId();

                                        $db->getConnection()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                        $stmt = $db->getConnection()->prepare("INSERT INTO users.access (account_id, timestamp ) VALUES (:account_id, NOW() + INTERVAL 2 HOUR )");

                                        $stmt->bindParam(":account_id", $registration_account_id, PDO::PARAM_STR);
                                        $stmt->execute();
                                    }
                                    catch (PDOException $e){}
                                    echo "<button type='submit' class='btn btn-danger btn-lg' onClick=\"document.location.href='logout.php'\">Logout from Registration</button>";
                                }
                            }
                        }
                    ?>

                    <button type='submit' class='btn btn-white btn-lg m-1'><a href="?showAll=<?php echo $users?>">Show all logins</a></button>
                </div>
                    <?php
                        if($_GET['showAll']){
                            if($users){
                                echo "
                                <div class='col-7'>
                                    <h1>Login history</h1>
                                    <table class='table table-bordered table-light'>
                                        <thead class='table-dark'>
                                        <tr>
                                            <th scope='col'>Full Name</th>
                                            <th scope='col'>Email</th>
                                            <th scope='col'>Login Type</th>
                                            <th scope='col'>Time</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                    ";
                                    try{
                                        $db = new Database();
                                        $conn = $db->getConnection();
                                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                        $stmt = $db->getConnection()->prepare("SELECT user.name, user.email, account.type, access.timestamp FROM users.user JOIN users.account ON user.id = account.user_id JOIN users.access ON account.id = access.account_id;");
                                        $stmt->execute();
                                        $allUsers = $stmt->fetchAll(PDO::FETCH_CLASS, "Users");

                                        $stmt = $db->getConnection()->prepare("SELECT SUM(account.type = 'REGISTRATION') AS registration,  SUM(account.type = 'OAUTH') AS google, SUM(account.type = 'LDAP') AS ldap FROM users.account;");
                                        $stmt->execute();
                                        $numberOfLogins = $stmt->fetchAll(PDO::FETCH_CLASS, "Users");
                                    }catch (PDOException $e){}

                                    foreach ($allUsers as $person)
                                        echo $person->getAllUsers();

                                    echo "</tbody></table><h4>LOGINS WITH LDAP ->"; foreach ($numberOfLogins as $value) echo $value->getLdap();"</h4>";
                                    echo "<h4>LOGINS WITH GOOGLE ->"; foreach ($numberOfLogins as $value) echo $value->getGoogle(); "</h4>";
                                    echo "<h4>LOGINS WITH REGISTRATION ->"; foreach ($numberOfLogins as $value) echo $value->getRegistration(); "</h4>";
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </section>

</body>
</html>





