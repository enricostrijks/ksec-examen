<?php
    include_once("IdP/IdP.php");
    $username = "reisbureau ZIP";
    $password = "123";
    $credentials = array();
    $credentials['username'] = $username;
    $credentials['password'] = $password;
    $credentials['exp'] = time() + (60*60);

    $idp = new IdP($credentials);
    $token = $idp->getToken();

    $APIurl = "http://localhost/reisbureau/IdP/microservices/GetStedentripsApi.php";
    // crēeer url resource
    $ch = curl_init ($APIurl);
    // set header opties
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$token));
?>