<?php

Class DbConnection{
    function getdbconnect(){
        $conn = mysqli_connect("localhost", "root", "", "reisbureau") or die("Couldn't connect");
        return $conn;
    }
}
?>
