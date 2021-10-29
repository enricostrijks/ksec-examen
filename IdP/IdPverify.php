<?php
include_once("jwt_helper.php");
include_once('IdP/IdP.php');
include_once("private/databasecon.php");

class IdPVerify extends JWT {
    protected $bearerCredentials = "";
    protected $bearerToken = "";
    protected $credentials = "";
    public function __construct($bearerCredentials) {
        $this->bearerCredentials = $bearerCredentials;
    }
    public function checkApiKey() {
        $Dbobj = new DbConnection(); 
        $query = mysqli_query($Dbobj->getdbconnect(), "SELECT * FROM users WHERE username = '". $this->bearerCredentials['username'] ."' AND password = '". $this->bearerCredentials['password'] ."' AND apiKey = '". $this->bearerCredentials['apiKey'] ."' ");

        return $query;
    }
    public function checkMethod() {
        $currentRequestMethod = $_SERVER['REQUEST_METHOD'];

        $allowedRequestMethods = $this->bearerCredentials['methode'];

        if(!in_array($currentRequestMethod, $allowedRequestMethods)){
            $this->logAuditLog("Post method invalid (returned 405)");
            header($_SERVER["SERVER_PROTOCOL"]." 405 Method Not Allowed", true, 405);
            exit;
        }
    }
    public function logAuditLog($evenement = null) {
        $Dbobj = new DbConnection();
        $mysqli = $Dbobj->getdbconnect();
        $stmt = $mysqli->prepare("INSERT INTO auditlogs (ip, gebruiker, evenement) VALUES (?,?,?)");
        $stmt->bind_param("sss", $ip, $gebruiker, $event);

        $ip = $_SERVER['REMOTE_ADDR'];
        if(is_null($this->bearerCredentials['username'])) {
            $gebruiker = "unknown";
        } else {
            $gebruiker = $this->bearerCredentials['username'];
        }
        $event = $evenement;
        
        $stmt->execute();
    }

    public function getVerifiedToken() {
        $this->queryResult = $this->checkApiKey();
        $this->whitelistMethod = $this->checkMethod();

        if ($this->whitelistMethod) {
            $this->logAuditLog("Post method invalid (did not return 405)");
            throw new Exception("HTTP Method is not valid.");
        }

        if (mysqli_num_rows($this->queryResult) == 0){
            $this->logAuditLog("Login failed");   
            throw new Exception("Username or Password or API Key is not valid.");
        }

        $idp = new IdP($this->bearerCredentials);
        $token = $idp->getToken();
        $this->logAuditLog("Login succesvol");

        return $token;  
    }
}

?>