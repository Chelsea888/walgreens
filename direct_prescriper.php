<?php
require "header.php";

$rx_num = isset($_REQUEST['rx_num']) ? $_REQUEST['rx_num'] : '';
$stmt = $db->prepare( "select first_name, last_name from Prescriber, Prescription where Prescriber.prescriber_id = 
        Prescription.prescriber_id and rx_num = ?");
$stmt->execute([$rx_num]);

while($row=$stmt->fetch()) {
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
}
?>
<div class="container marketing">

    <div class="row pt-5 pb-5">
        <div class="col-lg-1"></div>
        <div class="col-lg-9">
                        <h3><?php echo "Please contact Dr."." ".$first_name." ". $last_name." to get new prescription.</h3>"; ?>
                   </div>
        <div class="col-lg-2"></div>
               </div>
           </div>


