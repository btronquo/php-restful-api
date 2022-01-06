<?php

require_once('../includes/database.php');
//require_once('../model/Books.php');
require_once('../model/api.php');

try {
    // connect to db
    $database = SimplePDO::getInstance();

} catch (PDOException $th) {
 
    // write the error in the php log
    error_log('Database Connection error: '. $th, 0);

    // return an error to the client
    $api = new API();
    $api->setStatusCode(false);
    $api->setResponseCode(400);
    $api->addResponseData("Error in database connection");
    $api->sendData();
    exit();
}

if(array_key_exists("author_name", $_GET)) {

    // replace the underscore by a space and Uppercase the first letter of each word
    $author_name = ucwords(preg_replace('/([_])/', ' ', $_GET['author_name']));

    if (array_key_exists("order", $_GET) && ($_GET['order'] === 'id' || $_GET['order'] === 'title')) {
        
        if ($_GET['order'] === 'id') {
            $query_param =  'b.`name`';
        } elseif ($_GET['order'] === 'title'){
            $query_param = 'b.`name`';
        }

        $database->query("
                            SELECT b.`id`, a.`name`, 'book' as type, b.`name` as title
                            FROM `author` a
                            LEFT JOIN books b ON b.`author` = a.`id`
                            WHERE a.`name` = :author_name
                           ");

        $database->bind(':author_name', $author_name);
        $result = $database->resultSet();

        $api = new API();
        $api->setStatusCode(true);
        $api->setResponseCode(200);
        $api->addResponseData($result);
        $api->sendData();

    } else {
        $api = new API();
        $api->setStatusCode(false);
        $api->setResponseCode(400);
        $api->addResponseData("Error in parameters");
        $api->sendData();
        exit();
    }

}

?>