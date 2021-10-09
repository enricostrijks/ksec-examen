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

        $allowedRequestMethods = array('GET', 'POST');

        if(!in_array($currentRequestMethod, $allowedRequestMethods)){
            header($_SERVER["SERVER_PROTOCOL"]." 405 Method Not Allowed", true, 405);
            exit;
        }
    }
    public function getVerifiedToken() {
        $userMessages = array(
            'HTTP Method not allowed.',
            'API Key not valid.'
        );

        $this->queryResult = $this->checkApiKey();
        $this->whitelistMethod = $this->checkMethod();

        if ($this->whitelistMethod) {
            throw new Exception("HTTP Method is not valid.");
        }

        if (mysqli_num_rows($this->queryResult) == 0){   
            throw new Exception("Username or Password or API Key is not valid.");
        }

        $idp = new IdP($this->bearerCredentials);
        $token = $idp->getToken();
        return $token;  
    }
}

?>