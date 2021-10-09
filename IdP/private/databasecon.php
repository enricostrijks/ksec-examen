<?php

Class DbConnection{
    function getdbconnect(){
        $conn = mysqli_connect("localhost", "root", "", "reisbureau") or die("Couldn't connect");
        
        $data = 
        array(
        "SERVER"=>$_SERVER,
        "POST"=>$_POST,
        "GET"=>$_GET
        );
        $conn->query("INSERT INTO accesslogs (
        uri,
        ip,
        _SERVER,
        _POST,
        _GET) 
        VALUES
        (
        '".$_SERVER['REQUEST_URI']."',
        '".$_SERVER['REMOTE_ADDR']."',
        '".json_encode($data['SERVER'])."',
        '".json_encode($data['POST'])."',
        '".json_encode($data['GET'])."'
        )");

        return $conn;
    }
} ?>
