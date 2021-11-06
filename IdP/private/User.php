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

    public function getUserInformation($idParam = null) {
        $Dbobj = new DbConnection();
        $mysqli = $Dbobj->getdbconnect();
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);

        $id = $idParam;

        $stmt->execute();

        $userInfo = $stmt->get_result();

        $row = $userInfo->fetch_assoc();

        return $row;
    }
    

    public function approveUser($idParam = null)
    {
        $this->userInfo = $this->getUserInformation($idParam);
        $Dbobj = new DbConnection();
        $mysqli = $Dbobj->getdbconnect();
        $stmt = $mysqli->prepare("UPDATE users SET is_allowed = ?, apiKey = ? WHERE id = ?");
        $stmt->bind_param("isi", $approved, $apiKey, $id);

        $approved = 1;
        $apiKey = implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 6));
        $id = $idParam;

        $stmt->execute();

        $to_email = $this->userInfo['email'];
        $subject = 'Hotel Rooms Approved your API request!';
        $message = 'Your request for an api key is approved! Your API key =' . $apiKey;
        $headers = 'From: noreply@hotelrooms.com';
        mail($to_email,$subject,$message,$headers);
    }

    public function denyUser($idParam = null)
    {
        $this->userInfo = $this->getUserInformation($idParam);
        $Dbobj = new DbConnection();
        $mysqli = $Dbobj->getdbconnect();
        $stmt = $mysqli->prepare("UPDATE users SET is_allowed = ? WHERE id = ?");
        $stmt->bind_param("ii", $denied, $id);

        $denied = 0;
        $id = $idParam;

        $stmt->execute();

        $to_email = $this->userInfo['email'];
        $subject = 'Hotel Rooms Denied your API request!';
        $message = 'Your request for an api key is denied. Try again later!';
        $headers = 'From: noreply@hotelrooms.com';
        mail($to_email,$subject,$message,$headers);
    }

    public function selectAppliedUsers() {
        $Dbobj = new DbConnection();
        $mysqli = $Dbobj->getdbconnect();
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE is_allowed = ?");
        $stmt->bind_param("i", $is_allowed);

        $is_allowed = 0;
        $stmt->execute();

        $selectAppliedUsers = $stmt->get_result();

        return $selectAppliedUsers;
    }

    public function Logout()
    {
        return session_destroy();
    }
}