<?php

require_once('../includes/database.php');
require_once('../model/Books.php');
require_once('../model/Api.php');

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

if(array_key_exists("bookid", $_GET)) {

    // get the task id
    $bookid = $_GET['bookid'];

    //echo $bookid;

    // if bookid is a non numeric or empty -> error 400
    if($bookid == '' || !is_numeric($bookid)) {

        $api = new API();
        $api->setStatusCode(false);
        $api->setResponseCode(400);
        $api->addResponseData("Id of the book is missing");
        $api->sendData();

    }


    // ---- REQUEST METHODS
    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        //
        try {
            $database->query("SELECT a.`id`, a.`name` as `title`, b.`id` as author_id, b.`name` as author_name
                                FROM `books` a
                                LEFT JOIN author b ON a.`author` = b.`id`
                                WHERE a.id = :bookid
                            ");
            $database->bind(':bookid', $bookid);
            $result = $database->resultSet();

            $author_item = array(
                    'id' =>  (int) $result[0]['author_id'],
                    'name' =>  $result[0]['author_name']
                );

            $book_item = array(
                'id' => $result[0]['id'],
                'type' => 'book',
                'title' => html_entity_decode($result[0]['title']),
                'author' => $author_item
            );

            $api = new API();
            $api->setStatusCode(true);
            $api->setResponseCode(200);
            $api->addResponseData($book_item);
            $api->sendData();

        } catch (PDOException $th) {
            //throw $th;
        }
    } else {
        $api = new API();
        $api->setStatusCode(false);
        $api->setResponseCode(405);
        $api->addResponseData("REQUEST_METHOD not allowed");
        $api->sendData();
        exit();
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {

        // if we set params order
        if(array_key_exists("order", $_GET)) {

            // get the param of the order
            $querryOrder = $_GET['order'];

            
                if ($querryOrder == 'author') {
                    $query_param = 'b.`name`';
                } elseif($querryOrder == 'title') {
                    $query_param = 'title';
                } else {
                    $query_param = 'a.`id`';
                }    
        } else {
            // by default we return by id asc
            $query_param = 'a.`id`';
        }

        $database->query("SELECT a.`id`, a.name as title, b.name as author_name, b.id as author_id
                            FROM `books` a
                            LEFT JOIN author b ON a.`author` = b.`id`
                            ORDER BY $query_param ASC");
        $database->bind(':query_param', $query_param);
        $result = $database->resultSet();

        if (sizeof($result) > 0) {

            $output = array();
           
            foreach ($result as $item) {

                $author_item = array(
                    'id' =>  (int) $item['author_id'],
                    'name' =>  $item['author_name']
                );
        
                $book_item = array(
                    'id' => $item['id'],
                    'type' => 'book',
                    'title' => html_entity_decode($item['title']),
                    'author' => $author_item
                );

                array_push($output, $book_item);

            }

            // send the response
            $api = new API();
            $api->setStatusCode(true);
            $api->setResponseCode(200);
            $api->addResponseData($output);
            $api->sendData();

        }

    } catch (PDOException $th) {
    // write the error in the php log
    error_log('Error [GET] query: '. $th, 0);

    // return an error to the client
    $api = new API();
    $api->setStatusCode(false);
    $api->setResponseCode(400);
    $api->addResponseData("Error in GET method");
    $api->sendData();
    exit();
    }

} elseif($_SERVER['REQUEST_METHOD'] === 'POST') {   

    // if title and author are set
    if(isset($_GET['title']) && isset($_GET['author'])) {
        $book_title = $_GET['title'];
        $book_author = $_GET['author'];
        // create the record
        $database = SimplePDO::getInstance();
        $database->query("INSERT INTO 
                            `books` (`id`, `name`, `author`) 
                            VALUES (NULL, :title, :author_id)");

        $database->bind(':title', $book_title);
        $database->bind(':author_id', $book_author);
        $result = $database->execute();

        if($result === true) {
            $last_id = $database->lastInsertId();

            $database->query("SELECT a.`id`, a.`name` as `title`, b.`id` as author_id, b.`name` as author_name
                                FROM `books` a
                                LEFT JOIN author b ON a.`author` = b.`id`
                                WHERE a.id = :bookid");
            $database->bind(':bookid', $last_id);
            $result = $database->resultSet();

            $author_item = array(
                    'id' =>  (int) $result[0]['author_id'],
                    'name' =>  $result[0]['author_name']
                );

            $book_item = array(
                'id' => $result[0]['id'],
                'type' => 'book',
                'title' => html_entity_decode($result[0]['title']),
                'author' => $author_item
            );

            $api = new API();
            $api->setStatusCode(true);
            $api->setResponseCode(200);
            $api->addResponseData($book_item);
            $api->sendData();

        } else {
            $api = new API();
            $api->setStatusCode(false);
            $api->setResponseCode(400);
            $api->addResponseData("Invalid parameters");
            $api->sendData();
            exit();
        }

    } else {
        $api = new API();
        $api->setStatusCode(false);
        $api->setResponseCode(400);
        $api->addResponseData("Missing parameter");
        $api->sendData();
        exit();
    }

} else {
    $api = new API();
    $api->setStatusCode(false);
    $api->setResponseCode(405);
    $api->addResponseData("REQUEST_METHOD not allowed");
    $api->sendData();
    exit();
}

?>