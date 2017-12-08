<?php
/**
 * Created by PhpStorm.
 * User: webwerks1
 * Date: 17/8/17
 * Time: 4:40 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'/libraries/REST_Controller.php';
class Common_api extends REST_Controller
{
    /*
     * construct
     * constructor method
     * @author Gourav Thatoi
     * @access private
     * @param none
     * @return void
     *
     */
    function __construct()
    {
        // Initialization of class
        parent::__construct();
        $this->load->model('App');

    }

    public function login_post(){
        $params = $this->input->post();

        if(!isset($params['email']) || !isset($params['device_token']) || !isset($params['password']) || ($params['email'] == NULL) || ($params['device_token'] == NULL) || ($params['password'] == NULL)){
            $err['status'] = false;
            $err['message'] = "Invalid Request";
            returnJson($err);
        }

        $email = $params['email'];
        $password = $params['password'];
        $device_token = $params['device_token'];

        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, 'http://10.0.11.33/payo_app/users/update_synapse_info');
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_POST, 1);
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, array(
            'name' => $email,
            'email' => $password
        ));

        $buffer = curl_exec($curl_handle);
        curl_close($curl_handle);

        $result = json_decode($buffer);

        if(isset($result['status']) && $result['status'] == 'success'){

            $table = "db_app_login_logs";
            $data = array('device_token'=> $device_token,
                           'employee_id'=>$result
                          );
            $this->app->insert_login_log($table,$data);
        }

        else{
            $error = array(
                "status" => false,
                "message" => "Invalid username or password."
            );
            returnJson($error);
        }
    }

}
