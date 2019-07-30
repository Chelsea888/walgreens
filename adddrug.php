<?php
require "header.php";

use Cart\CartItem;

try {
    $cart->restore();
} catch (Exception $e) {
}

$rx_num = $_REQUEST['rx_num'];
$NDC = $_REQUEST['NDC'];
$qty = $_REQUEST['qty'];
$price = $_REQUEST['price'];

$item = new CartItem;
$item->rx_num = $rx_num;
$item->NDC = $NDC;
$item->qty = $qty;
$item->price = $price;

$cart->add($item);

$cart->save();

$sql = "SELECT rx_written_date from Prescription WHERE rx_num = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$rx_num]);
$rx_written_date = $stmt->fetchColumn();

?>

<div class="container marketing">

    <div class="row pt-5 pb-5">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>Added to Cart</h5>
                </div>
                <div>
                    <h5><?php echo $rx_num. "Generated " . $rx_written_date; ?></h5>
                    <div>
                        <?php echo $drug_array[$NDC]; ?>
                    </div>
                    <div>
                        <?php echo "Qty: " . $qty; ?>
                    </div>
                    <div>
                        <?php echo "$" . ($qty * $price); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <a class="btn btn-primary" href="/<?php echo $PROJECTNAME; ?>/">Go to My Prescriptions</a>
                    </div>
                    <div class="col">
                        <a class="btn btn-primary" href="checkout.php">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2"></div>
    </div>
</div>

<?php
require "footer.php";
?>


