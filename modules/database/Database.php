<?php
    class Database {
        private $host;
        private $db_name;
        private $username;
        private $password;
        private $conn;

        public function __construct() {
            include('../modules/database/dbCredentials.php');
            $this->host = $h;
            $this->db_name = $n;
            $this->username = $u;
            $this->password = $p;
        }

        public function connect() {
            $this->conn = null;

            try {
                $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo 'Connection Error: ' . $e->getMessage();
            }

            return $this->conn;
        }
    }