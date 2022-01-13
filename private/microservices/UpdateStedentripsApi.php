<?php
include_once("../IdC.php");
include_once("../database/databasecon.php");

class UpdateStedentripsApi
{
    protected $bearerToken = "";
    protected $bearerCredentials = "";
    protected $returnData = "";
    protected $geverifieerd = "";

    public function __construct()
    {
    }

    public function checkToken()
    {
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

    public function logAuditLog($evenement = null)
    {
        $Dbobj = new DbConnection();
        $mysqli = $Dbobj->getdbconnect();
        $stmt = $mysqli->prepare("INSERT INTO auditlogs (ip, gebruiker, evenement) VALUES (?,?,?)");
        $stmt->bind_param("sss", $ip, $gebruiker, $event);

        $ip = $_SERVER['REMOTE_ADDR'];
        if (is_null($this->bearerCredentials['username'])) {
            $gebruiker = "unknown";
        } else {
            $gebruiker = $this->bearerCredentials['username'];
        }
        $event = $evenement;

        $stmt->execute();
    }

    public function reserveRoom()
    {
        $Dbobj = new DbConnection();
        $id = $_POST['id'];
        $cancel = $_POST['cancel'];

        $mysqli = $Dbobj->getdbconnect();

        if ($cancel == 'false') {
            $this->logAuditLog("Hotel kamer gereserveerd");
            $stmt = $mysqli->prepare("UPDATE hotel_rooms SET room_reserved = ? WHERE id = ?");
            $stmt->bind_param("si", $roomReserve, $idUser);

            $roomReserve = "Y";
            $idUser = $id;

            $stmt->execute();
        } else {
            $this->logAuditLog("Hotel kamer geannuleerd");
            $stmt = $mysqli->prepare("UPDATE hotel_rooms SET room_reserved = ? WHERE id = ?");
            $stmt->bind_param("si", $roomReserve, $idUser);

            $roomReserve = "Y";
            $idUser = $id;

            $stmt->execute();
        }

        if (!$stmt) {
            $this->logAuditLog("Hotel kamer reservering mislukt");
            return $query = "Er is iets misgegaan, probeer het later opnieuw!";
        } else {
            return $query = "Actie Geslaagd";
        }
    }

    public function getService()
    {
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
