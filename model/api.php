<?php

class Api {

    // we need the response code and the response data (later in json)
    private $response_code; # http 200/etc
    private $status_code; # true if no error in the query
    private $response_data = array();

    /*
        Set the http response code and store it in the $response_code var
    */
    public function setResponseCode($responseCode) {
        $this->response_code = $responseCode;
    }

    /*
        Add the data to the array response_data
    */
   public function addResponseData($data) {
        //append the $data to response_data array
       $this->response_data['data'] = $data;
    }

    /*
        Set the status code
    */
    public function setStatusCode($statusCode) {
        //append the $data to response_data array
       $this->status_code = $statusCode;
    }

    /*
        send the response
    */
    public function sendData() {

        header('Content-type: application/json;charset=utf-8');
        http_response_code($this->response_code);

        if($this->status_code === true) {
            // OK
            echo json_encode($this->response_data);
        } else {
            // NOT OK
            //$this->response_data[] = 'error';
            echo json_encode($this->response_data);
        }

    }
}
?>
