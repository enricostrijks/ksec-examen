<?php
include_once("../IdP/private/User.php");

$user = new User();

if (isset($_POST['username']) AND isset($_POST['email']) AND isset($_POST['password'])) {
    try {
       $user->registerUser();
       header("Location: register.php");
    } catch (Exception $e) {
        echo 'Error!: ' .$e->getMessage();
    }

} else {
    $message = "Please fill all fields";
}

// $debug = new User();
// $debug->checkLoginCredentials();

// print_r($debug);

?>
<h1>Register</h1>

<?php 
if (isset($message)) {
    echo $message;
}?>

<form name="register" method="post" action="">
  <label for="username">Username:</label><br>
  <input type="text" id="username" name="username" placeholder="Inter"><br>
  <label for="email">E-mail:</label><br>
  <input type="email" id="email" name="email" placeholder="info@inter.nl"><br><br>
  <label for="password">Password:</label><br>
  <input type="password" id="password" name="password" placeholder="*****"><br><br>
  <input type="submit" value="Submit">
</form> 

<h2>Already have an account? Login!</h2>
<a href="login.php"><button>Login</button></a>