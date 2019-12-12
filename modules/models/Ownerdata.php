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

        public function setPassword($oldpass, $newpass)
        {
            $query = 'UPDATE ' . $this->table . ' SET pwd="' . $newpass . '" WHERE id="' . $this->id . '" AND pwd="' . $oldpass . '"';
            try
            {
                if ($this->conn->query($query) === TRUE)
                {
                    if ($this->conn->affected_rows > 0)
                    {
                        $this->pwd = $newpass;
                        return true;
                    }
                }
                else
                {
                    echo json_encrypt(array("message" => $conn->error));
                    return false;
                }
            }
            catch (Exception $e)
            {
                return false;
            }
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
                        return false;
                    }
            }
            catch (Exception $e)
            {
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

        public function getDataWithToken($token)
        {
            $query = 'SELECT 
                        o.id, o.username, o.pwd, o.usertoken, o.last_activity
                    FROM ' . $this->table . ' as o WHERE usertoken="' . $token . '"';
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

        public function updateUserToken($usertoken)
        {

        }

        /**
         * asettaa uuden käyttäjän usernamen ja passwordin
         */
        public function setData($username, $password)
        {
            if (!isset($username) || !isset($password)) {
                echo "error at Ownerdata.setData: invalid arguments";
                return false;
            }
            $this->username = $username;
            $this->password = $password;
        }

        /**
         * vie uuden käyttäjän tietokantaan
         */
        public function create() {
            
            if ($this->checkIfUserAlreadyExists($this->username)) {
                return false;
            }

            $user = mysqli_real_escape_string($this->conn, $this->username);
            $pass = mysqli_real_escape_string($this->conn, $this->password);

            $query = "INSERT INTO $this->table (username, pwd) VALUES ('$user' , '$pass')";

            try {
                if ($this->conn->query($query)) {
                    $this->id = $this->conn->insert_id;
                    return true;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                echo "Virhe";
                return false;
            }

        }

        public function checkIfUserAlreadyExists($username) {
            $username = mysqli_real_escape_string($this->conn, $username);
            $query = "SELECT id FROM $this->table WHERE username='$username'";
            $result = $this->conn->query($query);
            if (mysqli_num_rows($result) > 0) {
                return true;
            } else {
                return false;
            }
        }
    }