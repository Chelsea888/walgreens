<?php
require "header.php";

$edit = isset($_REQUEST['edit']) ? $_REQUEST['edit'] : 0;
$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid'] : -1;
$usertype = isset($_REQUEST['usertype']) ? $_REQUEST['usertype'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($userid == -1) {
    $userid = $auth->getUserId();
}

if ($action == 'update') {
    $username = $_REQUEST['username'];
    $req = $db->prepare("UPDATE users set username = :username where id = :id");
    $req->execute(array('username' => $username, 'id' => $userid));

    if ($usertype == 'prescriber') {
        $first_name = $_REQUEST['first_name'];
        $last_name = $_REQUEST['last_name'];
        $expertise = $_REQUEST['expertise'];
        $phone = $_REQUEST['phone'];
        $location = $_REQUEST['location'];

        $req = $db->prepare("UPDATE Prescriber set 
                                        first_name = :first_name,
                                        last_name  = :last_name,
                                        expertise  = :expertise,
                                        phone      = :phone,
                                        location   = :location
                                        WHERE prescriber_id = :id");

        $req->execute(array('first_name'=> $first_name,
                            'last_name' => $last_name,
                            'expertise' => $expertise,
                            'phone'     => $phone,
                            'location'  => $location,
                            'id' => $userid));

    } elseif ($usertype == 'deliverer') {
        $first_n = $_REQUEST['first_n'];
        $last_n = $_REQUEST['last_n'];
        $location = $_REQUEST['location'];

        $req = $db->prepare("UPDATE Deliverer set 
                                        first_n = :first_n,
                                        last_n  = :last_n,
                                        location   = :location
                                        WHERE deli_id = :id");

        $req->execute(array('first_n'=> $first_n,
            'last_n' => $last_n,
            'location'  => $location,
            'id' => $userid));

    } elseif ($usertype == 'customer') {
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

        #$isInCustomerTable = false;
        #$stmt = $db->query("SELECT * FROM Customer where cus_id = " . $userid);
        #foreach ($stmt as $row) {
           # $isInCustomerTable = true;
           # break;
        #}

       # print_r($isInCustomerTable);

        if (isCustomer($db, $userid)) {
            $req = $db->prepare("UPDATE Customer set 
                                            gwid        = :gwid,
                                            first_name  = :first_name,
                                            last_name   = :last_name,
                                            DoB         = :DoB,
                                            insurance   = :insurance,
                                            addr_street = :addr_street,
                                            addr_apt    = :addr_apt,
                                            addr_city   = :addr_city,
                                            addr_state  = :addr_state,
                                            zipcode     = :zipcode,
                                            phone       = :phone
                                            WHERE cus_id = :id");

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
        } else {
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
    }
}
?>

  <div class="container marketing">

    <div class="row">
        <div class="col-lg-3"></div>
      <div class="col-lg-6">

<?php
if (isPrescriber($db, $userid)) {
    $sql = "SELECT users.email, users.username, P.first_name, P.last_name, P.expertise, P.location, P.phone
                FROM Prescriber P
                JOIN users ON P.prescriber_id = users.id
                where users.id = " . $userid;
    if ($edit) {
        $stmt = $db->query($sql);
        echo "<form action='profile.php' method='POST'>";
        echo "<input type='hidden' name='userid' value='$userid' />";
        echo "<input type='hidden' name='usertype' value='prescriber' />";
        echo "<input type='hidden' name='action' value='update' />";
        echo "<table class='table'>";
        while($row=$stmt->fetch()) {
            echo "<tr>";
            echo "<td>Email</td>";
            echo "<td>$row[email]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>User Name</td>";
            echo "<td><input type='text' name='username' value='$row[username]' /></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>First Name</td>";
            echo "<td><input type='text' name='first_name' value='$row[first_name]' /></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Last Name</td>";
            echo "<td><input type='text' name='last_name' value='$row[last_name]' /></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Expertise</td>";
            echo "<td><input type='text' name='expertise' value='$row[expertise]' /></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Phone</td>";
            echo "<td><input type='text' name='phone' value='$row[phone]' /></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Location</td>";
            echo "<td><input type='text' name='location' value='$row[location]' /></td>";
            echo "</tr>";
        }
        echo "</table>";

        echo "<div><button type='submit' class='btn btn-dark'>Update</button></div>";
        echo "</form>";
    } else {
        $stmt = $db->query($sql);
        echo "<table class='table'>";
        while($row=$stmt->fetch()) {
            echo "<tr>";
            echo "<td>Email</td>";
            echo "<td>$row[email]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>User Name</td>";
            echo "<td>$row[username]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>First Name</td>";
            echo "<td>$row[first_name]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Last Name</td>";
            echo "<td>$row[last_name]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Expertise</td>";
            echo "<td>$row[expertise]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Phone</td>";
            echo "<td>$row[phone]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Location</td>";
            echo "<td>$row[location]</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<div><button type='button' class='btn btn-dark'><a href='profile.php?userid=$userid&edit=1'>Edit</a></button></div>";
    }
}

if (isDeliverer($db, $userid)) {
    $sql = " SELECT users.email, users.username, D.first_n, D.last_n, D.location
            FROM users 
            LEFT JOIN Deliverer D ON D.deli_id = users.id
            where users.id = " . $userid;
    if ($edit) {
        $stmt = $db->query($sql);
        echo "<form action='profile.php' method='POST'>";
        echo "<input type='hidden' name='userid' value='$userid' />";
        echo "<input type='hidden' name='usertype' value='deliverer' />";
        echo "<input type='hidden' name='action' value='update' />";
        echo "<table class='table'>";
        while($row=$stmt->fetch()) {
            echo "<tr>";
            echo "<td>Email</td>";
            echo "<td>$row[email]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>User Name</td>";
            echo "<td><input type='text' name='username' value='$row[username]' /></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>First Name</td>";
            echo "<td><input type='text' name='first_n' value='$row[first_n]' /></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Last Name</td>";
            echo "<td><input type='text' name='last_n' value='$row[last_n]' /></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Location</td>";
            echo "<td><input type='text' name='location' value='$row[location]' /></td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<div><button type='submit' class='btn btn-dark'>Update</button></div>";
        echo "</form>";
    } else {
        $stmt = $db->query($sql);
        echo "<table class='table'>";
        while($row=$stmt->fetch()) {
            echo "<tr>";
            echo "<td>Email</td>";
            echo "<td>$row[email]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>User Name</td>";
            echo "<td>$row[username]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>First Name</td>";
            echo "<td>$row[first_n]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Last Name</td>";
            echo "<td>$row[last_n]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Location</td>";
            echo "<td>$row[location]</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<div><button type='button' class='btn btn-dark'><a href='profile.php?userid=$userid&edit=1'>Edit</a></button></div>";
    }
}

if (isCustomer($db, $userid)) {
    $sql = "SELECT users.email, users.username, C.gwid, C.first_name, C.last_name, C.DoB, C.insurance, C.addr_street, 
            C.addr_apt, C.addr_city, C.addr_state, C.zipcode, C.phone
            FROM users
            LEFT JOIN Customer C ON C.cus_id = users.id
            where users.id = " . $userid;
    if ($edit) {
        $stmt = $db->query($sql);
        echo "<form action='profile.php' method='POST'>";
        echo "<input type='hidden' name='userid' value='$userid' />";
        echo "<input type='hidden' name='usertype' value='customer' />";
        echo "<input type='hidden' name='action' value='update' />";
        echo "<table class='table'>";
        while($row=$stmt->fetch()) {
            echo "<tr>";
            echo "<td>Email</td>";
            echo "<td>$row[email]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>User Name</td>";
            echo "<td><input type='text' name='username' value='$row[username]' /></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>GWID</td>";
            echo "<td><input type='text' name='gwid' value='$row[gwid]' placeholder='GWID (required)'/></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>First Name</td>";
            echo "<td><input type='text' name='first_name' value='$row[first_name]' /></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Last Name</td>";
            echo "<td><input type='text' name='last_name' value='$row[last_name]' /></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Date of Birth</td>";
            echo '<td><input type="text" name="DoB" class="datepicker" data-date-format="mm/dd/yyyy" value="' . $row['DoB'] . '" /></td>';
            echo "</tr>";

            echo "<tr>";
            echo "<td>Insurance</td>";
            echo "<td><input type='text' name='insurance' value='$row[insurance]' /></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Street</td>";
            echo "<td><input type='text' name='addr_street' value='$row[addr_street]' /></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Apt</td>";
            echo "<td><input type='text' name='addr_apt' value='$row[addr_apt]' /></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>City</td>";
            echo "<td><input type='text' name='addr_city' value='$row[addr_city]' /></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>State</td>";
            echo "<td><input type='text' name='addr_state' value='$row[addr_state]' /></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Zipcode</td>";
            echo "<td><input type='text' name='zipcode' value='$row[zipcode]' /></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Phone</td>";
            echo "<td><input type='text' name='phone' value='$row[phone]' /></td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<div><button type='submit' class='btn btn-dark'>Update</button></div>";
        echo "</form>";
    } else {
        $stmt = $db->query($sql);
        echo "<table class='table'>";
        while($row=$stmt->fetch()) {
            echo "<tr>";
            echo "<td>Email</td>";
            echo "<td>$row[email]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>User Name</td>";
            echo "<td>$row[username]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>GWID</td>";
            echo "<td>$row[gwid]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>First Name</td>";
            echo "<td>$row[first_name]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Last Name</td>";
            echo "<td>$row[last_name]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Date of Birth</td>";
            echo "<td>$row[DoB]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Insurance</td>";
            echo "<td>$row[insurance]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Street</td>";
            echo "<td>$row[addr_street]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Apt</td>";
            echo "<td>$row[addr_apt]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>City</td>";
            echo "<td>$row[addr_city]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>State</td>";
            echo "<td>$row[addr_state]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Zipcode</td>";
            echo "<td>$row[zipcode]</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Phone</td>";
            echo "<td>$row[phone]</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<div><button type='button' class='btn btn-dark'><a href='profile.php?userid=$userid&edit=1'>Edit</a></button></div>";
    }
}
?>
      </div><!-- /.col-lg-4 -->
        <div class="col-lg-3"></div>
    </div><!-- /.row -->
</div><!-- /.row -->

<link id="bsdp-css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.js"></script>
<script type="text/javascript">
    $('.datepicker').datepicker({
    });
</script>
<?php
require "footer.php";
?>
