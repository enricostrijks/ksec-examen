<?php 

class ApiKeyGenerator {
    
    public function __construct() {

    }

    private function apikeygenerator() {
        $key = implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 6));

        return $key;
    }
    
}