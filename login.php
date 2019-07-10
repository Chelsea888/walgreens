<?php
\error_reporting(\E_ALL);
\ini_set('display_errors', 'stdout');

// enable assertions
\ini_set('assert.active', 1);
@\ini_set('zend.assertions', 1);
\ini_set('assert.exception', 1);

\header('Content-type: text/html; charset=utf-8');

require __DIR__.'/vendor/autoload.php';

$db = new \PDO('mysql:dbname=walgreens;host=127.0.0.1;charset=utf8mb4', 'wguser', 'AXhf$tu2p5R2');
#$db = new \PDO('mysql:dbname=walgreens;host=localhost;charset=utf8mb4', 'testuser', 'mypassword');
$auth = new \Delight\Auth\Auth($db);
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Walgreens X GWU Drug Store</title>
</head>
<body>
<?php
echo "<a href='/CJ_Project/'>Walgreens X GWU Drug Store</a> <input type=\"text\" name=\"q\" /> ";
if (!$auth->isLoggedIn()) {
    if (isset($_REQUEST['email']) && isset($_REQUEST['password'])) {
        try {
            $auth->login($_POST['email'], $_POST['password']);

            echo 'User is logged in';
            header("Location: /CJ_Project/");
        } catch (\Delight\Auth\InvalidEmailException $e) {
            die('Wrong email address');
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Wrong password');
        } catch (\Delight\Auth\EmailNotVerifiedException $e) {
            die('Email not verified');
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
    } else {
        ?>
        <div class="container">
            <div class="d-flex justify-content-center h-100">
                <div class="card">
                    <div class="card-header">
                        <h3>Sign In</h3>
                    </div>
                    <div class="card-body">
                        <form action="login.php" method="post" accept-charset="utf-8">
                            <input type="hidden" name="action" value="login" />
                            <div class="input-group form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" class="form-control" name="email" placeholder="Email Address">

                            </div>
                            <div class="input-group form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" class="form-control" name="password" placeholder="Password">
                            </div>
                            <div class="row align-items-center remember">
                                <input name="remember" type="checkbox">&nbsp; Remember Me
                            </div>
                            <div class="form-group">
                                <input type="submit" value="Login" class="btn float-right login_btn">
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-center links">
                            Don't have an account? <a href="register.php">Sign Up</a>
                        </div>
                        <div class="d-flex justify-content-center">
                            <a href="#">Forgot your password?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
        require "footer.php";
    }
} else {
    header("Location: /CJ_Project/");
}

require "footer.php";
