<?php
session_start();
    if(isset($_POST['register-name'])){
        if(isset($_POST['register-email'])){
            if(isset($_POST['register-password'])){
                $_SESSION['registration-name'] = $_POST['register-name'];
                $_SESSION['registration-email'] = $_POST['register-email'];
                $_SESSION['registration-password'] = $_POST['register-password'];
                setcookie("register-name",$_SESSION['registration-name'], time()+60);
                setcookie("register-email",$_SESSION['registration-email'], time()+60);
                setcookie("register-password",$_SESSION['registration-password'], time()+60);
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" type="text/javascript"></script>
</head>
<body class="p-5 bg-warning">
<div class="container-fluid">
    <section class="row">
        <div class="col text-center mb-3">
            <h1 id="loginstatus" class="text-dark display-2">Not logged in</h1>
            <form action="check.php" method="post">
                <div class="form-group col-4 offset-4">
                    Code: <input type="text" class="form-control" id="googlecode" name="code">
                </div>
                <input type="submit" class="btn btn-success btn-lg" id="submit-googlecode" value="Submit">
            </form>
        </div>
    </section>

<script>
    $('input#submit-googlecode').on('click', function() {
        var googlecode = $('input#googlecode').val();
        if ($.trim(googlecode) != '') {
            $.post('check.php', {code: googlecode}, function(data) {
                $('div#loginstatus').text(data);
                if (data == 1) {
                    $('div#loginstatus').text('Logged in');
                    $('div#loginform').hide();
                }
            });
        }
    });
</script>
</body>
</html>