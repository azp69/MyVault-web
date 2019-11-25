<?php
    class Credential {
        private $conn;
        private $table = 'credentials';

        private $id;
        private $ownerId;
        private $credentialDescription;
        private $username;
        private $password;
        private $iv;
        private $url;

        public function __construct($connection) {
            $this->conn = $connection;
        }

        public function read($ownerId) {

            $oid = mysqli_real_escape_string($this->conn, $ownerId);

            $query = 'SELECT 
                        c.id, c.ownerId, c.credentialDescription, c.username, c.pwd, c.iv, c.url
                    FROM ' . $this->table . ' as c WHERE ownerId=' . $oid;

            $result = $this->conn->query($query);

            return $result;
        }

        public function set($data) {
            if (isset($data['ownerId']) && isset($data['credentialDescription']) && 
                isset($data['username']) && isset($data['pwd']) && isset($data['iv'])) {

                $this->ownerId = $data['ownerId'];
                $this->credentialDescription = $data['credentialDescription'];
                $this->username = $data['username'];
                $this->pwd = $data['pwd'];
                $this->iv = $data['iv'];
                $this->url = $data['url'];

            } else {
                throw new Exception("Invalid arguments!");
            }
        }

        public function create() {
            try {

                $oid = mysqli_real_escape_string($this->conn, $this->ownerId);
                $desc = mysqli_real_escape_string($this->conn, $this->credentialDescription);
                $user = mysqli_real_escape_string($this->conn, $this->username);
                $pass = mysqli_real_escape_string($this->conn, $this->pwd);
                $ivv = mysqli_real_escape_string($this->conn, $this->iv);
                $addr = mysqli_real_escape_string($this->conn, $this->url);

                $query = 'INSERT INTO ' . $this->table .
                        ' (ownerId, credentialDescription, username, pwd, iv, url)' .
                    " VALUES ('$oid', '$desc', '$user', '$pass', '$ivv', '$addr')";

                if ($this->conn->query($query) === TRUE) {
                    return true;
                } else {
                    throw new Exception($this->conn->error);
                }
            } catch(Exception $e) {
                throw $e;
            }
            
        }

        public function update(Credential $update) {
            
        }
    }