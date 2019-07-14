<?php
require "header.php";
require_once "functions.php";
?>

<div class="container marketing">

    <div class="row pt-5 pb-5">
        <div class="col-lg-3"></div>
        <div class="col-lg-6">
<?php
$rx_num = $_REQUEST['rx_num'];

$doc_id = $auth->getUserId();

$pres_obj = get_prescritpion($db, $rx_num);
?>
            <div class="card">
                <h5 class="card-header">Prescription <?php echo $pres_obj->rx_num; ?></h5>
                <div class="card-body">
                    <h5 class="card-title">Generated on <?php  echo $pres_obj->rx_written_date; ?></h5>
                    <p class="card-text">By Doctor <?php echo $pres_obj->doc_firstn . " " . $pres_obj->doc_lastn; ?></p>
                </div>
                <ul class="list-group list-group-flush">
                    <?php
                    foreach ($pres_obj->drugs as $drug) {
                        echo "<li class='list-group-item'>" . $drug->drug_name . " " . $drug->drug_stren . " $" . $drug->unit_price . "</li>";
                    }
                    ?>
                </ul>
            </div>
      </div>
        <div class="col-lg-3"></div>
    </div>
</div>
<?php
require "footer.php";
?>
