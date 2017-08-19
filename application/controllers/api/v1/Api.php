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

//        if(!isset($params['user_id']) || !isset($params['device_token']) || !isset($params['password']) || ($params['user_id'] == NULL) || ($params['device_token'] == NULL) || ($params['password'] == NULL)){
//            $err['result'] = false;
//            $err['data'] = "Invalid Request";
//            returnJson($err);
//        }

        if(!isset($params['user_id']) || !isset($params['password']) || ($params['user_id'] == NULL) ||  ($params['password'] == NULL)){
            $err['result'] = false;
            $err['data'] = "Invalid Request";
            returnJson($err);
        }

        $user_id = $params['user_id'];
        $password = $params['password'];
        //$device_token = $params['device_token'];

//        $curl_handle = curl_init();
//        curl_setopt($curl_handle, CURLOPT_URL, 'http://10.0.11.33/payo_app/users/update_synapse_info');
//        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($curl_handle, CURLOPT_POST, 1);
//        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, array(
//            'user_id' => $user_id,
//            'password' => $password
//        ));
//
//        $buffer = curl_exec($curl_handle);
//        curl_close($curl_handle);
//
//        $result = json_decode($buffer);
          $data['basic_info']= array(
              'hrms_id' => '1234',
              'dept_id' => '12',
              'dept_type_id' => '123',
              'dept_type_name' => 'BR',
              'branch_id' => '1234',
              'district_id' => '1234',
              'state_id' => '1234',
              'zone_id' => '1234',
              'full_name' => 'mukesh kurmi',
              'supervisor_id' => '009',
              'designation_id' => '4',
              'designation_name' => 'BM',
              'mobile' => '9975772432',
              'email_id' => 'mukesh.kurmi@wwindia.com',
          );
        $data['employee_list'][]= array(
            'id' => '12',
            'full_name' => 'mukesh kurmi',
        );
        $data['employee_list'][]= array(
            'id' => '13',
            'full_name' => 'anup',
        );
        $data['branch_list'][]= array(
            'id' => '12',
            'full_name' => 'branch1',
        );
        $data['branch_list'][]= array(
            'id' => '13',
            'full_name' => 'branch2',
        );
        $data['zone_list'][]= array(
            'id' => '12',
            'full_name' => 'zone1',
        );
        $data['zone_list'][]= array(
            'id' => '13',
            'full_name' => 'zone2',
        );
        returnJson($data);

//        if(isset($result['status']) && $result['status'] == 'success'){
//
//            $data = array('device_token'=> $device_token,
//                'employee_id'=>$result
//            );
//            $this->App->insert_login_log($data);
//        }
//
//        else{
//            $error = array(
//                "result" => false,
//                "data" => "Invalid username or password."
//            );
//            returnJson($error);
//        }
    }

    public function master_get(){

        $data['zone_list'][]= array(
            'id' => '12',
            'name' => 'zone1',
        );
        $data['zone_list'][]= array(
            'id' => '13',
            'name' => 'zone2',
        );
        $data['state_list'][]= array(
            'id' => '12',
            'zone_id' => '1',
            'name' => 'maharashtra',
        );
        $data['state_list'][]= array(
            'id' => '13',
            'zone_id' => '1',
            'name' => 'UP',
        );
        $data['district_list'][]= array(
            'id' => '12',
            'state_id' => '12',
            'name' => 'mumbai',
        );
        $data['district_list'][]= array(
            'id' => '13',
            'state_id' => '13',
            'name' => 'Deoria',
        );
        $data['branch_list'][]= array(
            'id' => '12',
            'district_id' => '13',
            'name' => 'BKC',
        );
        $data['branch_list'][]= array(
            'id' => '13',
            'district_id' => '12',
            'name' => 'salempur branch',
        );
        returnJson($data);

//        if(isset($result['status']) && $result['status'] == 'success'){
//
//            $table = "db_app_login_logs";
//            $data = array('device_token'=> $device_token,
//                'employee_id'=>$result
//            );
//            $this->app->insert_login_log($table,$data);
//        }
//
//        else{
//            $error = array(
//                "result" => false,
//                "data" => "Invalid username or password."
//            );
//            returnJson($error);
//        }
    }

    public function lead_generated_converted_get(){
        $id = $this->input->get();

        if((isset($id['hrms_id']) && $id['hrms_id'] == '') || (isset($id['branch_id']) && $id['branch_id'] == '')){
            $err['result'] = false;
            $err['data'] = "Invalid Request";
            returnJson($err);
        }
        if(isset($id['hrms_id']) && $id['hrms_id'] != ''){
            $created_id = $id['hrms_id'];
            $where_month_Array = array('created_by'=>$created_id,
                'MONTH(created_on)'=>date('m'));
            $where_year_Array = array('created_by'=>$created_id,
                'YEAR(created_on)'=>date('Y'));

            $result_generated = $this->App->get_generated_lead($where_month_Array,$where_year_Array);
            $where_month_Array = array('employee_id'=>$created_id,
                'MONTH(created_on)'=>date('m'));
            $where_year_Array = array('employee_id'=>$created_id,
                'YEAR(created_on)'=>date('Y'));
        }
        if(isset($id['branch_id']) && $id['branch_id'] != ''){
            $branch_id = $id['branch_id'];
            $where_month_Array = array('branch_id'=>$branch_id,
                'MONTH(created_on)'=>date('m'));
            $where_year_Array = array('branch_id'=>$branch_id,
                'YEAR(created_on)'=>date('Y'));

            $result_generated = $this->App->get_generated_lead($where_month_Array,$where_year_Array);

        }

        $result_converted = $this->App->get_converted_lead($where_month_Array,$where_year_Array);
        echo json_encode($result_generated);
        echo json_encode($result_converted);

    }

}