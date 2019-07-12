<?php
require "header.php";
?>

<?php
$userid = $auth->getUserId();
if(isDeliverer($db, $userid)){
    echo "Deliver functions are in progress";
}

if(isPrescriber($db, $userid)){
    header('Location: /CJ_Project/prescriper.php');
    exit;
}

if(isCustomer($db, $userid)) {
    show_prescritpions_toCustomer($db, $userid);
}

require "footer.php";