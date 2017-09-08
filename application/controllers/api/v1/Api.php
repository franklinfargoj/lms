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
        $this->load->model('Master_model');
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

        if (!isset($params['user_id']) || !isset($params['password']) || !isset($params['device_token']) ||
            !isset($params['device_type']) || ($params['device_type'] == NULL) ||
            ($params['user_id'] == NULL) || ($params['password'] == NULL || ($params['device_token'] == NULL))) {
            $err['result'] = false;
            $err['data'] = "Invalid Request";
            returnJson($err);
        }

        $user_id = $params['user_id'];
        $password = $params['password'];
        $device_token = $params['device_token'];
        $device_type = $params['device_type'];

        $result = get_details($params['designation_name']);

//        returnJson($result);

        if (isset($result['status']) && $result['status'] == 'success') {

            $data = array('device_token' => $device_token,
                'employee_id' => $result['basic_info']['hrms_id'],
                'device_type' => $device_type
            );
            $this->Login_model->insert_login_log($data);

            if (isset($result['basic_info']['designation_name']) && $result['basic_info']['designation_name'] == 'BM') {
                if (isset($result['basic_info']['branch_id']) && $result['basic_info']['branch_id'] != '') {
                    $branch_id = $result['basic_info']['branch_id'];
                    $type = 'BM';
                    $final = $this->count($type, $branch_id, $result);

                    $leads['generated_converted'] = $final;
                    //for assigned lead
                    $where_assigned_Array = array('branch_id' => $branch_id,
                        'YEAR(created_on)' => date('Y'));
                }
                $leads['assigned_leads'] = $this->Lead->get_assigned_leads($where_assigned_Array);
                $action = 'count';
                $select = array();
                $table = Tbl_Leads;
                $where = array(Tbl_Leads . '.branch_id' => $result['basic_info']['branch_id'],Tbl_LeadAssign . 'lead_id', NULL);
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
                    $type = 'ZM';
                    $final = $this->count($type, $zone_id, $result);
                    $leads['generated_converted'] = $final;
                }
            }
            if (isset($result['basic_info']['designation_name']) && $result['basic_info']['designation_name'] == 'GM') {
                $type = 'GM';
                $final = $this->count($type, '', $result);
                $leads['generated_converted'] = $final;
            }


            $result = array(
                "result" => True,
                "data" => ['count' => $leads, 'basic_info' => $result['basic_info']]
            );
            returnJson($result);
        } else {
            $error = array(
                "result" => false,
                "data" => "Invalid username or password."
            );
            returnJson($error);
        }
    }

    public function leads_performance_post()
    {
        $params = $this->input->post();
        $type = $params['type'];
        $source = $this->config->item('lead_source');
        if (isset($params['id']) && !empty($params['id'])) {
            switch ($type) {
                case 'EM':
                case 'BM':
                    $join = array();
                    $created_by = $params['id'];
                    $action = 'count';
                    $table = Tbl_Leads;
                    $join[] = array('table' => Tbl_LeadAssign, 'on_condition' => Tbl_Leads . '.id = ' . Tbl_LeadAssign . '.lead_id', 'type' => '');
                    $select = array();
                    $i = 0;
                    foreach ($source as $key => $lead_source) {

                        $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'),
                            Tbl_Leads . '.lead_source' => $lead_source);
                        $result[$i]['year_lead_assigned'] = $this->Lead->get_leads($action, $table, $select, $where, $join, '', '');

                        $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'),
                            Tbl_Leads . '.lead_source' => $lead_source);
                        $result[$i]['month_lead_assigned'] = $this->Lead->get_leads($action, $table, $select, $where, $join, '', '');

                        $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'),
                            Tbl_Leads . '.lead_source' => $lead_source, Tbl_LeadAssign . '.status' => 'Converted');
                        $result[$i]['year_lead_converted'] = $this->Lead->get_leads($action, $table, $select, $where, $join, '', '');

                        $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'),
                            Tbl_Leads . '.lead_source' => $lead_source, Tbl_LeadAssign . '.status' => 'Converted');
                        $result[$i]['month_lead_converted'] = $this->Lead->get_leads($action, $table, $select, $where, $join, '', '');
                        $result[$i]['lead_source'] = $lead_source;
                        $i++;
                    }
                    $data = array('result' => True,
                        'data' => $result);
                    returnJson($data);
                    break;
                case 'ZM':
                    $join = array();
                    $branch_id = $params['id'];
                    $action = 'count';
                    $table = Tbl_Leads;
                    $join[] = array('table' => Tbl_LeadAssign, 'on_condition' => Tbl_Leads . '.id = ' . Tbl_LeadAssign . '.lead_id', 'type' => '');
                    $select = array();
                    $i = 0;
                    foreach ($source as $key => $lead_source) {
                        $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => $lead_source);
                        $result[$i]['year_lead_assigned_' . $key] = $this->Lead->get_leads($action, $table, $select, $where, $join, '', '');
                        $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => $lead_source);
                        $result[$i]['month_lead_assigned_' . $key] = $this->Lead->get_leads($action, $table, $select, $where, $join, '', '');

                        $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => $lead_source,
                            Tbl_LeadAssign . '.status' => 'Converted');
                        $result[$i]['year_lead_converted_' . $key] = $this->Lead->get_leads($action, $table, $select, $where, $join, '', '');

                        $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => $lead_source,
                            Tbl_LeadAssign . '.status' => 'Converted');
                        $result[$i]['month_lead_converted_' . $key] = $this->Lead->get_leads($action, $table, $select, $where, $join, '', '');
                        $result[$i]['lead_source'] = $lead_source;
                        $i++;
                    }

                    $data = array('result' => True,
                        'data' => $result);
                    returnJson($data);
            }
        }
        $data = array('result' => False,
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
            'latitude' => 'Latitude', 'longitude' => 'Longitude',
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
            $result = array('result' => False,
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

        //send sms
        /*$message = 'Thanks for showing interest with Dena Bank. We will contact you shortly';
        send_sms($this->input->post('contact_no'),$message);*/

        //Push notification
        //sendNotificationSingleClient($device_id,$device_type,$message,$title=NULL);

        $result = array('result' => True,
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
            $result = array('result' => True,
                'data' => $tickers);
            returnJson($result);
        }
        $result = array('result' => False,
            'data' => 'No data found');
        returnJson($result);
    }

    public function faq_get()
    {

        $where = array('is_deleted' => 0);
        $faqs = $this->faq->view('question,answer', $where, Tbl_Faq);
        if (!empty($faqs)) {
            $result = array('result' => True,
                'data' => $faqs);
            returnJson($result);
        }
        $result = array('result' => False,
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
            $res = array('result' => True,
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
            && ((isset($params['id']) && !empty($params['id'])))) {
            switch ($params['type']) {
                case 'BM':
                case 'EM':
                    $table = Tbl_LeadAssign;
                    $action = 'count';
                    $employee_id = $params['id'];
                    if (!empty($status)) {
                        $i = 0;
                        foreach ($status as $key => $value) {
                            $whereArray = array(Tbl_Leads . '.created_by' => $employee_id, 'status' => $key, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'));
                            $result[$i]['Year'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');

                            $whereArray = array(Tbl_Leads . '.created_by' => $employee_id, 'status' => $key, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'));
                            $result[$i]['Month'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');
                            $result[$i]['status'] = $value;
                            $i++;
                        }
                    }
                    $res = array('result' => True,
                        'data' => $result);
                    returnJson($res);
                    break;
                case 'ZM':
                    $table = Tbl_LeadAssign;
                    $action = 'count';
                    $branch_id = $params['id'];

                    if (!empty($status)) {
                        $i = 0;
                        foreach ($status as $key => $value) {
                            $whereArray = array(Tbl_Leads . '.branch_id' => $branch_id, 'status' => $key, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'));
                            $result[$i]['YEAR'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');

                            $whereArray = array(Tbl_Leads . '.branch_id' => $branch_id, 'status' => $key, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'));
                            $result[$i]['MONTH'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');
                            $i++;
                        }
                    }
                    $res = array('result' => True,
                        'data' => $result);
                    returnJson($res);
                    break;
            }
        }
        $error = array(
            "result" => False,
            "data" => "Missing Parameters."
        );
        returnJson($error);
    }

    public function masters_get()
    {
        $lead_status['branch_details'] = dummy_branch_details();

        $final = array();
        $table = Tbl_Category;
        $join = array();
        $select = array(Tbl_Category . '.title', Tbl_Category . '.id');
        $result = $this->Lead->lists($table, $select, array(), $join, array(), array());
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
            $lead_status['category_products'] = $final;
            $lead_status['status'] = $this->config->item('lead_status');
            $lead_status['lead_source'] = $this->config->item('lead_source');
        }
        if (!empty($lead_status)) {
            $res = array('result' => True,
                'data' => $lead_status);
            returnJson($res);
        }
        $res = array('result' => False,
            'data' => 'No data found');
        returnJson($res);
    }

    public function leads_assigned_list_post()
    {
        $params = $this->input->post();
        if (!empty($params) && isset($params['hrms_id']) && !empty($params['hrms_id']) &&
            isset($params['type']) && !empty($params['type'])) {

            if ($params['type'] == 'EM') {
                $login_user['hrms_id'] = $params['hrms_id'];
                $arrData = $this->em_view($login_user);
                $res = array('result' => True,
                    'data' => $arrData);
                returnJson($res);
            }
        }
        $res = array('result' => False,
            'data' => 'Wrong Parameters');
        returnJson($res);
    }

    private function em_view($login_user)
    {

        //Parameters buiding for sending to list function.
        $action = 'list';
        $table = Tbl_Leads . ' as l';
        $join = array();
        $join[] = array('table' => Tbl_Products . ' as p', 'on_condition' => 'l.product_id = p.id AND l.product_category_id = p.category_id', 'type' => '');

        $select = array('l.id', 'l.customer_name', 'l.lead_identification', 'l.created_on', 'l.lead_source', 'p.title', 'la.status'/*,'p1.title as interested_product_title'*/, 'r.remind_on');
        $where = array('la.employee_id' => $login_user['hrms_id'], 'la.is_deleted' => 0, 'YEAR(la.created_on)' => date('Y'));
        $join[] = array('table' => Tbl_LeadAssign . ' as la', 'on_condition' => 'la.lead_id = l.id', 'type' => '');

        $join[] = array('table' => Tbl_Reminder . ' as r', 'on_condition' => 'la.lead_id = r.lead_id AND r.is_cancelled = "No"', 'type' => 'left');
        $arrData['leads'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by = array(), $order_by = array());
        return $arrData;
    }

    public function product_details_post()
    {
        $params = $this->input->post();
        if (!empty($params) && isset($params['product_id']) && !empty($params['product_id'])) {
            $table = Tbl_ProductDetails;
            $prod_id = $params['product_id'];
            $select = array(Tbl_ProductDetails . '.title', Tbl_ProductDetails . '.description_text');
            $where = array('product_id' => $prod_id);
            $prod_details = $this->Master_model->view($select, $where, $table);
            $res = array('result' => True,
                'data' => $prod_details);
            returnJson($res);
        } else {
            $res = array('result' => False,
                'data' => 'Wrong Parameters');
            returnJson($res);
        }
    }

    private function count($type, $ids, $result)
    {
        switch ($type) {
            case 'BM':
                $where_month_Array = array('branch_id' => $ids,
                    'MONTH(created_on)' => date('m'));
                $generated['generated_leads'] = $this->Lead->get_generated_lead_bm_zm($where_month_Array);
                $generated_key_value = array();
                $final = array();
                foreach ($generated['generated_leads'] as $k => $v) {
                    $generated_key_value[$v['created_by']] = $v['total'];
                }
                foreach ($result['employee_list'] as $key => $val) {
                    if (!array_key_exists($val['id'], $generated_key_value)) {
                        $push_generated = array(
                            'created_by' => $val['id'],
                            'created_by_name' => $val['full_name'],
                            'total_generated' => 0);
                    } else {
                        $push_generated = array(
                            'created_by' => $val['id'],
                            'created_by_name' => $val['full_name'],
                            'total_generated' => $generated_key_value[$val['id']]);
                    }
                    $final[$val['id']] = $push_generated;
                }
                foreach ($final as $id => $value) {

                    $where_month_Array = array('employee_id' => $value['created_by'],
                        'MONTH(created_on)' => date('m'),
                        'status' => 'converted');
                    $converted = $this->Lead->get_converted_lead_bm_zm($where_month_Array);
                    if (empty($converted)) {
                        $converted = 0;
                    }
                    $final[$value['created_by']]['total_converted'] = $converted;
                }
                return $final;
                break;

            case 'ZM':
                $where_month_Array = array('zone_id' => $ids,
                    'MONTH(created_on)' => date('m'));

                $generated['generated_leads'] = $this->Lead->get_generated_lead_bm_zm($where_month_Array);
                $generated_key_value = array();
                $final = array();
                foreach ($generated['generated_leads'] as $k => $v) {
                    $generated_key_value[$v['branch_id']] = $v['total'];
                }
                foreach ($result['branch_list'] as $key => $val) {
                    if (!array_key_exists($val['id'], $generated_key_value)) {
                        $push_generated = array(
                            'created_by_branch_id' => $val['id'],
                            'created_by_branch_name' => $val['full_name'],
                            'total_generated' => 0);
                    } else {
                        $push_generated = array(
                            'created_by_branch_id' => $val['id'],
                            'created_by_branch_name' => $val['full_name'],
                            'total_generated' => $generated_key_value[$val['id']]);
                    }
                    $final[$val['id']] = $push_generated;
                }
                //for converted
                foreach ($final as $id => $value) {

                    $where_month_Array = array('branch_id' => $value['created_by_branch_id'],
                        'MONTH(created_on)' => date('m'),
                        'status' => 'converted');
                    $converted = $this->Lead->get_converted_lead_bm_zm($where_month_Array);
                    if (empty($converted)) {
                        $converted = 0;
                    }
                    $final[$value['created_by_branch_id']]['total_converted'] = $converted;
                }
                return $final;
                break;

            case 'GM':
                $where_generated_Array = array('zone_id !=' => NULL,
                    'MONTH(created_on)' => date('m'));
                $generated['generated_leads'] = $this->Lead->get_generated_lead_bm_zm($where_generated_Array);
                $generated_key_value = array();
                $final = array();
                foreach ($generated['generated_leads'] as $k => $v) {
                    $generated_key_value[$v['zone_id']] = $v['total'];
                }
                foreach ($result['zone_list'] as $key => $val) {
                    if (!array_key_exists($val['id'], $generated_key_value)) {
                        $push_generated = array(
                            'created_by_zone_id' => $val['id'],
                            'created_by_zone_name' => $val['full_name'],
                            'total_generated' => 0);
                    } else {
                        $push_generated = array(
                            'created_by_zone_id' => $val['id'],
                            'created_by_zone_name' => $val['full_name'],
                            'total_generated' => $generated_key_value[$val['id']]);
                    }
                    $final[$val['id']] = $push_generated;
                }
                //for converted
                foreach ($final as $id => $value) {

                    $where_month_Array = array('zone_id' => $value['created_by_zone_id'],
                        'MONTH(created_on)' => date('m'),
                        'status' => 'converted');
                    $converted = $this->Lead->get_converted_lead_bm_zm($where_month_Array);
                    if (empty($converted)) {
                        $converted = 0;
                    }
                    $final[$value['created_by_zone_id']]['total_converted'] = $converted;
                }
                return $final;
                break;

        }
    }

    /**
     * unassigned_leads
     * loads the unassigned leads count filtered by lead source
     * @autor Gourav Thatoi
     * @accss public
     * @return array
     */
    public function unassigned_leads_post()
    {
        $params = $this->input->post();
        if (!empty($params) && isset($params['branch_id']) && !empty($params['branch_id'])) {
            $select = array('db_leads.lead_source,COUNT(db_leads.lead_source) as total');
            $table = Tbl_Leads;
            $join = array('db_lead_assign', 'db_lead_assign.lead_id = db_leads.id ', 'left');
            $group_by = array('db_leads.lead_source');
            $where = array(Tbl_Leads . '.branch_id' => $params['branch_id'], Tbl_LeadAssign . '.lead_id' => NULL, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'));
            $arrData['unassigned_leads_count'] = $this->Lead->unassigned_status_count($select, $table, $join, $where, $group_by);
            $response = array();
            $keys = array('Walk-in' => "0", 'Analytics' => "0", 'Tie Ups' => "0", 'Enquiry' => "0");
            foreach ($arrData['unassigned_leads_count'] as $k => $v) {
                $keys[$v['lead_source']] = $v['total'];

            }
            //echo "<pre>";print_r($keys);die;
            //$arrData['unassigned_leads_count'] = $keys;
            $res = array('result' => True,
                'data' => $keys);
            returnJson($res);
        } else {
            $res = array('result' => False,
                'data' => 'Invalid Request');
            returnJson($res);
        }

    }

    /**
     * unassigned_leads_list
     * loads the unassigned leads list filtered by lead source
     * @autor Gourav Thatoi
     * @accss public
     * @return array
     */
    public function unassigned_leads_list_post()
    {
        $params = $this->input->post();
        if (!empty($params) && isset($params['lead_source']) && !empty($params['lead_source'] && isset($params['branch_id']) && !empty($params['branch_id']))) {
            $lead_source = $params['lead_source'];
            $branch_id = $params['branch_id'];
            $unassigned_leads = $this->Lead->unassigned_leads_api($lead_source, $branch_id);
            $res = array('result' => True,
                'data' => $unassigned_leads);
            returnJson($res);
        } else {
            $res = array('result' => False,
                'data' => 'Invalid Request');
            returnJson($res);
        }

    }

    /**
     * unassigned_leads_list
     * loads the unassigned leads list filtered by lead source
     * @autor Gourav Thatoi
     * @accss public
     * @return array
     */
    public function assigned_leads_list_post()
    {
        $params = $this->input->post();
        if (!empty($params) && isset($params['type']) && !empty($params['type'] && isset($params['id']) && !empty($params['id']))) {
            $type = $params['type'];
            $id = $params['id'];
            $action = 'list';
            $table = Tbl_Leads . ' as l';
            $join = array();
            $join[] = array('table' => Tbl_Products . ' as p', 'on_condition' => 'l.product_id = p.id AND l.product_category_id = p.category_id', 'type' => '');

            $select = array('l.id', 'l.customer_name', 'l.lead_identification', 'l.created_on', 'l.lead_source', 'p.title', 'la.status'/*,'p1.title as interested_product_title'*/, 'r.remind_on');
            $where = array('la.is_deleted' => 0, 'la.is_updated' => 1, 'YEAR(la.created_on)' => date('Y'));
            if ($type == 'EM') {
                $where['la.employee_id'] = $id;
            }
            if ($type == 'BM') {
                $where['la.branch_id'] = $id;
            }

            $join[] = array('table' => Tbl_LeadAssign . ' as la', 'on_condition' => 'la.lead_id = l.id', 'type' => '');
            /*$join[] = array('table' => Tbl_Products.' as p1','on_condition' => 'l.interested_product_id = p1.id','type' => 'left');*/

            $join[] = array('table' => Tbl_Reminder . ' as r', 'on_condition' => 'la.lead_id = r.lead_id AND r.is_cancelled = "No"', 'type' => 'left');
            $arrData['leads'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by = array(), $order_by = array());
            $res = array('result' => True,
                'data' => $arrData['leads']);
            returnJson($res);
        } else {
            $res = array('result' => False,
                'data' => 'Invalid Request');
            returnJson($res);
        }
    }

    /**
     * details
     * Get Leads details based on type of lead (Generated,Converted,Assigned)
     * @author Ashok Jadhav
     * @access public
     * @param $type ,$till,$lead_id
     * @return array
     */
    public function lead_details_post()
    {
        $params = $this->input->post();
        if (!empty($params) && isset($params['lead_id']) && !empty($params['lead_id']) && isset($params['type']) && !empty($params['type'])) {
            $lead_id = $params['lead_id'];
            $type = $params['type'];
            $action = 'list';
            $table = Tbl_Leads . ' as l';
            $where = array('l.id' => $lead_id);
            $join = array();
            $join[] = array('table' => Tbl_Products . ' as p', 'on_condition' => 'l.product_id = p.id AND l.product_category_id = p.category_id', 'type' => '');
            $join[] = array('table' => Tbl_Category . ' as c', 'on_condition' => 'l.product_category_id = c.id', 'type' => '');

            if ($type == 'generated') {
                $select = array('l.id', 'l.customer_name', 'l.lead_identification', 'l.lead_source', 'l.contact_no', 'l.product_id', 'p.title AS product_title', 'c.title AS category_title', 'l.product_category_id', 'la.status');
                $join[] = array('table' => Tbl_LeadAssign . ' as la', 'on_condition' => 'la.lead_id = l.id', 'type' => 'left');
            }

            if ($type == 'converted') {
                $select = array('l.id', 'l.customer_name', 'l.lead_identification', 'l.lead_source', 'l.contact_no', 'l.product_id', 'p.title AS product_title', 'c.title AS category_title', 'l.product_category_id', 'la.status');
                $where['la.is_deleted'] = 0;
                $where['la.is_updated'] = 1;
                $join[] = array('table' => Tbl_LeadAssign . ' as la', 'on_condition' => 'la.lead_id = l.id', 'type' => '');
            }

            if ($type == 'assigned') {
                //SELECT COLUMNS
                $select = array('l.id', 'l.remark', 'l.customer_name', 'l.lead_identification', 'l.lead_source', 'l.contact_no', 'l.product_id', 'p.title AS product_title'/*,'l.interested_product_id','p1.title AS interested_product_title'*/, 'c.title AS category_title', 'l.product_category_id', 'la.status', 'la.employee_id', 'r.remind_on', 'r.reminder_text');

                $where['la.is_deleted'] = 0;
                $where['la.is_updated'] = 1;

                //JOIN CONDITIONS
                $join[] = array('table' => Tbl_LeadAssign . ' as la', 'on_condition' => 'la.lead_id = l.id', 'type' => '');
                $join[] = array('table' => Tbl_Reminder . ' as r', 'on_condition' => 'la.lead_id = r.lead_id AND r.is_cancelled = "No"', 'type' => 'left');
                /*$join[] = array('table' => Tbl_Products.' as p1','on_condition' => 'l.interested_product_id = p1.id','type' => 'left');*/
            }
            $arrData['leads'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by = array(), $order_by = array());
            $res = array('result' => True,
                'data' => $arrData['leads']);
            returnJson($res);
        } else {
            $res = array('result' => False,
                'data' => 'Invalid Request');
            returnJson($res);
        }
    }

    public function assign_to_post()
    {
        $params = $this->input->post();
        if (!empty($params) && isset($params['lead_id']) && !empty($params['lead_id']) &&
            isset($params['employee_id']) && !empty($params['employee_id']) &&
            isset($params['employee_name']) && !empty($params['employee_name']) &&
            isset($params['branch_id']) && !empty($params['branch_id']) &&
            isset($params['district_id']) && !empty($params['district_id']) &&
            isset($params['state_id']) && !empty($params['state_id']) &&
            isset($params['zone_id']) && !empty($params['zone_id']) &&
            isset($params['branch_manager_id']) && !empty($params['branch_manager_id']) &&
            isset($params['branch_manager_name']) && !empty($params['branch_manager_name'])) {
            $assign_data = array(
                'lead_id' => $params['lead_id'],
                'employee_id' => $params['employee_id'],
                'employee_name' => $params['employee_name'],
                'branch_id' => $params['branch_id'],
                'district_id' => $params['district_id'],
                'state_id' => $params['state_id'],
                'zone_id' => $params['zone_id'],
                'status' => 'NC',
                'created_by' => $params['branch_manager_id'],
                'created_by_name' => $params['branch_manager_name']
            );
            $assigned = $this->Lead->insert_assign($assign_data);
            if ($assigned) {
                $title = "New Lead Assigned";
                $description = "New Lead Assigned to you by " . $params['branch_manager_name'];
                $notification_to = $params['employee_id'];
                $priority = "Normal";
                notification_log($title, $description, $priority, $notification_to);
                $res = array('result' => True,
                    'data' => 'Lead Assigned Successfully');
                returnJson($res);
            }
        } else {
            $res = array('result' => False,
                'data' => 'Invalid Request');
            returnJson($res);
        }
    }

    /**
     * update_lead_status
     * Only for assigned lead list able to change lead status / Add Follow Up details
     * @author Ashok Jadhav
     * @access public
     * @param empty
     * @return array
     */
    public function update_lead_status_post()
    {
        $params = $this->input->post();
        if (!empty($params) && isset($params['lead_id']) && !empty($params['lead_id']) &&
            isset($params['employee_id']) && !empty($params['employee_id']) &&
            isset($params['status']) && !empty($params['status']) &&
            isset($params['lead_identification']) && !empty($params['lead_identification']) &&
            isset($params['employee_name']) && !empty($params['employee_name']) &&
            isset($params['branch_id']) && !empty($params['branch_id']) &&
            isset($params['district_id']) && !empty($params['district_id']) &&
            isset($params['state_id']) && !empty($params['state_id']) &&
            isset($params['zone_id']) && !empty($params['zone_id']) &&
            isset($params['branch_manager_id']) && !empty($params['branch_manager_id']) &&
            isset($params['branch_manager_name']) && !empty($params['branch_manager_name'])
        ) {
            $result['status'] = 'error';
            $result2['status'] = 'error';
            $result3['status'] = 'error';
            $result4['status'] = 'error';
            $action = 'list';
            $join[] = array('table' => Tbl_Leads . ' as l', 'on_condition' => 'l.id = la.lead_id', 'type' => '');
            $table = Tbl_LeadAssign . ' as la';
            $select = array('la.*', 'l.lead_identification');
            $where = array('la.lead_id' => $params['lead_id'], 'la.is_updated' => 1);
            $leadsAssign = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by = array(), $order_by = array());
            if (empty($leadsAssign)) {
                $res = array('result' => False,
                    'data' => 'No assigned lead found.');
                returnJson($res);
            }
            $leads_data = $leadsAssign[0];
            /****************************************************************
             * If interested in other product
             *****************************************************************/
            if (isset($params['interested']) && !empty($params['interested'])) {
                $interested = $params['interested'];
                if ($interested == 1) {
                    if (isset($params['product_category_id']) && !empty($params['product_category_id']) && isset($params['product_id']) && !empty($params['product_id'])) {
                        $product_category_id = $params['product_category_id'];
                        $product_id = $params['product_id'];
                        //Function call for add new leads in selected product category hierarchy
                        $result3 = $this->update_lead_product($params['lead_id'], $product_category_id, $product_id);

                    } else {
                        $res = array('result' => False,
                            'data' => 'Invalid Request for Other Interest');
                        returnJson($res);
                    }

                }
            }

            $all_status = $this->config->item('lead_status');
            if(!array_key_exists($params['status'],$all_status)){
                $res = array('result'=>false,
                    'data'=>'Unknown status.');
                returnJson($res);
            }
            /*****************************************************************/
            $response1['status'] = 'success';
            if (($leads_data['status'] != $params['status']) ||
                (isset($params['reroute_to']) && !empty($params['reroute_to']))) {
//                pe($params['status']);
                //Set current entry as old (set is_updated = 0)
                $lead_status_data = array('is_updated' => 0);
                $response1 = $this->Lead->update_lead_data($where, $lead_status_data, $table);

                if ($response1['status'] == 'success') {
                    //Create new entry in table Lead Assign with changed status.

                    /****************************************************************
                     * Update Lead Status
                     *****************************************************************/
                    $lead_status_data = array(
                        'lead_id' => $params['lead_id'],
                        'employee_id' => $params['employee_id'],
                        'employee_name' => $params['employee_name'],
                        'branch_id' => $params['branch_id'],
                        'district_id' => $params['district_id'],
                        'state_id' => $params['state_id'],
                        'zone_id' => $params['zone_id'],
                        'status' => $params['status'],
                        'created_by' => $params['branch_manager_id'],
                        'created_by_name' => $params['branch_manager_name']
                    );

                    /*****************************************************************/

                    /****************************************************************
                     * Reroute Lead
                     *****************************************************************/
                    if (isset($params['reroute_to']) && !empty($params['reroute_to'])) {
                        $lead_status_data['employee_id'] = $params['reroute_to'];
                        $lead_status_data['employee_name'] = 'New Employee2';
                        if ($leads_data['status'] != $params['status']) {
                            $lead_status_data['status'] = $params['status'];
                        } else {
                            $lead_status_data['status'] = $leads_data['status'];
                        }
                        $result4['status'] = 'reroute';
                    }

                    $result = $this->Lead->insert_lead_data($lead_status_data, Tbl_LeadAssign);
                }
            }
            $response2['status'] = '';
            /*****************************************************************
             * Update Lead Identification
             *****************************************************************/
            if ($leads_data['lead_identification'] != $params['lead_identification']) {
                $all_lead_types = $this->config->item('lead_type');
                if (array_key_exists($params['lead_identification'], $all_lead_types)) {
                    $where = array('id' => $params['lead_id']);
                    $lead_identification_data = array(
                        'lead_identification' => $params['lead_identification']
                    );
                    $response2 = $this->Lead->update_lead_data($where, $lead_identification_data, Tbl_Leads);
                } else {
                    $res = array('result' => False,
                        'data' => 'Unknown lead identification');
                    returnJson($res);
                }
            }
            /*****************************************************************/


            if (($response1['status'] == 'error') || ($response2['status'] == 'error')) {
                $res = array('result' => False,
                    'data' => 'Failed to update lead information');
                returnJson($res);
            } else {
                if ($params['status'] == 'FU') {
                    if (isset($params['remind_on']) && !empty($params['remind_on']) &&
                        isset($params['remind_to']) && !empty($params['remind_to']) &&
                        isset($params['reminder_text']) && !empty($params['reminder_text'])) {
                        $remindData = array(
                            'lead_id' => $params['lead_id'],
                            'remind_on' => date('y-m-d-H-i-s', strtotime($params['remind_on'])),
                            'remind_to' => $params['remind_to'],
                            'reminder_text' => $params['reminder_text']
                        );
                        //This will add entry into reminder scheduler for status (Interested/Follow up)
                        $result2 = $this->Lead->add_reminder($remindData);
                    } else {
                        $res = array('result' => False,
                            'data' => 'Invalid Request For Following Status');
                        returnJson($res);
                    }
                }
            }


            /*****************************************************************/

            if ($result['status'] == 'success' && $result2['status'] == 'success' && $result3['status'] == 'success') {
                $res = array('result' => True,
                    'data' => 'Lead Status Change and Reminder and Other Product Save Successfully');
                returnJson($res);
            } elseif ($result['status'] == 'success' && $result4['status'] == 'reroute') {
                $res = array('result' => True,
                    'data' => 'Lead Status Changed Successfully and Lead rerouted successfully.');
                returnJson($res);
            } elseif ($result['status'] == 'success' && $response2['status'] == 'success') {
                $res = array('result' => True,
                    'data' => 'Lead Status and Lead Identification Changed Successfully ');
                returnJson($res);
            } elseif ($response2['status'] == 'success') {
                $res = array('result' => True,
                    'data' => 'Lead Identification Changed Successfully ');
                returnJson($res);
            } elseif ($result['status'] == 'success') {
                $res = array('result' => True,
                    'data' => 'Lead Status Changed Successfully');
                returnJson($res);
            } elseif ($result2['status'] == 'success' && $response2['status'] == 'success') {
                $res = array('result' => True,
                    'data' => 'Lead Identification and Reminder Saved Successfully');
                returnJson($res);
            } elseif ($result2['status'] == 'success') {
                $res = array('result' => True,
                    'data' => 'Reminder Saved Successfully');
                returnJson($res);
            } elseif ($result3['status'] == 'success') {
                $res = array('result' => True,
                    'data' => 'Other Interested product Saved Successfully');
                returnJson($res);
            } else {
                $res = array('result' => True,
                    'data' => 'Nothing To Update');
                returnJson($res);
            }

        } else {
            $res = array('result' => False,
                'data' => 'Invalid Request');
            returnJson($res);
        }
    }

    public function refresh_dashboard_post()
    {
        $params = $this->input->post();
        if (!empty($params) && isset($params['user_id']) && !empty($params['user_id']) && isset($params['designation_name']) && !empty($params['designation_name'])) {
            $user_id = $params['user_id'];

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
            $result['status'] = 'success';
//        returnJson($data);


            if (isset($result['basic_info']['designation_name']) && $result['basic_info']['designation_name'] == 'BM') {
                if (isset($result['basic_info']['branch_id']) && $result['basic_info']['branch_id'] != '') {
                    $branch_id = $result['basic_info']['branch_id'];
                    $type = 'BM';
                    $final = $this->count($type, $branch_id, $result);

                    $leads['generated_converted'] = $final;
                    //for assigned lead
                    $where_assigned_Array = array('branch_id' => $branch_id,
                        'YEAR(created_on)' => date('Y'));
                }
                $leads['assigned_leads'] = $this->Lead->get_assigned_leads($where_assigned_Array);
                $action = 'count';
                $select = array();
                $table = Tbl_Leads;
                $where = array(Tbl_Leads . '.branch_id' => $result['basic_info']['branch_id'], Tbl_LeadAssign . 'lead_id', NULL);
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
                    $type = 'ZM';
                    $final = $this->count($type, $zone_id, $result);
                    $leads['generated_converted'] = $final;
                }
            }
            if (isset($result['basic_info']['designation_name']) && $result['basic_info']['designation_name'] == 'GM') {
                $type = 'GM';
                $final = $this->count($type, '', $result);
                $leads['generated_converted'] = $final;
            }


            $result = array(
                "result" => True,
                "data" => ['count' => $leads, 'basic_info' => $result['basic_info']]
            );
            returnJson($result);
        } else {
            $res = array('result' => False,
                'data' => 'Invalid Request');
            returnJson($res);
        }
    }

    /**
     * update_lead_product
     * Interested in other product
     * @author Ashok Jadhav
     * @access public
     * @param empty
     * @return array
     */
    public function update_lead_product($lead_id, $product_category_id, $product_id)
    {
        //Building input parameters
        $table = Tbl_Leads;
        $where = array(Tbl_Leads . '.id' => $lead_id);
        //link interested product id with current lead.
        $data['product_category_id'] = $product_category_id;
        $data['product_id'] = $product_id;
        $response = $this->Lead->update($where, $table, $data);
        if ($response['status'] == 'error') {
            return false;
        } else {
            return true;
        }

    }

    public function multiple_leads_assign_post()
    {
        $params = $this->input->post();
        if (isset($params) && isset($params['employee_id']) && isset($params['lead_id']) && isset($params['hrms_id']) &&
            isset($params['employee_name']) && isset($params['branch_id']) && isset($params['district_id']) &&
            isset($params['state_id']) && isset($params['zone_id']) && isset($params['status'])
            && !empty($params['employee_id']) && !empty($params['lead_id']) && !empty($params['employee_name'])
            && !empty($params['branch_id']) && !empty($params['district_id']) && !empty($params['state_id'])
            && !empty($params['zone_id']) && !empty($params['status'])
            && !empty($params['hrms_id']) && !empty($params['full_name'])) {
            $insertData = array();
            $assign_data = array(
                'employee_id' => $params['employee_id'],
                'employee_name' => $params['employee_name'],
                'branch_id' => $params['branch_id'],
                'district_id' => $params['district_id'],
                'state_id' => $params['state_id'],
                'zone_id' => $params['zone_id'],
                'status' => $params['status'],
                'created_by' => $params['hrms_id'],
                'created_by_name' => $params['full_name']
            );
            if (is_array($params['lead_id'])) {
                $leads = $params['lead_id'];
            } else {
                $leads[] = $params['lead_id'];
            }
            foreach ($leads as $key => $value) {
                $assign_data['lead_id'] = $value;
                $insertData[] = $assign_data;
            }
            $this->db->insert_batch(Tbl_LeadAssign, $insertData);
            $res = array('result' => True,
                'data' => 'Leads assigned successfully');
            returnJson($res);

        } else {
            $res = array('result' => False,
                'data' => 'Parameters missing');
            returnJson($res);
        }

    }

    public function authenticationnew_post()
    {
        $params = $this->input->post();

        if (!isset($params['user_id']) || !isset($params['password']) || !isset($params['device_token']) ||
            !isset($params['device_type']) || ($params['device_type'] == NULL) ||
            ($params['user_id'] == NULL) || ($params['password'] == NULL || ($params['device_token'] == NULL))) {
            $err['result'] = false;
            $err['data'] = "Invalid Request";
            returnJson($err);
        }

        $user_id = $params['user_id'];
        $password = $params['password'];
        $device_token = $params['device_token'];
        $device_type = $params['device_type'];

        //$auth_response = call_external_url(HRMS_API_URL_AUTH.'?username='.$user_id.'?password='.$password);
        $auth_response = call_external_url(HRMS_API_URL_AUTH.'/'.$user_id.'/'.$password);
        $auth = json_decode($auth_response);
        if ($auth->DBK_LMS_AUTH->password == 'True') {
           // $records_response = call_external_url(HRMS_API_URL_GET_RECORD.$result->DBK_LMS_AUTH->username);
            $records_response = call_external_url(HRMS_API_URL_GET_RECORD.'/'.$auth->DBK_LMS_AUTH->username);
            $records = json_decode($records_response);
            $data = array('device_token' => $device_token,
                'employee_id' => $records->dbk_lms_emp_record1->EMPLID,
                'device_type' => $device_type
            );
            $this->Login_model->insert_login_log($data); // login log

            $result['basic_info'] = array(
                'hrms_id' => $records->dbk_lms_emp_record1->EMPLID,
                'dept_id' => $records->dbk_lms_emp_record1->deptid,
                'dept_type_id' => $records->dbk_lms_emp_record1->dbk_dept_type,
                'dept_type_name' => $records->dbk_lms_emp_record1->dept_discription,
                'branch_id' => $records->dbk_lms_emp_record1->deptid,
                'district_id' => $records->dbk_lms_emp_record1->district,
                'state_id' => $records->dbk_lms_emp_record1->state,
                'zone_id' => $records->dbk_lms_emp_record1->dbk_state_id,
                'full_name' => $records->dbk_lms_emp_record1->name,
                'supervisor_id' => $records->dbk_lms_emp_record1->supervisor,
                'designation_id' => $records->dbk_lms_emp_record1->designation_id,
                'designation_name' => $records->dbk_lms_emp_record1->designation_descr,
                'mobile' => $records->dbk_lms_emp_record1->phone,
                'email_id' => $records->dbk_lms_emp_record1->email,
            );

            // employee
            if ($records->dbk_lms_emp_record1->designation_id == '540401') {
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
            // BM
            if ($records->dbk_lms_emp_record1->designation_id == '520299') {
                if (isset($result['basic_info']['branch_id']) && $result['basic_info']['branch_id'] != '') {
                    $branch_id = $result['basic_info']['branch_id'];
                    $type = 'BM';
                    $final = $this->countnew($type, $branch_id, $records->dbk_lms_emp_record1->DBK_LMS_COLL);

                    $leads['generated_converted'] = $final;
                    //for assigned lead
                    $where_assigned_Array = array('branch_id' => $branch_id,
                        'YEAR(created_on)' => date('Y'));
                }
                $leads['assigned_leads'] = $this->Lead->get_assigned_leads($where_assigned_Array);
                $action = 'count';
                $select = array();
                $table = Tbl_Leads;
                $where = array(Tbl_Leads . '.branch_id' => $result['basic_info']['branch_id'],Tbl_LeadAssign . 'lead_id', NULL);
                $join[] = array('table' => Tbl_LeadAssign, 'on_condition' => Tbl_LeadAssign . '.lead_id = ' . Tbl_Leads . '.id', 'type' => '');
                $leads['un_assigned_leads'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by = array(), $order_by = array());
            }
            //ZM
            if ($records->dbk_lms_emp_record1->designation_id == '550502') {
                if (isset($result['basic_info']['zone_id']) && $result['basic_info']['zone_id'] != '') {
                    $zone_id = $result['basic_info']['zone_id'];
                    $type = 'ZM';
                    $final = $this->countnew($type, $zone_id, $records->dbk_lms_emp_record1->DBK_LMS_COLL);
                    $leads['generated_converted'] = $final;
                }
            }
            // GM
            if ($records->dbk_lms_emp_record1->designation_id == '560601') {
                $type = 'GM';
                $final = $this->countnew($type, '', $records->dbk_lms_emp_record1->DBK_LMS_COLL);
                $leads['generated_converted'] = $final;
            }
            $result = array(
                "result" => True,
                "data" => ['count' => $leads, 'basic_info' => $result['basic_info']]
            );
            returnJson($result);
        }else{
            $err['result'] = false;
            $err['data'] = "Invalid Login Credential.Please Enter Again OR Contact Administrator";
            returnJson($err);
        }
    }

    private function countnew($type, $ids, $result)
    {
        switch ($type) {
            case 'BM':
                $where_month_Array = array('branch_id' => $ids,
                    'MONTH(created_on)' => date('m'));
                $generated['generated_leads'] = $this->Lead->get_generated_lead_bm_zm($where_month_Array);
                $generated_key_value = array();
                $final = array();
                foreach ($generated['generated_leads'] as $k => $v) {
                    $generated_key_value[$v['created_by']] = $v['total'];
                }
                foreach ($result as $key => $val) {
                    if (!array_key_exists($val->DESCR10, $generated_key_value)) {
                        $push_generated = array(
                            'created_by' => $val->DESCR10,
                            'created_by_name' => $val->DESCR30,
                            'total_generated' => 0);
                    } else {
                        $push_generated = array(
                            'created_by' => $val->DESCR10,
                            'created_by_name' => $val->DESCR30,
                            'total_generated' => $generated_key_value[$val->DESCR10]);
                    }
                    $final[$val->DESCR10] = $push_generated;
                }
                foreach ($final as $id => $value) {

                    $where_month_Array = array('employee_id' => $value['created_by'],
                        'MONTH(created_on)' => date('m'),
                        'status' => 'converted');
                    $converted = $this->Lead->get_converted_lead_bm_zm($where_month_Array);
                    if (empty($converted)) {
                        $converted = 0;
                    }
                    $final[$value['created_by']]['total_converted'] = $converted;
                }
                return $final;
                break;

            case 'ZM':
                $where_month_Array = array('zone_id' => $ids,
                    'MONTH(created_on)' => date('m'));

                $generated['generated_leads'] = $this->Lead->get_generated_lead_bm_zm($where_month_Array);
                $generated_key_value = array();
                $final = array();
                foreach ($generated['generated_leads'] as $k => $v) {
                    $generated_key_value[$v['branch_id']] = $v['total'];
                }
                foreach ($result as $key => $val) {
                    if (!array_key_exists($val->DESCR10, $generated_key_value)) {
                        $push_generated = array(
                            'created_by_branch_id' => $val->DESCR10,
                            'created_by_branch_name' => $val->DESCR30,
                            'total_generated' => 0);
                    } else {
                        $push_generated = array(
                            'created_by_branch_id' => $val->DESCR10,
                            'created_by_branch_name' => $val->DESCR30,
                            'total_generated' => $generated_key_value[$val->DESCR10]);
                    }
                    $final[$val->DESCR10] = $push_generated;
                }
                //for converted
                foreach ($final as $id => $value) {

                    $where_month_Array = array('branch_id' => $value['created_by_branch_id'],
                        'MONTH(created_on)' => date('m'),
                        'status' => 'converted');
                    $converted = $this->Lead->get_converted_lead_bm_zm($where_month_Array);
                    if (empty($converted)) {
                        $converted = 0;
                    }
                    $final[$value['created_by_branch_id']]['total_converted'] = $converted;
                }
                return $final;
                break;

            case 'GM':
                $where_generated_Array = array('zone_id !=' => NULL,
                    'MONTH(created_on)' => date('m'));
                $generated['generated_leads'] = $this->Lead->get_generated_lead_bm_zm($where_generated_Array);
                $generated_key_value = array();
                $final = array();
                foreach ($generated['generated_leads'] as $k => $v) {
                    $generated_key_value[$v['zone_id']] = $v['total'];
                }
                foreach ($result as $key => $val) {
                    if (!array_key_exists($val->DESCR10, $generated_key_value)) {
                        $push_generated = array(
                            'created_by_zone_id' => $val->DESCR10,
                            'created_by_zone_name' => $val->DESCR30,
                            'total_generated' => 0);
                    } else {
                        $push_generated = array(
                            'created_by_zone_id' => $val->DESCR10,
                            'created_by_zone_name' => $val->DESCR30,
                            'total_generated' => $generated_key_value[$val->DESCR10]);
                    }
                    $final[$val->DESCR10] = $push_generated;
                }
                //for converted
                foreach ($final as $id => $value) {

                    $where_month_Array = array('zone_id' => $value['created_by_zone_id'],
                        'MONTH(created_on)' => date('m'),
                        'status' => 'converted');
                    $converted = $this->Lead->get_converted_lead_bm_zm($where_month_Array);
                    if (empty($converted)) {
                        $converted = 0;
                    }
                    $final[$value['created_by_zone_id']]['total_converted'] = $converted;
                }
                return $final;
                break;

        }
    }
}