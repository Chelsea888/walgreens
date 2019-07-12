<?php
require "header.php";
require_once "functions.php";
global $auth;
$cusid = $auth->getUserId();


review_order($db,$cusid);
function review_order($db, $cusid)
{

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    echo "<form  method = 'post'>";

    $sql = "SELECT rx_num, prescriber_id, rx_written_date FROM Prescription where cus_id = $cusid";

    echo "Hello " . get_username($db, $cusid) . "!&nbsp;&nbsp;Your order summary: </br>";
    $stmt = $db->query($sql);
    $prescriptions = array();
    get_refillnum($db, $cusid);
    while ($row = $stmt->fetch()) {
        $rx_num = $row['rx_num'];
        $rx_written_date = $row['rx_written_date'];
        $prescriber_id = $row['prescriber_id'];

        $pres_obj = new Prescription($rx_num, $cusid, $rx_written_date, $prescriber_id);
        $pres_obj->create_drug($db, $cusid);


        array_push($prescriptions, $pres_obj);
    }
    $total = 0;
    foreach ($prescriptions as $pres_obj) {
        if (isInOrder($db, $pres_obj->rx_num) == 0) {

            echo "<div>" . $pres_obj->rx_num . " generated on " . $pres_obj->rx_written_date .
                " by Doctor " . get_docname($db, $prescriber_id)[$prescriber_id][0] . " " .
                get_docname($db, $prescriber_id)[$prescriber_id][1] . "</div>";

            foreach ($pres_obj->drugs as $drug) {
                echo "<div>" . $drug->drug_name . " " . $drug->drug_stren . " $" . $drug->unit_price .
                    " Refill:" . get_refillnum($db, $cusid)[2] . "</div>";
                $total += $drug->unit_price++;
            }
            echo "<strong>Total: $$total</strong></br>";
            echo "<strong>Out to insurance:</strong></br>";
            echo "<strong>Patient owes:</strong></br>";
            echo "<div>";
            echo "<input type='hidden' name='action' value='Order Prescription' />";
            echo "<button type = 'submit' class='btn btn-dark'>Ready to Pay</button></div>";
        }
    }
}