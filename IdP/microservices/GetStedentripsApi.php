<?php
include_once("../IdC.php");
include_once("../private/databasecon.php");

class GetStedentripsApi {
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

    public function checkRooms() {
        $Dbobj = new DbConnection(); 
        $query = mysqli_query($Dbobj->getdbconnect(), "SELECT * FROM hotel_rooms");

        return $query;
    }

    public function getService() {
        $this->geverifieerd = $this->checkToken();
        $this->queryResult = $this->checkRooms();

        if (!$this->geverifieerd) {
            $this->returnData = array(
                "message" => "Error: Unauthorized Request.",
                "status" => "401",
                "bearerToken" => $this->bearerToken
            );
        } else {
            if (mysqli_num_rows($this->queryResult) == 0){   
                $this->returnData = array(
                    "message" => "Geen hotel kamers gevonden.",
                    "status" => "200",
                    "bearerToken" => $this->bearerToken
                );
            }else{
                while($row = mysqli_fetch_assoc($this->queryResult)){
                    $this->returnData = array(
                        "id" => $row['id'],
                        "hotel_name" => $row['hotel_name'],
                        "hotel_room" => $row['hotel_room'],
                        "room_city" => $row['room_city'],
                        "room_price" => $row['room_price'],
                        "room_rating" => $row['room_rating'],
                        "room_details" => $row['room_details'],
                        "room_shortad" => $row['room_shortad'],
                        "room_image" => $row['room_image'],
                        "room_beds" => $row['room_beds'],
                        "room_kitchen" => $row['room_kitchen'],
                        "room_bathroom" => $row['room_bathroom'],
                        "room_sale" => $row['room_sale'],
                        "status" => "200",
                        "bearerToken" => $this->bearerToken
                    );
                }
            }
        }

        header("HTTP/1.1" .  $this->returnData['status']);
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