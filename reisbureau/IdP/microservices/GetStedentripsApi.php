<?php
include_once("../IdC.php");
class GetStedentripsApi{
    protected $bearerToken = "";
    protected $bearerCredentials = "";
    protected $returnData = "";
    protected $geverifieerd = "";
    public function __construct() {
    }

    public function checkToken() {
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

        $allHeaders = getallheaders();
        $authorization = $allHeaders['Authorization'];
        list($type, $data) = explode(" ", $authorization, 2);
        $this->bearerToken = $data;

        $this->bearerCredentials = array();
        $this->bearerCredentials['username'] = $username;
        $this->bearerCredentials['password'] = $password;

        $idc = new IdC($this->bearerToken,
            $this->bearerCredentials);
        
        return $idc->decodeToken();
    }

    public function getService() {
        $this->geverifieerd = $this->checkToken();
        if(!$this->geverifieerd) {
            $this->returnData = array(
                "message" => "Error Unauthorized Request",
                "status" => "401",
                "bearerToken" => $this->bearerToken
            );
        } else {
            $this->returnData = array(
                "message" => "API Get service uitgevoerd",
                "status" => "200",
                "bearerToken" => $this->bearerToken
            );
        }

        header("HTTP/1.1 ".$this->returnData['status']);
        header("Access-Control-Allow-Origin: *");
        header("Content-Type:application/json;charset=UTF-8");
        header("X-Content-Type-Options: nosniff");
        header("Cache-Control: max-age=100");
        echo json_encode($this->returnData);
        exit; 
    }
}

$api = new GetStedentripsApi();
$api->getService();

?>