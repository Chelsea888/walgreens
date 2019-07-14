<?php
require "header.php";

$usertype = isset($_REQUEST['usertype']) ? $_REQUEST['usertype'] : 'prescriber';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == 'add') {
    $email = $_REQUEST['email'];
    $password = $_REQUEST['password'];

    $admin = new \Delight\Auth\Administration($db);
    $userid = $admin->createUser($email, $password);

    $firstname = $_REQUEST['firstname'];
    $lastname = $_REQUEST['lastname'];
    $location = $_REQUEST['location'];

    if ($usertype == 'prescriber') {
        $expertise = $_REQUEST['expertise'];
        $phone = $_REQUEST['phone'];

        try {
            $sql = "INSERT INTO Prescriber (prescriber_id, first_name, last_name, expertise, location, phone) VALUES (?, ?, ?, ?, ?, ?)";
            $db->prepare($sql)->execute([$userid, $firstname, $lastname, $expertise, $location, $phone]);
        } catch (PDOException $e) {
            print_r($e);
        }
    } else {
        try {
            $sql = "INSERT INTO Deliverer (deli_id, first_n, last_n, location) VALUES (?, ?, ?, ?)";
            $db->prepare($sql)->execute([$userid, $firstname, $lastname, $location]);
        } catch (PDOException $e) {
            print_r($e);
        }
    }

//    header("Location: /$PROJECTNAME/");
} else {
    ?>

<div class="container marketing">

    <div class="row pt-5 pb-5">
        <div class="col-lg-3"></div>
        <div class="col-lg-6">
    <?php
    if ($usertype == 'prescriber') {
        ?>
        <h3>Prescriber</h3>
        <form action="adduser.php" method="POST">
            <input type="hidden" name="usertype" value="prescriber" />
            <input type="hidden" name="action" value="add" />
            <div class="input-group form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="Enter Email">
                <small id="emailHelp" class="form-text text-muted">Email will be used to login</small>
            </div>

            <div class="input-group form-group">
                <label for="password">Password</label>
                <input type="text" class="form-control" id="password" name="password" placeholder="Enter Password">
            </div>

            <div class="input-group form-group">
                <label for="firstname">First name</label>
                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Enter First Name">
            </div>

            <div class="input-group form-group">
                <label for="lastname">Last name</label>
                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Enter Last Name">
            </div>

            <div class="input-group form-group">
                <label for="expertise">Expertise</label>
                <input type="text" class="form-control" id="expertise" name="expertise" placeholder="Enter Expertise">
            </div>

            <div class="input-group form-group">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter Phone">
            </div>

            <div class="input-group form-group">
                <label for="location">Location</label>
                <input type="textarea" class="form-control" id="location" name="location" placeholder="Enter Location">
            </div>

            <div class="form-group">
                <input type="submit" value="Add" class="btn btn-primary float-right login_btn">
            </div>
        </form>

        <?php
    } else if ($usertype == 'deliverer') {
        ?>
        <h3>Deliver</h3>
        <form action="adduser.php" method="POST">
            <input type="hidden" name="usertype" value="deliver" />
            <input type="hidden" name="action" value="add" />
            <div class="input-group form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="Enter Email">
                <small id="emailHelp" class="form-text text-muted">Email will be used to login</small>
            </div>

            <div class="input-group form-group">
                <label for="password">Password</label>
                <input type="text" class="form-control" id="password" name="password"  placeholder="Enter Password">
            </div>

            <div class="input-group form-group">
                <label for="firstname">First name</label>
                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Enter First Name">
            </div>

            <div class="input-group form-group">
                <label for="lastname">Last name</label>
                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Enter Last Name">
            </div>

            <div class="input-group form-group">
                <label for="location">Location</label>
                <input type="textarea" class="form-control" id="location" name="location" placeholder="Enter Location">
            </div>

            <div class="form-group">
                <input type="submit" value="Add" class="btn btn-primary float-right login_btn">
            </div>
        </form>
        <?php
    }

}
?>
      </div>
        <div class="col-lg-3"></div>
    </div>
</div>
<?php
require "footer.php";
?>
