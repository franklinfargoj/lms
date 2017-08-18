<?php
/**
 * Created by PhpStorm.
 * User: webwerks1
 * Date: 17/8/17
 * Time: 4:40 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';
class Api extends REST_Controller
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


    public function authentication_post(){
        $params = $this->input->post();

        if(!isset($params['user_id']) || !isset($params['device_token']) || !isset($params['password']) || ($params['user_id'] == NULL) || ($params['device_token'] == NULL) || ($params['password'] == NULL)){
            $err['result'] = false;
            $err['data'] = "Invalid Request";
            returnJson($err);
        }

        $user_id = $params['user_id'];
        $password = $params['password'];
        $device_token = $params['device_token'];

        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, 'http://10.0.11.33/payo_app/users/update_synapse_info');
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_POST, 1);
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, array(
            'user_id' => $user_id,
            'password' => $password
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
                "result" => false,
                "data" => "Invalid username or password."
            );
            returnJson($error);
        }
    }

}