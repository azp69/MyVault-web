<?php

include_once '../../modules/database/Db.php';
include_once '../../modules/models/Credential.php';

function readCredentails($ownerId) {
    $database = new Db();
    $db = null;
    //try {
        $db = $database->connect();
    //} catch (Exception $e) {
        //throw $e;
    //}

    $credential = new Credential($db);

    $result = $credential->read($ownerId);
    $row_count = $result->num_rows;

    if ($row_count > 0) {
        $credentialArray = array();
        $credentialArray['data'] = array();

        while ($row = $result->fetch_assoc()) {
            extract($row);

            // id:n lähetys
            $credelntialItem = array(
                'credentialDescription' => $credentialDescription,
                'username' => $username,
                'pwd' => $pwd,
                'iv' => $iv,
                'url' => $url
            );

            array_push($credentialArray['data'], $credelntialItem);
        }
        return json_encode($credentialArray);
    } else {
        return json_encode(
            array('message' => 'No Credentials Found')
        );
    }
}
