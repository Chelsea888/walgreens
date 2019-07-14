<?php
require "header.php";
?>

<div class="container marketing">

    <div class="row pt-5 pb-5">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
<?php
$userid = $auth->getUserId();
if(isDeliverer($db, $userid)){
    echo "Deliver functions are in progress";
}

if(isPrescriber($db, $userid)){
    ?>
    <a class="btn btn-primary" role="button" href="/<?php echo $PROJECTNAME; ?>/prescriper.php">Add Prescription</a>
    <?php
    $sql = "SELECT rx_num, rx_written_date FROM Prescription WHERE prescriber_id = ? order by rx_written_date desc";
    $stmt = $db->prepare($sql);
    $stmt->execute([$userid]);
    ?>
    <table class="table mt-lg-3">
        <tr><th>RX Number</th><th>Written Date</th></tr>
        <?php
    while($row=$stmt->fetch()) {
        echo "<tr><td><a href='/$PROJECTNAME/myprescriptions.php?rx_num=$row[rx_num]'>$row[rx_num]</a></td> <td>$row[rx_written_date]</td></tr>";
    }
    ?>
    </table>
        <?php
}

if(isCustomer($db, $userid)) {
//    show_prescritpions_toCustomer($db, $userid);
    #display_customer_prescriptions($db, $userid);
    $prescriptions = get_prescriptions_ready_to_order($db, $userid);
    ?>
    <div class="card">
        <div class="card-header">
            <h5>Ready to Order</h5>
        </div>
        <?php
    foreach ($prescriptions as $pres) {
        ?>
            <div class="card">
                <div class="card-header">
                    <h6><?php echo $pres->rx_num; ?> generated on <?php echo $pres->rx_written_date; ?></h6>
                    <table class="table">
                        <?php
                        foreach ($pres->drugs as $drug) {
                            ?>
                            <tr>
                                <td>
                                    <div></div>
                                    <?php echo "<div>$drug->NDC</div>
                                                <div>$drug->qty</div>
                                                <div>" . $drug->qty*$drug->unit_price . "</div>";
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($drug->refilled < $drug->refill) {
                                        echo "<a class='btn btn-primary' href=''>Refill</a>";
                                    } else {
                                        echo "<a class='btn btn-dark'>Refill</a>";
                                    }
                                    ?>
                                </td>
                            </tr>
            <?php
                        }
                        ?>


                    </table>
                </div>

            </div>

        <?php
    }
    ?>
    </div>
    <?php

}

if(isDeliverer($db, $userid)) {
    show_orders($db, $userid);
}

?>
        </div>
        <div class="col-lg-2"></div>
    </div>
</div>
<?php
require "footer.php";