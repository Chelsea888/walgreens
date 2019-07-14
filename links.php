<?php
require "header.php";
?>

<div class="container marketing">

    <div class="row pt-5 pb-5">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
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
            <div>
                <a href="review_order.php">Review Order</a>
            </div>
        </div>
        <div class="col-lg-2"></div>
    </div>
</div>
<?php
require "footer.php";
?>
