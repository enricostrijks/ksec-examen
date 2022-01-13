<?php
include_once("../IdC.php");
include_once("../database/databasecon.php");

class GetStedentripsApi
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

    public function checkRooms()
    {
        $Dbobj = new DbConnection();
        $query = mysqli_query($Dbobj->getdbconnect(), "SELECT * FROM hotel_rooms");

        return $query;
    }

    public function getService()
    {
        try {
            $this->geverifieerd = $this->checkToken();
            $this->queryResult = $this->checkRooms();

            if (!$this->geverifieerd) {
                $this->returnData = array(
                    "message" => "Error: Unauthorized Request.",
                    "status" => "401",
                    "bearerToken" => $this->bearerToken
                );
            } else {
                if (mysqli_num_rows($this->queryResult) == 0) {
                    $this->returnData = array(
                        "message" => "Geen hotel kamers gevonden.",
                        "status" => "200",
                        "bearerToken" => $this->bearerToken
                    );
                } else {
                    while ($row = $this->queryResult->fetch_assoc()) {
                        $rows[] = $row;

                        $this->returnData = $rows;
                    }
                }
            }
        } catch (Exception $e) {
            $this->returnData = $e->getMessage();
            die();
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
$api = new GetStedentripsApi();
$api->getService();
