<?php
session_start();
    //Throw login redirect.
    $authsite='https://login.eveonline.com';
    $authurl='/oauth/authorize';
    $client_id='123652c2803f49f9aa942b42960141a0';
    $redirect_uri="https%3A%2F%2Fwww.fuzzwork.co.uk%2FfleetTracker%2Fauth.php";
    $state=uniqid();

    $redirecturl=$_SERVER['HTTP_REFERER'];
    
    $redirecturl="https://www.fuzzwork.co.uk/fleetTracker/";

    $_SESSION['auth_state']=$state;
    $_SESSION['auth_redirect']=$redirect_to;
    session_write_close();
    header(
        'Location:'.$authsite.$authurl
        .'?response_type=code&redirect_uri='.$redirect_uri
        .'&client_id='.$client_id.'&scope=fleetRead&state='.$state
    );
    exit;
