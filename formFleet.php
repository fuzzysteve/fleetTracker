<?php
session_start();
include('auth_functions.php');

refresh_token();

if (!isset($_GET['url'])) {
    echo "Need a fleet URL";
    exit();
}

if (!preg_match('#^https://crest-tq.eveonline.com/fleets/\d+/$#', $_GET['url'])) {
    echo "Need a valid fleet URL";
    exit();
}

$_SESSION['fleet_url']=$_GET['url'];
session_write_close();
header('Location: /fleetTracker/');
