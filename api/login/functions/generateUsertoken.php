<?php
    include_once('../../modules/database/Db.php');
    include_once('../../modules/models/Credential.php');

    function generateUserToken($ownerData)
    {
        $data = json_decode($ownerData, TRUE);
        $userid = $data["id"];
        $pwd = $data["password"];
        $username = $data["username"];


        $salt = "qwerty"; // HOX!!!

        $rnd = random_bytes(10);

        $token = $rnd . $salt . $userid . $pwd . $username;

        return hash("sha256", $token);
    }
?>