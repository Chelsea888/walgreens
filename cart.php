<?php
require "header.php";
?>
<div class="container marketing">

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
              <div class="card-header">
                  <h5>Shopping Cart</h5>
              </div>
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
                          $drug_name = $drug_array[$item->NDC];
                          echo "<tr><td>$drug_name</td><td>$item->rx_num</td><td>$item->qty</td><td>$item->price</td> </tr>";
                      }
                  }
                  ?>
                  </tbody>
              </table>

              <div class="row">
                  <div class="col">
                      <a class="btn btn-primary" href="checkout.php">Proceed to Checkout</a>
                  </div>
                  <div class="col">
                      <a class="btn btn-primary" href="clearcart.php">Clear Cart</a>
                  </div>
              </div>
          </div>

          <?php
          ?>
      </div>
          <div class="col-lg-3"></div>
      </div>
</div>
<?php
require "footer.php";
?>
