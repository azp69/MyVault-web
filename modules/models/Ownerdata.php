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

        // Hakee käyttäjän tiedot kannasta ID:llä
        public function getData($id)
        {
            $query = 'SELECT 
                        o.id, o.username, o.pwd, o.usertoken, o.last_activity
                    FROM ' . $this->table . ' as o WHERE id=' . $id;
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

        public function checkLogin($username, $password)
        {
            $suola = "fsBd7ASDaigfDDds789sdf!";

            $query = 'SELECT 
                        o.id, o.username, o.pwd, o.usertoken, o.last_activity
                    FROM ' . $this->table . ' as o WHERE username="' . $username . '" AND pwd="' . $password .'"';
            try
            {
                $result = $this->conn->query($query);
                if (mysqli_num_rows($result) > 0)
                {
                    $data = mysqli_fetch_assoc($result);

                    $userid = $data['id'];
                    $usertoken = $data['usertoken'];
                    $lastActivity = $data['last_activity'];
                    $password = $data['pwd'];
                    $username = $data['username'];
    
                    $arr = json_encode(array('id' => $userid, 'usertoken' => $usertoken, 'username' => $username, 'password' => $password));
                    return $arr;
                }
                else
                {
                    return null;
                }
                

            }
            catch (Exception $e)
            {
                echo $e->getMessage();
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
            // ??TODO: tarkistus, onko käyttäjä jo kannassa??

            $user = mysqli_real_escape_string($this->conn, $this->username);
            $pass = mysqli_real_escape_string($this->conn, $this->password);

            $query = "INSERT INTO $this->table (username, pwd) VALUES ('$user' , '$pass')";

            try {
                if ($this->conn->query($query)) {
                    $this->id = $this->conn->insert_id;
                    return true;
                }
            } catch (Exception $e) {
                echo "Virhe";
                return false;
            }

        }
    }