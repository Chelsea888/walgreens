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

if (!$auth->isLoggedIn()) {
    if ($_SERVER['REQUEST_URI'] != '/CJ_Project/register.php') {
        header("Location: /CJ_Project/login.php");
    }
}

use Cart\Cart;
use Cart\Storage\SessionStore;

$cartid = 'cart-' . $auth->getUserId();
$cartSessionStore = new SessionStore();

$cart = new Cart($cartid, $cartSessionStore);

if($cart->totalItems() > 0) {
    $cart->restore();
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
</head>
<body>
<?php
echo "<a href='/CJ_Project/'>Walgreens X GWU Drug Store</a> <input type=\"text\" name=\"q\" /> " . $auth->getUsername();
if ($auth->isLoggedIn()) {
    echo " <a href='profile.php'>" . $auth->getEmail() . "</a> <a href='logout.php'>Log Out</a>";
}

echo "Cart (" . $cart->totalItems() . ")";


