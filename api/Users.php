<?php
if (!defined('SRKEY')){echo'This file can only be called via the main index.php file, and not directly';exit();}

/**
 * Demo
 *
 * @author Vince Urag
 */
class Users extends SR_Controller {

    public function __construct() {
        parent::__construct();
        $this->load("model");
        $this->load('jwt');
    }

    public function get_index() {
        $auth = json_decode($this->jwt->check(), true);
        if($auth['authorization'] == "authorized") {
            $user_id = $auth['userId'];
            $result = $this->model->getUser($user_id);
            if ($result['result'] == 1) {
                $this->sendResponse($result['data'][0], HTTP_Status::HTTP_OK);

            } else {
                $json_response = array(
                    "error" => [
                        "title" => "Invalid ID",
                        "detail" => "No such user ",
                        "status" => 404
                      ]
                );

                $this->sendResponse($json_response, HTTP_Status::HTTP_NOT_FOUND);
            }
            // var_dump($result);

        } else {
            $this->sendResponse(array("error" => "unauthorized"), HTTP_Status::HTTP_UNAUTHORIZED);
        }

    }

    public function post_login() {
        $myArray = $this->getJsonData();

        $result = $this->model->authentication($myArray);

        if ($result['result'] == 1) {
          $payload_token = array(
            "username" => $result['data']['username']
          );

          $token = $this->jwt->generate_token($result['data']['user_id'], $payload_token);

          $json_response = array(
            'id' => $result['data']['user_id'],
            'username' => $result['data']['username'],
            'meta' => [
                  'token' => $token
              ]
          );

          $this->sendResponse($json_response, HTTP_Status::HTTP_OK);
        } else {
          $json_response = array(
              "error" => [
                  "title" => "Authorization Error",
                  "detail" => "Invalid credentials",
                  "status" => 404
                ]
          );

          $this->sendResponse($json_response, HTTP_Status::HTTP_NOT_FOUND);
        }

    }

    public function post_index(){
        $myArray = $this->getJsonData();

        $result = $this->model->createUser($myArray);

        if($result['result'] == 0) {
          $json_response = array(
              "error" => [
                  "title" => "Invalid inputs",
                  "detail" => "Missing input in several fields.",
                  "status" => 400
                ]
          );

          $this->sendResponse($json_response, HTTP_Status::HTTP_BAD_REQUEST);
        } else if ($result['result'] == -1) {
          $json_response = array(
              "error" => [
                  "title" => "Invalid inputs",
                  "detail" => "Password does not match.",
                  "status" => 400
                ]
          );

          $this->sendResponse($json_response, HTTP_Status::HTTP_BAD_REQUEST);
        } else if ($result['result'] == -2) {
          $json_response = array(
              "error" => [
                  "title" => "Account Creation Failed",
                  "detail" => "User already exists.",
                  "status" => 400
                ]
          );

          $this->sendResponse($json_response, HTTP_Status::HTTP_BAD_REQUEST);
        } else {
          $json_response = array(
              "success" => [
                  "title" => "Success",
                  "detail" => "Account created",
                  "status" => 201
                ]
          );

          $this->sendResponse($json_response, HTTP_Status::HTTP_CREATED);
        }
    }


}
