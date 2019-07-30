<?php
\error_reporting(\E_ALL);
\ini_set('display_errors', 'stdout');

// enable assertions
\ini_set('assert.active', 1);
@\ini_set('zend.assertions', 1);
\ini_set('assert.exception', 1);

\header('Content-type: text/html; charset=utf-8');

require __DIR__.'/vendor/autoload.php';

#$db = new \PDO('mysql:dbname=walgreens;host=127.0.0.1;charset=utf8mb4', 'wguser', 'AXhf$tu2p5R2');
$db = new \PDO('mysql:dbname=walgreens;host=localhost;charset=utf8mb4', 'testuser', 'mypassword');
$auth = new \Delight\Auth\Auth($db);

$PROJECTNAME = "walgreens";

if (!$auth->isLoggedIn()) {
    if ($_SERVER['REQUEST_URI'] != "/$PROJECTNAME/register.php") {
        header("Location: /$PROJECTNAME/login.php");
    }
}

$datatype = isset($_REQUEST['datatype']) ? $_REQUEST['datatype'] : '';

if ($datatype == 'drug') {
    $category = isset($_REQUEST['category']) ? $_REQUEST['category'] : '';

    $output = array();
    if ($category) {
        $sql = "SELECT NDC, drug_name FROM Drug WHERE category = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$category]);
        while($row = $stmt->fetch()) {
            $output[$row['NDC']] = $row['drug_name'];
        }
    }

    echo json_encode($output);

} elseif ($datatype == 'category') {
    $output = array();
    $sql = "SELECT DISTINCT category FROM Drug order by LOWER(category)";
    $stmt = $db->query($sql);

    while ($row = $stmt->fetch()) {
        $output[] = $row['category'];
    }

    echo json_encode($output);
} elseif ($datatype == 'gwid') {
    $term = $_REQUEST['term'];
    $output = array();
    $sql = "SELECT gwid, first_name, last_name FROM Customer where gwid like ? or first_name like ? or last_name like ?";
    $stmt = $db->prepare($sql);
    $stmt->execute(["%$term%", "%$term%", "%$term%"]);

    while ($row = $stmt->fetch()) {
        $output[] = "$row[first_name] $row[last_name] | $row[gwid]";
    }
    echo json_encode($output);
}
