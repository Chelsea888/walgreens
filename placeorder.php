<?php
require "header.php";
?>
    <div class="container marketing">

        <div class="row pt-5 pb-5">
            <div class="col-lg-3"></div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Congratulations, XX!</h3>
                        <div>Your order has been placed.</div>
                        <div>Please check my prescriptions for order status.</div>
                    </div>
                    <div class="row m-2">
                        <div class="col-6">
                            <a class="btn btn-primary" href="/<?php echo $PROJECTNAME; ?>/">Go to My Prescriptions</a></div>
                        <div class="col-6">
                            <a class="btn btn-primary" href="/<?php echo $PROJECTNAME; ?>/">See Order Details</a></div>
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
