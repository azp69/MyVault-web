<?php 

include_once './functions/readCredential.php';
include_once './functions/createCredential.php';

header('Access-Control-Allow-Origin: *');
header('Conten-Type: application/json');

// usertokenin validointi


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    sendBadRequestResponse("not a post, but " . $_SERVER['REQUEST_METHOD']);
} else {
    // otetaan kiinni pyynnön body
    $data = json_decode(file_get_contents('php://input'), true);

    // ohjataan pyyntö
    switch(strtoupper($data['requestType'])) {
        case 'READ':
            sendCredentials($data['usertoken']);
            break;
        case 'CREATE':
            createCred($data['usertoken'], $data['requestData']);
            break;
        case 'UPDATE':
            break;
        case 'DELETE':
            break;
        default:
            sendBadRequestResponse("default");
            break;
    }
}

function sendBadRequestResponse($message) {
    header(http_response_code(400));
    header('Content-Type: application/json');
    echo json_encode(array('message' => $message));
    exit();
}

function sendCredentials($ownerId) {
    try {
        header('Content-Type: application/json');
        echo readCredentails($ownerId);
    } catch (Exception $e) {
        // message vain debuggausta varten => TODO: poista
        sendBadRequestResponse($e->getMessage());
    }
}

function createCred($data) {
    try {
        header(http_response_code(201));
        header('Content-Type: application/json');
        $dataToSend = createCredential($data);
        echo $dataToSend;
    } catch (Exception $e) {
        // message vain debuggausta varten => TODO: poista
        sendBadRequestResponse($e->getMessage());
    }
}