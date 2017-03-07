<?php

/**
 * Demo model
 *
 * @author Vince Urag
 */
class Model extends SR_Model {


    public function __construct() {
        parent::__construct();
    }

    public function getUsers() {
        // return $this->db->get_row("users", array("id"=>1, "user_name"=>"V"));
        // return $this->db->get_value("users", "password", array("id"=>1,"user_name"=>"V"));
        // return $this->db->update_row("users", array("user_name"=>"Kabedng", "password" => "new_pass"), array("id"=>2));
        // return $this->db->has_row("users", array("id"=>1, "user_name"=>"V"));\
        // return $this->db->insert_row("users", array("user_name"=>"from insert", "password"=>"testing"));
        return $this->db->exec("SELECT * FROM users");
    }

    public function editUser($name, $password, $id) {
        return $this->db->update_record("users", array("user_name" => $name, "password" => $password), "id=".$id);
    }

    public function getUser($user_id) {

        $filter = array(DB_USER_ID => $user_id);

        $data['data'] = $this->db->get_row(TABLE_USER, $filter);

        if (empty($data)) {
          $data['result'] = 0;
        } else {
          $data['result'] = 1;
        }

        return $data;
    }

    public function authentication($myArray){

      $myArray['password'] = hash('SHA512', $myArray['password']);
      $filter = array(
        DB_USERNAME => $myArray['username'],
        DB_PASSWORD => $myArray['password']
      );

      if (empty($myArray['username']) || empty($myArray['password'])) {
        $data['result'] = -1;
      } else {
        if ($this->db->has_row(TABLE_USER, $filter)) {
          $data['result'] = 1;
          $data['data']['user_id'] = $this->db->get_value(TABLE_USER, DB_USER_ID, $filter);
          $data['data']['username'] = $myArray['username'];


        } else {
          $data['result'] = 0;
        }
      }

      return $data;
    }

    public function createUser($myArray){

      if($myArray['first_name'] != "" && $myArray['last_name'] != "" && $myArray['username'] != "" &&
          $myArray['password'] != "" && $myArray['re-password'] != "") {

            if ($myArray['re-password'] == $myArray['password']) {

              if(!($this->db->has_row(TABLE_USER, array(DB_USERNAME => $myArray['username'])))){
                $myArray['password'] = hash('SHA512', $myArray['password']);
                $filter = array(
                  DB_FIRST_NAME => $myArray['first_name'],
                  DB_LAST_NAME => $myArray['last_name'],
                  DB_USERNAME => $myArray['username'],
                  DB_PASSWORD => $myArray['password']
                );
                $this->db->insert_row(TABLE_USER, $filter);
                $data['result'] = 1;
              } else {
                $data['result'] = -2;
              }

            } else {
              $data['result'] = -1;
            }
      } else {
        $data['result'] = 0;
      }

      return $data;
    }

    public function getResto($resto_id) {

      $filter = array(DB_RESTO_ID => $resto_id);

      $data['data'] = $this->db->get_row(TABLE_RESTO, $filter);

      if (empty($data)) {
        $data['result'] = 0;
      } else {
        $data['result'] = 1;
      }

      return $data;
    }

    public function getAllResto() {
      $data['data'] = $this->db->exec("SELECT resto_id, resto_name FROM resto");

      if (empty($data)) {
        $data['result'] = 0;
      } else {
        $data['result'] = 1;
      }

      return $data;
    }

    public function getTop10Resto() {
      $data['data'] = $this->db->exec("SELECT resto_id, resto_name, rating FROM resto ORDER BY rating DESC limit 4");

      if (empty($data)) {
        $data['result'] = 0;
      } else {
        $data['result'] = 1;
      }

      return $data;
    }

    public function addRating($myArray){

      $filter = array(
        DB_RESTO_ID => $myArray['resto_id']
      );

      if (!(empty($myArray['rating']))) {
        $ratingDB = $this->db->get_value(TABLE_RESTO, DB_RESTO_RATING, $filter);
        $ratingDB += $myArray['rating'];
        $this->db->update_row(TABLE_RESTO, array(DB_RESTO_RATING => $ratingDB),  $filter);
        $data['rating'] = $ratingDB; 
        $data['result'] = 1;
      } else {
        $data['result'] = 0;
      }

      return $data;
    }
}
