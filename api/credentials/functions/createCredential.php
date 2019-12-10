<?php 

include_once '../../modules/database/Db.php';
include_once '../../modules/models/Credential.php';

/**
 * Muodostaa yhteyden tietokantaan, luo uuden credential-objectin parametrina annetuilla tiedoilla ja luo sen kantaan
 */
function createCredential($usertoken, $data) {
    
    if (!isset($usertoken)) { throw new Exception('Invalid usertoken in createCredential.createCredential: ' . $usertoken); }
    if (!isset($data)) { throw new Exception('Invalid data in createCredential.createCredential'); }

    try {
        $database = new Db();
        $db = $database->connect();
        $credential = new Credential($db);
        // asetetaan credential-objektin propertyt
        $credential->set($data);
        // viedään credential kantaan
        if ($id = $credential->create($usertoken)) {
            return json_encode(array('message' => 200, 'id' => $id));
        } else {
            // 409 = conflict
            return json_encode(array('message' => 409));
        }
    } catch(Exception $e) {
        if ($e->getMessage == '409') {
            return json_encode(array('message' => 409));
        } else {
            throw $e;
        }
    }
}