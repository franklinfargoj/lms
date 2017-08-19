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
        $this->load->model('Lead');
        $this->load->model('Login_model');

    }


    public function authentication_post(){
        $params = $this->input->post();

        if(!isset($params['user_id']) || !isset($params['device_token']) || !isset($params['password']) || ($params['user_id'] == NULL) || ($params['device_token'] == NULL) || ($params['password'] == NULL)){
            $err['result'] = false;
            $err['data'] = "Invalid Request";
            returnJson($err);
        }

        if(!isset($params['user_id']) || !isset($params['password']) || ($params['user_id'] == NULL) ||  ($params['password'] == NULL)){
            $err['result'] = false;
            $err['data'] = "Invalid Request";
            returnJson($err);
        }

        $user_id = $params['user_id'];
        $password = $params['password'];
        $device_token = $params['device_token'];

        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, '');
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_POST, 1);
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, array(
            'user_id' => $user_id,
            'password' => $password
        ));

        $buffer = curl_exec($curl_handle);
        curl_close($curl_handle);

        $result = json_decode($buffer);

//          $data['basic_info']= array(
//              'hrms_id' => '1234',
//              'dept_id' => '12',
//              'dept_type_id' => '123',
//              'dept_type_name' => 'BR',
//              'branch_id' => '1234',
//              'district_id' => '1234',
//              'state_id' => '1234',
//              'zone_id' => '1234',
//              'full_name' => 'mukesh kurmi',
//              'supervisor_id' => '009',
//              'designation_id' => '4',
//              'designation_name' => 'BM',
//              'mobile' => '9975772432',
//              'email_id' => 'mukesh.kurmi@wwindia.com',
//          );
//        $data['employee_list'][]= array(
//            'id' => '12',
//            'full_name' => 'mukesh kurmi',
//        );
//        $data['employee_list'][]= array(
//            'id' => '13',
//            'full_name' => 'anup',
//        );
//        $data['branch_list'][]= array(
//            'id' => '12',
//            'full_name' => 'branch1',
//        );
//        $data['branch_list'][]= array(
//            'id' => '13',
//            'full_name' => 'branch2',
//        );
//        $data['zone_list'][]= array(
//            'id' => '12',
//            'full_name' => 'zone1',
//        );
//        $data['zone_list'][]= array(
//            'id' => '13',
//            'full_name' => 'zone2',
//        );
//        returnJson($data);

        if(isset($result['status']) && $result['status'] == 'success') {

            $where_year_Array = array();
            $data = array('device_token' => $device_token,
                'employee_id' => $result
            );
            $return = $this->Login_model->insert_login_log($data);

            if (isset($result['basic_info']['designation_name']) && $result['basic_info']['designation_name'] == 'BM') {
                if (isset($result['basic_info']['branch_id']) && $result['basic_info']['branch_id'] != '') {
                    $branch_id = $result['basic_info']['branch_id'];

                    //for generated lead
                    $where_month_Array = array('branch_id' => $branch_id,
                        'created_by' != 0,
                        'MONTH(created_on)' => date('m'));

                    $generated_result = $this->Lead->get_generated_lead_bm_zm($where_month_Array);
                    $generated_key_value = array();
                    foreach ($generated_result as $k => $v){
                        $generated_key_value[$v['created_by']] = $v['total'];
                    }

                    foreach ($result['employee_list'] as $key => $val){
                        if(!array_key_exists($val['id'],$generated_key_value)){
                            $push_generated = array($val['created_by']=>$val['id'],
                                          $val['total']=>0);
                            array_push($generated_result,$push_generated);
                        }
                    }
                    //for converted lead
                    $where_month_Array = array('branch_id' => $branch_id,
                        'MONTH(created_on)' => date('m'),
                        'created_by' != 0,
                        'status' => 'converted');

                    $converted_result = $this->Lead->get_converted_lead_bm_zm($where_month_Array);
                    $converted_key_value = array();
                    foreach ($converted_result as $k => $v){
                        $converted_key_value[$v['created_by']] = $v['total'];
                    }

                    foreach ($result['employee_list'] as $key => $val){
                        if(!array_key_exists($val['id'],$converted_key_value)){
                            $push_converted = array($val['created_by']=>$val['id'],
                                'created_by' != 0,
                                $val['total']=>0);
                            array_push($converted_result,$push_converted);
                        }
                    }
                    //for assigned lead
                    $where_assigned_Array = array('branch_id' => $branch_id,
                        'YEAR(created_on)' => date('Y'));
                }
            }
            if(isset($result['basic_info']['designation_name']) && $result['basic_info']['designation_name'] == 'EM'){
                if (isset($result['basic_info']['hrms_id']) && $result['basic_info']['hrms_id'] != '') {
                    $created_id = $result['basic_info']['hrms_id'];

                    //for generated lead
                    $where_month_Array = array('created_by' => $created_id,
                        'MONTH(created_on)' => date('m'));
                    $where_year_Array = array('created_by' => $created_id,
                        'YEAR(created_on)' => date('Y'));

                    $generated_result = $this->Lead->get_generated_lead($where_month_Array, $where_year_Array);

                    //for converted lead
                    $where_month_Array = array('employee_id' => $created_id,
                        'MONTH(created_on)' => date('m'),
                        'status' => 'converted');
                    $where_year_Array = array('employee_id' => $created_id,
                        'YEAR(created_on)' => date('Y'),
                        'status' => 'converted');
                    $converted_result = $this->Lead->get_converted_lead($where_month_Array,$where_year_Array);
                    //for assigned lead
                    $where_assigned_Array = array('employee_id' => $created_id,
                        'created_by' != 0,
                        'YEAR(created_on)' => date('Y'));
                }
            }
            if(isset($result['basic_info']['designation_name']) && $result['basic_info']['designation_name'] == 'ZM'){
                if(isset($result['basic_info']['zone_id']) && $result['basic_info']['zone_id'] != ''){
                    $zone_id = $result['basic_info']['zone_id'];

                    //for generated lead
                    $where_month_Array = array('zone_id'=>$zone_id,
                        'created_by' != 0,
                        'MONTH(created_on)'=>date('m'));

                    $generated_result = $this->Lead->get_generated_lead_bm_zm($where_month_Array);
                    $generated_key_value = array();
                    foreach ($generated_result as $k => $v){
                        $generated_key_value[$v['branch_id']] = $v['total'];
                    }

                    foreach ($result['branch_list'] as $key => $val){
                        if(!array_key_exists($val['id'],$generated_key_value)){
                            $push_generated = array($val['branch_id']=>$val['id'],
                                $val['total']=>0);
                            array_push($generated_result,$push_generated);
                        }
                    }
                    //for converted lead
                    $where_month_Array = array('zone_id'=>$zone_id,
                        'MONTH(created_on)'=>date('m'),
                        'created_by' != 0,
                        'status'=>'converted');

                    $converted_result = $this->Lead->get_converted_lead_bm_zm($where_month_Array);
                    $converted_key_value = array();
                    foreach ($converted_result as $k => $v){
                        $converted_key_value[$v['branch_id']] = $v['total'];
                    }

                    foreach ($result['branch_list'] as $key => $val){
                        if(!array_key_exists($val['id'],$converted_key_value)){
                            $push_converted = array($val['branch_id']=>$val['id'],
                                $val['total']=>0);
                            array_push($converted_result,$push_converted);
                        }
                    }

                    //for assigned lead
                    $where_assigned_Array = array('zone_id'=>$zone_id,
                        'YEAR(created_on)'=>date('Y'));
                }
            }

            $assigned_result = $this->Lead->get_assigned_leads($where_assigned_Array);

            $result = array(
                "result" => True,
                "data" => array_merge($generated_result,$converted_result,$assigned_result)
            );
            returnJson($result);
        }

        else{
            $error = array(
                "result" => false,
                "data" => "Invalid username or password."
            );
            returnJson($error);
        }
    }

    public function test_get(){
        $res = $this->input->get();
        $zone_id = $res['branch_id'];

        //for generated lead
        $where_month_Array = array('branch_id'=>$zone_id,
            'MONTH(created_on)'=>date('m'));
        $result1 = $this->Lead->get_generated_lead_bm_zm($where_month_Array);

        pe($result1);

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