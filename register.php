<?php
require "header.php";
?>
    <div class="container marketing mt-lg-5">

        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-6">

<?php

if (isset($_REQUEST['email']) && isset($_REQUEST['password'])) {
    try {
        /*
        $userId = $auth->register($_POST['email'], $_POST['password'], $_POST['username'], function ($selector, $token) {
            echo 'Send ' . $selector . ' and ' . $token . ' to the user (e.g. via email)';
        });
        echo 'We have signed up a new user with the ID ' . $userId;
        */

        $email = $_REQUEST['email'];
        $password = $_REQUEST['password'];
        $username = $_REQUEST['username'];
        $gwid = $_REQUEST['gwid'];
        $first_name = $_REQUEST['first_name'];
        $last_name = $_REQUEST['last_name'];
        $DoB = $_REQUEST['DoB'];
        $insurance = $_REQUEST['insurance'];
        $addr_street = $_REQUEST['addr_street'];
        $addr_apt = $_REQUEST['addr_apt'];
        $addr_city = $_REQUEST['addr_city'];
        $addr_state = $_REQUEST['addr_state'];
        $zipcode = $_REQUEST['zipcode'];
        $phone = $_REQUEST['phone'];

        $admin = new \Delight\Auth\Administration($db);
        $userid = $admin->createUser($email, $password);

        $req = $db->prepare("UPDATE users set username = :username where id = :id");
        $req->execute(array('username' => $username, 'id' => $userid));

        $req = $db->prepare("INSERT INTO Customer (
                                                cus_id, gwid, first_name, last_name, DoB,
                                                insurance, addr_street, addr_apt, addr_city,
                                                addr_state, zipcode, phone) VALUES (
                                                    :id,
                                                    :gwid,
                                                    :first_name,
                                                    :last_name,
                                                    :DoB,
                                                    :insurance,
                                                    :addr_street,
                                                    :addr_apt,
                                                    :addr_city,
                                                    :addr_state,
                                                    :zipcode,
                                                    :phone)");

        /*
        echo "INSERT INTO Customer ( cus_id, gwid, first_name, last_name, DoB,
                                    insurance, addr_street, addr_apt, addr_city,
                                    addr_state, zipcode, phone) VALUES (
                                                    '$userid',
                                                    '$gwid',
                                                    '$first_name',
                                                    '$last_name',
                                                    '" . date("Y-m-d H:i:s", strtotime($DoB)) . "',
                                                    '$insurance',
                                                    '$addr_street',
                                                    '$addr_apt',
                                                    '$addr_city',
                                                    '$addr_state',
                                                    '$zipcode',
                                                    '$phone')";
        */

        $req->execute(array(
            'gwid'        => $gwid,
            'first_name'  => $first_name,
            'last_name'   => $last_name,
            'DoB'         => date("Y-m-d H:i:s", strtotime($DoB)),
            'insurance'   => $insurance,
            'addr_street' => $addr_street,
            'addr_apt'    => $addr_apt,
            'addr_city'   => $addr_city,
            'addr_state'  => $addr_state,
            'zipcode'     => $zipcode,
            'phone'       => $phone,
            'id' => $userid));

    }
    catch (\Delight\Auth\InvalidEmailException $e) {
        die('Invalid email address');
    }
    catch (\Delight\Auth\InvalidPasswordException $e) {
        die('Invalid password');
    }
    catch (\Delight\Auth\UserAlreadyExistsException $e) {
        die('User already exists');
    }
    catch (\Delight\Auth\TooManyRequestsException $e) {
        die('Too many requests');
    }
} else {
    /*
    echo '<form action="register.php" method="post" accept-charset="utf-8">';
    echo '<input type="hidden" name="action" value="register" />';
    echo '<input type="text" name="email" placeholder="Email address" /> ';
    echo '<input type="text" name="password" placeholder="Password" /> ';
    echo '<input type="text" name="GWID" placeholder="GWID" />';
    echo '<input type="text" name="username" placeholder="Username (optional)" /> ';
    echo '<select name="require_verification" size="1">';
    echo '<option value="0">Require email confirmation? — No</option>';
    echo '<option value="1">Require email confirmation? — Yes</option>';
    echo '</select> ';
    echo '<select name="require_unique_username" size="1">';
    echo '<option value="0">Username — Any</option>';
    echo '<option value="1">Username — Unique</option>';
    echo '</select> ';
    echo '<button type="submit">Register</button>';
    echo '</form>';
    */

    echo "<form action='register.php' method='POST' accept-charset='utf-8'>";
    echo '<input type="hidden" name="action" value="register" />';
    echo "<table class='table'>";

    echo "<tr>";
    echo "<td>Email</td>";
    echo "<td><input type='text' name='email' value='' /></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>Password</td>";
    echo "<td><input type='text' name='password' value='' /></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>User Name</td>";
    echo "<td><input type='text' name='username' value='' /></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>GWID</td>";
    echo "<td><input type='text' name='gwid' value='' placeholder='GWID (required)'/></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>First Name</td>";
    echo "<td><input type='text' name='first_name' value='' /></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>Last Name</td>";
    echo "<td><input type='text' name='last_name' value='' /></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>Date of Birth</td>";
    echo '<td><input type="text" name="DoB" class="datepicker" data-date-format="mm/dd/yyyy" value="" /></td>';
    echo "</tr>";

    echo "<tr>";
    echo "<td>Insurance</td>";
    echo "<td><input type='text' name='insurance' value='' /></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>Street</td>";
    echo "<td><input type='text' name='addr_street' value='' /></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>Apt</td>";
    echo "<td><input type='text' name='addr_apt' value='' /></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>City</td>";
    echo "<td><input type='text' name='addr_city' value='' /></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>State</td>";
    echo "<td><input type='text' name='addr_state' value='' /></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>Zipcode</td>";
    echo "<td><input type='text' name='zipcode' value='' /></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>Phone</td>";
    echo "<td><input type='text' name='phone' value='' /></td>";
    echo "</tr>";

    echo "</table>";
    echo "<div><button type='submit' class='btn btn-primary'>Register</button></div>";
    echo "</form>";
}

?>
            </div>
            <div class="col-lg-3"></div>
        </div>
    </div>

<link id="bsdp-css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.js"></script>
<script type="text/javascript">
    $('.datepicker').datepicker({
    });
</script>
<?php
require "footer.php";
