<?php
function auth_error($error_message)
{
    print "There's been an error";
    error_log($error_message);
    exit();
}

function refresh_token()
{
    if (!isset($_SESSION['fleet_expiry'])) {
        header("location: /fleetTracker/login.php");
    }
    if (time()>$_SESSION['fleet_expiry']) {
        include('secretreal.php');
        $url='https://login.eveonline.com/oauth/token';
        $header='Authorization: Basic '.base64_encode($clientid.':'.$secret);
        $fields_string='';
        $fields=array(
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $_SESSION['fleet_refresh_token']
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
        $_SESSION['fleet_auth_token']=$auth_token;
        $_SESSION['fleet_refresh_token']=$refresh_token;
        $_SESSION['fleet_expiry']=time()+(60*19);
        session_write_close();
    }

}
