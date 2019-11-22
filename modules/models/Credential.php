<?php
    class Credential {
        private $conn;
        private $table = 'credentials';

        private $id;
        private $ownerId;
        private $description;
        private $username;
        private $password;
        private $iv;
        private $url;

        public function __construct($connection) {
            $this->$conn = $connection;
        }

        public function read() {
            $query = 'SELECT 
                        c.id, c.ownerId, c.credentialDescription, c.username, c.pwd, c.iv, c.url
                    FROM ' . $this->table;
            $stmt = $this->conn->prepare($query);

            $stmt->execute();
            return $stmt;
        }
    }