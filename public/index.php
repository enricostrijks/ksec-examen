<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 

if(!isset($_SESSION['username'])){
    include_once("login.php");
}else{
    include_once("logged_in.php");
}
?>