<?php
    class Ownerdata
    {
        private $conn;
        private $table = "vaultOwner";
        private $id;
        private $username;
        private $pwd;
        private $usertoken;
        private $last_activity;

        public function __construct($connection)
        {
            $this->conn = $connection;
        }

        public function __destruct()
        {
            $this->conn->close();
        }

        public function setId($id)
        {
            $this->id = $id;
        }

        public function getId()
        {
            return $this->id;
        }

        public function getUsername()
        {
            return $this->username;
        }

        public function getPassword()
        {
            return $this->pwd;
        }
        
        public function setUsertoken($token)
        {
            $query = 'UPDATE ' . $this->table .' SET usertoken="'. $token .'" WHERE id=' . $this->id;
            try 
            {
                    if ($this->conn->query($query) === TRUE) 
                    {
                        $this->usertoken = $token;
                    }
                    else
                    {
                        echo json_encrypt(array("message" => $conn->error));
                    }
            }
            catch (Exception $e)
            {
                echo "Virhe";
                return false;
            }
        }

        // Hakee käyttäjän tiedot kannasta userilla ja passulla
        public function getData($username, $password)
        {
            $query = 'SELECT 
                        o.id, o.username, o.pwd, o.usertoken, o.last_activity
                    FROM ' . $this->table . ' as o WHERE username="' . $username . '" AND pwd="' . $password .'"';
            try {
                $result = $this->conn->query($query);
                if (mysqli_num_rows($result) > 0)
                {
                    $data = mysqli_fetch_assoc($result);
                    
                    $this->id = $data['id'];
                    $this->username = $data['username'];
                    $this->pwd = $data['pwd'];
                    $this->usertoken = $data['usertoken'];
                    $this->last_activity = $data['last_activity'];
                    return true;
                }
            }
            catch (Exception $e)
            {
                echo "Virhe";
                return false;
            }
        }
    }