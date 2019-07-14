<?php
require "header.php";

try {
    $auth->logOutEverywhere();
}
catch (\Delight\Auth\NotLoggedInException $e) {
    header("Location: /$PROJECTNAME/index.php");
    die("Not Logged In");
}
header("Location: /$PROJECTNAME/index.php");
