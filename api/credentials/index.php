<?php 

include_once './functions/readCredential.php';
include_once './functions/createCredential.php';
include_once './functions/updateCredential.php';
include_once './functions/deleteCredential.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// usertokenin validointi


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    sendBadRequestResponse("All requests must be POSTs");
} else {
    // otetaan kiinni pyynnön body
    $data = json_decode(file_get_contents('php://input'), true);

    // ohjataan pyyntö
    switch(strtoupper($data['requestType'])) {
        case 'READ':
            sendCredentials($data['usertoken']);
            break;
        case 'CREATE':
            createCred($data['usertoken'], $data);
            break;
        case 'UPDATE':
            updateCred($data['usertoken'], $data);
            break;
        case 'DELETE':
            deleteCred($data['usertoken'], $data);
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

function sendCredentials($usertoken) {
    try {
        header('Content-Type: application/json');
        echo readCredentials($usertoken);
    } catch (Exception $e) {
        // message vain debuggausta varten => TODO: poista
        sendBadRequestResponse($e->getMessage());
    }
}

function createCred($usertoken, $data) {
    try {
        header(http_response_code(201));
        header('Content-Type: application/json');
        $dataToSend = createCredential($usertoken, $data);
        echo $dataToSend;
    } catch (Exception $e) {
        // message vain debuggausta varten => TODO: poista
        sendBadRequestResponse($e->getMessage());
    }
}

function updateCred($usertoken, $data) {
    try {
        header(http_response_code(200));
        header('Content-Type: application/json');
        $dataToSend = updateCredential($usertoken, $data);
        echo $dataToSend;
    } catch (Exception $e) {
        // message vain debuggausta varten => TODO: poista
        sendBadRequestResponse($e->getMessage());
    }
}

function deleteCred($usertoken, $data) {
    try {
        header(http_response_code(201));
        header('Content-Type: application/json');
        $dataToSend = deleteCredential($usertoken, $data);
        echo $dataToSend;
    } catch (Exception $e) {
        // message vain debuggausta varten => TODO: poista
        sendBadRequestResponse($e->getMessage());
    }
}