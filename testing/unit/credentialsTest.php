<?php 

include_once '../../modules/models/Credential.php';
include_once '../../modules/database/Db.php';

class CredentialTest {
    public function testRead($usertoken) {
        $database = new Db();
        $db = $database->connect();
        $credential = new Credential($db);
        assert($result->num_rows > 0, '$result->num_rows > 0', $e);
    }
}

$test = new CredentialTest();

$test->testRead("");