<?php

include_once '../../modules/database/Db.php';
include_once '../../modules/models/Credential.php';

/**
 * Muodostaa yhteyden tietokantaan, hakee usertokenin perusteella ownerin credentiaalit kannasta ja palauttaa ne JSON-objektina
 */
function readCredentials($usertoken) {

    // tarkistetaan, että usertoken on asetettu
    // tämä varmaan tulee muutoin tarkistettavaksi
    if (!isset($usertoken)) { throw new Exception('Invalid usertoken in readCredentials.readCredentials'); }

    try {
        // luodaan yhteys kantaan
        $database = new Db();
        $db = $database->connect();

        // luodaan uusi credential-objekti ja haetaan sen avulla credentialit kannasta
        $credential = new Credential($db);
        $result = $credential->read($usertoken);

        // luodaan tietokannan riveistä associative array ja palautetaan se JSON-objektina
        $row_count = $result->num_rows;
        if ($row_count > 0) {
            $credentialArray = array();
            $credentialArray['data'] = array();
    
            while ($row = $result->fetch_assoc()) {
                extract($row);
                $credentialItem = array(
                    'id' => $id,
                    'credentialDescription' => $credentialDescription,
                    'username' => $username,
                    'pwd' => $pwd,
                    'iv' => $iv,
                    'url' => $url
                );
                array_push($credentialArray['data'], $credentialItem);
            }
            return json_encode($credentialArray);
        } else {
            return json_encode(
                array('message' => 'No Credentials Found')
            );
        }
    } catch(Exception $e) {
        throw $e;
    }

    
}
