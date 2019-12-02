<?php
    class Credential {
        private $conn;
        private $table = 'credentials';

        private $id;
        private $ownerId;
        private $credentialDescription;
        private $username;
        private $pwd;
        private $salt;
        private $iv;
        private $url;
        private $checkForLastActivity = false;

        public function __construct($connection) {
            $this->conn = $connection;
        }

        /**
         * Hakee kaikki omistajan credentiaalit kannasta usertokenin perusteella
         */
        public function read($usertoken) {
            // haetaan ownerId kannasta
            $oid = $this->fetchOwnerId($usertoken);
            // tehdään query kantaan
            $query = 'SELECT 
                        c.id, c.ownerId, c.credentialDescription, c.username, c.pwd, c.salt, c.iv, c.url
                    FROM ' . $this->table . ' as c WHERE ownerId=' . $oid;
            $result = $this->conn->query($query);

            if (!$result->error) { 
                return $result; 
            } else {
                // vain debuggausta varten, jotain muuta lopulliseen versioon
                throw new Exception($result->error);
            }
        }

        /**
         * Asettaa credential-objektin propertyt associative arrayn perusteella
         */
        public function set($data) {
            if (isset($data['credentialDescription']) && isset($data['username']) && 
                isset($data['pwd']) && isset($data['salt']) && isset($data['iv'])) {
                $this->credentialDescription = $data['credentialDescription'];
                $this->username = $data['username'];
                $this->pwd = $data['pwd'];
                $this->salt = $data['salt'];
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
                $oid = $this->fetchOwnerId($usertoken);
                $desc = mysqli_real_escape_string($this->conn, $this->credentialDescription);
                $user = mysqli_real_escape_string($this->conn, $this->username);
                $pass = mysqli_real_escape_string($this->conn, $this->pwd);
                $salt = mysqli_real_escape_string($this->conn, $this->salt);
                $ivv = mysqli_real_escape_string($this->conn, $this->iv);
                $addr = mysqli_real_escape_string($this->conn, $this->url);

                $query = 'INSERT INTO ' . $this->table .
                        ' (ownerId, credentialDescription, username, pwd, salt, iv, url)' .
                    " VALUES ('$oid', '$desc', '$user', '$pass', '$salt', '$ivv', '$addr')";

                // TODO: palauta credentiaali clientille

                if ($this->conn->query($query) === TRUE) {
                    $last_id = $this->conn->insert_id;
                    return $last_id;
                } else {
                    throw new Exception($this->conn->error);
                }
            } catch(Exception $e) {
                throw $e;
            }
            
        }

        /**
         * Tekee tarkistuksen, että id:llä ja ownerId:llä löytyy kannasta credentiaali ja päivittää sen
         */
        public function update($usertoken, $id) {
            try {
                $id = mysqli_real_escape_string($this->conn, $id);
                // haetaan ownerId kannasta
                $oid = $this->fetchOwnerId($usertoken);

                // haetaan vanha credentiaali kannasta
                $old = $this->fetchOldCredential($id, $oid);

                if ($old == null) { 
                    throw new Exception('No matching credential found'); 
                }

                $desc = mysqli_real_escape_string($this->conn, $this->credentialDescription);
                $user = mysqli_real_escape_string($this->conn, $this->username);
                $pass = mysqli_real_escape_string($this->conn, $this->pwd);
                $salt = mysqli_real_escape_string($this->conn, $this->salt);
                $ivv = mysqli_real_escape_string($this->conn, $this->iv);
                $addr = mysqli_real_escape_string($this->conn, $this->url);

                // tehdään query kantaan ja palautetaan sen antama boolean
                $query = "UPDATE $this->table " . 
                        "SET credentialDescription='$desc', username='$user', pwd='$pass', salt='$salt', iv='$ivv', url='$addr' " .
                        "WHERE id='$id'";
                if ($this->conn->query($query)) { 
                    return true; 
                } else { 
                    return false; 
                }
            } catch (Exception $e) {
                throw $e;
            }
        }

        /**
         * Tekee tarkistuksen, että id:llä ja ownerId:llä löytyy kannasta credentiaali ja poistaa sen
         */
        public function delete($usertoken, $data) {
            try {
                $id = mysqli_real_escape_string($this->conn, $data['id']);
                // haetaan ownerId kannasta
                $oid = $this->fetchOwnerId($usertoken);
                // haetaan vanha credentiaali kannasta
                /*
                $old = $this->fetchOldCredential($id, $oid);
                if ($old == null) { throw new Exception('No matching credential found'); }
                // tehdään query kantaan
                */
                $query = "DELETE FROM $this->table WHERE id='$id'";
                if ($this->conn->query($query)) { 
                    return true; 
                } else { 
                    echo $conn->error;
                    return false; 
                }
            } catch (Exception $e) {
                throw $e;
            }
        }

        /**
         * hakee vanhan credentialin kannasta tarkistuksia varten id:n ja ownerId:n perusteella
         */
        private function fetchOldCredential($id, $oid) {
            try {
                // TODO: tarvittaessa haetaan muutkin datat
                // tehdään query kantaan
                $query = 'SELECT id, ownerId FROM ' . $this->table . ' ' . 
                            'WHERE id=' . $id . ' AND ownerId=' . $oid;
                $result = $this->conn->query($query);
                // jos kanta palautta rivin, palautetaan se
                if (!$this->conn->error && $result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    return $row;
                } else { 
                    return null; 
                }
            } catch (Exception $e) {
                throw $e;
            }
        }

        /**
         * hakee ownerId:n usertokenin perusteella kannasta
         */
        private function fetchOwnerId($usertoken) {
            $token = mysqli_real_escape_string($this->conn, $usertoken);
            $query = 'SELECT id, last_activity FROM vaultOwner WHERE usertoken="'. $token . '"';
            $result = $this->conn->query($query);

            if (!$this->conn->error && $result->num_rows == 1) {
                $row = $result->fetch_assoc();

                if ($this->checkForLastActivity == false)
                {
                    return $row['id'];
                }

                // tarkistetaan, onko token edelleen validi, ja päivitetään last_activityä jos on
                if ($this->validateAndUpdateLastActivity($row['id'], $row['last_activity'])) { 
                    return $row['id'];
                } else { 
                    // throw new Exception("Invalid usertoken in Credential.fetchOwnerId"); 
                 }
            } else { 
                throw new Exception("Invalid usertoken in Credential.fetchOwnerId"); 
            }
        }

        /**
         * Tarkastaa käyttäjän viimeisimmän aktiviteetin ajankohdan, päivittää ajankohdan
         * ja palauttaa truen, mikäli käyttäjän token on edelleen voimassa
         * (= viimeisin aktiviteetti alle tunti sitten)
         */
        private function validateAndUpdateLastActivity($id, $lastActivity) {
            // otetaan kiinni ajanhetki millisekunteina nyt
            $milliseconds = round(microtime(true) * 1000);
            // timezone (stackoverflowssa oli näin, en tiedä onko pakollinen)
            date_default_timezone_set('Europe/Helsinki');
            // muutetaan kannasta saatu timestamp millisekunteiksi
            $stamp = strtotime($lastActivity);
            $lastActivity = $stamp * 1000;
            // jos ajanhetki nyt on yli tunti, palautetaan false, eli käyttäjän pitää kirjautua uudestaan
            // jotta saa uuden tokenin
            if (($milliseconds - $lastActivity) > 1000 * 60 * 60) { 
                return false; 
            } else {
                // muutoin päivitetään ajankohtaa kannassa ja palautetaan siitä saatava boolean 
                // (jos päivitys ei onnistu niin ei varmaan sitten kannata antaa tehdä mitään)
                $query = 'UPDATE vaultOwner SET last_activity=NOW() WHERE id="' . $id . '"';
                return $this->conn->query($query);
            }
        }
    }