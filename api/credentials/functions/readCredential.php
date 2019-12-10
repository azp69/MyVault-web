<?php

include_once '../../modules/database/Db.php';
include_once '../../modules/models/Credential.php';

/**
 * Muodostaa yhteyden tietokantaan, hakee usertokenin perusteella ownerin credentiaalit kannasta ja palauttaa ne JSON-objektina
 */
function readCredentials($usertoken) {

    if (!isset($usertoken)) { 
        return json_encode(
            array('message' => 401)); 
    }

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
            $responseArray = array();
            $responseArray['data'] = array();
            $responseArray['message'] = 200;
    
            while ($row = $result->fetch_assoc()) {
                extract($row);
                $credentialItem = array(
                    'id' => $id,
                    'credentialDescription' => $credentialDescription,
                    'username' => $username,
                    'pwd' => $pwd,
                    'salt' => $salt,
                    'iv' => $iv,
                    'url' => $url
                );
                array_push($responseArray['data'], $credentialItem);
            }
            return json_encode($responseArray);
        } else {
            return json_encode(
                array('message' => 201)
            );
        }
    } catch(Exception $e) {
        throw $e;
    }

    
}
