<?php

/* header('Access-Control-Allow-Origin: *');
header('Conten-Type: application/json'); */

include_once '../modules/database/Database.php';
include_once '../modules/models/Credential.php';

function readCredentails($ownerId) {
    $database = new Database();
    $db = $database->connect();

    $credential = new Credential($db);

    $result = $credential->read($ownerId);
    $row_count = $result->rowCount();

    if ($row_count > 0) {
        $credentialArray = array();
        $credentialArray['data'] = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $credelntialItem = array(
                //'id' => $id,
                'credentialDescription' => $credentialDescription,
                'username' => $username,
                'pwd' => $pwd,
                'iv' => $iv,
                'url' => $url
            );

            array_push($credentialArray['data'], $credelntialItem);
        }

        //echo json_encode($credentialArray);
        return json_encode($credentialArray);
    } else {
        /* echo json_encode(
            array('message' => 'No Credentials Found')
        ); */
        return json_encode(
            array('message' => 'No Credentials Found')
        );
    }
}
