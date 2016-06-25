<?php
session_start();
require_once('auth_functions.php');
require_once('secretreal.php');

$useragent="Fuzzwork Fleet Tracker";

// Make sure that the secret matches the one set before the redirect.
if (isset($_SESSION['auth_state']) and isset($_GET['state']) and $_SESSION['auth_state']==$_GET['state']) {
    $code=$_GET['code'];
    $state=$_GET['state'];


    //Do the initial check.
    $url='https://login.eveonline.com/oauth/token';
    $verify_url='https://login.eveonline.com/oauth/verify';
    $header='Authorization: Basic '.base64_encode($clientid.':'.$secret);
    $fields_string='';
    $fields=array(
                'grant_type' => 'authorization_code',
                'code' => $code
            );
    foreach ($fields as $key => $value) {
        $fields_string .= $key.'='.$value.'&';
    }
    rtrim($fields_string, '&');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    $result = curl_exec($ch);

    if ($result===false) {
        auth_error(curl_error($ch));
    }
    curl_close($ch);
    $response=json_decode($result);
    $auth_token=$response->access_token;
    $refresh_token=$response->refresh_token;
    $ch = curl_init();
    $_SESSION['fleet_expiry']=time()+(60*19);
    $_SESSION['fleet_auth_token']=$auth_token;
    $_SESSION['fleet_refresh_token']=$refresh_token;
    session_write_close();
    header("Location: /fleetTracker/");
} else {
    echo "State is wrong. Did you make sure to actually hit the login url first?";
    error_log($_SESSION['auth_state']);
    error_log($_GET['state']);
}
