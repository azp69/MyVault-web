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

        /**
         * Hakee kaikki omistajan credentiaalit kannasta usertokenin perusteella
         */
        public function read($usertoken) {
            // haetaan ownerId kannasta
            $oid = $this->fetchOwnerId($usertoken);

            $query = 'SELECT 
                        c.id, c.ownerId, c.credentialDescription, c.username, c.pwd, c.iv, c.url
                    FROM ' . $this->table . ' as c WHERE ownerId=' . $oid;

            $result = $this->conn->query($query);

            return $result;
        }

        /**
         * Asettaa credential-objektin propertyt associative arrayn perusteella
         */
        public function set($data) {
            if (isset($data['credentialDescription']) && isset($data['username']) && isset($data['pwd']) && isset($data['iv'])) {
                $this->credentialDescription = $data['credentialDescription'];
                $this->username = $data['username'];
                $this->pwd = $data['pwd'];
                $this->iv = $data['iv'];
                $this->url = $data['url'];

            } else {
                throw new Exception("Invalid arguments!");
            }
        }

        /**
         * Vie credentiaalin kantaan
         */
        public function create($usertoken) {
            try {
                // haetaan ownerId kannasta
                $oid = fetchOwnerId($usertoken);
                $desc = mysqli_real_escape_string($this->conn, $this->credentialDescription);
                $user = mysqli_real_escape_string($this->conn, $this->username);
                $pass = mysqli_real_escape_string($this->conn, $this->pwd);
                $ivv = mysqli_real_escape_string($this->conn, $this->iv);
                $addr = mysqli_real_escape_string($this->conn, $this->url);

                $query = 'INSERT INTO ' . $this->table .
                        ' (ownerId, credentialDescription, username, pwd, iv, url)' .
                    " VALUES ('$oid', '$desc', '$user', '$pass', '$ivv', '$addr')";

                // TODO: palauta credentiaali clientille

                if ($this->conn->query($query) === TRUE) {
                    return true;
                } else {
                    throw new Exception($this->conn->error);
                }
            } catch(Exception $e) {
                throw $e;
            }
            
        }

        /**
         * kesken
         */
        public function update($usertoken, $old) {
            try {
                // tarkistus, että ownerId täsmää
                $id = mysqli_real_escape_string($this->conn, $old['id']);
                // haetaan ownerId kannasta
                $oid = fetchOwnerId($usertoken);

                $query = 'SELECT c.id, c.ownerId FROM ' . $this->table . ' as c ' . 
                        'WHERE id=' . $id . ' AND ownerId=' . $oid;

                $result = $this->conn->query($query);

                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    if ($row['ownerId'] != $old['ownerId']) {
                        // ei anneta muokata muiden credentiaaleja
                        throw new Exception('No matching credential found');
                    }
                } else {
                    throw new Exception('No matching credential found');
                }

                $desc = mysqli_real_escape_string($this->conn, $this->credentialDescription);
                $user = mysqli_real_escape_string($this->conn, $this->username);
                $pass = mysqli_real_escape_string($this->conn, $this->pwd);
                $ivv = mysqli_real_escape_string($this->conn, $this->iv);
                $addr = mysqli_real_escape_string($this->conn, $this->url);
            } catch (Exception $e) {
                throw $e;
            }
        }

        /**
         * hakee ownerId:n usertokenin perusteella kannasta
         */
        private function fetchOwnerId($usertoken) {
            $token = mysqli_real_escape_string($this->conn, $usertoken);
            $query = 'SELECT id, last_activity FROM vaultowner WHERE usertoken="'. $token . '"';

            $result = $this->conn->query($query);

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                // tarkistetaan, onko token edelleen validi, ja päivitetään last_activityä jos on
                if ($this->validateAndUpdateLastActivity($row['id'], $row['last_activity'])) {
                    return $row['id'];
                } else {
                    throw new Exception("Invalid usertoken in Credential.fetchOwnerId");
                }
            } else {
                throw new Exception("Invalid usertoken in Credential.fetchOwnerId");
            }
        }

        /**
         * Tarkastaa käyttäjän viimeisimmän aktiviteetin ajankohdan, ja palauttaa truen, mikäli käyttäjän token on edelleen voimassa
         * (= viimeisin aktiviteetti alle tunti sitten)
         */
        private function validateAndUpdateLastActivity($id, $lastActivity) {
            $milliseconds = round(microtime(true) * 1000);
            date_default_timezone_set('Europe/Helsinki');
            $stamp = strtotime($lastActivity);
            $lastActivity = $stamp * 1000;
            if (($milliseconds - $lastActivity) > 1000 * 60 * 60) {
                return false;
            } else {
                $query = 'UPDATE vaultowner SET last_activity=NOW() WHERE id="' . $id . '"';
                return $this->conn->query($query);
            }
        }
    }