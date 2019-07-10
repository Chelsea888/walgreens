<?php
require "header.php";
require_once "functions.php";

    global $auth;
    $doc_id = $auth->getUserId();


$rx_num = $_REQUEST['rx_num'];

//$cusid = $auth->getUserId();

//show_prescritpions($cusid);

    show_prescritpion($db, $rx_num,$doc_id);
