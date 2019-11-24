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
            $query = 'SELECT 
                        c.id, c.ownerId, c.credentialDescription, c.username, c.pwd, c.iv, c.url
                    FROM ' . $this->table . ' as c WHERE ownerId = :id';
            $stmt = $this->conn->prepare($query);

            $stmt->execute(array('id' => $ownerId));
            return $stmt;
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
                $query = 'INSERT INTO ' . $this->table .
                        ' (ownerId, credentialDescription, username, pwd, iv, url)' .
                    ' VALUES (:ownerId, :credentialDescription, :username, :pwd, :iv, :url)';
                $stmt = $this->conn->prepare($query);

                /* $stmt->bindValue(':ownerId', $this->ownerId);
                $stmt->bindValue(':credentialDescription', $this->credentialDescription);
                $stmt->bindValue(':username', $this->username);
                $stmt->bindValue(':pwd', $this->pwd);
                $stmt->bindValue(':iv', $this->iv);
                $stmt->bindValue(':url', $this->url); */

                $success = $stmt->execute(array(
                    ':ownerId' => $this->ownerId,
                    ':credentialDescription' => $this->credentialDescription,
                    ':username' => $this->username,
                    ':pwd' => $this->pwd,
                    ':iv' => $this->iv,
                    ':url' => $this->url
                ));

                return $success;
            } catch(Exception $e) {
                throw $e;
            }
            
        }
    }