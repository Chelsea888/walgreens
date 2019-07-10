<?php
require "header.php";

try {
    $auth->logOutEverywhere();
}
catch (\Delight\Auth\NotLoggedInException $e) {
    header("Location: /CJ_Project/index.php");
    die("Not Logged In");
}
header("Location: /CJ_Project/index.php");
