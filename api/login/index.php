<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    include_once('../../modules/database/Db.php');
    include_once('../../modules/models/Ownerdata.php');
    include_once('functions/hashPassword.php');
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
                handleUserRegistry($data['username'], $data['password'], $data['serialkey']);
            break;
            case 'CHECKIFUSEREXISTS':
                checkIfUserExists($data['username']);
            break;
            case 'PWDUPDATE':
                updateUserPassword($data['usertoken'], $data['oldpassword'], $data['newpassword']);
            break;
            default:
                sendBadRequestResponse("Bad request");
            break;
        }
    }

    function sendBadRequestResponse($message) {
        header(http_response_code(400));
        header('Content-Type: application/json');
        header('ERROR: ' . $message);
        echo json_encode(array('message' => $message));
        exit();
    }

    function updateUserPassword($usertoken, $oldpass, $newpass)
    {
        $database = new Db();
        $db = $database->connect();

        $newpass = hashPassword(mysqli_real_escape_string($db, $newpass));
        $oldpass = hashPassword(mysqli_real_escape_string($db, $oldpass));

        $usertoken = mysqli_real_escape_string($db, $usertoken);

        $owner = new Ownerdata($db);

        $success = $owner->getDataWithToken($usertoken);

        if ($success)
        {
            if ($owner->setPassword($oldpass, $newpass))
            {
                echo json_encode(array("message" => "ok"));
                exit();
            }
        }
        sendBadRequestResponse("Something went wrong while changing password.");

    }

    function handleLogin($username, $password)
    {
        $database = new Db();
        $db = $database->connect();

        $password = hashPassword(mysqli_real_escape_string($db, $password));
        $username = mysqli_real_escape_string($db, $username);

        $owner = new Ownerdata($db);

        $success = $owner->getData($username, $password);

        // $result = $owner->checkLogin($username, $password);
        
        // echo $result;
        if ($success == true)
        {
            $usertoken = generateUserToken($owner);

            echo json_encode(array("usertoken" => $usertoken));
            
            $owner->setUsertoken($usertoken);
        }
        else
        {
            sendBadRequestResponse("Failed login");
            // echo json_encode(array("message" => "Failed login"));
            exit();
        }

        

    }

    function checkIfUserExists($username) {
        $database = new Db();
        $db = $database->connect();
        $owner = new Ownerdata($db);
        $userexists = $owner->checkIfUserAlreadyExists($username);
        if ($userexists) {
            header(http_response_code(200));
            echo json_encode(array("message" => "Username already taken!"));
            exit();
        } else {
            header(http_response_code(204));
            exit();
        }
    }

    function handleUserRegistry($username, $password, $serialkey) {
        try { 
            if ($serialkey != 'Beta 2019' || strlen($password) < 8) {
                echo json_encode(array("message" => "Registry failed"));
                exit();
                return;
            }
            $database = new Db();
            $db = $database->connect();

            $password = hashPassword(mysqli_real_escape_string($db, $password));
            $username = mysqli_real_escape_string($db, $username);

            $owner = new Ownerdata($db);
            // asetetaan username ja password
            $owner->setData($username, $password);
            // viedään käyttäjä kantaan
            if ($owner->create()) {
                $usertoken = generateUserToken($owner);
                echo json_encode(array("usertoken" => $usertoken));
                $owner->setUsertoken($usertoken);
                exit();
            } else {
                echo json_encode(array("message" => "Registry failed"));
                exit();
            }
        } catch (Exception $e) {
            echo json_encode(array("message" => $e->getMessage()));
        }
    }

?>