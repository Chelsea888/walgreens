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
    header("Location: /$PROJECTNAME/deliverer.php");
}

if(isManager($userid)){
    header("Location: /$PROJECTNAME/view_prescription_report.php");
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
                    <h6 class="d-flex justify-content-between"><b><?php echo $pres->rx_num; ?></b> generated on <?php echo $pres->rx_written_date; ?>
                        <?php if (prescription_has_refill($db, $pres->rx_num)) {
                            echo "<a class='btn btn-primary' href='placeorder.php?rx_num=$pres->rx_num'>Order</a>";
                        }
                        ?>
                    </h6>
                    <table class="table">
                        <?php
                        foreach ($pres->drugs as $drug) {
                            ?>
                            <tr class="d-flex justify-content-between">
                                <td>
                                    <div></div>
                                    <?php $drug_name= $drug_array[$drug->NDC];
                                          echo "<div>Drug Name: $drug_name</div>
                                                <div>Quantity: $drug->qty</div>
                                                <div>Refill: $drug->refilled/$drug->refill</div>
                                                <div>Price: " . $drug->unit_price . "</div>";
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($drug->refilled < $drug->refill) {
                                        echo "<a class='btn btn-secondary' href='adddrug.php?rx_num=$pres->rx_num&NDC=$drug->NDC&qty=$drug->qty&price=$drug->unit_price'>Refill</a>";
                                    } else {
                                        echo "No Refill";
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

    $prescriptions = get_ordered_prescriptions($db, $userid);

    ?>
    <div class="card mt-5">
        <div class="card-header">
            <h5>In Progress</h5>
        </div>
        <?php
    foreach ($prescriptions as $pres) {
        ?>
            <div class="card">
                <div class="card-header">
                    <h6 class="d-flex justify-content-between"><b><?php echo $pres->rx_num; ?></b> generated on <?php echo $pres->rx_written_date; ?></h6>
                    <table class="table">
                        <?php
                        foreach ($pres->drugs as $drug) {
                            ?>
                            <tr class="d-flex justify-content-between">
                                <td>
                                    <div></div>
                                    <?php $drug_name= $drug_array[$drug->NDC];
                                          echo "<div>Drug Name: $drug_name</div>
                                                <div>Quantity: $drug->qty</div>
                                                <div>Refill: $drug->refilled/$drug->refill</div>
                                                <div>Amount: " . $drug->qty*$drug->unit_price . "</div>";
                                    ?>
                                </td>
                                <td>
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

        $prescriptions = get_delivered_prescriptions($db, $userid);
            ?>
    <div class="card mt-4">
        <div class="card-header">
            <h5>Delivered</h5>
        </div>
        <?php
    foreach ($prescriptions as $pres) {
        ?>
            <div class="card">
                <div class="card-header">
                    <h6 class="d-flex justify-content-between"><b><?php echo $pres->rx_num; ?></b> generated on <?php echo $pres->rx_written_date; ?>
                        <?php if (prescription_has_refill($db, $pres->rx_num)) {
                            echo "<a class='btn btn-primary' href='placeorder.php?rx_num=$pres->rx_num'>Order</a>";
                        }
                        ?>
                    </h6>
                    <table class="table">
                        <?php
                        foreach ($pres->drugs as $drug) {
                            ?>
                            <tr class="d-flex justify-content-between">
                                <td>
                                    <div></div>
                                    <?php $drug_name= $drug_array[$drug->NDC];
                                          echo "<div>Drug Name: $drug_name</div>
                                                <div>Quantity: $drug->qty</div>
                                                <div>Refill: $drug->refilled/$drug->refill</div>
                                                <div>Amount: " . $drug->qty*$drug->unit_price . "</div>";
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($drug->refilled < $drug->refill) {
                                        echo "<a class='btn btn-secondary' href='adddrug.php?rx_num=$pres->rx_num&NDC=$drug->NDC&qty=$drug->qty&price=$drug->unit_price'>Refill</a>";
                                    } else {
                                        echo "No Refill";
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
            <?php } ?>
    </div>
    <?php

}


?>
        </div>
        <div class="col-lg-2"></div>
    </div>
</div>
<?php
require "footer.php";
