<?php

include_once '../../modules/database/Db.php';
include_once '../../modules/models/Credential.php';

/**
 * Muodostaa yhteyden tietokantaan, luo uuden credential-objectin ja poistaa id:n mukaisen credentialin kannasta
 */
function deleteCredential($usertoken, $id) {
    // tehdään tarkistukset, että parametrit on asetettu oikein
    if (!isset($usertoken)) { throw new Exception('Invalid usertoken in deleteCredential.deleteCredential'); }
    if (!isset($id) || is_nan($id)) { throw new Exception('Invalid $id in deleteCredential.deleteCredential'); }
    
    try {
        // luodaan yhteys kantaan
        $database = new Db();
        $db = $database->connect();
        $credential = new Credential($db);
        // poistetaan credential kannasta
        $result = $credential->delete($usertoken, $id);

        if ($result === TRUE) {
            return json_encode(array('message' => 'Credential deleted!'));
        } else {
            return json_encode(array('message' => 'Could not delete credential', "id" => $data['id']));
        }
    } catch(Exception $e) {
        throw $e;
    }
}