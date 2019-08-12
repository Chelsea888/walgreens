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

$PROJECTNAME = "walgreens";

require_once "functions.php";
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Walgreens X GWU Drug Store</title>

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>
    <!-- Custom styles for this template -->
    <link href="carousel.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-md navbar-light fixed-top " style="background-color: #5f3f70;">
        <a class="navbar-brand" style = "color: white" href="/<?php echo $PROJECTNAME; ?>/">Walgreens X GWU Drug Store</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" style = "color: white" href="/<?php echo $PROJECTNAME; ?>/">Home <span class="sr-only">(current)</span></a>
                </li>

<!--                <li class="nav-item">-->
<!--                    <a class="nav-link disabled" href="#">Disabled</a>-->
<!--                </li>-->

            </ul>
            <?php
            if ($auth->isLoggedIn()) {
            ?>
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" style = "color: white" href="profile.php"><?php echo getUserFullName($db, $auth->getUserId()); ?><span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" style = "color: white" href="logout.php">Log Out</a>
                </li>
            </ul>
            <?php } else { ?>
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link" style = "color: white" href="login.php">Log In</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" style = "color: white" href="register.php">Register</a>
                    </li>
                </ul>
            <?php
            } ?>
        </div>
    </nav>
</header>


<main role="main">

<?php
if (!$auth->isLoggedIn()) {
    if (isset($_REQUEST['email']) && isset($_REQUEST['password'])) {
        try {
            $auth->login($_POST['email'], $_POST['password']);

            echo 'User is logged in';
            header("Location: /$PROJECTNAME/");
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
        <div class="container marketing mt-lg-5">
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
                                <input type="text" class="form-control" name="email" placeholder="Email Address" aria-label="Email Address">

                            </div>
                            <div class="input-group form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" class="form-control" name="password" placeholder="Password" aria-label="Password">
                            </div>
                            <div class="row align-items-center remember">
                                <input name="remember" type="checkbox" aria-label="remember">&nbsp; Remember Me
                            </div>
                            <div class="form-group">
                                <input type="submit" value="Login" class="btn btn-primary float-right login_btn">
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
    header("Location: /$PROJECTNAME/");
}
?>

