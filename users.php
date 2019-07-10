<?php
require "header.php";

$stmt = $db->query("SELECT * FROM users");
foreach ($stmt as $row) {
    echo "<div>";
    echo "$row[id] <a href='profile.php?userid=$row[id]'>$row[email]</a> <a href='profile.php?userid=$row[id]&edit=1'>Edit</a>";
    echo "</div>";
}
