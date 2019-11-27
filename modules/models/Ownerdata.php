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

        // Hakee käyttäjän tiedot kannasta ID:llä
        public static function getData($id)
        {
            $query = 'SELECT 
                        o.id, o.username, o.pwd, o.usertoken, o.last_activity
                    FROM ' . $this->table . ' as o WHERE id=' . $id;
            $result = $this->conn->query($query);

            if (!$result->error) { 
                return $result; 
            } else {
                throw new Exception($result->error);
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
    }