<?php
require_once "classes.php";
/**
 * @param $db
 * @param $cusid
 */
/*function get_username($db,$cusid){
    $sql = "SELECT C.first_name, C.last_name from Customer C where C.cus_id =$cusid";

        $stmt = $db->query($sql);
        while($row=$stmt->fetch()) {
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
        }
        return "$first_name $last_name";

}

function show_prescritpions($db, $cusid) {
    $countsql = "SELECT count(*) FROM Prescription where cus_id = $cusid";
    $sql = "SELECT P.rx_num, P.doc_lastn, P.doc_firstn, P.rx_written_date, P.refill, P.cus_id, D.drug_name,D.drug_stren, D.unit_price FROM Prescription P
            Join  Prescription_drug PD ON P.rx_num = PD.rx_num
            JOIN Drug D ON PD.NDC = D.NDC
            where P.cus_id =  $cusid";

   /* $countsql = "SELECT count(*) FROM Prescription where cus_id = $cusid";
    $sql = "SELECT rx_num, doc_lastn, doc_firstn, rx_written_date, refill FROM Prescription where cus_id = $cusid";

    $count = $db->query($countsql)->fetchColumn();

    if ($count == 0) {

        echo "<div>You have no prescriptions.</div>";
    } else {
        echo "Hello ".get_username($db, $cusid)."!";
        echo "<div>Your prescriptions</div>";

        $stmt = $db->query($sql);

        while($row=$stmt->fetch()) {
            $rx_num = $row['rx_num'];
            $doc_lastn = $row['doc_lastn'];
            $doc_firstn = $row['doc_firstn'];
            $rx_written_date = $row['rx_written_date'];
            $refill = $row['refill'];
            $drug_name = $row['drug_name'];
            $drug_stren = $row['drug_stren'];
            $unit_price = $row['unit_price'];

            ?>
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
    /* $countsql = "SELECT count(*) FROM Prescription where cus_id = $cusid";
     $sql = "SELECT P.rx_num, P.doc_lastn, P.doc_firstn, P.rx_written_date, P.refill, P.cus_id, D.drug_name,D.drug_stren, D.unit_price FROM Prescription P
             Join  Prescription_drug PD ON P.rx_num = PD.rx_num
             JOIN Drug D ON PD.NDC = D.NDC
             where P.cus_id =  $cusid"; */

    $countsql = "SELECT count(*) FROM Prescription where cus_id = $cusid";
    $sql = "SELECT rx_num, prescriber_id, rx_written_date FROM Prescription where cus_id = $cusid";

    $count = $db->query($countsql)->fetchColumn();

    if ($count == 0) {

        echo "<div>You have no prescriptions.</div>";
    } else {

        echo "Hello " . get_username($db, $cusid) . "!";

        echo "<div>Your prescriptions</div>";

        $stmt = $db->query($sql);

        $prescriptions = array();

        while ($row = $stmt->fetch()) {
            $rx_num = $row['rx_num'];
           # $doc_lastn = $row['doc_lastn'];
            #$doc_firstn = $row['doc_firstn'];
            $rx_written_date = $row['rx_written_date'];
            $prescriber_id = $row['prescriber_id'];

            $pres_obj = new Prescription($rx_num, $cusid,  $rx_written_date,$prescriber_id);
            $pres_obj->create_drug($db, $cusid);


            array_push($prescriptions, $pres_obj);
        }

        foreach ($prescriptions as $pres_obj) {
            echo "<div>" . $pres_obj->rx_num . " generated on " . $pres_obj->rx_written_date .
                " by Doctor " . get_docname($db,$prescriber_id)[$prescriber_id][0] . " " . get_docname($db,$prescriber_id)[$prescriber_id][1] . "</div>";

            foreach ($pres_obj->drugs as $drug) {
                echo "<div>" . $drug->drug_name . " " . $drug->drug_stren . " $" . $drug->unit_price . " Refill:".get_refillnum($db,$cusid)[2]."</div>";
            }
        }
    }get_refillnum($db,$cusid);
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

?>