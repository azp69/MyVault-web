<?php

class Db {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        include('../../modules/database/dbCredentials.php');
        $this->host = $h;
        $this->db_name = $n;
        $this->username = $u;
        $this->password = $p;
    }

    public function connect() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
        if ($this->conn->connect_error) {
            throw new Exception($this->conn->connect_error);
        }
        return $this->conn;
    }

    function __destruct() {
        $this->conn->close();
    }

}