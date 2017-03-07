<?php
if (!defined('SRKEY')){echo'This file can only be called via the main index.php file, and not directly';exit();}

/**
 * Demo
 *
 * @author Vince Urag
 */
class Restaurants extends SR_Controller {

    public function __construct() {
        parent::__construct();
        $this->load("model");
        $this->load('jwt');
    }

    //Getting a single or all restaurant profile
    public function get_index($resto_id = null) {

      if(!($resto_id == null)) {
          $result = $this->model->getResto($resto_id);
          if ($result['result'] == 1) {
              $this->sendResponse($result['data'][0], HTTP_Status::HTTP_OK);

          } else {
              $json_response = array(
                  "error" => [
                      "title" => "Invalid ID",
                      "detail" => "No such restaurant.",
                      "status" => 404
                    ]
              );

              $this->sendResponse($json_response, HTTP_Status::HTTP_NOT_FOUND);
          }
      } else {
        $result = $this->model->getAllResto();
        if ($result['result'] == 1) {
            $this->sendResponse($result['data'], HTTP_Status::HTTP_OK);

        } else {
            $json_response = array(
                "error" => [
                    "title" => "Missing restaurants",
                    "detail" => "No restaurants found.",
                    "status" => 404
                  ]
            );

            $this->sendResponse($json_response, HTTP_Status::HTTP_NOT_FOUND);
        }
      }
    }

    //Getting top 10 restaurants
    public function get_Topten(){
      $result = $this->model->getTop10Resto();
      if ($result['result'] == 1) {
          $this->sendResponse($result['data'], HTTP_Status::HTTP_OK);

      } else {
          $json_response = array(
              "error" => [
                  "title" => "Missing restaurants",
                  "detail" => "No restaurants found.",
                  "status" => 404
                ]
          );

          $this->sendResponse($json_response, HTTP_Status::HTTP_NOT_FOUND);
      }
    }

    //Add rating
    public function put_index($resto_id){
      $auth = json_decode($this->jwt->check(), true);
      if($auth['authorization'] == "authorized") {
          $myArray = $this->getJsonData();
          $myArray['resto_id'] = $resto_id;
          $result = $this->model->addRating($myArray);
          if ($result['result'] == 1) {
              $json_response = array(

                  "data" => [
                    "rating" => $result['rating']
                  ],
                  "success" => [
                      "title" => "Add Rating",
                      "detail" => "Rating has been accumulated",
                      "status" => 200
                  ]
              );
              $this->sendResponse($json_response, HTTP_Status::HTTP_CREATED);
          } else {
              $json_response = array(
                  "error" => [
                      "title" => "Add Rating Failed",
                      "detail" => "No rating given.",
                      "status" => 400
                    ]
              );

              $this->sendResponse($json_response, HTTP_Status::HTTP_NOT_FOUND);
          }
      } else {
          $this->sendResponse(array("error" => "unauthorized"), HTTP_Status::HTTP_UNAUTHORIZED);
      }
    }



}
