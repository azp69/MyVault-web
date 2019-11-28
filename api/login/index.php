<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    include_once('../../modules/database/Db.php');
    include_once('../../modules/models/Ownerdata.php');
    include_once('functions/generateUsertoken.php');
    
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        sendBadRequestResponse("All requests must be POSTs");
    } 
    else 
    {
        // otetaan kiinni pyynnön body
        $data = json_decode(file_get_contents('php://input'), true);
    
        // ohjataan pyyntö
        switch(strtoupper($data['requestType'])) {
            case 'LOGIN':
                handleLogin($data['username'], $data['password']);
            break;
            case 'REGISTER':
                handleUserRegistry($data['username'], $data['password']);
            break;
            default:
                sendBadRequestResponse("Bad request");
            break;
        }
    }

    function sendBadRequestResponse($message) {
        header(http_response_code(400));
        header('Content-Type: application/json');
        echo json_encode(array('message' => $message));
        exit();
    }

    function handleLogin($username, $password)
    {
        $database = new Db();
        $db = $database->connect();

        $owner = new Ownerdata($db);
        $result = $owner->checkLogin($username, $password);
        
        // echo $result;
        if ($result != null)
        {
            $usertoken = generateUserToken($result);
            echo json_encode(array("usertoken" => $usertoken));
            $data = json_decode($result, TRUE);
            $userid = $data["id"];
            $owner->getData($userid);
            $owner->setUsertoken($usertoken);
        }
        else
        {
            echo json_encode(array("message" => "Failed login"));
            exit();
        }

        

    }


    function handleUserRegistry($username, $password) {
        $database = new Db();
        $db = $database->connect();

        $owner = new Ownerdata($db);
        // asetetaan username ja password
        $owner->setData($username, $password);
        // viedään käyttäjä kantaan
        $owner->create();
        // TODO: generoidaan token ja lähetetään se clientille tai ilmoitetaan rekisteröinnin epäonnistumisesta
    }

?>