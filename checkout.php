<?php
require "header.php";

?>

<?php
$cusid = $auth->getUserId();

?>
<div class="container marketing">

    <div class="row p-5">
        <div class="col-lg-6">
             <div class="card">
                 <div class="card-header">
                     <h5>Order Details</h5>
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
                            <td>$</td>
                        </tr>
                        <tr>
                            <td>Shipping:</td>
                            <td>$</td>
                        </tr>
                        <tr>
                            <td>Total before tax:</td>
                            <td>$</td>
                        </tr>
                        <tr>
                            <td>Estimated tax to be collected:</td>
                            <td>$</td>
                        </tr>
                        <tr>
                            <td><b>Order Total</b></td>
                            <td><b>$</b></td>
                        </tr>
                    </table>
                    <a class="btn btn-primary" href="placeorder.php">Place Your Order</a>

                </div>
            </div>
        </div>

    </div>

      <div class="row pt-5 pb-5">
          <div class="col-lg-3"></div>
          <div class="col-lg-6">
          <?php
          try {
              $cart->restore();
          } catch (Exception $e) {
          }
?>
          <div class="card">
              <table class="table">
                  <thead>
                      <th>Drug Name</th>
                      <th>RX Number</th>
                      <th>Qty</th>
                      <th>Price</th>
                  </thead>
                  <tbody>
<?php
                  if ($cart->totalItems() > 0) {
                      foreach ($cart->all() as $item) {
                          echo "<tr><td>$item->name</td><td>RX-Num</td><td>$item->quantity</td><td>$item->price</td> </tr>";
                      }
                  }
                  ?>
                  </tbody>
              </table>
          </div>
      </div>
          <div class="col-lg-3"></div>
      </div>
</div>
<?php
require "footer.php";
?>
