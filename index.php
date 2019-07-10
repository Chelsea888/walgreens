<?php
require "header.php";
require "functions.php";
?>

<div>
    <a href="register.php">Sign Up</a>
</div>
<div>
    <a href="login.php">Log in</a>
</div>

<div>
    <a href="adddrug.php">Add Drug</a>
</div>

<div>
    <a href="adduser.php?usertype=deliverer">Add Deliverer</a>
</div>
<div>
    <a href="adduser.php?usesrtype=prescriber">Add Prescriper</a>
</div>
<div>
    <a href="users.php">Users</a>
</div>
<div>
    <a href="prescriper.php">Add Prescription</a>
</div>
<?php

if ($auth->isLoggedIn()) {
    $userid = $auth->getUserId();
    if(isDeliverer($db, $userid)){
        echo "Deliver functions are in progress";}
        else if(isPrescriber($db, $userid)){
            header('Location: /CJ_Project/prescriper.php');
            exit;
        }
        else {
            $cusid = $userid;
            show_prescritpions_toCustomer($db, $userid);
    }
}

