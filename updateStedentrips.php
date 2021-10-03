<?php
include_once('IdP/IdP.php');

$username = "reisbureau ZIP";
$password = "123";
$credentials = array();
$credentials['username'] = $username;
$credentials['password'] = $password;
$credentials['exp'] = time() + (60 * 60);

$idp = new IdP($credentials);
$token = $idp->getToken();

$APIurl = "http://localhost/reisbureau/IdP/microservices/UpdateStedentripsApi.php";

$ch = curl_init($APIurl);
$curl_post_data = array(
    'apiKey' => '1234567890',
    'username' => $username,
    'password' => $password,
    'id' => $_GET['id'],
    'cancel' => $_GET['cancel'],
);

curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $token));
curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_post_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$resultStatus = curl_getinfo($ch);

// print_r($response);
// print_r($resultStatus);

$decoded = json_decode($response, true);

// print_r($decoded);
// print_r($decoded['room_sale']);

echo "<br>" . $decoded['message'];
echo "<br> <a href='../getStedentrips.php'>Ga naar stedentrips</a>";

curl_close($ch);

// print_r($decoded);