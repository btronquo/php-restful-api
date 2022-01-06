<?php

// Test response
require_once('../model/Api.php');
require_once('../includes/database.php');

// query db
$database = SimplePDO::getInstance();
$database->query("SELECT * FROM `books`");
$result = $database->resultSet();

// send the response
$api = new Api();
$api->setResponseCode(200);
$api->setStatusCode(true);
$api->addResponseData($result);
$api->sendData();

?>