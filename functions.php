<?php
require_once "classes.php";
/**
 * @param $db
 * @param $cusid
 */


function get_prescritpion($db, $rx_num)
{
    $cus_firstn = "";
    $cus_lastn = "";
    $sql = "SELECT first_name, last_name FROM Customer C 
            JOIN Prescription P ON C.cus_id = P.cus_id AND P.rx_num = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$rx_num]);
    while($row=$stmt->fetch()) {
        $cus_firstn = $row['first_name'];
        $cus_lastn = $row['last_name'];
    }

    $doc_firstn = "";
    $doc_lastn = "";
    $sql = "SELECT first_name, last_name FROM Prescriber PR
            JOIN Prescription PN ON PR.prescriber_id = PN.prescriber_id AND PN.rx_num = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$rx_num]);
    while($row=$stmt->fetch()) {
        $doc_firstn = $row['first_name'];
        $doc_lastn = $row['last_name'];
    }

    $sql = "SELECT rx_num, cus_id, rx_written_date
            FROM Prescription
            where rx_num = ? order by rx_written_date desc";

    $stmt = $db->prepare($sql);
    $stmt->execute([$rx_num]);

    $pres_obj = null;
    while ($row = $stmt->fetch()) {
        $rx_num = $row['rx_num'];
        $rx_written_date = $row['rx_written_date'];
        $cusid = $row['cus_id'];

        $pres_obj = new Prescription($db, $rx_num, $cusid, $rx_written_date);
        $pres_obj->doc_firstn = $doc_firstn;
        $pres_obj->doc_lastn = $doc_lastn;
        $pres_obj->cus_firstn = $cus_firstn;
        $pres_obj->cus_lastn = $cus_lastn;
    }

    return $pres_obj;

}

function get_prescriptions_ready_to_order($db, $cusid) {
    $sql = "SELECT rx_num, cus_id, rx_written_date FROM Prescription 
            where rx_num not in (select rx_num from Order_Drug)
            AND cus_id = $cusid
            ORDER BY rx_written_date desc 
            ";
    $rows = $db->query($sql);

    $prescriptions = array();
    foreach ($rows as $row) {
        $prescriptions[] = new Prescription($db, $row['rx_num'], $row['cus_id'], $row['rx_written_date']);
    }
    return $prescriptions;
}

function get_ordered_prescriptions($db, $cusid) {
    $sql = "SELECT Prescription.rx_num, Prescription.cus_id, 
                Prescription.rx_written_date FROM Prescription 
            JOIN Order_Drug ON Prescription.rx_num = Order_Drug.rx_num
            JOIN `Order` ON `Order`.order_id = Order_Drug.order_id
            where Prescription.rx_num in (select rx_num from Order_Drug)
            AND Prescription.cus_id = $cusid
            AND `Order`.deliver_date IS NULL
            ORDER BY rx_written_date desc 
            ";
    $rows = $db->query($sql);

    $prescriptions = array();
    foreach ($rows as $row) {
        $prescriptions[] = new Prescription($db, $row['rx_num'], $row['cus_id'], $row['rx_written_date']);
    }
    return $prescriptions;
}

function get_delivered_prescriptions($db, $cusid) {
    /*
    $sql = "SELECT Prescription.rx_num, Prescription.cus_id, 
                Prescription.rx_written_date FROM Prescription 
            JOIN Order_Drug ON Prescription.rx_num = Order_Drug.rx_num
            JOIN `Order` ON `Order`.order_id = Order_Drug.order_id
            where Prescription.rx_num in (select rx_num from Order_Drug)
            AND Prescription.cus_id = $cusid
            AND `Order`.deliver_date IS NOT NULL
            ORDER BY rx_written_date desc 
            ";
    */
    $sql = "SELECT Prescription.rx_num, Prescription.cus_id, 
                Prescription.rx_written_date 
            FROM Prescription 
            where Prescription.cus_id = $cusid
            AND Prescription.rx_num IN (SELECT rx_num FROM `Order` WHERE deliver_date IS NOT NULL)
            ORDER BY rx_written_date desc ";
    $rows = $db->query($sql);

    $prescriptions = array();
    foreach ($rows as $row) {
        $prescriptions[] = new Prescription($db, $row['rx_num'], $row['cus_id'], $row['rx_written_date']);
    }
    return $prescriptions;
}

function prescription_has_refill($db, $rx_num) {
    $sql = "SELECT count(*) FROM Prescription_drug WHERE rx_num = ? AND refill > refilled";
    $stmt = $db->prepare($sql);
    $stmt->execute([$rx_num]);
    $hasRefill = false;
    while($row=$stmt->fetch()) {
        $hasRefill = true;
        break;
    }
    return $hasRefill;
}


function display_customer_prescriptions($db, $cusid) {
    // rx not in order

    $sql = "SELECT rx_num, cus_id, rx_written_date FROM Prescription 
            where rx_num not in (select rx_num from Order_Drug)
            ORDER BY rx_written_date desc 
            ";
    $rows = $db->query($sql);

    $prescriptions = array();
    foreach ($rows as $row) {
        $prescriptions[] = new Prescription($db, $row['rx_num'], $row['cus_id'], $row['rx_written_date']);
    }

    // rx in order delivered = null

    // rx in order delivered
}

function show_prescritpions_toCustomer($db, $cusid)
{
    global $PROJECTNAME;

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

            $pres_obj = new Prescription($db, $rx_num, $cusid, $rx_written_date);

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
                header("Location: /$PROJECTNAME/review_order.php?");
                exit;
                echo "</form>";
            }
        }
}

function isInOrder($db, $rx_num){
    #$rxnum_inOrder = false;
    $countsql = "select count(\"Order\".rx_num) from \"Order\",Prescription where order.rx_num =  '$rx_num'";
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
        $req = $db->prepare("INSERT INTO \"Order\" (cus_id,rx_num,gen_date) VALUES (:cus_id,:rx_num,:gen_date)");
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

function get_deliname($db,$delid){
    $stmt= $db->query("select first_n, last_n from Deliverer where deli_id=$delid");
    while (($row = $stmt->fetch())){
        $first_name = $row['first_n'];
        $last_name = $row['last_n'];
    }
    return "$first_name $last_name";
}

function getAddr($db,$cusid){
    $stmt = $db->prepare("select Address_line_1, Address_line_2, addr_city,addr_state,zipcode from Customer where cus_id =?");

    $stmt->execute([$cusid]);

    while ($row = $stmt->fetch()){
        $addr_line1 = $row['Address_line_1'];
        $addr_line2 = $row['Address_line_2'];
        $addr_city = $row['addr_city'];
        $addr_state = $row['addr_state'];
        $zipcode = $row['zipcode'];
    }
    $addr = [$addr_line1,$addr_line2,$addr_city,$addr_state,$zipcode];
    foreach ($addr as $value) {
        echo $value." ";
    }
}

function isDeliverer($db, $userid) {
    $res = $db->query("select count(*) from Deliverer where deli_id = " . $userid);
    if ($res && $res->fetchColumn() > 0) {
        return true;
    } else {
        return false;
    }
}

function isPrescriber($db, $userid) {
    $res = $db->query("select count(*) from Prescriber where prescriber_id <> 20 and prescriber_id = " . $userid);
    if ($res && $res->fetchColumn() > 0) {
        return true;
    } else {
        return false;
    }
}

function isManager($userid){

    if ($userid == 20) {
        return true;
    } else {
        return false;
    }
}

function isCustomer($db, $userid) {
    if (!isDeliverer($db, $userid) && !(isPrescriber($db, $userid))&& !(isManager($userid))) {
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

function addPrescription_cus($db, $prescriber_id, $gwid, $drugs, $qtys, $refills)
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
        $sql = "INSERT INTO Prescription_drug (rx_num, NDC, qty, refill) VALUES (:rx_num, :NDC, :qty, :refill)";
        $stmt = $db->prepare($sql);
        $stmt->execute(array('rx_num'=>$rx_num, 'NDC'=>$drugs[$i], 'qty' => $qtys[$i], 'refill'=>$refills[$i]));
    }
    return $rx_num;
}

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

        $pres_obj = new Prescription($db, $rx_num, $cusid, $rx_written_date);


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
function getCusAddr($db){

    $stmt = $db->query("select `Order`.order_id, C.first_name, C.last_name, C.Address_line_1, C.Address_line_2, 
    C.addr_city,C.addr_state,C.zipcode from Customer C left join `Order`
    on Order.cus_id = C.cus_id where order_id is not null and Order.deli_id is null");
    $cusAddr=$stmt->fetchAll(PDO::FETCH_ASSOC);
    return $cusAddr;
    }
function showOrdersToDeli($db)
{
    $cusAddr = getCusAddr($db);

    echo "<form method = 'post' action ='pick_order.php' >";
    echo "<legend>Please select the orders to deliver:</legend>";
    echo "<table class='table'><tr><th>Selection</th><th>Order Number</th><th>Customer Name</th><th>Customer Address</th></tr>";
    foreach ($cusAddr as $value) {
        echo "<tr><td><input type = 'checkbox' class='order-row' name = 'del_order[]' value = '".$value['order_id']."'><td>".$value['order_id'].
            "</td><td>".$value['first_name'] ." ".$value['last_name']."</td><td>".$value['Address_line_1'] ." ".$value['Address_line_2'] ." ".
            $value['addr_city'] ." ". $value['addr_state']."</td></tr>";
    }echo  "</table><input type = 'button' class = 'btn btn-warning' value = 'Select All' onclick = 'selectAll();'> ";
     echo "&nbsp;<input type = 'reset' class = 'btn btn-warning' value = 'Reset'>";
     echo "&nbsp;<input type = 'submit' class = 'btn btn-info' vlaue ='submit'></form>";


}

/*"<strong>Order Number: </strong>". $value['order_id''] . "</br>
        <strong>Customer Name: </strong>" . $value['first_name'] . $value['last_name'] . " </br>
        <strong>Customer Address: </strong>" . $value['Address_line_1'] . $value['Address_line_2'] .
            $value['addr_city'] . $value['addr_state'] . </br>/>";
;
echo "</form>";
    foreach ($cusAddr as $value) {
        echo "<input type = 'checkbox' name = 'del_order[]' value = '".$value['order_id']."'>
        <strong>Order Number: </strong>".$value['order_id'].
        "</br><strong>Customer Name: </strong>". $value['first_name'] .$value['last_name'] .
        "</br><strong>Customer Address: </strong> ".$value['Address_line_1'] .$value['Address_line_2'] .
            $value['addr_city'] . $value['addr_state']."<br/>";



   // echo "<button><a href='/$PROJECTNAME/pick_order.php?orderid=$orderid'>Deliver This Order</a></button></br>";


# show orders
//function show_orders($db) {
//    $sql = "select * from "
//    $db->pre


/*$orderid = $row['order_id'];
$first_name = $row['first_name'];
$last_name = $row['last_name'];
$addr_line1 = $row['Address_line_1'];
$addr_line2 = $row['Address_line_2'];
$addr_city = $row['addr_city'];
$addr_state = $row['addr_state'];*/