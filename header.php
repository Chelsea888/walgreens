<?php
\error_reporting(\E_ALL);
\ini_set('display_errors', 'stdout');

// enable assertions
\ini_set('assert.active', 1);
@\ini_set('zend.assertions', 1);
\ini_set('assert.exception', 1);

\header('Content-type: text/html; charset=utf-8');

require __DIR__.'/vendor/autoload.php';


$PROJECTNAME= "walgreens";


#$db = new \PDO('mysql:dbname=walgreens;host=127.0.0.1;charset=utf8mb4', 'wguser', 'AXhf$tu2p5R2');
$db = new \PDO('mysql:dbname=walgreens;host=localhost;charset=utf8mb4', 'testuser', 'mypassword');
$auth = new \Delight\Auth\Auth($db);

if (!$auth->isLoggedIn()) {
    if ($_SERVER['REQUEST_URI'] != "/$PROJECTNAME/register.php") {
        header("Location: /$PROJECTNAME/login.php");
    }
}

require_once "functions.php";

use Cart\Cart;
use Cart\Storage\SessionStore;

$cartid = 'cart-' . $auth->getUserId();
$cartSessionStore = new SessionStore();

$cart = new Cart($cartid, $cartSessionStore);

#generate an array 'NDC'=>'drug_name'
$drug_array =[];
$sql = "select  NDC, drug_name from Drug";
$res = $db->query($sql);

foreach ($res as $value){
    $drug_array[$value['NDC']] = $value['drug_name'];
}

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
                <!--
                <li class="nav-item">
                    <a class="nav-link" style = "color: darkgray" href="links.php">Quick Links</a>
                </li>
                -->
<!--                <li class="nav-item">-->
<!--                    <a class="nav-link disabled" href="#">Disabled</a>-->
<!--                </li>-->
            </ul>
            <?php if ($auth->isLoggedIn() && isCustomer($db, $auth->getUserId())) {
                ?>
                <form class="form-inline mt-2 mt-md-0 col-lg-6" action="search.php">
                    <input class="form-control mr-sm-2 input-lg col-lg-10" type="text" name="query" placeholder="Search for RX Number or Drug Name" aria-label="Search">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                </form>
            <?php
            }
            ?>
            <?php
            if ($auth->isLoggedIn()) {
            ?>
            <ul class="navbar-nav">
                <?php
                if (isCustomer($db, $auth->getUserId())) {
                    ?>
                    <li class="nav-item active">
                        <a class="nav-link" href="cart.php"><img class="img-fluid" style="width: 40px;height: 30px" src="shopping_cart_PNG4.png"></a>
                    </li>
                <?php
                }
                ?>
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
