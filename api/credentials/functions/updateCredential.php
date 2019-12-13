<?php

include_once '../../modules/database/Db.php';
include_once '../../modules/models/Credential.php';

/**
 * Muodostaa yhteyden tietokantaan, luo uuden credential-objectin päivitetyillä tiedoilla ja päivittää id:n mukaisen credentiaalin kantaan
 */
function updateCredential($usertoken, $data) {

    // tehdään tarkistukset, että parametrit on asetettu oikein
    if (!isset($usertoken)) {
        return json_encode(
            array('message' => 401));
    }
    if (!isset($data)) {
        return json_encode(
            array('message' => 409));
    }
    if (!isset($data['id']) || is_nan($data['id'])) {
        return json_encode(
            array('message' => 409));
    }
    
    try {
        // luodaan yhteys kantaan
        $database = new Db();
        $db = $database->connect();
        $credential = new Credential($db);
        // asetetaan credential-objektin propertyt
        $credential->set($data);
        // päivitetään credential kantaan
        $result = $credential->update($usertoken, $data['id']);
        if ($result === TRUE) {
            return json_encode(array('message' => 200));
        } else {
            return json_encode(array('message' => 409, "id" => $data['id']));
        }
    } catch(Exception $e) {
        if ($e->getMessage == '409') {
            return json_encode(array('message' => 409));
        } else {
            throw $e;
        }
    }
}