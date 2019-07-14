<?php
require "header.php";
?>
<div class="container marketing">

    <div class="row pt-5 pb-5">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
<?php
$stmt = $db->query("SELECT * FROM users");
foreach ($stmt as $row) {
    echo "<div>";
    echo "$row[id] <a href='profile.php?userid=$row[id]'>$row[email]</a> <a href='profile.php?userid=$row[id]&edit=1'>Edit</a>";
    echo "</div>";
}
?>
        </div>
        <div class="col-lg-2"></div>
    </div>
</div>
<?php
require "footer.php";
?>
