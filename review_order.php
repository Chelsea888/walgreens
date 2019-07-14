<?php
require "header.php";
require_once "functions.php";
global $auth;
$cusid = $auth->getUserId();

?>

<div class="container marketing">

    <div class="row pt-5 pb-5">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
<?php
review_order($db,$cusid);
?>
        </div>
        <div class="col-lg-2"></div>
    </div>
</div>
<?php
require "footer.php";
?>


