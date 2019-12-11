<?php

include_once '../../modules/database/Db.php';
include_once '../../modules/models/Credential.php';

/**
 * Muodostaa yhteyden tietokantaan, luo uuden credential-objectin ja poistaa id:n mukaisen credentialin kannasta
 */
function deleteCredential($usertoken, $id) {
    // tehdään tarkistukset, että parametrit on asetettu oikein
    if (!isset($usertoken)) { 
        return json_encode(
            array('message' => 401));
    }
    if (!isset($id) || is_nan($id)) { 
        return json_encode(
            array('message' => 400));
    }
    
    try {
        // luodaan yhteys kantaan
        $database = new Db();
        $db = $database->connect();
        $credential = new Credential($db);
        // poistetaan credential kannasta
        $result = $credential->delete($usertoken, $id);

        if ($result === TRUE) {
            return json_encode(array('message' => 200));
        } else {
            return json_encode(array('message' => 409, "id" => $id));
        }
    } catch(Exception $e) {
        if ($e->getMessage == '409') {
            return json_encode(array('message' => 409));
        } else {
            throw $e;
        }
    }
}