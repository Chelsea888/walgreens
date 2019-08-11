<?php
require "header.php";

?>

<?php
$cusid = $auth->getUserId();
try {
  $cart->restore();
} catch (Exception $e) {
}

$itemAmount = 0;

if ($cart->totalItems() > 0) {
    foreach ($cart->all() as $item) {
        $itemAmount += $item->qty * $item->price;
    }
}

$itemAmount = round($itemAmount, 2);
$insuranceCoverage = round($itemAmount * 0.5, 2);
$totalBeforeTax = round($itemAmount - $insuranceCoverage, 2);
$tax = round($totalBeforeTax * .0575, 2);
$shipping = round($totalBeforeTax * 0.05, 2);
$orderTotal = round($totalBeforeTax + $tax + $shipping, 2);

?>

?>
<div class="container marketing">

    <div class="row p-4">

        <div class="col-lg-6">
            <h5>Order Details</h5>
            <table class="table">
                <thead>
                <th>Drug Name</th>
                <th>Qty</th>
                <th>Price</th>
                </thead>
                <tbody>
                <?php
                if ($cart->totalItems() > 0) {
                    foreach ($cart->all() as $item) {
                        $drug_name = $drug_array[$item->NDC];
                        echo "<tr><td>$drug_name</td><td>$item->rx_num</td><td>$item->qty</td><td>$item->price</td> </tr>";
                    }
                }
                ?>
                </tbody>
            </table>

            <div class="card">
                <div class="card-header">
                    <div><b>Shipping Address</b></div>
                    <div>Name: <?php echo get_username($db,$cusid);?></div>
                    <div>Address: <?php echo getAddr($db,$cusid);?></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Order Summary</h5>
                    <table class="table">
                        <tr>
                            <td>Items:</td>
                            <td>$<?php echo $itemAmount; ?></td>
                        </tr>
                        <tr>
                            <td>Insurance Coverage:</td>
                            <td>-$<?php echo $insuranceCoverage; ?></td>
                        </tr>
                        <tr>
                            <td>Total before tax:</td>
                            <td>$<?php echo $totalBeforeTax; ?></td>
                        </tr>
                        <tr>
                            <td>Estimated tax to be collected:</td>
                            <td>$<?php echo $tax; ?></td>
                        </tr>
                        <tr>
                            <td>Shipping:</td>
                            <td>$<?php echo $shipping; ?></td>
                        </tr>
                        <tr>
                            <td><b>Order Total</b></td>
                            <td><b>$<?php echo $orderTotal ?></b></td>
                        </tr>
                    </table>
                    <a class="btn btn-primary" href="placeorder.php">Check Out</a>
                </div>
            </div>
        </div>
    </div>
</div>



<?php
require "footer.php";
?>
