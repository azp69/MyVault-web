<?php 

include_once '../../modules/database/Db.php';
include_once '../../modules/models/Credential.php';

function createCredential($usertoken, $data) {
    
    try {
        $database = new Db();
        $db = $database->connect();
        $credential = new Credential($db);
        // asetetaan credential-objektin propertyt
        $credential->set($data);
        // viedään credential kantaan
        if ($credential->create()) {
            return json_encode(array('message' => 'New credential created!'));
        } else {
            return json_encode(array('message' => 'Could not create'));
        }
    } catch(Exception $e) {
        throw $e;
    }
}