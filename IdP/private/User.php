<?php
include_once("databasecon.php");

class User
{
    public function __construct()
    {
    }

    public function logAuditLog($evenement = null)
    {
        $Dbobj = new DbConnection();
        $mysqli = $Dbobj->getdbconnect();
        $stmt = $mysqli->prepare("INSERT INTO auditlogs (ip, gebruiker, evenement) VALUES (?,?,?)");
        $stmt->bind_param("sss", $ip, $gebruiker, $event);

        $ip = $_SERVER['REMOTE_ADDR'];
        if (is_null($_POST['username'])) {
            $gebruiker = "unknown";
        } else {
            $gebruiker = $_POST['username'];
        }
        $event = $evenement;

        $stmt->execute();
    }

    public function checkLoginCredentials()
    {
        $Dbobj = new DbConnection();
        $mysqli = $Dbobj->getdbconnect();
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);

        $username = $_POST['username'];

        // $username = 'reisbureauzip';
        // $password = '123';
        $stmt->execute();

        $credentialsexecution = $stmt->get_result();

        return $credentialsexecution;
    }

    public function LoginUser()
    {
        $this->credentialsCheck = $this->checkLoginCredentials();
        $query = $this->checkLoginCredentials();

        if (mysqli_num_rows($this->credentialsCheck) == 0) {
            $this->logAuditLog("Login form username not found");
            throw new Exception("Username not found.");
        }

        $row = $query->fetch_assoc();
        $password = $_POST['password'];

        if(!password_verify($password, $row['password'])) {
            $this->logAuditLog("Login form wrong password");
            throw new Exception("Password is wrong.");
        }

        if($row['is_allowed'] == 0) {
            $this->logAuditLog("Login without access granted");
            throw new Exception("Account is not granted yet.");
        }

        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $_POST['username'];
        $_SESSION['password'] = $row['password'];
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['apiKey'] = $row['apiKey'];
        $_SESSION['role'] = $row['role'];
    }

    public function registerUser()
    {
        $Dbobj = new DbConnection();
        $mysqli = $Dbobj->getdbconnect();
        $stmt = $mysqli->prepare("INSERT INTO users (username, email, password) VALUES (?,?,?)");
        $stmt->bind_param("sss", $username, $email, $password );

        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])){
            throw new Exception('Please fill all fields');
        }
        
        // if (!$stmt->execute()) {
        //    throw new Exception('error executing statement: ' . $stmt->error);
        // }

        $stmt->execute();
    }
    

    public function approveUser()
    {
        $Dbobj = new DbConnection();
        $mysqli = $Dbobj->getdbconnect();
        $stmt = $mysqli->prepare("INSERT INTO users (ip, gebruiker, evenement) VALUES (?,?,?)");
        $stmt->bind_param("sss", $ip, $gebruiker, $event);
    }

    public function denyUser()
    {
        $Dbobj = new DbConnection();
        $mysqli = $Dbobj->getdbconnect();
        $stmt = $mysqli->prepare("INSERT INTO users (ip, gebruiker, evenement) VALUES (?,?,?)");
        $stmt->bind_param("sss", $ip, $gebruiker, $event);
    }

    // public function checkAdmin()
    // {
    //     $Dbobj = new DbConnection();
    //     $mysqli = $Dbobj->getdbconnect();
    //     $stmt = $mysqli->prepare("SELECT is_admin FROM users WHERE username = ?");
    //     $stmt->bind_param("s", $username);

    //     $username = $_SESSION['username'];

    //     $stmt->execute();
        
    //     $query = $stmt->get_result();
    //     $row = $query->fetch_assoc();

    //     if($row['is_admin'] == 1) {
    //         $admin = 0;
    //     } else {
    //         $admin = 1;
    //     }

    //     return $admin;
    // }

    public function Logout()
    {
        return session_destroy();
    }
}