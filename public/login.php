<?php
include_once("../IdP/private/User.php");

$user = new User();

if (isset($_POST['username'])) {
    try {
        $username = $user->LoginUser();
        header("Location: index.php");
    } catch (Exception $e) {
        echo 'Error!: ' .$e->getMessage();
    }

    // print_r($username);
}

// $debug = new User();
// $debug->checkLoginCredentials();

// print_r($debug);

?>
<h1>Login</h1>
<form name="login" method="post" action="">
  <label for="username">Username:</label><br>
  <input type="text" id="username" name="username" placeholder="Inter"><br>
  <label for="password">Password:</label><br>
  <input type="password" id="password" name="password" placeholder="*****"><br><br>
  <input type="submit" value="Submit">
</form> 

<h2>No account yet? Apply for an api key!</h2>
<a href="register.php"><button>Register</button></a>
