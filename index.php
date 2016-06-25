<?php
session_start();
include('auth_functions.php');

refresh_token();

if (!isset($_SESSION['fleet_url'])) {
    ?>
    <html>
    <head><title>Enter Fleet URL</title></head>
    <body>
    <form action="formFleet.php">
    <label for="url">Enter Fleet URL (copy from the fleet drop down while boss)</label><input type="text" name=url>
    <input type=submit>
    </form>
    </body>
    </html>
    <?php
    exit();
} else {
    $url=$_SESSION['fleet_url'];
    $ch = curl_init();
    $header='Authorization: Bearer '.$_SESSION['fleet_auth_token'];
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    $result = curl_exec($ch);
    $response=json_decode($result);
    $url=$response->members->href;
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    $response=json_decode($result);

}
?>

<html>
<head><title>Fleet Tracker Example</title></head>
<body>
<table>
<tr><th>Name</th><th>Location</th><th>Docked at</th><th>Ship</th></tr>
<?php
foreach ($response->items as $member) {
    print "<tr><td>".$member->character->name."</td>";
    print "<td>".$member->solarSystem->name."</td>";
    if (isset($member->station)) {
        print "<td>".$member->station->name."</td>";
    } else {
        print "<td>Undocked</td>";
    }
    print "<td>".$member->ship->name."</td>";
    print "</tr>";

}
?>
</table>
</body>
</html>
