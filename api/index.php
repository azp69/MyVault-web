<?php 

include_once './functions/read.php';
include_once './functions/create.php';

header('Access-Control-Allow-Origin: *');
header('Conten-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendBadRequestResponse();
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
            sendBadRequestResponse();
            break;
    }
}

function sendBadRequestResponse() {
    header(http_response_code(400));
    exit();
}

function sendCredentials($ownerId) {
    header('Content-Type: application/json');
    echo readCredentails($ownerId);
}

function createCred($data) {
    try {
        header(http_response_code(201));
        header('Content-Type: application/json');
        $dataToSend = createCredential($data);
        echo $dataToSend;
    } catch (Exception $err) {
        header(http_response_code(400));
        header('Content-Type: application/json');
        echo json_encode(array('message' => $err->getMessage()));
    }
}