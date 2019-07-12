<?php
require_once "classes.php";
/**
 * @param $db
 * @param $cusid
 */


function show_prescritpion($db, $rx_num,$prescriber_id)
{
    $sql = "SELECT rx_num, cus_id, PR.first_name, PR.last_name, rx_written_date
            FROM Prescription PN
            LEFT JOIN Prescriber PR
            ON PN.prescriber_id = PR.prescriber_id
            where rx_num = ?";

    $stmt = $db->prepare($sql);
    $stmt->execute([$rx_num]);

    $prescriptions = array();

    while ($row = $stmt->fetch()) {
        $rx_num = $row['rx_num'];
        $doc_lastn = $row['last_name'];
        $doc_firstn = $row['first_name'];
        $rx_written_date = $row['rx_written_date'];
        $cusid = $row['cus_id'];

        $pres_obj = new Prescription($rx_num, $cusid, $rx_written_date,$prescriber_id);
        $pres_obj->create_drug($db, $cusid);


        array_push($prescriptions, $pres_obj);
    }

    foreach ($prescriptions as $pres_obj) {
        echo "<div>" . $pres_obj->rx_num . " generated on " .$pres_obj->rx_written_date.
            " by Doctor " . $doc_firstn . " " . $doc_lastn . "</div>";

        foreach ($pres_obj->drugs as $drug) {
            echo "<div>" . $drug->drug_name . " " . $drug->drug_stren . " $" . $drug->unit_price . "</div>";
        }
    }
}

function show_prescritpions_toCustomer($db, $cusid)
{

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    echo "<form  method = 'post'>";
    $countsql = "SELECT count(*) FROM Prescription where cus_id = $cusid";
    $sql = "SELECT rx_num, prescriber_id, rx_written_date FROM Prescription where cus_id = $cusid";

    $count = $db->query($countsql)->fetchColumn();

    if ($count == 0) {

        echo "<div>" . getUserFullName($db, $cusid) . " You have no prescriptions.</div>";
    } else {


        echo "Hello " . get_username($db, $cusid) . "!</br>";
        $stmt = $db->query($sql);
        $prescriptions = array();
        get_refillnum($db, $cusid);
        while ($row = $stmt->fetch()) {
            $rx_num = $row['rx_num'];
            # $doc_lastn = $row['doc_lastn'];
            #$doc_firstn = $row['doc_firstn'];
            $rx_written_date = $row['rx_written_date'];
            $prescriber_id = $row['prescriber_id'];

            $pres_obj = new Prescription($rx_num, $cusid, $rx_written_date, $prescriber_id);
            $pres_obj->create_drug($db, $cusid);


            array_push($prescriptions, $pres_obj);
        }
        echo "<h4>Your prescriptions are ready to order</h4>";
        foreach ($prescriptions as $pres_obj) {
            if (isInOrder($db, $pres_obj->rx_num) == 0) {

                echo "<div>" . $pres_obj->rx_num . " generated on " . $pres_obj->rx_written_date .
                    " by Doctor " . get_docname($db, $prescriber_id)[$prescriber_id][0] . " " .
                    get_docname($db, $prescriber_id)[$prescriber_id][1] . "</div>";

                foreach ($pres_obj->drugs as $drug) {
                    echo "<div>" . $drug->drug_name . " " . $drug->drug_stren . " $" . $drug->unit_price . " Refill:" . get_refillnum($db, $cusid)[2] . "</div>";
                }
                echo "<div>";
                echo "<input type='hidden' name='action' value='Order Prescription' />";
                echo "<button type = 'submit' class='btn btn-dark'>Order Prescription</button></div>";
            }
        }
        echo "<h4>The prescriptions are delivered</h4>";
        foreach ($prescriptions as $pres_obj) {
            if (isInOrder($db, $pres_obj->rx_num) == 1) {

                echo "<div>" . $pres_obj->rx_num . " generated on " . $pres_obj->rx_written_date .
                    " by Doctor " . get_docname($db, $prescriber_id)[$prescriber_id][0] . " " .
                    get_docname($db, $prescriber_id)[$prescriber_id][1] . "</div>";

                foreach ($pres_obj->drugs as $drug) {
                    echo "<div>" . $drug->drug_name . " " . $drug->drug_stren . " $" . $drug->unit_price . " Refill:" . get_refillnum($db, $cusid)[2] . "</div>";
                }
            }
        }

        if ($action == "Order Prescription") {

            echo "<div>";
            echo "<input type='hidden' name='action' value='review order' />";
            echo "<button type = 'submit' class='btn btn-dark'>Review my orders</button></div>";}

            if ($action == "review order") {
                header("Location: /CJ_Project/review_order.php?");
                exit;
                echo "</form>";
            }
        }

}
function isInOrder($db, $rx_num){
    #$rxnum_inOrder = false;
    $countsql = "select count(order.rx_num) from `order`,Prescription where order.rx_num =  '$rx_num'";
    $count = $db->query($countsql)->fetchColumn();
    if ($count != 0) {
        return 1;
    } else {
        return 0;
    }
}
function order_Prescription($db,$rx_num){
    global $auth;
    $cusid = $auth->getUserId();
    $gen_datedate = date("Y-m-d H:i:s");
        $req = $db->prepare("INSERT INTO `order` (cus_id,rx_num,gen_date) VALUES (:cus_id,:rx_num,:gen_date)");
        $req->execute(array(
            'cus_id'=>$cusid,
            'rx_num'=>$rx_num,
            'gen_date' =>$gen_datedate));
}


function get_docname($db,$prescriber_id){
    $sql = "select distinct first_name, last_name from Prescriber, Prescription where Prescriber.prescriber_id = ".$prescriber_id;
    $stmt = $db->query($sql);

    while ($row = $stmt->fetch()) {
        $doc_firstname = $row['first_name'];
        $doc_lastname = $row['last_name'];
        $prescriber_id_name[$prescriber_id] = array($doc_firstname,$doc_lastname);}
        return $prescriber_id_name;
}

function get_refillnum($db,$cusid){
    $sql = "select Prescription_drug.rx_num, NDC, refill from Prescription_drug, Prescription 
where Prescription_drug.rx_num = Prescription.rx_num
and cus_id =".$cusid;
    $stmt = $db->query($sql);
;
    while($row=$stmt->fetch()) {
        $rx_num = $row['rx_num'];
        $NDC =$row['NDC'];
        $refill = $row['refill'];
        $refillnum = array($rx_num,$NDC,$refill);
        return $refillnum;
    }
}

function get_username($db,$cusid){
    $sql = "SELECT C.first_name, C.last_name from Customer C where C.cus_id =$cusid";

    $stmt = $db->query($sql);
    while($row=$stmt->fetch()) {
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
    }
    return "$first_name $last_name";
    }




function isDeliverer($db, $userid) {
    $res = $db->query("select count(*) from Deliverer where deli_id = " . $userid);
    if ($res->fetchColumn() > 0) {
        return true;
    } else {
        return false;
    }
}

function isPrescriber($db, $userid) {
    $res = $db->query("select count(*) from Prescriber where prescriber_id = " . $userid);
    if ($res->fetchColumn() > 0) {
        return true;
    } else {
        return false;
    }
}

function isCustomer($db, $userid) {
    if (!isDeliverer($db, $userid) && !(isPrescriber($db, $userid))) {
        return true;
    } else {
        return false;
    }
}

function getUserFullName($db, $userid) {
    $fullname = "";

    $sql = "SELECT C.first_name, C.last_name from Customer C where C.cus_id =$userid";
    $stmt = $db->query($sql);
    while($row=$stmt->fetch()) {
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];

        $fullname = "$first_name $last_name";
        return $fullname;
    }

    $sql = "SELECT first_n, last_n from Deliverer where deli_id =$userid";
    $stmt = $db->query($sql);
    while($row=$stmt->fetch()) {
        $first_name = $row['first_n'];
        $last_name = $row['last_n'];

        $fullname = "$first_name $last_name";
        return $fullname;
    }

    $sql = "SELECT first_name, last_name from Prescriber where prescriber_id =$userid";
    $stmt = $db->query($sql);
    while($row=$stmt->fetch()) {
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];

        $fullname = "$first_name $last_name";
        return $fullname;
    }

    return $fullname;
}

function RXNumerExists($db, $rxnum)
{
    $rxnum_exists = false;
    $sql = "select rx_num from Prescription where rx_num = '$rxnum'";
    $stmt = $db->query($sql);
    while ($row = $stmt->fetch()) {
        $rxnum_exists = true;
    }
    return $rxnum_exists;
}

function generateRXnum($db)
{
    $rx_num = "RX" . rand(1000000, 9999999) . "-" . rand(1000, 9999);
    while (RXNumerExists($db, $rx_num)) {
        $rx_num = "RX" . rand(1000000, 9999999) . "-" . rand(1000, 9999);
    }
    return $rx_num;
}

function getDocName($db)
{
    global $auth;
    $doc_id = $auth->getUserId();
    $sql = "select first_name, last_name from Prescriber where prescriber_id = " . $doc_id;
    $stmt = $db->query($sql);
    while ($row = $stmt->fetch()) {
        $doc_firstn = $row['first_name'];
        $doc_lastn = $row['last_name'];
    }
    return array($doc_firstn, $doc_lastn);
}

function getCus_Id($db,$gwid)
{
    $sql = "select cus_id from Customer where gwid= '" .$gwid . "'";
    $stmt = $db->query($sql);
    while ($row = $stmt->fetch()) {
        $cus_id = $row['cus_id'];
    }
    return $cus_id;
}

function addPrescription_cus($db, $prescriber_id, $gwid, $drugs, $refills)
{
    $rx_num = generateRXnum($db);
    $cus_id = getCus_Id($db, $gwid);
    $rx_written_date = date("Y-m-d H:i:s");
    $req = $db->prepare("INSERT INTO Prescription ( rx_num, prescriber_id, cus_id, rx_written_date
                                               ) VALUES (
                                                    :rx_num,
                                                    :prescriber_id,
                                                    :cus_id,
                                                    :rx_written_date)");

    $req->execute(array(
        'rx_num' => $rx_num,
        'prescriber_id' => $prescriber_id,
        'cus_id' => $cus_id,
        'rx_written_date' => $rx_written_date));

    for($i=0; $i < count($drugs); $i++) {
        $sql = "INSERT INTO Prescription_drug (rx_num, NDC, refill) VALUES (:rx_num, :NDC, :refill)";
        $stmt = $db->prepare($sql);
        $stmt->execute(array('rx_num'=>$rx_num, 'NDC'=>$drugs[$i], 'refill'=>$refills[$i]));
    }
    return $rx_num;
}

?>