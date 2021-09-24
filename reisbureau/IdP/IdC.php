<?php

include_once("jwt_helper.php");

class IdC extends JWT {
    protected $bearerCredentials = "";
    protected $bearerToken = "";

    public function __construct(
        $bearerToken, $bearerCredentials) {
        $this->bearerToken = $bearerToken;
        $this->bearerCredentials = $bearerCredentials;
    }

    public function decodeToken() {
        $decoded = JWT::decode($this->bearerToken, 'secret_server_keys');
        if (($decoded->username ==
           $this->bearerCredentials['username']) &&
                ($decoded->password ==
           $this->bearerCredentials['password']) &&
                ($decoded->exp > time())) {
                return true;
        } else {
                return false;
        }
    }
}
?>