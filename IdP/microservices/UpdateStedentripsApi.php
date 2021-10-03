<?php
include_once("../IdC.php");
include_once("../private/databasecon.php");

class UpdateStedentripsApi {
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

        $idc = new IdC($this->bearerToken, $this->bearerCredentials);
        return $idc->decodeToken();

    }

    public function reserveRoom() {
        $Dbobj = new DbConnection(); 
        $id = $_POST['id'];
        $cancel = $_POST['cancel'];

        if ($cancel == 'false') {
            $query = mysqli_query($Dbobj->getdbconnect(), "UPDATE hotel_rooms SET room_reserved = 'Y' WHERE id = $id");
        } else {
            $query = mysqli_query($Dbobj->getdbconnect(), "UPDATE hotel_rooms SET room_reserved = 'N' WHERE id = $id");
        }

        if(!$query){
            return $query = print_r($query);
        } else {
            return $query = "Actie Geslaagd";
        }
    }

    public function getService() {
        $this->geverifieerd = $this->checkToken();
        $this->queryResult = $this->reserveRoom();

        if (!$this->geverifieerd) {
            $this->returnData = array(
                "message" => "Error: Unauthorized Request.",
                "status" => "401",
                "bearerToken" => $this->bearerToken
            );
        } else {
            $this->returnData = array(
                "message" => $this->queryResult,
                "status" => "200",
                "bearerToken" => $this->bearerToken
            );
        }

        header("HTTP/1.1" .  '200');
        header("Access-Control-Allow-Origin: *");
        header("Content-Type:application/json;charset=UTF-8");
        header("X-Content-Type-Options: nosniff");
        header("Cache-Control: max-age=100");
        echo json_encode($this->returnData);
        exit;
    }
}

// execute service
$api = new UpdateStedentripsApi();
$api->getService();