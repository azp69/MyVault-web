<?php 

include_once '../modules/database/Database.php';
include_once '../modules/models/Credential.php';

function createCredential($data) {
    
    try {
        $database = new Database();
        $db = $database->connect();
        $credential = new Credential($db);
        $credential->set($data);
        if ($credential->create()) {
            return json_encode(array('message' => 'New credential created!'));
        } else {
            return json_encode(array('message' => 'Could not create'));
        }
    } catch(Exception $e) {
        throw $e;
    }
}