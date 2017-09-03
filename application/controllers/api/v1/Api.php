<?php
/**
 * Created by PhpStorm.
 * User: webwerks1
 * Date: 17/8/17
 * Time: 4:40 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

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
        $this->load->model('Ticker_model', 'ticker');
        $this->load->model('Faq_model', 'faq');

    }


    public function authentication_post()
    {
        $params = $this->input->post();

//
//        if(!isset($params['user_id']) || !isset($params['device_token']) || !isset($params['password']) || ($params['user_id'] == NULL) || ($params['device_token'] == NULL) || ($params['password'] == NULL)){
//            $err['result'] = false;
//            $err['data'] = "Invalid Request";
//            returnJson($err);
//        }

        if (!isset($params['user_id']) || !isset($params['password']) || ($params['user_id'] == NULL) || ($params['password'] == NULL)) {
            $err['result'] = false;
            $err['data'] = "Invalid Request";
            returnJson($err);
        }

        $user_id = $params['user_id'];
        $password = $params['password'];
        //$device_token = $params['device_token'];

//        $curl_handle = curl_init();
//        curl_setopt($curl_handle, CURLOPT_URL, 'http://10.0.11.33/payo_app/users/update_synapse_info');
//
//        if(!isset($params['user_id']) || !isset($params['password']) || ($params['user_id'] == NULL) ||  ($params['password'] == NULL)){
//            $err['result'] = false;
//            $err['data'] = "Invalid Request";
//            returnJson($err);
//        }
//
//        $user_id = $params['user_id'];
//        $password = $params['password'];
//        $device_token = $params['device_token'];
//
//        $curl_handle = curl_init();
//        curl_setopt($curl_handle, CURLOPT_URL, '');
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

        $result['basic_info'] = array(
            'hrms_id' => '12',
            'dept_id' => '12',
            'dept_type_id' => '123',
            'dept_type_name' => 'BR',
            'branch_id' => '12',
            'district_id' => '1234',
            'state_id' => '1234',
            'zone_id' => '1234',
            'full_name' => 'mukesh kurmi',
            'supervisor_id' => '009',
            'designation_id' => '4',
            'designation_name' => $params['designation_name'],
            'mobile' => '9975772432',
            'email_id' => 'mukesh.kurmi@wwindia.com',
        );
        $result['employee_list'][] = array(
            'id' => '12',
            'full_name' => 'mukesh kurmi',
        );
        $result['employee_list'][] = array(
            'id' => '13',
            'full_name' => 'anup',
        );
        $result['employee_list'][] = array(
            'id' => '15',
            'full_name' => 'anup',
        );
        $result['branch_list'][] = array(
            'id' => '12',
            'full_name' => 'branch1',
        );
        $result['branch_list'][] = array(
            'id' => '13',
            'full_name' => 'branch2',
        );
        $result['zone_list'][] = array(
            'id' => '12',
            'full_name' => 'zone1',
        );
        $result['zone_list'][] = array(
            'id' => '13',
            'full_name' => 'zone2',
        );
//        returnJson($data);

//        if(isset($result['status']) && $result['status'] == 'success') {
//
//            $where_year_Array = array();
//            $data = array('device_token' => $device_token,
//                'employee_id' => $result
//            );
//            $return = $this->Login_model->insert_login_log($data);

        if (isset($result['basic_info']['designation_name']) && $result['basic_info']['designation_name'] == 'BM') {
            if (isset($result['basic_info']['branch_id']) && $result['basic_info']['branch_id'] != '') {
                $branch_id = $result['basic_info']['branch_id'];
                //for generated lead
                $where_month_Array = array('branch_id' => $branch_id,
                    'created_by !=' => 0,
                    'MONTH(created_on)' => date('m'));
                $leads['generated_leads'] = $this->Lead->get_generated_lead_bm_zm($where_month_Array);
                $generated_key_value = array();
                foreach ($leads['generated_leads'] as $k => $v) {
                    $generated_key_value[$v['created_by']] = $v['total'];
                }
                foreach ($result['employee_list'] as $key => $val) {
                    if (!array_key_exists($val['id'], $generated_key_value)) {
                        $push_generated = array('created_by' => $val['id'],
                            'created_by_name' => $val['full_name'],
                            'total' => 0);
                        array_push($leads['generated_leads'], $push_generated);
                    }
                }
                //for converted lead
                $where_month_Array = array('branch_id' => $branch_id,
                    'MONTH(created_on)' => date('m'),
                    'created_by !=' => 0,
                    'status' => 'converted');

                $leads['converted_leads'] = $this->Lead->get_converted_lead_bm_zm($where_month_Array);
                $converted_key_value = array();
                foreach ($leads['converted_leads'] as $k => $v) {
                    $converted_key_value[$v['created_by']] = $v['total'];
                }

                foreach ($result['employee_list'] as $key => $val) {
                    if (!array_key_exists($val['id'], $converted_key_value)) {
                        $push_converted = array('created_by' => $val['id'],
                            'created_by_name' => $result['employee_list'][$key]['full_name'],
                            'total' => 0);
                        array_push($leads['converted_leads'], $push_converted);
                    }
                }
                //for assigned lead
                $where_assigned_Array = array('branch_id' => $branch_id,
                    'created_by !=' => 0,
                    'YEAR(created_on)' => date('Y'));
            }
            $leads['assigned_leads'] = $this->Lead->get_assigned_leads($where_assigned_Array);
            $action = 'count';
            $select = array();
            $table = Tbl_Leads;
            $where = array(Tbl_LeadAssign . 'lead_id', NULL);
            $join[] = array('table' => Tbl_LeadAssign, 'on_condition' => Tbl_LeadAssign . '.lead_id = ' . Tbl_Leads . '.id', 'type' => '');
            $leads['un_assigned_leads'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by = array(), $order_by = array());
        }
        if (isset($result['basic_info']['designation_name']) && $result['basic_info']['designation_name'] == 'EM') {
            if (isset($result['basic_info']['hrms_id']) && $result['basic_info']['hrms_id'] != '') {
                $created_id = $result['basic_info']['hrms_id'];

                //Parameters buiding for sending to list function.
                $action = 'count';
                $select = array();
                $join = array();
                $group_by = array();

                //For Generated Leads Count
                $table = Tbl_Leads;

                //Month till date
                $where = array(Tbl_Leads . '.created_by' => $created_id, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'));
                $leads['generated_mtd'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());

                //Year till date
                $where = array(Tbl_Leads . '.created_by' => $created_id, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'));
                $leads['generated_ytd'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());

                //For converted leads Count
                $table = Tbl_LeadAssign;

                //Month till date
                $where = array(Tbl_LeadAssign . '.employee_id' => $created_id, Tbl_LeadAssign . '.status' => 'Converted', Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'));
                $leads['converted_mtd'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());


                //Year till date
                $where = array(Tbl_LeadAssign . '.employee_id' => $created_id, Tbl_LeadAssign . '.status' => 'Converted', Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
                $leads['converted_ytd'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());

                //For assigned leads Count
                $table = Tbl_LeadAssign;

                //Year till date
                $where = array(Tbl_LeadAssign . '.employee_id' => $created_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'));
                $leads['assigned_leads'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());
            }

        }
        if (isset($result['basic_info']['designation_name']) && $result['basic_info']['designation_name'] == 'ZM') {
            if (isset($result['basic_info']['zone_id']) && $result['basic_info']['zone_id'] != '') {
                $zone_id = $result['basic_info']['zone_id'];

                //for generated lead
                $where_month_Array = array('zone_id' => $zone_id,
                    'created_by !=' => 0,
                    'MONTH(created_on)' => date('m'));

                $leads['generated_leads'] = $this->Lead->get_generated_lead_bm_zm($where_month_Array);
                $generated_key_value = array();
                foreach ($leads['generated_leads'] as $k => $v) {
                    $generated_key_value[$v['branch_id']] = $v['total'];
                }

                foreach ($result['branch_list'] as $key => $val) {
                    if (!array_key_exists($val['id'], $generated_key_value)) {
                        $push_generated = array('branch_id' => $val['id'],

                            'total' => 0);
                        array_push($leads['generated_leads'], $push_generated);
                    }
                }
                //for converted lead
                $where_month_Array = array('zone_id' => $zone_id,
                    'MONTH(created_on)' => date('m'),
                    'created_by !=' => 0,
                    'status' => 'converted');

                $leads['converted_leads'] = $this->Lead->get_converted_lead_bm_zm($where_month_Array);
                $converted_key_value = array();
                foreach ($leads['converted_leads'] as $k => $v) {
                    $converted_key_value[$v['branch_id']] = $v['total'];
                }

                foreach ($result['branch_list'] as $key => $val) {
                    if (!array_key_exists($val['id'], $converted_key_value)) {
                        $push_converted = array('branch_id' => $val['id'],
                            'total' => 0);
                        array_push($leads['converted_leads'], $push_converted);
                    }
                }

                //for assigned lead
                $where_assigned_Array = array('zone_id' => $zone_id,
                    'created_by !=' => 0,
                    'YEAR(created_on)' => date('Y'));
            }
//                $leads['assigned_leads'] = $this->Lead->get_assigned_leads($where_assigned_Array);
        }


        $result = array(
            "result" => 'True',
            "data" => ['count' => $leads, 'basic_info' => $result['basic_info']]
        );
        returnJson($result);
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

    public function leads_performance_post()
    {
        $params = $this->input->post();
        if (isset($params['employee_id']) && $params['employee_id'] != '') {
            $join = array();
            $created_by = $params['employee_id'];
            $action = 'count';
            $table = Tbl_Leads;
            $join[] = array('table' => Tbl_LeadAssign, 'on_condition' => Tbl_Leads . '.id = ' . Tbl_LeadAssign . '.lead_id', 'type' => '');
            $select = array();

            //Walk-in
            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'),
                Tbl_Leads . '.lead_source' => 'Walk-in');
            $result['lead_assigned_walkin'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'),
                Tbl_Leads . '.lead_source' => 'Walk-in');
            $result['month_lead_assigned_walkin'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'),
                Tbl_Leads . '.lead_source' => 'Walk-in', Tbl_LeadAssign . '.status' => 'Converted');
            $result['lead_converted_walkin'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'),
                Tbl_Leads . '.lead_source' => 'Walk-in', Tbl_LeadAssign . '.status' => 'Converted');
            $result['month_lead_converted_walkin'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            //Third Party
            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Tie Ups');
            $result['lead_assigned_tie_ups'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Tie Ups');
            $result['month_lead_assigned_tie_ups'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Tie Ups',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['lead_converted_tie_ups'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Tie Ups',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['month_lead_converted_tie_ups'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            //Bank Website
            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Enquiry');
            $result['lead_assigned_enquiry'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Enquiry');
            $result['month_lead_assigned_enquiry'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Enquiry',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['lead_converted_enquiry'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Enquiry',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['month_lead_converted_enquiry'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            //Analytics
            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Analytics');
            $result['lead_assigned_analytics'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Analytics');
            $result['month_lead_assigned_analytics'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Analytics',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['lead_converted_analytics'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Analytics',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['month_lead_converted_analytics'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');


            $data = array('result' => 'true',
                'data' => $result);
            returnJson($data);

        }
        if (isset($params['branch_id']) && $params['branch_id'] != '') {
            $join = array();
            $branch_id = $params['branch_id'];
            $action = 'count';
            $table = Tbl_Leads;
            $join[] = array('table' => Tbl_LeadAssign, 'on_condition' => Tbl_Leads . '.id = ' . Tbl_LeadAssign . '.lead_id', 'type' => '');
            $select = array();

            //Walk-in
            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Walk-in');
            $result['lead_assigned_walkin'] = $this->master->get_leads($action, $table, $select, $where, '', '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Walk-in');
            $result['month_lead_assigned_walkin'] = $this->master->get_leads($action, $table, $select, $where, '', '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Walk-in',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['lead_converted_walkin'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Walk-in',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['month_lead_converted_walkin'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            //Third Party
            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Tie Ups');
            $result['lead_assigned_tie_ups'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Tie Ups');
            $result['month_lead_assigned_tie_ups'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Tie Ups',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['lead_converted_tie_ups'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Tie Ups',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['month_lead_converted_tie_ups'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            //Bank Website
            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Enquiry');
            $result['lead_assigned_enquiry'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Enquiry');
            $result['month_lead_assigned_enquiry'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Enquiry',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['lead_converted_enquiry'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Enquiry',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['month_lead_converted_enquiry'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            //Analytics
            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Analytics');
            $result['lead_assigned_analytics'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Analytics');
            $result['month_lead_assigned_analytics'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => 'Analytics',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['lead_converted_analytics'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => 'Analytics',
                Tbl_LeadAssign . '.status' => 'Converted');
            $result['month_lead_converted_analytics'] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');


            $data = array('result' => 'true',
                'data' => $result);
            returnJson($data);

        }
        $data = array('result' => 'false',
            'data' => 'parameter missing');
        returnJson($data);
    }

    public function add_lead_post()
    {
        $params = $this->input->post();
        $error = array();
        $validations = array('customer_name' => 'Customer Name', 'contact_no' => 'Phone No',
            'product_category_id' => 'Product Category', 'product_id' => 'Product', 'lead_ticket_range' => 'Range',
            'is_own_branch' => 'Own Branch / Other Branch', 'created_by' => 'Created By', 'created_by_name' => 'Created By Name',
            'state_id' => 'State', 'district_id' => 'District',
            'zone_id' => 'Zone', 'branch_id' => 'Branch', 'department_name' => 'Department Name',
            'department_id' => 'Department Id', 'created_by_state_id' => 'Created By State',
            'created_by_district_id' => 'Created By District',
            'created_by_zone_id' => 'Created By Zone', 'created_by_branch_id' => 'Created By Branch',
            'remark' => 'Remark');
        $phone_extra = '';
        $cust_name_extra = '';
        foreach ($params as $k => $value) {
            if (array_key_exists($k, $validations)) {

                if ($k == 'phone_no') {
                    $phone_extra = '|max_length[10]|min_length[10]|numeric';
                }
                if ($k == 'customer_name') {
                    $cust_name_extra = '|callback_alphaNumeric';
                }
                $this->form_validation->set_rules($k, '', 'required' . $phone_extra . $cust_name_extra);
                if ($this->form_validation->run() === FALSE) {
                    $error[] = form_error($k);
                } else {
                    $lead_data[$k] = $value;
                }
                unset($validations[$k]);
            }
        }
        if (!empty($validations)) {
            foreach ($validations as $k => $v) {
                $error[] = $v . ' field is required.';
            }
        }
        if (!empty($error)) {
            $result = array('result' => 'false',
                'data' => $error);
            returnJson($result);
        }
        $lead_data['lead_name'] = $this->input->post('customer_name');
        $assign_to = $this->Lead->get_product_assign_to($lead_data['product_id']);
        $whereArray = array('product_id' => $lead_data['product_id'], 'branch_id' => $lead_data['branch_id']);
        $routed_id = $this->Lead->check_mapping($whereArray);
        if (!is_array($routed_id)) {
            $lead_data['reroute_from_branch_id'] = $lead_data['branch_id'];
            $lead_data['branch_id'] = $routed_id;
        }
        $lead_id = $this->Lead->add_leads($lead_data);
        if ($assign_to == 'self') {
            $lead_assign['lead_id'] = $lead_id;
            $lead_assign['employee_id'] = $params['created_by'];
            $lead_assign['employee_name'] = $params['created_by_name'];
            $lead_assign['branch_id'] = $params['branch_id'];
            $lead_assign['district_id'] = $params['district_id'];
            $lead_assign['state_id'] = $params['state_id'];
            $lead_assign['zone_id'] = $params['zone_id'];
            $lead_assign['created_by'] = $params['created_by'];
            $lead_assign['created_by_name'] = $params['created_by_name'];
            $this->Lead->insert_assign($lead_assign);
        }

        //start send sms to customer

        $customer_name = $lead_data['lead_name'];
        $customer_mobile = $lead_data['contact_no'];
        send_sms($customer_name, $customer_mobile);

        //end send sms to customer

        $result = array('result' => 'true',
            'data' => 'Lead added Successfully.');
        returnJson($result);

    }

    ##################################
    /*Private Functions*/
    ##################################
    /*
    * Validation for alphabetical letters
    * @param array $pwd,$dataArray
    * @return String
    */
    public function alphaNumeric($str)
    {
        if (!preg_match('/^[a-zA-Z0-9\s]+$/i', $str)) {
            $this->form_validation->set_message('alphaNumeric', 'Please enter only alpha numeric characters.');
            return FALSE;
        } else {
            return TRUE;
        }
    }


    public function test_get()
    {
        $res = $this->input->get();
        $zone_id = $res['branch_id'];

        //for generated lead
        $where_month_Array = array('branch_id' => $zone_id,
            'MONTH(created_on)' => date('m'));
        $result1['generated_leads'] = $this->Lead->get_generated_lead_bm_zm($where_month_Array);

        pe($result1);

    }

    public function tickers_get()
    {

        $where = array('is_deleted' => 0);
        $tickers = $this->ticker->view('title,description_text', $where, Tbl_Ticker, '', '', 2);
        if (!empty($tickers)) {
            $result = array('result' => 'True',
                'data' => $tickers);
            returnJson($result);
        }
        $result = array('result' => 'False',
            'data' => 'No data found');
        returnJson($result);
    }

    public function faq_get()
    {

        $where = array('is_deleted' => 0);
        $faqs = $this->faq->view('question,answer', $where, Tbl_Faq);
        if (!empty($faqs)) {
            $result = array('result' => 'True',
                'data' => $faqs);
            returnJson($result);
        }
        $result = array('result' => 'False',
            'data' => 'No data found');
        returnJson($result);
    }

    public function view_lead_details_post()
    {
        $action = 'list';
        $table = Tbl_Leads;
        $select = '';
    }

    public function lead_list_post()
    {
        $action = 'list';
        $table = Tbl_Leads;
        $select = '';
    }

    public function Lead_get()
    {

        $data['zone_list'][] = array(
            'id' => '12',
            'name' => 'zone1',
        );
        $data['zone_list'][] = array(
            'id' => '13',
            'name' => 'zone2',
        );
        $data['state_list'][] = array(
            'id' => '12',
            'zone_id' => '1',
            'name' => 'maharashtra',
        );
        $data['state_list'][] = array(
            'id' => '13',
            'zone_id' => '1',
            'name' => 'UP',
        );
        $data['district_list'][] = array(
            'id' => '12',
            'state_id' => '12',
            'name' => 'mumbai',
        );
        $data['district_list'][] = array(
            'id' => '13',
            'state_id' => '13',
            'name' => 'Deoria',
        );
        $data['branch_list'][] = array(
            'id' => '12',
            'district_id' => '13',
            'name' => 'BKC',
        );
        $data['branch_list'][] = array(
            'id' => '13',
            'district_id' => '12',
            'name' => 'salempur branch',
        );
        returnJson($data);

        if (isset($result['status']) && $result['status'] == 'success') {

            $table = "db_app_login_logs";
            $data = array('device_token' => $device_token,
                'employee_id' => $result
            );
            $this->app->insert_login_log($table, $data);
        } else {
            $error = array(
                "result" => false,
                "data" => "Invalid username or password."
            );
            returnJson($error);
        }
    }


    public function category_products_get()
    {
        $final = array();
        $table = Tbl_Category;
        $join = array();
        $select = array(Tbl_Category . '.title', Tbl_Category . '.id');
        $result = $this->Lead->lists($table, $select, array(), $join, array(), array());
        $products = array();
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $table = Tbl_Products;
                $join = array();
                $where = array(Tbl_Products . '.category_id' => $value['id']);
                $select = array(Tbl_Products . '.title', Tbl_Products . '.id');
                $products = $this->Lead->lists($table, $select, $where, $join, array(), array());
                $data['category_id'] = $value['id'];
                $data['category_title'] = $value['title'];
                $data['product'] = $products;
                if (!empty($products)) {
                    $final[] = $data;
                }
            }
            $res = array('result' => 'True',
                'data' => $final);
            returnJson($res);
        }
        $error = array(
            "result" => false,
            "data" => "No data found."
        );
        returnJson($error);


    }

    public function leads_status_post()
    {
        $params = $this->input->post();
        $status = $this->config->item('lead_status');
        $join[] = array('table' => Tbl_Leads, 'on_condition' => Tbl_Leads . '.id = ' . Tbl_LeadAssign . '.lead_id', 'type' => '');
        if ((isset($params) && !empty($params) && isset($params['type']) && !empty($params['type']))
            && ((isset($params['branch_id']) && !empty($params['branch_id'])) ||
                (isset($params['employee_id']) && !empty($params['employee_id'])))) {
            if($params['type'] == 'BM' || $params['type'] == 'EM' &&
                (isset($params['employee_id']) || !empty($params['employee_id']))) {
                $table = Tbl_LeadAssign;
                $action = 'count';
                $employee_id = $params['employee_id'];
                if (!empty($status)) {
                    foreach ($status as $key => $value) {
                        $whereArray = array(Tbl_Leads . '.created_by' => $employee_id, 'status' => $key, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'));
                        $result[$key]['Year'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');

                        $whereArray = array(Tbl_Leads . '.created_by' => $employee_id, 'status' => $key, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'));
                        $result[$key]['Month'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');
                    }
                }
            }
            if($params['type'] == 'ZM' && (isset($params['branch_id']) && !empty($params['branch_id']))) {
                $table = Tbl_LeadAssign;
                $action = 'count';
                $branch_id = $params['branch_id'];

                if(!empty($status)){
                    foreach ($status as $key => $value) {
                        $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => $key, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'));
                        $result[$key]['YEAR'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');

                        $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => $key, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'));
                        $result[$key]['MONTH'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');
                    }
                }
            }
            $res = array('result' => 'True',
                'data' => $result);
            returnJson($res);
        }
        $error = array(
            "result" => false,
            "data" => "Missing Parameters."
        );
        returnJson($error);
    }

}