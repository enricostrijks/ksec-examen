<?php
include_once('../IdP/private/User.php');

if (isset($_GET['logout'])) {
    $logout = new User();
    $logout->Logout();

    header("Location: index.php");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotels</title>
</head>

<body>
    <h2>Kies een service</h2>
    <a href='../getStedentrips.php'>Stedentrips</a>
    
    <br>
    <br>
    <a href='?logout'>Log uit</a>
</body>

</html>