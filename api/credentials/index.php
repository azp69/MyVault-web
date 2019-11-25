<?php 

include_once './functions/readCredential.php';
include_once './functions/createCredential.php';

header('Access-Control-Allow-Origin: *');
header('Conten-Type: application/json');

// usertokenin validointi

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    sendBadRequestResponse("not a post, but " . $_SERVER['REQUEST_METHOD']);
} else {
    $data = json_decode(file_get_contents('php://input'), true);

    switch(strtoupper($data['requestType'])) {
        case 'READ':
            sendCredentials($data['requestData']['ownerId']);
            break;
        case 'CREATE':
            createCred($data['requestData']);
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
    } catch (Exception $err) {
        sendBadRequestResponse($err->getMessage());
    }
}

function createCred($data) {
    try {
        header(http_response_code(201));
        header('Content-Type: application/json');
        $dataToSend = createCredential($data);
        echo $dataToSend;
    } catch (Exception $err) {
        sendBadRequestResponse($err->getMessage());
    }
}