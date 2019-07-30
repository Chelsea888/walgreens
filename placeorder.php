<?php
require "header.php";
use Cart\CartItem;

$rx_num = isset($_REQUEST['rx_num']) ? $_REQUEST['rx_num'] : '';

?>

    <div class="container marketing">
<?php
if ($rx_num) {
    try {
        $cart->restore();
    } catch (Exception $e) {
    }

    $stmt = $db->prepare("select PD.NDC, PD.qty, D.unit_price D.drug_name FROM Prescription_drug PD JOIN Drug D ON PD.NDC = D.NDC 
            WHERE PD.rx_num = ? AND refilled < refill");
    $stmt->execute([$rx_num]);
    $drugs = array();
    while($row=$stmt->fetch()) {
        $item = new CartItem;
        $item->rx_num = $rx_num;
        $item->NDC = $row['NDC'];
        $item->qty = $row['qty'];
        $item->price = $row['unit_price'];
        $item->drug_name = $row['drug_name'];

        $cart->add($item);
    }
    $cart->save();

    header("Location: /$PROJECTNAME/checkout.php");
} else {
    try {
        $cart->restore();
    } catch (Exception $e) {
    }

    $itemAmount = 0;

    //    $item = new CartItem;
    //    $item->rx_num = $rx_num;
    //    $item->NDC = $NDC;
    //    $item->qty = $qty;
    //    $item->price = $price;

    // split into rx
    if ($cart->totalItems() > 0) {
        $rxes = array();
        foreach ($cart->all() as $item) {
            $rx_num = $item->rx_num;
            if (array_key_exists($rx_num, $rxes)) {
                $drugarr = $rxes[$rx_num];
                $drugarr[] = $item;
            } else {
                $drugarr = array($item);
                $rxes[$rx_num] = $drugarr;
            }
        }

        # TODO refill button should only refill once in a session

        foreach ($rxes as $rx_num => $drugs) {
            $itemAmount = 0;
            foreach ($drugs as $drug) {
                $itemAmount += $drug->qty * $drug->price;
            }
            $itemAmount = round($itemAmount, 2);
            $insuranceCoverage = round($itemAmount * 0.5, 2);
            $totalBeforeTax = round($itemAmount - $insuranceCoverage, 2);
            $tax = round($totalBeforeTax * .0575, 2);
            $shipping = round($totalBeforeTax * 0.05, 2);
            $orderTotal = round($totalBeforeTax + $tax + $shipping, 2);

            # add order
            $sql = "INSERT INTO `Order` (cus_id, rx_num, gen_date, total_amount) VALUES (:cus_id, :rx_num, :gen_date, :total_amount)";
            $stmt = $db->prepare($sql);
            try {
                $db->beginTransaction();
                $gen_datedate = date("Y-m-d H:i:s");
                $stmt->execute( array('cus_id' => $auth->getUserId(),
                    'rx_num' => $rx_num,
                    'gen_date' => $gen_datedate,
                    'total_amount' => $orderTotal));
                $orderid = $db->lastInsertId();

                foreach ($drugs as $drug) {
                    # add drugs
                    $sql = "insert into Order_Drug (order_id, rx_num, NDC, qty) VALUES (:order_id, :rx_num, :NDC, :qty)";
                    $stmt = $db->prepare($sql);
                    $stmt->execute(array("order_id" => $orderid, 'rx_num' => $rx_num, 'NDC' => $drug->NDC, 'qty' => $drug->qty));

                    # update prescription refill counter
                    $sql2 = "UPDATE Prescription_drug SET refilled = refilled+1 WHERE rx_num = :rx_num AND NDC = :NDC";
                    $stmt2 = $db->prepare($sql2);
                    $stmt2->execute(array('rx_num' => $rx_num, 'NDC' => $drug->NDC));
                }

                $db->commit();

                $cart->clear();
            } catch(PDOExecption $e) {
                $db->rollback();
                print "Error!: " . $e->getMessage() . "</br>";
            }
        }
    }
}
        ?>

        <div class="row pt-5 pb-5">
            <div class="col-lg-3"></div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Congratulations, <?php echo getUserFullName($db, $auth->getUserId()); ?>!</h3>
                        <div>Your order has been placed.</div>
                        <div>Please check my prescriptions for order status.</div>
                    </div>
                    <div class="row m-2">
                        <div class="col-6"> </div>
                        <div class="col-6">
                            <a class="btn btn-primary" href="/<?php echo $PROJECTNAME; ?>/">Go to My Prescriptions</a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3"></div>
        </div>
    </div>
<?php
require "footer.php";
?>
