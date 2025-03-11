<?php

class serverCon{
    public $conn;


    function __construct(){
        include_once 'config.php';
        $this->$conn = new mysqli(DB_HOST_NAME, DB_USERNAME, DB_PASSWORD, DB_NAME);
        if ($conn->connect_error) {
            return "Connectoion Failed";
        }
    }
}