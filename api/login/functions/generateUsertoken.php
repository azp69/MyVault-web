<?php
    include_once('../../modules/database/Db.php');

    function generateUserToken($ownerData)
    {
        
        $userid = $ownerData->getId();
        $pwd = $ownerData->getPassword();
        $username = $ownerData->getUsername();


        $salt = "qwerty"; // HOX!!!

        $rnd = random_bytes(10);

        $token = $rnd . $salt . $userid . $pwd . $username;

        return hash("sha256", $token);
    }
?>