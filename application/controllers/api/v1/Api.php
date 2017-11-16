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
        $this->load->model('Notification_model', 'notification');
        $method = $this->router->method;
        $authorised_methods = $this->config->item('authorised_methods');
//        if(in_array($method,$authorised_methods)){
//            return true;
//        }else{
//            $params = $this->input->post();
//            $headers = getallheaders();
//
//            if(!empty($headers) && !isset($params['password'])){
//                if(isset($headers['authorisation_key']) && $headers['authorisation_key'] !=NULL &&
//                    isset($headers['hrms_id']) && $headers['hrms_id'] !=NULL){
//                    $response = array('result'=>False,
//                        'data'=>array('Wrong authorisation key.'));
//                    $check_response = check_authorisation($headers['authorisation_key'],$headers['hrms_id']);
//                    if(!$check_response)
//                        returnJson($response);
//                }else{
//                    $response = array('result'=>False,
//                        'data'=>array('authorisation key or hrms id missing.'));
//                    returnJson($response);
//                }
//            }else{
//                if(!isset($params['password'])){
//                    $response = array('result'=>False,
//                        'data'=>array('authorisation key or hrms id missing.'));
//                    returnJson($response);
//                }
//            }
//        }
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

                        $whereyear = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0, Tbl_Leads . '.lead_source' => $key);

                        $wheremonth = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0,Tbl_Leads . '.lead_source' => $key);

                        $yr_start_date=date('Y').'-04-01 00:00:00';
                        $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
                        $whereyear[Tbl_LeadAssign.".created_on >='".$yr_start_date."'"] = NULL;
                        $whereyear[Tbl_LeadAssign.".created_on <='".$yr_end_date."'"] = NULL;

                        $wheremonth['MONTH(' . Tbl_LeadAssign . '.created_on)'] = date('m'); //Month till date filter
                        $wheremonth['YEAR(' . Tbl_LeadAssign . '.created_on)'] = date('Y');

                        $result[$i]['year_lead_assigned'] = $this->Lead->get_leads($action, $table, $select, $whereyear, $join, '', '');

                        //$where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'),Tbl_Leads . '.lead_source' => $key);
                        $result[$i]['month_lead_assigned'] = $this->Lead->get_leads($action, $table, $select, $wheremonth, $join, '', '');

                       // $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'),       Tbl_Leads . '.lead_source' => $key, Tbl_LeadAssign . '.status' => 'Converted');

                        $whereyear[Tbl_LeadAssign . '.status'] =  'Converted';
                        $result[$i]['year_lead_converted'] = $this->Lead->get_leads($action, $table, $select, $whereyear, $join, '', '');

                        //$where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'),                      Tbl_Leads . '.lead_source' => $key, Tbl_LeadAssign . '.status' => 'Converted');
                        $wheremonth[Tbl_LeadAssign . '.status'] =  'Converted';
                        $result[$i]['month_lead_converted'] = $this->Lead->get_leads($action, $table, $select, $wheremonth, $join, '', '');
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
                        $whereyear = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0, Tbl_Leads . '.lead_source' => $key);
                        $wheremonth = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0, Tbl_Leads . '.lead_source' => $key);
                        $yr_start_date=date('Y').'-04-01 00:00:00';
                        $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
                        $whereyear[Tbl_LeadAssign.".created_on >='".$yr_start_date."'"] = NULL;
                        $whereyear[Tbl_LeadAssign.".created_on <='".$yr_end_date."'"] = NULL;

                        $wheremonth['MONTH(' . Tbl_LeadAssign . '.created_on)'] = date('m'); //Month till date filter
                        $wheremonth['YEAR(' . Tbl_LeadAssign . '.created_on)'] = date('Y');

                        $result[$i]['year_lead_assigned'] = $this->Lead->get_leads($action, $table, $select, $whereyear, $join, '', '');
                        //$where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => $key);
                        $result[$i]['month_lead_assigned'] = $this->Lead->get_leads($action, $table, $select, $wheremonth, $join, '', '');

                        //$where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => $key,                           Tbl_LeadAssign . '.status' => 'Converted');
                        $whereyear[Tbl_LeadAssign . '.status'] =  'Converted';
                        $result[$i]['year_lead_converted'] = $this->Lead->get_leads($action, $table, $select, $whereyear, $join, '', '');

//                        $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => $key,
//                            Tbl_LeadAssign . '.status' => 'Converted');
                        $wheremonth[Tbl_LeadAssign . '.status'] =  'Converted';
                        $result[$i]['month_lead_converted'] = $this->Lead->get_leads($action, $table, $select, $wheremonth, $join, '', '');
                        $result[$i]['lead_source'] = $lead_source;
                        $i++;
                    }

                    $data = array('result' => True,
                        'data' => $result);
                    returnJson($data);
                    break;
                case 'GM':
                    $join = array();
                    $zone_id = $params['id'];
                    $action = 'count';
                    $table = Tbl_Leads;
                    $join[] = array('table' => Tbl_LeadAssign, 'on_condition' => Tbl_Leads . '.id = ' . Tbl_LeadAssign . '.lead_id', 'type' => '');
                    $select = array();
                    $i = 0;
                    foreach ($source as $key => $lead_source) {
                        $whereyear = array(Tbl_LeadAssign . '.zone_id' => $zone_id, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0,  Tbl_Leads . '.lead_source' => $key);
                        $wheremonth = array(Tbl_LeadAssign . '.zone_id' => $zone_id, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0, Tbl_Leads . '.lead_source' => $key);
                        $yr_start_date=date('Y').'-04-01 00:00:00';
                        $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
                        $whereyear[Tbl_LeadAssign.".created_on >='".$yr_start_date."'"] = NULL;
                        $whereyear[Tbl_LeadAssign.".created_on <='".$yr_end_date."'"] = NULL;

                        $wheremonth['MONTH(' . Tbl_LeadAssign . '.created_on)'] = date('m'); //Month till date filter
                        $wheremonth['YEAR(' . Tbl_LeadAssign . '.created_on)'] = date('Y');

                        $result[$i]['year_lead_assigned'] = $this->Lead->get_leads($action, $table, $select, $whereyear, $join, '', '');
                        //$where = array(Tbl_LeadAssign . '.zone_id' => $zone_id, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => $key);
                        $result[$i]['month_lead_assigned'] = $this->Lead->get_leads($action, $table, $select, $wheremonth, $join, '', '');

//                        $where = array(Tbl_LeadAssign . '.zone_id' => $zone_id, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => $key,
//                            Tbl_LeadAssign . '.status' => 'Converted');
                        $whereyear[Tbl_LeadAssign . '.status'] =  'Converted';
                        $result[$i]['year_lead_converted'] = $this->Lead->get_leads($action, $table, $select, $whereyear, $join, '', '');

//                        $where = array(Tbl_LeadAssign . '.zone_id' => $zone_id, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => $key,
//                            Tbl_LeadAssign . '.status' => 'Converted');
                        $wheremonth[Tbl_LeadAssign . '.status'] =  'Converted';
                        $result[$i]['month_lead_converted'] = $this->Lead->get_leads($action, $table, $select, $wheremonth, $join, '', '');
                        $result[$i]['lead_source'] = $lead_source;
                        $i++;
                    }

                    $data = array('result' => True,
                        'data' => $result);
                    returnJson($data);
                    break;
            }
        }
        $data = array('result' => False,
            'data' => array('parameter missing'));
        returnJson($data);
    }

    public function add_lead_post()
    {
        $params = $this->input->post();
        $error = array();
        $validations = array('customer_name' => 'Customer Name', 'contact_no' => 'Phone No',
            'product_category_id' => 'Product Category', 'product_id' => 'Product', 'lead_ticket_range' => 'Range',
            'is_own_branch' => 'Own Branch / Other Branch', 'created_by' => 'Created By','created_by_name' => 'Created By Name',
            'state_id' => 'State', 'district_id' => 'District',
            'zone_id' => 'Zone', 'branch_id' => 'Branch', 'department_name' => 'Department Name',
            'department_id' => 'Department Id', 'created_by_state_id' => 'Created By State',
            'created_by_district_id' => 'Created By District',
            'created_by_zone_id' => 'Created By Zone', 'created_by_branch_id' => 'Created By Branch',
            'latitude' => 'Latitude', 'longitude' => 'Longitude','remark'=>'Remark',
            'unique_id' => 'Unique Id');
        $phone_extra = '';
        $cust_name_extra = '';
        foreach ($params as $k => $value) {
            if (array_key_exists($k, $validations)) {

                if ($k == 'phone_no') {
                    $phone_extra = '|max_length[10]|min_length[10]|numeric';
                }
                if ($k == 'customer_name') {
                    $cust_name_extra = '|callback_alpha_dash_space["Customer Name"]';
                }
                if($k != 'remark'){
                    $this->form_validation->set_rules($k, '', 'required' . $phone_extra . $cust_name_extra);
                }
                if ($this->form_validation->run() === FALSE) {
                    $error[] = form_error($k);
                } else {
                    if($k=='customer_name'){
                        $value = ucwords(strtolower($value));
                        $lead_data[$k] = $value;
                    }else{
                        $lead_data[$k] = $value;
                    }
                }
                unset($validations[$k]);
                $phone_extra = '';
                $cust_name_extra = '';
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
        $unique_id = $lead_data['unique_id'];
        unset($lead_data['unique_id']);

        $lead_data['lead_name'] = $this->input->post('customer_name');
        $assign_to = $this->Lead->get_product_assign_to($lead_data['product_id']);
        $action = 'list';
        $select = array('map_with','title');
        $table = Tbl_Products;
        $where = array('id'=>$lead_data['product_id']);
        $product_mapped_with = $this->Lead->get_leads($action,$table,$select,$where,'','','');
        $product_name=$product_mapped_with[0]['title'];
        $product_mapped_with=$product_mapped_with[0]['map_with'];
        $whereArray = array('processing_center'=>$product_mapped_with, 'branch_id' => $lead_data['branch_id']);
        $routed_id = $this->Lead->check_mapping($whereArray);
        if (!is_array($routed_id)) {
            $lead_data['reroute_from_branch_id'] = $lead_data['branch_id'];
            $lead_data['branch_id'] = $routed_id;
        }
        $lead_id = $this->Lead->add_leads($lead_data);

        if (is_array($lead_id)) {
            $result = array('result' => False,
                'data' => array('wrong product id or category id .'));
            returnJson($result);
        }
        if($lead_id != false){
            //send sms
        $sms = 'Thanks for showing interest in '.ucwords($product_name).' with Dena Bank. We will contact you shortly.';
        send_sms($lead_data['contact_no'],$sms);

        //Push notification
            $emp_id = $params['created_by'];
            $title = 'Lead Submitted Successfully';
            $push_message = 'Lead Submitted Successfully for '.ucwords($product_name);
            sendPushNotification($emp_id,$push_message,$title);

        //Save notification
        $this->insert_notification($lead_data);
        }

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
            $title = 'New Lead Assigned';
            $push_message = "New Lead Assigned to you";
            sendPushNotification($emp_id,$push_message,$title);
        }

        //send sms
        /*$message = 'Thanks for showing interest with Dena Bank. We will contact you shortly';
        send_sms($this->input->post('contact_no'),$message);*/

        //Push notification
        //sendNotificationSingleClient($device_id,$device_type,$message,$title=NULL);
        $success_message = array('Lead Submitted Successfully.');
        $result = array('result' => True,
            'data' => $success_message,
            'unique id' => $unique_id);
        returnJson($result);

    }

    ##################################
    /*Public Functions*/
    ##################################
    /*
    * Validation for alphabetical letters
    * @param array $pwd,$dataArray
    * @return String
    */
    public function alphaNumeric($str, $name = '')
    {
        if (!preg_match('/^[a-zA-Z0-9\s]+$/i', $str)) {
            $this->form_validation->set_message('alphaNumeric', 'Please enter only alpha numeric characters for ' . $name . '.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function alpha_dash_space($str,$name='')
    {
        $check =  ( ! preg_match("/^([-a-z_ ])+$/i", $str)) ? FALSE : TRUE;
        if(!$check){
            $this->form_validation->set_message('alpha_dash_space', 'Please enter only alphabets for '.$name.'.');
        }
        return $check;
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
            'data' => array('No data found'));
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
            'data' => array('No data found'));
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

    public function category_products_get()
    {
        $final = array();
        $table = Tbl_Category;
        $join = array();
        $select = array(Tbl_Category . '.title', Tbl_Category . '.id');
        $where = array(Tbl_Category . '.status !='=>'inactive',Tbl_Category . '.is_deleted != '=>'1');
        $result = $this->Lead->lists($table, $select, $where, $join, array(), array());
        $products = array();
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $table = Tbl_Products;
                $join = array();
                $where = array(Tbl_Products . '.category_id' => $value['id'],
                    Tbl_Products . '.status !='=>'inactive',Tbl_Products. '.is_deleted !='=>'1');
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
            "data" => array("No data found.")
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
                            $whereArray = array(Tbl_Leads . '.created_by' => $employee_id, 'status' => $key, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0);
                            $yr_start_date=date('Y').'-04-01 00:00:00';
                            $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
                            $whereArray[Tbl_Leads  .".created_on >='$yr_start_date'"] = NULL;
                            $whereArray[Tbl_Leads  .".created_on <='$yr_end_date'"] = NULL;
                            $result[$i]['Year'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');

                            $whereArray = array(Tbl_Leads . '.created_by' => $employee_id, 'status' => $key, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0);
                            $whereArray['MONTH(' . Tbl_Leads . '.created_on)'] = date('m'); //Month till date filter
                            $whereArray['YEAR(' . Tbl_Leads . '.created_on)'] = date('Y');


                            $result[$i]['Month'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');
                            $result[$i]['status'] = $value;
                            $i++;
                        }
                        $action = 'count';
                        $select = array();
                        $table = Tbl_Leads;
                        $join_assign[] = array('table' =>Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = '.Tbl_Leads.'.id ','type' => 'left');
                        $whereYear = array($table . '.created_by' => $employee_id,'la.lead_id' => NULL);
                        $yr_start_date=date('Y').'-04-01 00:00:00';
                        $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
                        $whereYear[$table .".created_on >='$yr_start_date'"] = NULL;
                        $whereYear[$table .".created_on <='$yr_end_date'"] = NULL;
                        $whereMonth = array($table . '.created_by' => $employee_id,'la.lead_id' => NULL);
                        $whereMonth['MONTH(' . $table . '.created_on)'] = date('m'); //Month till date filter
                        $whereMonth['YEAR(' . $table . '.created_on)'] = date('Y');

                        $unassigned_leads_count_month = $this->Lead->get_leads($action,$table,$select,$whereMonth,$join_assign,$group_by = array(),$order_by = array());
                        $unassigned_leads_count_year = $this->Lead->get_leads($action,$table,$select,$whereYear,$join_assign,$group_by = array(),$order_by = array());
                        $result[$i]['Year'] = $unassigned_leads_count_year;
                        $result[$i]['Month'] = $unassigned_leads_count_month;
                        $result[$i]['status'] = 'Unassigned Leads';
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
                            $whereArray = array(Tbl_Leads . '.branch_id' => $branch_id, 'status' => $key, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'), Tbl_LeadAssign . '.is_updated' => 1);
                            $result[$i]['Year'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');

                            $whereArray = array(Tbl_Leads . '.branch_id' => $branch_id, 'status' => $key, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'), Tbl_LeadAssign . '.is_updated' => 1);
                            $result[$i]['Month'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');
                            $result[$i]['status'] = $value;
                            $i++;
                        }
                    }
                    $res = array('result' => True,
                        'data' => $result);
                    returnJson($res);
                    break;
                case 'GM':
                    $table = Tbl_LeadAssign;
                    $action = 'count';
                    $zone_id = $params['id'];

                    if (!empty($status)) {
                        $i = 0;
                        foreach ($status as $key => $value) {
                            $whereArray = array(Tbl_Leads . '.zone_id' => $zone_id, 'status' => $key, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'), Tbl_LeadAssign . '.is_updated' => 1);
                            $result[$i]['Year'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');

                            $whereArray = array(Tbl_Leads . '.zone_id' => $zone_id, 'status' => $key, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'), Tbl_LeadAssign . '.is_updated' => 1);
                            $result[$i]['Month'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');
                            $result[$i]['status'] = $value;
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
            "data" => array("Missing Parameters.")
        );
        returnJson($error);
    }
    public function masters_get()
    {

        $lead_status['branch_details'] = array();
        //get zone and state list
        $action = 'list';
        $select = array('TRIM(z.code) as z_code', 'TRIM(z.name) as z_name', 'TRIM('.Tbl_state . '.name) as s_name', 'TRIM('.Tbl_state . '.code) as s_code');
        $where = array('z.name !='=>'',Tbl_state.'.name !='=>'');
        $orderBy = Tbl_state.'.name ASC';
        $table = Tbl_zone . ' as z';
        $join[] = array('table' => Tbl_state, 'on_condition' => Tbl_state . '.zone_code = z.code', 'type' => 'left');
        $zone_state = $this->Lead->get_leads($action, $table, $select, $where, $join, '', $orderBy);

        $final_details = array();$respone = array();$available_states = array();$available_dists = array();
        foreach ($zone_state as $key => $state_zone) {
            if(!in_array($state_zone['s_code'],$available_states)){

                $table = Tbl_district . ' as d';
                $select = array('DISTINCT(TRIM(name)) AS name','TRIM(code) AS id');
                $where = array('state_code' => $state_zone['s_code'],'name !='=>'');
                $orderBy = 'name ASC';
                $districts = $this->Lead->get_leads($action, $table, $select, $where, '', '', $orderBy);
                foreach ($districts as $dist_key => $all_dist) {
                    if(!in_array($all_dist['id'],$available_dists)){
                        $table = Tbl_branch . ' as b';
                        $select = array('TRIM(code) AS id', 'TRIM(name) AS name');
                        $where = array('district_code' => $all_dist['id'],'name !='=>'');
                        $orderBy = 'name ASC';
                        $branches = $this->Lead->get_leads($action, $table, $select, $where, '', '', $orderBy);
                        $districts[$dist_key]['branches'] = $branches;
                    }
                    $available_dists[$dist_key] = $all_dist['id'];
                }
                $final_details['zone_id'] = $state_zone['z_code'];
                $final_details['zone_name'] = $state_zone['z_name'];
                $final_details['state'] = [
                    array('id'=>$state_zone['s_code'],'name'=>$state_zone['s_name'],'districts'=>$districts)
                ];
                $respone[] = $final_details;
            }
            $available_states[$key] = $state_zone['s_code'];
        }
        $lead_status['branch_details'] = $respone;

        $final = array();
        $table = Tbl_Category;
        $join = array();
        $select = array(Tbl_Category . '.title', Tbl_Category . '.id');
        $where = array(Tbl_Category . '.status !='=>'inactive',Tbl_Category . '.is_deleted != '=>'1');
        $result = $this->Lead->lists($table, $select, $where, $join, array(), array());
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $table = Tbl_Products;
                $join = array();
                $where = array(Tbl_Products . '.category_id' => $value['id'],
                    Tbl_Products . '.status !='=>'inactive',Tbl_Products . '.is_deleted != '=>'1');
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
            $final_status = array();
            $final_source = array();
            $final_type = array();
            foreach ($this->config->item('lead_status') as $key => $value){
                $status['id'] = $key;
                $status['title'] = $value;
                $final_status[] = $status;
            }
            foreach ($this->config->item('lead_source') as $key => $value){
                $status['id'] = $key;
                $status['title'] = $value;
                $final_source[] = $status;
            }
            foreach ($this->config->item('lead_type') as $key => $value){
                $status['id'] = $key;
                $status['title'] = $value;
                $final_type[] = $status;
            }
            $lead_status['status'] = $final_status;
            $lead_status['lead_source'] = $final_source;
            $lead_status['lead_identification'] = $final_type;
        }
        if (!empty($lead_status)) {
            $res = array('result' => True,
                'data' => $lead_status);
            returnJson($res);
        }
        $res = array('result' => False,
            'data' => array('No data found'));
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
            'data' => array('Wrong Parameters'));
        returnJson($res);
    }

    private function em_view($login_user)
    {

        //Parameters buiding for sending to list function.
        $action = 'list';
        $table = Tbl_Leads . ' as l';
        $join = array();
        $join[] = array('table' => Tbl_Products . ' as p', 'on_condition' => 'l.product_id = p.id AND l.product_category_id = p.category_id', 'type' => '');

        $select = array('l.id', 'l.customer_name', 'l.lead_identification', 'l.created_on', 'l.lead_source',
          "CONCAT(UCASE(LEFT(p.title, 1)),LCASE(SUBSTRING(p.title, 2))) as title", 'la.status'/*,'p1.title as interested_product_title'*/, 'r.remind_on');
        $where = array('la.employee_id' => $login_user['hrms_id'], 'la.is_deleted' => 0, 'YEAR(la.created_on)' => date('Y'));
        $join[] = array('table' => Tbl_LeadAssign . ' as la', 'on_condition' => 'la.lead_id = l.id', 'type' => '');

        $join[] = array('table' => Tbl_Reminder . ' as r', 'on_condition' => 'la.lead_id = r.lead_id AND r.is_cancelled = "No"', 'type' => 'left');
        $order_by = 'la.created_on desc';
        $arrData['leads'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by = array(), $order_by);
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
                'data' => array('Wrong Parameters'));
            returnJson($res);
        }
    }

    private function count($type, $ids, $result)
    {
        switch ($type) {
            case 'BM':
                $where_month_Array = array('branch_id' => $ids,
                    'MONTH(created_on)' => date('m'),'is_updated'=>1,'is_deleted'=>0);
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
                        'status' => 'converted','is_updated'=>1,'is_deleted'=>0);
                    $converted = $this->Lead->get_converted_lead_bm_zm($where_month_Array);
                    if (empty($converted)) {
                        $converted = 0;
                    }
                    $final[$value['created_by']]['total_converted'] = $converted;
                }
                $refinal = array_values($final);
                return $refinal;
                break;

            case 'ZM':
                $where_month_Array = array('zone_id' => $ids,
                    'MONTH(created_on)' => date('m'),'is_updated'=>1,'is_deleted'=>0);

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
                        'status' => 'converted','is_updated'=>1,'is_deleted'=>0);
                    $converted = $this->Lead->get_converted_lead_bm_zm($where_month_Array);
                    if (empty($converted)) {
                        $converted = 0;
                    }
                    $final[$value['created_by_branch_id']]['total_converted'] = $converted;
                }
                $refinal = array_values($final);
                return $refinal;
                break;

            case 'GM':
                $where_generated_Array = array('zone_id !=' => NULL,
                    'MONTH(created_on)' => date('m'),'is_updated'=>1,'is_deleted'=>0);
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
                        'status' => 'converted','is_updated'=>1,'is_deleted'=>0);
                    $converted = $this->Lead->get_converted_lead_bm_zm($where_month_Array);
                    if (empty($converted)) {
                        $converted = 0;
                    }
                    $final[$value['created_by_zone_id']]['total_converted'] = $converted;
                }
                $refinal = array_values($final);
                return $refinal;
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
            $where = array(Tbl_Leads . '.branch_id' => $params['branch_id'], Tbl_LeadAssign . '.lead_id' => NULL, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'), 'DATEDIFF( CURDATE( ) , ' . Tbl_Leads . '.created_on) <=' => Elapsed_day);
            $arrData['unassigned_leads_count'] = $this->Lead->unassigned_status_count($select, $table, $join, $where, $group_by);
            $response = array();
            $keys = array('walkin' => "0", 'analytics' => "0", 'tie_ups' => "0", 'enquiry' => "0");
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
                'data' => array('Invalid Request'));
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
        if (!empty($params) && isset($params['lead_source']) && !empty($params['lead_source']
                && isset($params['branch_id']) && !empty($params['branch_id']))) {
            $lead_source = $params['lead_source'];
            $branch_id = $params['branch_id'];
            $unassigned_leads = $this->Lead->unassigned_leads_api($lead_source, $branch_id);
            $res = array('result' => True,
                'data' => $unassigned_leads);
            returnJson($res);
        } else {
            $res = array('result' => False,
                'data' => array('Invalid Request'));
            returnJson($res);
        }

    }

    /**
     * assigned_leads_list
     * loads the assigned leads list filtered by lead source
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
            $join[] = array('table' => Tbl_Category . ' as c', 'on_condition' => 'l.product_category_id = c.id', 'type' => '');

            $select = array('l.id', 'l.customer_name', 'l.contact_no', 'l.lead_identification', 'la.created_on', 'l.lead_source', 'p.title', 'la.status'/*,'p1.title as interested_product_title'*/,
                'r.remind_on', 'DATEDIFF(CURDATE( ),la.created_on) as elapsed_day','c.title as category_title');
            $where = array('la.is_deleted' => 0, 'la.is_updated' => 1, 'DATEDIFF( CURDATE( ) , la.created_on) <=' => Elapsed_day);
            $yr_start_date=date('Y').'-04-01 00:00:00';
            $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
           // $where["la.created_on >='".$yr_start_date."' AND la.created_on <='".$yr_end_date."'"] = NULL;
            if ($type == 'EM') {
                $where['la.employee_id'] = $id;
                $where["la.status NOT IN('Closed','Converted')"] = NULL;
                $order_by = 'la.created_on desc';
            }
            if ($type == 'BM') {
                $where['la.branch_id'] = $id;
                $where["la.status NOT IN('Closed','Converted')"] = NULL;
                $where2 = array(Tbl_LeadAssign . '.branch_id' => $id);
                //$order_by = "CASE WHEN la.status = 'AO' THEN 1 WHEN la.status = 'NI' THEN 2 ELSE 3 END , elapsed_day ASC";
                $order_by = "CASE WHEN la.status = 'AO' THEN 1 WHEN c.title = 'Fee Income' && la.status = 'DC' THEN 2 WHEN la.status = 'NI' THEN 3 ELSE 4 END , elapsed_day ASC";
            }

            $join[] = array('table' => Tbl_LeadAssign . ' as la', 'on_condition' => 'la.lead_id = l.id', 'type' => '');
            /*$join[] = array('table' => Tbl_Products.' as p1','on_condition' => 'l.interested_product_id = p1.id','type' => 'left');*/

            $join[] = array('table' => Tbl_Reminder . ' as r', 'on_condition' => 'la.lead_id = r.lead_id AND r.is_cancelled = "No"', 'type' => 'left');
            $arrData['leads'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by = array(), $order_by);

            if ($type == 'EM') {
                $table2 = Tbl_LeadAssign;
                $where2 = array(Tbl_LeadAssign . '.employee_id' => $id);
                $lead_status_data2 = array('view_status' => 1);
                $response1 = $this->Lead->update_lead_data($where2, $lead_status_data2, $table2);
            }

            $res = array('result' => True,
                'data' => $arrData['leads']);
            returnJson($res);
        } else {
            $res = array('result' => False,
                'data' => array('Invalid Request'));
            returnJson($res);
        }
    }

    /**
     * details
     * Get Leads details based on type of lead (Generated,Converted,Assigned)
     * @author Gourav Thatoi
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
                $select = array('l.id', 'l.customer_name', 'l.lead_identification','l.opened_account_no', 'l.lead_source', 'l.contact_no', 'l.product_id', 'p.title AS product_title', 'c.title AS category_title', 'l.product_category_id', 'la.status', 'l.remark');
                $join[] = array('table' => Tbl_LeadAssign . ' as la', 'on_condition' => 'la.lead_id = l.id', 'type' => 'left');
            }

            if ($type == 'converted') {
                $select = array('l.id', 'l.customer_name', 'l.lead_identification','l.opened_account_no', 'l.lead_source', 'l.contact_no', 'l.product_id', 'p.title AS product_title', 'c.title AS category_title', 'l.product_category_id', 'la.status', 'l.remark');
                $where['la.is_deleted'] = 0;
                $where['la.is_updated'] = 1;
                $join[] = array('table' => Tbl_LeadAssign . ' as la', 'on_condition' => 'la.lead_id = l.id', 'type' => '');
            }

            if ($type == 'assigned') {
                //SELECT COLUMNS
                $select = array('l.id', 'l.remark', 'l.customer_name', 'l.lead_identification','l.opened_account_no', 'l.lead_source', 'l.contact_no', 'l.product_id', 'p.title AS product_title'/*,'l.interested_product_id','p1.title AS interested_product_title'*/, 'c.title AS category_title', 'l.product_category_id', 'la.status', 'la.employee_id', 'la.employee_name', 'r.remind_on', 'r.reminder_text', 'l.remark','la.reason_for_drop');

                $where['la.is_deleted'] = 0;
                $where['la.is_updated'] = 1;

                //JOIN CONDITIONS
                $join[] = array('table' => Tbl_LeadAssign . ' as la', 'on_condition' => 'la.lead_id = l.id', 'type' => '');
                $join[] = array('table' => Tbl_Reminder . ' as r', 'on_condition' => 'la.lead_id = r.lead_id AND r.is_cancelled = "No"', 'type' => 'left');
                /*$join[] = array('table' => Tbl_Products.' as p1','on_condition' => 'l.interested_product_id = p1.id','type' => 'left');*/
            }
            $arrData['leads'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by = array(), $order_by = array());
            $arrData['leads'][0]['product_title']=ucwords($arrData['leads'][0]['product_title']);
            $arrData['leads'][0]['category_title']=ucwords($arrData['leads'][0]['category_title']);
            $res = array('result' => True,
                'data' => $arrData['leads']);
            returnJson($res);
        } else {
            $res = array('result' => False,
                'data' => array('Invalid Request'));
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
                //push notification
                $emp_id = $params['employee_id'];
                sendPushNotification($emp_id,$description,$title);

                $res = array('result' => True,
                    'data' => 'Lead Assigned Successfully');
                returnJson($res);
            }
        } else {
            $res = array('result' => False,
                'data' => array('Invalid Request'));
            returnJson($res);
        }
    }

    /**
     * update_lead_status
     * Only for assigned lead list able to change lead status / Add Follow Up details
     * @author Gourav Thatoi
     * @access public
     * @param empty
     * @return array
     */
//    public function update_lead_status_post()
//    {
//        $params = $this->input->post();
//        if (!empty($params) && isset($params['lead_id']) && !empty($params['lead_id']) &&
//            isset($params['employee_id']) && !empty($params['employee_id']) &&
//            isset($params['status']) && !empty($params['status']) &&
//            isset($params['lead_identification']) && !empty($params['lead_identification']) &&
//            isset($params['logged_in_hrms_id']) && !empty($params['logged_in_hrms_id'])) {
//            $result['status'] = 'error';
//            $result2['status'] = 'error';
//            $result4['status'] = 'error';
//            $response1['status'] = '';
//            $action = 'list';
//            $join[] = array('table' => Tbl_Leads . ' as l', 'on_condition' => 'l.id = la.lead_id', 'type' => '');
//            $table = Tbl_LeadAssign . ' as la';
//            $select = array('la.*', 'l.lead_identification');
//            $where = array('la.lead_id' => $params['lead_id'], 'la.is_updated' => 1);
//            $leadsAssign = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by = array(), $order_by = array());
//            $leads = isset($leadsAssign[0]) ? $leadsAssign[0]:'';
//            if (empty($leadsAssign)) {
//                $res = array('result' => False,
//                    'data' => array('No assigned lead found.'));
//                returnJson($res);
//            }
//            $all_status = $this->config->item('lead_status');
//            if (!array_key_exists($params['status'], $all_status)) {
//                $res = array('result' => false,
//                    'data' => array('Unknown status.'));
//                returnJson($res);
//            }
//            if (isset($params['reroute_to_own_branch'])) {
//
//            if ($params['reroute_to_own_branch'] == 0) {
//                if (!isset($params['branch_id']) || empty($params['branch_id']) ||
//                    !isset($params['district_id']) || empty($params['district_id']) ||
//                    !isset($params['state_id']) || empty($params['state_id'])) {
//                    $res = array('result' => False,
//                        'data' => array('State id or District id or Branch id missing.'));
//                    returnJson($res);
//                }
//                $action = 'list';
//                $table = Tbl_Leads;
//                $select = array(Tbl_Leads . '.*');
//                $where = array(Tbl_Leads . '.id' => $params['lead_id']);
//                $leadsAssign = $this->Lead->get_leads($action, $table, $select, $where, $join = array(), $group_by = array(), $order_by = array());
//                $leads_data = $leadsAssign[0];
//                $id = $leads_data['id'];
//                $leads_data['reroute_from_branch_id'] = $leads_data['branch_id'];
//                $leads_data['state_id'] = $params['state_id'];
//                $leads_data['branch_id'] = $params['branch_id'];
//                $leads_data['district_id'] = $params['district_id'];
//                unset($leads_data['id']);
//                $this->Lead->insert_lead_data($leads_data, Tbl_Leads);
//                $whereUpdate = array('lead_id' => $id);
//                $table = Tbl_LeadAssign;
//                $data = array('is_updated' => 0);
//                $this->Lead->update($whereUpdate, $table, $data);
//            } else {
//                /*****************************************************************/
//                //Building input parameters for function to get_leads
//                $action = 'list';
//                $table = Tbl_LeadAssign;
//                $select = array(Tbl_LeadAssign . '.*');
//                $where = array(Tbl_LeadAssign . '.lead_id' => $params['lead_id'], Tbl_LeadAssign . '.is_updated' => 1);
//                $leadsAssign = $this->Lead->get_leads($action, $table, $select, $where, $join = array(), $group_by = array(), $order_by = array());
//                $leads_data = $leadsAssign[0];
//                $response1['status'] = 'success';
//                if (($leads_data['status'] != $params['status']) ||
//                    (isset($params['reroute_to']) && !empty($params['reroute_to']))) {
//                    //Set current entry as old (set is_updated = 0)
//                    $lead_status_data = array('is_updated' => 0);
//                    $response1 = $this->Lead->update_lead_data($where, $lead_status_data, $table);
//
//                    if ($response1['status'] == 'success') {
//                        //Create new entry in table Lead Assign with changed status.
//
//                        /****************************************************************
//                         * Update Lead Status
//                         *****************************************************************/
//                        $lead_status_data = array(
//                            'lead_id' => $leads_data['lead_id'],
//                            'employee_id' => $leads_data['employee_id'],
//                            'employee_name' => $leads_data['employee_name'],
//                            'branch_id' => $leads_data['branch_id'],
//                            'district_id' => $leads_data['district_id'],
//                            'state_id' => $leads_data['state_id'],
//                            'zone_id' => $leads_data['zone_id'],
//                            'status' => $params['status'],
//                            'is_updated' => 1,
//                            'created_on' => date('y-m-d-H-i-s'),
//                            'created_by' => $params['logged_in_hrms_id'],
//                            'created_by_name' => $params['logged_in_emp_name']
//                        );
//
//                        /*****************************************************************/
//
//                        /****************************************************************
//                         * Reroute Lead
//                         *****************************************************************/
//                        if (isset($params['reroute_to']) && !empty($params['reroute_to'])) {
//                            $lead_status_data['employee_id'] = $params['reroute_to'];
//                            $lead_status_data['employee_name'] = $params['employee_name'];
//                            if ($leads_data['status'] != $params['status']) {
//                                $lead_status_data['status'] = $params['status'];
//                            } else {
//                                $lead_status_data['status'] = $leads_data['status'];
//                            }
//                            $result4['status'] = 'reroute';
//                            $title = 'Lead assigned';
//                            $description = 'Lead assigned';
//                            $priority = 'Normal';
//                            $notification_to = $params['reroute_to'];
//                            notification_log($title,$description,$priority,$notification_to);
//                        } else {
//                            $res = array('result' => False,
//                                'data' => array('Reroute to parameter missing.'));
//                            returnJson($res);
//                        }
//
//                        $result = $this->Lead->insert_lead_data($lead_status_data, Tbl_LeadAssign);
//                    }
//                }
//            }
//            }
//            $response2['status'] = '';
//            /*****************************************************************
//             * Update Lead Identification
//             *****************************************************************/
//            $all_lead_types = $this->config->item('lead_type');
//            if ($leads['lead_identification'] != $params['lead_identification'] &&
//                array_key_exists($params['lead_identification'], $all_lead_types)) {
//                $where = array('id' => $params['lead_id']);
//                $lead_identification_data = array(
//                    'lead_identification' => $params['lead_identification']
//                );
//                $response2 = $this->Lead->update_lead_data($where, $lead_identification_data, Tbl_Leads);
//            } else {
//                $res = array('result' => False,
//                    'data' => array('Unknown lead identification'));
//                returnJson($res);
//            }
//            /*****************************************************************/
//
//
//            if (($response1['status'] == 'error') || ($response2['status'] == 'error')) {
//                $res = array('result' => False,
//                    'data' => 'Failed to update lead information');
//                returnJson($res);
//            } else {
//                if ($params['status'] == 'FU') {
//                    if (isset($params['remind_on']) && !empty($params['remind_on']) &&
//                        isset($params['remind_to']) && !empty($params['remind_to']) &&
//                        isset($params['reminder_text']) && !empty($params['reminder_text'])) {
//                        $remindData = array(
//                            'lead_id' => $params['lead_id'],
//                            'remind_on' => date('y-m-d-H-i-s', strtotime($params['remind_on'])),
//                            'remind_to' => $params['remind_to'],
//                            'reminder_text' => $params['reminder_text']
//                        );
//                        //This will add entry into reminder scheduler for status (Interested/Follow up)
//                        $result2 = $this->Lead->add_reminder($remindData);
//                    } else {
//                        $res = array('result' => False,
//                            'data' => array('Invalid Request For Following Status'));
//                        returnJson($res);
//                    }
//                }
//                if ($params['status'] == 'AO') {
//                    if (isset($params['account_no']) && !empty($params['account_no'])) {
//                        if(strlen($params['account_no']) != 12){
//                            $res = array('result' => False,
//                                'data' => array('Invalid Request For Account Opened'));
//                            returnJson($res);
//                        }
//
//                        $api_res = verify_account($params['account_no']);
//                        $api_res = strip_tags($api_res);
//                        $api_res = json_decode($api_res,true);
//                        $responseData = array(
//                            'lead_id' => $params['lead_id'],
//                            'account_no'=>$params['account_no'],
//                            'response_data' => $api_res['data']
//                        );
//                        //This will add entry into cbs response for status (Account Opened)
//                        $this->Lead->insert_lead_data($responseData,Tbl_cbs);
//                    } else {
//                        $res = array('result' => False,
//                            'data' => array('Invalid Request For Account Opened'));
//                        returnJson($res);
//                    }
//                }
//            }
//
//
//            /*****************************************************************/
//
//            if ($result['status'] == 'success' && $result2['status'] == 'success') {
//                $res = array('result' => True,
//                    'data' => array('Lead Status Change and Reminder Saved Successfully'));
//                returnJson($res);
//            } elseif ($result['status'] == 'success' && $result4['status'] == 'reroute') {
//                $res = array('result' => True,
//                    'data' => array('Lead Status Changed Successfully and Lead rerouted successfully.'));
//                returnJson($res);
//            } elseif ($result['status'] == 'success' && $response2['status'] == 'success') {
//                $res = array('result' => True,
//                    'data' => array('Lead Status and Lead Identification Changed Successfully '));
//                returnJson($res);
//            } elseif ($response2['status'] == 'success') {
//                $res = array('result' => True,
//                    'data' => array('Lead Identification Changed Successfully '));
//                returnJson($res);
//            } elseif ($result['status'] == 'success') {
//                $res = array('result' => True,
//                    'data' => array('Lead Status Changed Successfully'));
//                returnJson($res);
//            } elseif ($result2['status'] == 'success' && $response2['status'] == 'success') {
//                $res = array('result' => True,
//                    'data' => array('Lead Identification and Reminder Saved Successfully'));
//                returnJson($res);
//            } elseif ($result2['status'] == 'success') {
//                $res = array('result' => True,
//                    'data' => array('Reminder Saved Successfully'));
//                returnJson($res);
//            } else {
//                $res = array('result' => True,
//                    'data' => array('Nothing To Update'));
//                returnJson($res);
//            }
//
//        } else {
//            $res = array('result' => False,
//                'data' => array('Invalid Request'));
//            returnJson($res);
//        }
//    }

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
                'data' => array('Invalid Request'));
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
            isset($params['state_id']) && isset($params['zone_id'])
//            && isset($params['status'])
            && isset($params['full_name'])
            && !empty($params['employee_id']) && !empty($params['lead_id']) && !empty($params['employee_name'])
            && !empty($params['branch_id']) && !empty($params['district_id']) && !empty($params['state_id'])
            && !empty($params['zone_id'])
//            && !empty($params['status'])
            && !empty($params['hrms_id']) && !empty($params['full_name'])) {
            $insertData = array();
            $assign_data = array(
                'employee_id' => $params['employee_id'],
                'employee_name' => $params['employee_name'],
                'branch_id' => $params['branch_id'],
                'district_id' => $params['district_id'],
                'state_id' => $params['state_id'],
                'zone_id' => $params['zone_id'],
                'status' => 'NC',
                'created_by' => $params['hrms_id'],
                'created_by_name' => $params['full_name']
            );

            $leads = explode(',', $params['lead_id']);
            if (is_array($leads)) {
                $leads_id = $leads;
            } else {
                $leads_id[] = $leads;
            }
            foreach ($leads_id as $key => $value) {
                $assign_data['lead_id'] = $value;
                $insertData = $assign_data;
                $response = $this->Lead->insert_lead_data($insertData,Tbl_LeadAssign);
                if($response['status']=='success'){

                    $action = 'list';
                    $select = array('lead.customer_name','product.title');
                    $table = Tbl_Leads.' AS lead';
                    $where = array('lead.id'=>$value);
                    $join[] = array('table' =>Tbl_Products.' AS product','on_condition'=>'product.id = lead.product_id','type'=>'');
                    $allData = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by=array(),$order_by=array());
                    //Add Notification
                    $title="New Lead Assigned";
                    $description="Lead for ".ucwords(strtolower($allData[0]['customer_name']))." assigned to you for ".ucwords(strtolower($allData[0]['title']));
                    $notification_to = $params['employee_id'];
                    $priority="Normal";
                    notification_log($title,$description,$priority,$notification_to);
                    //push notification
                    $emp_id = $params['employee_id'];
                    sendPushNotification($emp_id,$description,$title);
                }
            }
            $res = array('result' => True,
                'data' => 'Leads assigned successfully');
            returnJson($res);

        } else {
            $res = array('result' => False,
                'data' => array('Parameters missing'));
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
        $auth_response = call_external_url(HRMS_API_URL_AUTH.'username='.$user_id.'&password='.$password);
        $auth = json_decode($auth_response);
        if ($auth->DBK_LMS_AUTH->password == 'True') {
            // $records_response = call_external_url(HRMS_API_URL_GET_RECORD.$result->DBK_LMS_AUTH->username);
            $records_response = call_external_url(HRMS_API_URL_GET_RECORD.'hrms_id='.$auth->DBK_LMS_AUTH->username);
            $records = json_decode($records_response);
            $authorisation_key = random_number();
            $data = array('device_token' => $device_token,
                'employee_id' => $records->dbk_lms_emp_record1->EMPLID,
                'branch_id' => $records->dbk_lms_emp_record1->deptid,
                'zone_id' => $records->dbk_lms_emp_record1->dbk_state_id,
                'device_type' => $device_type,
                'authorisation_key'=>$authorisation_key
            );

            $this->Login_model->insert_login_log($data); // login log
            $fullname = array_map('trim', explode('.', $records->dbk_lms_emp_record1->name));
            if($fullname[0] == ''){
                $fullname1 = ucwords(strtolower(trim($fullname[1])));
            }else{
                $fullname1 = ucwords(strtolower(trim($fullname[0])));
            }
            $result['basic_info'] = array(
                'hrms_id' => $records->dbk_lms_emp_record1->EMPLID,
                'dept_id' => $records->dbk_lms_emp_record1->deptid,
                'dept_type_id' => $records->dbk_lms_emp_record1->dbk_dept_type,
                'dept_type_name' => $records->dbk_lms_emp_record1->dept_discription,
                'branch_id' => $records->dbk_lms_emp_record1->deptid,
                'district_id' => $records->dbk_lms_emp_record1->district,
                'state_id' => $records->dbk_lms_emp_record1->state,
                'zone_id' => $records->dbk_lms_emp_record1->dbk_state_id,
                'full_name' => $fullname1,
                'supervisor_id' => $records->dbk_lms_emp_record1->supervisor,
                'designation_id' => $records->dbk_lms_emp_record1->designation_id,
                'designation_name' => $records->dbk_lms_emp_record1->designation_descr,
                'mobile' => $records->dbk_lms_emp_record1->phone,
                'email_id' => $records->dbk_lms_emp_record1->email,
                'designation' => get_designation($records->dbk_lms_emp_record1->designation_id)
            );

            $hrms_id = $records->dbk_lms_emp_record1->EMPLID;

            $action = 'count';
            $table = Tbl_Notification . ' as n';
            $select = array('n.*');
            $unread_where = array('n.notification_to' => $hrms_id, 'n.is_read' => 0);
//            $order_by = "n.priority ASC";
            $leads['unread_notification'] = $this->notification->get_notifications($action, $select, $unread_where, $table, $join = array(), $order_by='');

            $read_where = array('n.notification_to' => $hrms_id, 'n.is_read' => 1);
            $leads['read_notification'] = $this->notification->get_notifications($action, $select, $read_where, $table, $join = array(), $order_by='');

            // employee
            if ($result['basic_info']['designation'] == 'EM') {
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
                    $where = array(Tbl_Leads . '.created_by' => $created_id, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'),'YEAR('.Tbl_Leads.'.created_on)' => date('Y'));
                    $leads['generated_mtd'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());

                    //Year till date
                    $where = array(Tbl_Leads . '.created_by' => $created_id);
                    $yr_start_date=date('Y').'-04-01 00:00:00';
                    $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
                    $where[Tbl_Leads.".created_on >='".$yr_start_date."' AND ".Tbl_Leads.".created_on <='".$yr_end_date."'"] = NULL;

                    $leads['generated_ytd'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());

                    //For converted leads Count
                    $table = Tbl_LeadAssign;

                    //Month till date
                    $where = array(Tbl_LeadAssign . '.employee_id' => $created_id, Tbl_LeadAssign . '.status' => 'Converted', Tbl_LeadAssign . '.is_deleted' => 0,Tbl_LeadAssign.'.is_updated' => 1, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'),'YEAR('.Tbl_LeadAssign.'.created_on)' => date('Y'));
                    $leads['converted_mtd'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());


                    //Year till date
                    $where = array(Tbl_LeadAssign . '.employee_id' => $created_id, Tbl_LeadAssign . '.status' => 'Converted', Tbl_LeadAssign . '.is_deleted' => 0,Tbl_LeadAssign.'.is_updated' => 1);
                    $yr_start_date=date('Y').'-04-01 00:00:00';
                    $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
                    $where[Tbl_LeadAssign.".created_on >='".$yr_start_date."' AND ".Tbl_LeadAssign.".created_on <='".$yr_end_date."'"] = NULL;

                    $leads['converted_ytd'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());

                    //For assigned leads Count
                    $table = Tbl_LeadAssign;

                    //Year till date
                    $where = array(Tbl_LeadAssign . '.employee_id' => $created_id, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0,Tbl_LeadAssign . '.view_status' => 0, 'DATEDIFF( CURDATE( ) , ' . Tbl_LeadAssign . '.created_on) <=' => Elapsed_day);
                    $yr_start_date=date('Y').'-04-01 00:00:00';
                    $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
                   // $where[Tbl_LeadAssign.".created_on >='".$yr_start_date."' AND ".Tbl_LeadAssign.".created_on <='".$yr_end_date."'"] = NULL;

                    $leads['assigned_leads'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());
                }

            }
            // BM
            if ($result['basic_info']['designation'] == 'BM') {
                if (isset($result['basic_info']['branch_id']) && $result['basic_info']['branch_id'] != '') {
                    $branch_id = $result['basic_info']['branch_id'];
                    $type = 'BM';
                    $final = $this->countnew($type, $branch_id, $records->dbk_lms_emp_record1->DBK_LMS_COLL);

                    $leads['generated_converted'] = $final;
                    //for assigned lead
//                    $where_assigned_Array = array('branch_id' => $branch_id, 'is_updated' => 1,
//                        'YEAR(created_on)' => date('Y'), 'DATEDIFF( CURDATE( ) , created_on) <=' => Elapsed_day);

                    $where_assigned_Array = "(status='AO' OR status='NI' AND branch_id =".$branch_id.") 
                     AND (is_updated = 1 AND is_deleted = 0 AND DATEDIFF( CURDATE( ) , created_on) <=".Elapsed_day.")";
                }
                $leads['assigned_leads'] = $this->Lead->get_assigned_leads($where_assigned_Array);
                $action = 'count';
                $select = array();
                $table = Tbl_Leads;
                $where = array(Tbl_Leads . '.branch_id' => $result['basic_info']['branch_id'], Tbl_LeadAssign . '.lead_id' => NULL, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'));
                $join[] = array('table' => Tbl_LeadAssign, 'on_condition' => Tbl_LeadAssign . '.lead_id = ' . Tbl_Leads . '.id', 'type' => 'left');
                $leads['un_assigned_leads'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by = array(), $order_by = array());
            }
            //ZM
            if ($result['basic_info']['designation'] == 'ZM') {
                if (isset($result['basic_info']['zone_id']) && $result['basic_info']['zone_id'] != '') {
                    $zone_id = $result['basic_info']['zone_id'];
                    $type = 'ZM';
                    $final = $this->countnew($type, $zone_id, $records->dbk_lms_emp_record1->DBK_LMS_COLL);
                    $leads['generated_converted'] = $final;
                }
            }
            // GM
            if ($result['basic_info']['designation'] == 'GM') {
                $type = 'GM';
                $final = $this->countnew($type, '', $records->dbk_lms_emp_record1->DBK_LMS_COLL);
                $leads['generated_converted'] = $final;
            }
            $result = array(
                "result" => True,
                "data" => ['count' => $leads, 'basic_info' => $result['basic_info'],'authorisation_key'=>$authorisation_key]
            );
            returnJson($result);
        } else {
            $err['result'] = false;
            $err['data'] = "Invalid login details.Kindly contact HRMS Admin.";
            returnJson($err);
        }
    }

    private function countnew($type, $ids, $result)
    {
        switch ($type) {
            case 'BM':
                $where_month_Array = array('branch_id' => $ids);
                $where_year_Array = array('branch_id' => $ids);

                $yr_start_date=date('Y').'-04-01 00:00:00';
                $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
                $where_year_Array["created_on >='".$yr_start_date."' AND created_on <='".$yr_end_date."'"] = NULL;
                // $year_where['YEAR(l.created_on)'] = date('Y');
                $where_month_Array['MONTH(created_on)'] = date('m'); //Month till date filter
                $where_month_Array['YEAR(created_on)'] = date('Y');

                $generated['monthly_generated_leads'] = $this->Lead->get_generated_lead_bm_zm($where_month_Array);
                $generated['yearly_generated_leads'] = $this->Lead->get_generated_lead_bm_zm($where_year_Array);
                $generated_key_value = array();
                $generated_key_value_year = array();
                $final = array();
                foreach ($generated['monthly_generated_leads'] as $k => $v) {
                    $generated_key_value[$v['created_by']] = $v['total'];
                }
                foreach ($generated['yearly_generated_leads'] as $k => $v) {
                    $generated_key_value_year[$v['created_by']] = $v['total'];
                }
                foreach ($result as $key => $val) {
                    if (!array_key_exists($val->DESCR10, $generated_key_value)) {
                        $push_generated = array(
                            'created_by' => $val->DESCR10,
                            'created_by_name' => ucwords(strtolower($val->DESCR30)),
                            'total_generated_mtd' => 0,
                            'total_generated_ytd' => 0);
                    } else {
                        $push_generated = array(
                            'created_by' => $val->DESCR10,
                            'created_by_name' => ucwords(strtolower($val->DESCR30)),
                            'total_generated_mtd' => $generated_key_value[$val->DESCR10],
                            'total_generated_ytd' => $generated_key_value_year[$val->DESCR10]);
                    }
                    $final[$val->DESCR10] = $push_generated;
                }
                foreach ($final as $id => $value) {

                    $where_month_Array = array('employee_id' => $value['created_by'], 'status' => 'converted');
                    $where_year_Array = array('employee_id' => $value['created_by'],'status' => 'converted');
                    $yr_start_date=date('Y').'-04-01 00:00:00';
                    $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
                    $where_year_Array["created_on >='".$yr_start_date."' AND created_on <='".$yr_end_date."'"] = NULL;
                    // $year_where['YEAR(l.created_on)'] = date('Y');
                    $where_month_Array['MONTH(created_on)'] = date('m'); //Month till date filter
                    $where_month_Array['YEAR(created_on)'] = date('Y');
                    $converted = $this->Lead->get_converted_lead_bm_zm($where_month_Array);
                    $converted_yearly = $this->Lead->get_converted_lead_bm_zm($where_year_Array);
                    if (empty($converted)) {
                        $converted = 0;
                    }
                    if (empty($converted_yearly)) {
                        $converted_yearly = 0;
                    }
                    $final[$value['created_by']]['total_converted_mtd'] = $converted;
                    $final[$value['created_by']]['total_converted_ytd'] = $converted_yearly;
                }
                $refinal = array_values($final);
                return $refinal;
                break;

            case 'ZM':
                $where_month_Array = array('zone_id' => $ids);

                $where_year_Array = array('zone_id' => $ids);

                $yr_start_date=date('Y').'-04-01 00:00:00';
                $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
                $where_year_Array["created_on >='".$yr_start_date."' AND created_on <='".$yr_end_date."'"] = NULL;
                // $year_where['YEAR(l.created_on)'] = date('Y');
                $where_month_Array['MONTH(created_on)'] = date('m'); //Month till date filter
                $where_month_Array['YEAR(created_on)'] = date('Y');

                $generated['monthly_generated_leads'] = $this->Lead->get_generated_lead_bm_zm($where_month_Array);
                $generated['yearly_generated_leads'] = $this->Lead->get_generated_lead_bm_zm($where_year_Array);
                $generated_key_value = array();
                $generated_key_value_year = array();
                $final = array();
                foreach ($generated['monthly_generated_leads'] as $k => $v) {
                    $generated_key_value[$v['branch_id']] = $v['total'];
                }
                foreach ($generated['yearly_generated_leads'] as $k => $v) {
                    $generated_key_value_year[$v['branch_id']] = $v['total'];
                }
                foreach ($result as $key => $val) {
                    if (!array_key_exists($val->DESCR10, $generated_key_value)) {
                        $push_generated = array(
                            'created_by' => $val->DESCR10,
                            'created_by_name' => ucwords(strtolower($val->DESCR30)),
                            'total_generated_mtd' => 0,
                            'total_generated_ytd' => 0);
                    } else {
                        $push_generated = array(
                            'created_by' => $val->DESCR10,
                            'created_by_name' => ucwords(strtolower($val->DESCR30)),
                            'total_generated_mtd' => $generated_key_value[$val->DESCR10],
                            'total_generated_ytd' => $generated_key_value_year[$val->DESCR10]);
                    }
                    $final[$val->DESCR10] = $push_generated;
                }
                //for converted
                foreach ($final as $id => $value) {

                    $where_month_Array = array('branch_id' => $value['created_by'],'status' => 'converted','is_updated'=>1,'is_deleted'=>0);
                    $where_year_Array = array('branch_id' => $value['created_by'],'status' => 'converted','is_updated'=>1,'is_deleted'=>0);
                    $yr_start_date=date('Y').'-04-01 00:00:00';
                    $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
                    $where_year_Array["created_on >='".$yr_start_date."' AND created_on <='".$yr_end_date."'"] = NULL;
                    // $year_where['YEAR(l.created_on)'] = date('Y');
                    $where_month_Array['MONTH(created_on)'] = date('m'); //Month till date filter
                    $where_month_Array['YEAR(created_on)'] = date('Y');
                    $converted = $this->Lead->get_converted_lead_bm_zm($where_month_Array);
                    $converted_yearly = $this->Lead->get_converted_lead_bm_zm($where_year_Array);
                    if (empty($converted)) {
                        $converted = 0;
                    }
                    if (empty($converted_yearly)) {
                        $converted_yearly = 0;
                    }
                    $final[$value['created_by']]['total_converted_mtd'] = $converted;
                    $final[$value['created_by']]['total_converted_ytd'] = $converted_yearly;
                }
                $refinal = array_values($final);
                return $refinal;
                break;

            case 'GM':
                $where_generated_Array = array('zone_id !=' => NULL);
                $where_year_Array = array('zone_id !=' => NULL);
                $yr_start_date=date('Y').'-04-01 00:00:00';
                $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
                $where_year_Array["created_on >='".$yr_start_date."' AND created_on <='".$yr_end_date."'"] = NULL;
                // $year_where['YEAR(l.created_on)'] = date('Y');
                $where_generated_Array['MONTH(created_on)'] = date('m'); //Month till date filter
                $where_generated_Array['YEAR(created_on)'] = date('Y');
                $generated['generated_leads'] = $this->Lead->get_generated_lead_bm_zm($where_generated_Array);
                $generated['yearly_generated_leads'] = $this->Lead->get_generated_lead_bm_zm($where_year_Array);
                $generated_key_value = array();
                $generated_key_value_year = array();
                $final = array();
                foreach ($generated['generated_leads'] as $k => $v) {
                    $generated_key_value[$v['zone_id']] = $v['total'];
                }
                foreach ($generated['yearly_generated_leads'] as $k => $v) {
                    $generated_key_value_year[$v['zone_id']] = $v['total'];
                }
                foreach ($result as $key => $val) {
                    if (!array_key_exists($val->DESCR10, $generated_key_value)) {
                        $push_generated = array(
                            'created_by' => $val->DESCR10,
                            'created_by_name' => ucwords(strtolower($val->DESCR30)),
                            'total_generated_mtd' => 0,
                            'total_generated_ytd' => 0);
                    } else {
                        $push_generated = array(
                            'created_by' => $val->DESCR10,
                            'created_by_name' => ucwords(strtolower($val->DESCR30)),
                            'total_generated_mtd' => $generated_key_value[$val->DESCR10],
                            'total_generated_ytd' => $generated_key_value_year[$val->DESCR10]);
                    }
                    $final[$val->DESCR10] = $push_generated;
                }
                //for converted
                foreach ($final as $id => $value) {

                    $where_month_Array = array('zone_id' => $value['created_by'],'status' => 'converted','is_updated'=>1,'is_deleted'=>0);
                    $where_year_Array = array('zone_id' => $value['created_by'],'status' => 'converted','is_updated'=>1,'is_deleted'=>0);
                    $yr_start_date=date('Y').'-04-01 00:00:00';
                    $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
                    $where_year_Array["created_on >='".$yr_start_date."' AND created_on <='".$yr_end_date."'"] = NULL;
                    // $year_where['YEAR(l.created_on)'] = date('Y');
                    $where_month_Array['MONTH(created_on)'] = date('m'); //Month till date filter
                    $where_month_Array['YEAR(created_on)'] = date('Y');
                    $converted = $this->Lead->get_converted_lead_bm_zm($where_month_Array);
                    $converted_yearly = $this->Lead->get_converted_lead_bm_zm($where_year_Array);
                    if (empty($converted)) {
                        $converted = 0;
                    }
                    if (empty($converted_yearly)) {
                        $converted_yearly = 0;
                    }
                    $final[$value['created_by']]['total_converted_mtd'] = $converted;
                    $final[$value['created_by']]['total_converted_ytd'] = $converted_yearly;
                }
                $refinal = array_values($final);
                return $refinal;
                break;

        }
    }

    /**
     * notification_list
     * gives the list of notifications order by priority
     * @author Gourav Thatoi
     */
    public function notification_list_post()
    {
        $params = $this->input->post();
        if (isset($params['hrms_id']) && !empty($params['hrms_id'])) {
            $action = 'list';
            $hrms_id = $params['hrms_id'];
            $table = Tbl_Notification;
            $select = array('*');
            $unread_where = array('notification_to' => $hrms_id, 'is_read' => 0);
            $result['unread'] = $this->notification->get_notifications($action, $select, $unread_where, $table, $join = array(), $order_by='');
            $update_data = array('is_read'=>1);
            $whereArray = array('notification_to'=>$hrms_id,'is_read'=>0);

            $this->Lead->update($whereArray,$table,$update_data);

//            $read_where = array('notification_to' => $hrms_id, 'is_read' => 1);
//            $result['read'] = $this->notification->get_notifications($action, $select, $read_where, $table, $join = array(), $order_by='');

            $res = array('result' => True,
                'data' => $result);
            returnJson($res);
        }
        $res = array('result' => False,
            'data' => array('Missing parameter.'));
        returnJson($res);
    }

    /**
     * assigned_leads_status
     * lead performance assigned leads count status wise
     * @author Gourav Thatoi
     */
    public function assigned_leads_status_post()
    {
        $result = array();
        $response = array();
        $params = $this->input->post();
        if (isset($params) && !empty($params) && isset($params['id']) && !empty($params['id'])
            && isset($params['lead_source']) && !empty($params['lead_source'])
            && isset($params['designation_name']) && !empty($params['designation_name'])) {

            $status = $this->config->item('lead_status');
            $id = $params['id'];
            $designation_type = $params['designation_name'];
            $lead_source = $params['lead_source'];
            //Building common parameters
            $action = 'count';
            $table = Tbl_Leads . ' as l';
            $where = array('la.is_deleted' => 0, 'la.is_updated' => 1);
            $join = array();
            $join[] = array('table' => Tbl_LeadAssign . ' as la', 'on_condition' => 'l.id = la.lead_id', 'type' => '');

            //User Level conditions
            if (!empty($designation_type) && $designation_type == 'GM') {
                $where['la.zone_id'] = $id;
            }
            if (!empty($designation_type) && $designation_type == 'ZM') {
                $where['la.branch_id'] = $id;
            }
            if (!empty($designation_type) && $designation_type == 'BM') {
                $where['la.employee_id'] = $id;
            }
            if (!empty($designation_type) && $designation_type == 'EM') {
                $where['la.employee_id'] = $id;
            }

            if (!empty($lead_source)) {
                $where['l.lead_source'] = $lead_source;
            }
            $year_where['YEAR(la.created_on)'] = date('Y');
            $month_where['MONTH(la.created_on)'] = date('m');
            if (!empty($status)) {
                foreach ($status as $key => $value) {
                    $where['status'] = $key;

                    //This Year Assigned
                    $year_where = array_merge($year_where, $where);

                    //This Month Assigned
                    $month_where = array_merge($month_where, $where);

                    $result['Month'] = $this->Lead->get_leads($action, $table, '', $month_where, $join, '', '');
                    $result['Year'] = $this->Lead->get_leads($action, $table, '', $year_where, $join, '', '');
                    $result['status'] = $value;
                    $response[] = $result;
                }
            }
            $res = array('result' => True,
                'data' => $response);
            returnJson($res);
        }
        $res = array('result' => False,
            'data' => array('Missing parameter.'));
        returnJson($res);

    }

    public function refresh_dashboardnew_post()
    {
        $params = $this->input->post();
        if (!empty($params) && isset($params['hrms_id']) && !empty($params['hrms_id'])) {

            // $records_response = call_external_url(HRMS_API_URL_GET_RECORD.$result->DBK_LMS_AUTH->username);
            $records_response = call_external_url(HRMS_API_URL_GET_RECORD.'hrms_id='.$params['hrms_id']);
            $records = json_decode($records_response);
            $fullname = array_map('trim', explode('.', $records->dbk_lms_emp_record1->name));
            if($fullname[0] == ''){
                $fullname1 = ucwords(strtolower(trim($fullname[1])));
            }else{
                $fullname1 = ucwords(strtolower(trim($fullname[0])));
            }
            $result['basic_info'] = array(
                'hrms_id' => $records->dbk_lms_emp_record1->EMPLID,
                'dept_id' => $records->dbk_lms_emp_record1->deptid,
                'dept_type_id' => $records->dbk_lms_emp_record1->dbk_dept_type,
                'dept_type_name' => $records->dbk_lms_emp_record1->dept_discription,
                'branch_id' => $records->dbk_lms_emp_record1->deptid,
                'district_id' => $records->dbk_lms_emp_record1->district,
                'state_id' => $records->dbk_lms_emp_record1->state,
                'zone_id' => $records->dbk_lms_emp_record1->dbk_state_id,
                'full_name' => $fullname1,
                'supervisor_id' => $records->dbk_lms_emp_record1->supervisor,
                'designation_id' => $records->dbk_lms_emp_record1->designation_id,
                'designation_name' => $records->dbk_lms_emp_record1->designation_descr,
                'mobile' => $records->dbk_lms_emp_record1->phone,
                'email_id' => $records->dbk_lms_emp_record1->email,
                'designation' => get_designation($records->dbk_lms_emp_record1->designation_id)
            );

            $hrms_id = $records->dbk_lms_emp_record1->EMPLID;

            $action = 'count';
            $table = Tbl_Notification . ' as n';
            $select = array('n.*');
            $unread_where = array('n.notification_to' => $hrms_id, 'n.is_read' => 0);
//            $order_by = "n.priority ASC";
            $leads['unread_notification'] = $this->notification->get_notifications($action, $select, $unread_where, $table, $join = array(), $order_by='');

            $read_where = array('n.notification_to' => $hrms_id, 'n.is_read' => 1);
            $leads['read_notification'] = $this->notification->get_notifications($action, $select, $read_where, $table, $join = array(), $order_by='');

            // employee
            if ($result['basic_info']['designation'] == 'EM') {
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
                    $where = array(Tbl_Leads . '.created_by' => $created_id, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'),'YEAR('.Tbl_Leads.'.created_on)' => date('Y'));
                    $leads['generated_mtd'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());

                    //Year till date
                    $where = array(Tbl_Leads . '.created_by' => $created_id);
                    $yr_start_date=date('Y').'-04-01 00:00:00';
                    $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
                    $where[Tbl_Leads.".created_on >='".$yr_start_date."' AND ".Tbl_Leads.".created_on <='".$yr_end_date."'"] = NULL;

                    $leads['generated_ytd'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());

                    //For converted leads Count
                    $table = Tbl_LeadAssign;

                    //Month till date
                    $where = array(Tbl_LeadAssign . '.employee_id' => $created_id, Tbl_LeadAssign . '.status' => 'Converted', Tbl_LeadAssign . '.is_deleted' => 0,Tbl_LeadAssign.'.is_updated' => 1, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'),'YEAR('.Tbl_LeadAssign.'.created_on)' => date('Y'));
                    $leads['converted_mtd'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());


                    //Year till date
                    $where = array(Tbl_LeadAssign . '.employee_id' => $created_id, Tbl_LeadAssign . '.status' => 'Converted', Tbl_LeadAssign . '.is_deleted' => 0,Tbl_LeadAssign.'.is_updated' => 1);
                    $yr_start_date=date('Y').'-04-01 00:00:00';
                    $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
                    $where[Tbl_LeadAssign.".created_on >='".$yr_start_date."' AND ".Tbl_LeadAssign.".created_on <='".$yr_end_date."'"] = NULL;

                    $leads['converted_ytd'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());

                    //For assigned leads Count
                    $table = Tbl_LeadAssign;

                    //Year till date
                    $where = array(Tbl_LeadAssign . '.employee_id' => $created_id, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0,Tbl_LeadAssign . '.view_status' => 0, 'DATEDIFF( CURDATE( ) , ' . Tbl_LeadAssign . '.created_on) <=' => Elapsed_day);
                    $leads['assigned_leads'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());
                }

            }
            // BM
            if ($result['basic_info']['designation'] == 'BM') {
                if (isset($result['basic_info']['branch_id']) && $result['basic_info']['branch_id'] != '') {
                    $branch_id = $result['basic_info']['branch_id'];
                    $type = 'BM';
                    $final = $this->countnew($type, $branch_id, $records->dbk_lms_emp_record1->DBK_LMS_COLL);

                    $leads['generated_converted'] = $final;
                    //for assigned lead
//                    $where_assigned_Array = array('branch_id' => $branch_id, 'is_updated' => 1,
//                        'YEAR(created_on)' => date('Y'), 'DATEDIFF( CURDATE( ) , created_on) <=' => Elapsed_day);
                    $where_assigned_Array = "(status='AO' OR status='NI' AND branch_id =".$branch_id.") 
                     AND (is_updated = 1 AND is_deleted = 0  AND DATEDIFF( CURDATE( ) , created_on) <=".Elapsed_day.")";
                }
                $leads['assigned_leads'] = $this->Lead->get_assigned_leads($where_assigned_Array);
                $action = 'count';
                $select = array();
                $table = Tbl_Leads;
                $where = array(Tbl_Leads . '.branch_id' => $result['basic_info']['branch_id'], Tbl_LeadAssign . '.lead_id' => NULL, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'), 'DATEDIFF( CURDATE( ) , ' . Tbl_Leads . '.created_on) <=' => Elapsed_day);
                $join[] = array('table' => Tbl_LeadAssign, 'on_condition' => Tbl_LeadAssign . '.lead_id = ' . Tbl_Leads . '.id', 'type' => 'left');
                $leads['un_assigned_leads'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by = array(), $order_by = array());
            }
            //ZM
            if ($result['basic_info']['designation'] == 'ZM') {
                if (isset($result['basic_info']['zone_id']) && $result['basic_info']['zone_id'] != '') {
                    $zone_id = $result['basic_info']['zone_id'];
                    $type = 'ZM';
                    $final = $this->countnew($type, $zone_id, $records->dbk_lms_emp_record1->DBK_LMS_COLL);
                    $leads['generated_converted'] = $final;
                }
            }
            // GM
            if ($result['basic_info']['designation'] == 'GM') {
                $type = 'GM';
                $final = $this->countnew($type, '', $records->dbk_lms_emp_record1->DBK_LMS_COLL);
                $leads['generated_converted'] = $final;
            }
            $result = array(
                "result" => True,
                "data" => ['count' => $leads, 'basic_info' => $result['basic_info']]
            );
            returnJson($result);
        } else {
            $res = array('result' => False,
                'data' => array('Invalid Access'));
            returnJson($res);
        }
    }

    /**
     * update_notification_count
     * updates notification count if one notifiaction is read
     * @param hrms_id , notification id
     * @return json
     * @author Gourav Thatoi
     */
    public function update_notification_count_post()
    {
        $params = $this->input->post();
        if (isset($params['hrms_id']) && !empty($params['hrms_id']) &&
            isset($params['notification_id']) && !empty($params['notification_id'])) {
            $hrms_id = $params['hrms_id'];
            $table = Tbl_Notification . ' as n';
            $where = array('n.notification_to' => $hrms_id, 'id' => $params['notification_id']);
            $data = array('is_read' => 1);
            $this->Lead->update($where, $table, $data);

            $action = 'count';
            $unread_where = array('n.notification_to' => $hrms_id, 'n.is_read' => 0);
            $result['unread'] = $this->notification->get_notifications($action, '', $unread_where, $table, $join = array(), '');

            $res = array('result' => True,
                'data' => array($result));
            returnJson($res);
        } else {

        }
    }

    /**
     * check_account_no
     * checks account number true or not if true inserts in the database
     * @param lead_id , account_no
     * @return json
     * @author Gourav Thatoi
     */
    public function check_account_no_post()
    {
        $params = $this->input->post();
        if (isset($params['lead_id']) && !empty($params['lead_id']) &&
            isset($params['account_no']) && !empty($params['account_no'])) {
            $url = '';
//            $result = call_external_url($url);
            $result['result'] = True;
            if ($result['result'] == True) {

                $table = Tbl_Leads;
                $where = array('id' => $params['lead_id']);
                $data = array('opened_account_no' => $params['account_no']);
                $update = $this->Lead->update_lead_data($where, $data, $table);
                if (isset($update['affected_rows']) && $update['affected_rows'] > 0) {
                    $result = array('result' => True,
                        'data' => array('Account number ' . $params['account_no'] . ' exists.'));
                    returnJson($result);
                }
                $res = array('result' => False,
                    'data' => array('Lead id does not exist.'));
                returnJson($res);
            }

        } else {
            $res = array('result' => False,
                'data' => array('Parameters missing.'));
            returnJson($res);
        }
    }

    /**
     * update_lead_status
     * Only for assigned lead list able to change lead status / Add Follow Up details
     * @author Gourav Thatoi
     * @access public
     * @param empty
     * @return array
     */
    public function update_lead_status_by_em_post()
    {
        $params = $this->input->post();
        if (!empty($params) && isset($params['lead_id']) && !empty($params['lead_id']) &&
            isset($params['status']) && !empty($params['status']) &&
            isset($params['lead_identification']) && !empty($params['lead_identification'])
        ) {
            $result1['status'] = 'error' ;
            $result2['status'] = 'error' ;
            $result3['status'] = 'error';
            $result4['status'] = 'error';
            $action = 'list';
            $table = Tbl_LeadAssign;
            $select = array(Tbl_LeadAssign . '.*');
            $where = array(Tbl_LeadAssign . '.lead_id' => $params['lead_id'], Tbl_LeadAssign . '.is_updated' => 1);
            $leadsAssign = $this->Lead->get_leads($action, $table, $select, $where, $join = array(), $group_by = array(), $order_by = array());
            $leads_data = $leadsAssign[0];


            $drop_reason = array();
            if($params['status'] == 'NI' && isset($params['reason']) && !empty($params['reason'])){
                $drop_reason = $params['reason'];
            }
            //=========================== Reroute to other branch

                if ($params['reroute_to_own_branch'] == 0) {
                    if (!isset($params['branch_id']) || empty($params['branch_id']) ||
                        !isset($params['district_id']) || empty($params['district_id']) ||
                        !isset($params['state_id']) || empty($params['state_id'])
                    ) {
                        $res = array('result' => False,
                            'data' => array('State id or District id or Branch id missing.'));
                        returnJson($res);
                    }
                    $action = 'list';
                    $table = Tbl_Leads;
                    $select = array(Tbl_Leads . '.*');
                    $where = array(Tbl_Leads . '.id' => $params['lead_id']);
                    $leadsAssign = $this->Lead->get_leads($action, $table, $select, $where, $join = array(), $group_by = array(), $order_by = array());
                    $leads_data = $leadsAssign[0];
                    $id = $leads_data['id'];
                    $update_lead_data['reroute_from_branch_id'] = $leads_data['branch_id'];
                    $update_lead_data['state_id'] = $params['state_id'];
                    $update_lead_data['branch_id'] = $params['branch_id'];
                    $update_lead_data['district_id'] = $params['district_id'];
                    $date = date('Y-m-d H:i:s');
                    $update_lead_data['modified_on'] = $date;
                    $whereUpdate = array('id'=>$id);
                    $this->Lead->update($whereUpdate,Tbl_Leads,$update_lead_data);
                    $whereUpdate = array('lead_id' => $id);
                    $table = Tbl_LeadAssign;
                    if(empty($drop_reason)){
                        $data = array('is_updated'=>0);
                    }else{
                        $data = array('is_updated'=>0,'reason_for_drop'=>$drop_reason);
                    }
                    $this->Lead->update($whereUpdate, $table, $data);
                    $res = array('result' => TRUE,
                        'data' => array('Lead Reroute to Other Branch Successfully'));
                    returnJson($res);
                }else{
                    if (($leads_data['status'] != $params['status'])) {

                        //Set current entry as old (set is_updated = 0)
                        $lead_status_data = array('is_updated' => 0);
                        $response1 = $this->Lead->update_lead_data($where, $lead_status_data, $table);

                        if ($response1['status'] == 'success') {
                            //Create new entry in table Lead Assign with changed status.

                            /****************************************************************
                             * Update Lead Status
                             *****************************************************************/
                            $lead_status_data = array(
                                'lead_id' => $leads_data['lead_id'],
                                'employee_id' => $leads_data['employee_id'],
                                'employee_name' => $leads_data['employee_name'],
                                'branch_id' => $leads_data['branch_id'],
                                'district_id' => $leads_data['district_id'],
                                'state_id' => $leads_data['state_id'],
                                'zone_id' => $leads_data['zone_id'],
                                'status' => $params['status'],
                                'reason_for_drop' => $params['reason'],
                                'is_updated' => 1,
                                'created_on' => $leads_data['created_on'],
                                'created_by' => $leads_data['created_by'],
                                'created_by_name' => $leads_data['created_by_name'],
                                'modified_on' => date('y-m-d-H-i-s'),
                                'modified_by' => $leads_data['employee_id'],
                                'modified_by_name' => $leads_data['employee_name']
                            );
                            $result1 = $this->Lead->insert_lead_data($lead_status_data, Tbl_LeadAssign);

                        }

                        if ($params['status'] == 'FU') {
                                $action = 'list';
                                $table = Tbl_Leads;
                                $select = array(Tbl_Leads . '.*');
                                $where = array(Tbl_Leads . '.id' => $params['lead_id']);
                                $leadsAssigned = $this->Lead->get_leads($action, $table, $select, $where, $join = array(), $group_by = array(), $order_by = array());
                                $leads_info = $leadsAssigned[0];

                                if($leads_info['lead_source'] == 'Analytics'){

                                    if($leads_info['reroute_from_branch_id'] == '' || $leads_info['reroute_from_branch_id'] == NULL){

                                        $action = 'list';
                                        $select = array('map_with');
                                        $table = Tbl_Products;
                                        $where = array('id'=>$leads_info['product_id']);
                                        $product_mapped_with = $this->Lead->get_leads($action,$table,$select,$where,'','','');
                                        $product_mapped_with=$product_mapped_with[0]['map_with'];
                                        $whereArray = array('processing_center'=>$product_mapped_with,'branch_id'=>$leads_data['branch_id']);
                                        $routed_id = $this->Lead->check_mapping($whereArray);
                                        $branch_id = $leads_data['branch_id'];
                                        if(!is_array($routed_id)){
                                            $update_data['reroute_from_branch_id'] = $branch_id;
                                            $update_data['branch_id'] = $routed_id;
                                            $where = array('id'=>$params['lead_id']);
                                            $table = Tbl_Leads;
                                            $this->Lead->update_lead_data($where,$update_data,$table);
                                            $whereUpdate = array('lead_id'=>$params['lead_id']);
                                            $table = Tbl_LeadAssign;
                                            $data = array('is_updated'=>0);
                                            $this->Lead->update($whereUpdate,$table,$data);
                                        }

                                    }

                                }
                        }
                    }
                    /****************************************************************
                     * Reminder set for follow up status
                     *****************************************************************/
                    if ($params['status'] == 'FU') {
                        if (isset($params['remind_on']) && !empty($params['remind_on']) &&
                            isset($params['reminder_text']) && !empty($params['reminder_text'])) {
                            $remindData = array(
                                'lead_id' => $params['lead_id'],
                                'remind_on' => date('y-m-d-H-i-s', strtotime($params['remind_on'])),
                                'remind_to' => $leads_data['employee_id'],
                                'reminder_text' => $params['reminder_text']
                            );
                            //This will add entry into reminder scheduler for status (Interested/Follow up)
                            $result3 = $this->Lead->add_reminder($remindData);
                        } else {
                            $res = array('result' => False,
                                'data' => array('Invalid Request For Follow up Status'));
                            returnJson($res);
                        }
                    }
                    /****************************************************************
                     * Point distribution When status is converted
                     *****************************************************************/
                    if($params['status'] == 'Converted'){
                        $this->points_distrubution($params['lead_id']);
                    }

                    $cat_name = $params['category_title'];
                    $customer_name = $params['customer_name'];
                    $statusNotification = array('AO','NI');
                    if(in_array($params['status'],$statusNotification) || ($cat_name == 'Fee Income' && $params['status'] == 'DC')){
                        $title="Action Required";
                        $description="Lead for ".ucwords(strtolower($customer_name))." requires your action";
                        $notification_to = $leads_data['created_by'];
                        $priority="Normal";
                        notification_log($title,$description,$priority,$notification_to);
                        //push notification
                        $emp_id = $leads_data['created_by'];
                        sendPushNotification($emp_id,$description,$title);
                    }

                    /*****************************************************************
                     * Update Lead Identification
                     *****************************************************************/
                    if ($params['lead_identification'] != 'NA') {
                        $where = array('id' => $params['lead_id']);
                        $lead_identification_data = array(
                            'lead_identification' => $params['lead_identification']
                        );
                        $result2 = $this->Lead->update_lead_data($where, $lead_identification_data, Tbl_Leads);
                    }


                    //====================Reroute to same branch
                    if (isset($params['reroute_to']) && !empty($params['reroute_to']) &&
                        isset($params['reroute_to_name']) && !empty($params['reroute_to_name']))
                    {
                        //Set current entry as old (set is_updated = 0)
                        $lead_status_data = array('is_updated' => 0);
                        $response1 = $this->Lead->update_lead_data($where, $lead_status_data, $table);

                        if ($response1['status'] == 'success') {
                            //Create new entry in table Lead Assign with changed status.

                            /****************************************************************
                             * Update Lead Status
                             *****************************************************************/
                            $lead_status_data = array(
                                'lead_id' => $leads_data['lead_id'],
                                'employee_id' => $params['reroute_to'],
                                'employee_name' => $params['reroute_to_name'],
                                'branch_id' => $leads_data['branch_id'],
                                'district_id' => $leads_data['district_id'],
                                'state_id' => $leads_data['state_id'],
                                'zone_id' => $leads_data['zone_id'],
                                'status' => $params['status'],
                                'is_updated' => 1,
                                'created_on' => date('y-m-d-H-i-s'),
                                'created_by' => $leads_data['created_by'],
                                'created_by_name' => $leads_data['created_by_name']
                            );
                            if(!empty($drop_reason)){
                                $lead_status_data['reason_for_drop'] = $drop_reason;
                            }
                            $result4 = $this->Lead->insert_lead_data($lead_status_data, Tbl_LeadAssign);

                        }
                    }
                    if ($result1['status'] == 'success' && $result2['status'] == 'success' && $result3['status'] == 'success') {
                        $res = array('result' => True,
                            'data' => array('Lead Status and Lead Identification and Reminder Save Successfully'));
                        returnJson($res);
                    }elseif ($result1['status'] == 'success' && $result2['status'] == 'success') {
                        $res = array('result' => True,
                            'data' => array('Lead Status and Lead Identification Save Successfully'));
                        returnJson($res);
                    }elseif ($result1['status'] == 'success' && $result3['status'] == 'success') {
                        $res = array('result' => True,
                            'data' => array('Lead Status and Lead Identification Reminder Save Successfully'));
                        returnJson($res);
                    }elseif ($result2['status'] == 'success' && $result3['status'] == 'success') {
                        $res = array('result' => True,
                            'data' => array('Lead Identification and Reminder Save Successfully'));
                        returnJson($res);
                    }elseif($result1['status'] == 'success'){
                        $res = array('result' => True,
                            'data' => array('Lead Status Updated Successfully'));
                        returnJson($res);
                    }elseif($result2['status'] == 'success'){
                        $res = array('result' => True,
                            'data' => array('Lead Identification Updated Successfully'));
                        returnJson($res);
                    }elseif($result3['status'] == 'success'){
                        $res = array('result' => True,
                            'data' => array('Reminder Save Successfully'));
                        returnJson($res);
                    }elseif($result4['status'] == 'success'){
                        $res = array('result' => True,
                            'data' => array('Reroute Successfully'));
                        returnJson($res);
                    }else {
                        $res = array('result' => True,
                            'data' => array('Nothing To Update'));
                        returnJson($res);
                    }
                }
        }else{
            $res = array('result' => False,
                'data' => array('Invalid Request'));
            returnJson($res);
        }
   }

    private function insert_notification($lead_data){
        if(!empty($lead_data)){
            $this->load->model('Master_model','master');
            $productData = $this->master->view_product($lead_data['product_id']);

            $title = 'New lead added';
            $description = 'Lead For '.ucwords($lead_data['customer_name']).' submitted sucessfully';
            $priority = 'Normal';
            $notification_to = $lead_data['created_by'];
            return notification_log($title,$description,$priority,$notification_to);
        }
    }

    public function verify_account_post(){
        $params = $this->input->post();
        if(isset($params['lead_id']) && $params['lead_id'] !='' &&
            isset($params['account_no']) && strlen(trim($params['account_no'])) == 12){
            $api_res = verify_account(trim($params['account_no']));
            $api_res = strip_tags($api_res);
            $api_res = json_decode($api_res,true);
            if($api_res['status'] != 'False'){
                $acc_no = $params['account_no'];
                $cbs_res = $api_res['data'];
                $split_cbs_resp = explode('~',$cbs_res);
                $responseData = array(
                    'lead_id' => $params['lead_id'],
                    'account_no'=>$acc_no,
                    'response_data' => $api_res['data'],
                    'amount' => substr($split_cbs_resp[0], 3),
                    'customer_name' => $split_cbs_resp[1],
                    'customer_contact_no' => $split_cbs_resp[2],
                    'email_id' => $split_cbs_resp[3],
                );
                //This will add entry into cbs response for status (Account Opened)
                $this->Lead->insert_lead_data($responseData,Tbl_cbs);
                $table = Tbl_Leads;
                $where = array('id'=>$params['lead_id']);
                $data = array('opened_account_no'=>$acc_no);
                $this->Lead->update_lead_data($where,$data,$table);
                $res = array('result' => True,
                    'data' => array('Successfully Verified.'));
                returnJson($res);
            }
            $res = array('result' => False,
                'data' => array('Verification Failed'));
            returnJson($res);
        }
        $res = array('result' => False,
            'data' => array('Invalid Request'));
        returnJson($res);
    }

    private function points_distrubution($lead_id){

        $action = 'list';

        //Get Amount Details
        $table = Tbl_cbs.' as a';
        $select = array('a.*');
        $where  = array('a.lead_id' => $lead_id);
        $join = array();
        $amount_data = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());
        if(!empty($amount_data)){
            $amount = $amount_data[0]['amount'];
            $table = Tbl_Leads.' as l';
            $select = array('l.id','l.product_id','l.created_by','la.employee_id','mp.points','pd.generator_contrubution','pd.convertor_contrubution');
            $where  = array('l.id' => $lead_id,'la.is_deleted' => 0,'la.is_updated' => 1,'la.status' => 'Converted','mp.from_range <=' => $amount,'mp.to_range >=' => $amount,'pd.active' => 1);
            $join = array();
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
            $join[] = array('table' => Tbl_Products.' as p','on_condition' => 'l.product_id = p.id AND l.product_category_id = p.category_id','type' => '');
            $join[] = array('table' => Tbl_Manage_Points.' as mp','on_condition' => 'mp.product_id = p.id','type' => '');
            $join[] = array('table' => Tbl_Points_Distributor.' as pd','on_condition' => 'pd.product_id = p.id','type' => '');
            $leadData = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());
            if(!empty($leadData)){
                $data = array('lead_id' => $leadData[0]['id'],'product_id' => $leadData[0]['product_id']);
                //Generator Contribution
                $generator_data = array('employee_id' => $leadData[0]['created_by'],'points' => ($leadData[0]['generator_contrubution'] * $leadData[0]['points'] * 0.01),'role_as' => 'Generator');
                $generator_data = array_merge($generator_data,$data);
                //Convertor Contribution
                $convertor_data = array('employee_id' => $leadData[0]['employee_id'],'points' => ($leadData[0]['convertor_contrubution'] * $leadData[0]['points'] * 0.01),'role_as' => 'Convertor');
                $convertor_data = array_merge($convertor_data,$data);

                $this->db->insert(Tbl_Points,$generator_data);
                $this->db->insert(Tbl_Points,$convertor_data);
            }
        }
    }

    public function leads_count_by_leads_status_post()
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
                            $whereArray = array(Tbl_LeadAssign . '.employee_id' => $employee_id, 'status' => $key, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'), Tbl_LeadAssign . '.is_updated' => 1);
                            $result[$i]['Year'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');

                            $whereArray = array(Tbl_LeadAssign . '.employee_id' => $employee_id, 'status' => $key, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'), Tbl_LeadAssign . '.is_updated' => 1);
                            $result[$i]['Month'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');
                            $result[$i]['status'] = $value;
                            $i++;
                        }
//                        $action = 'count';
//                        $select = array();
//                        $table = Tbl_Leads;
//                        $join_assign[] = array('table' =>Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = '.Tbl_Leads.'.id ','type' => 'left');
//                        $whereYear = array($table . '.created_by' => $employee_id,'la.lead_id' => NULL, 'YEAR(' . $table . '.created_on)' => date('Y'));
//                        $whereMonth = array($table . '.created_by' => $employee_id,'la.lead_id' => NULL, 'MONTH(' . $table . '.created_on)' => date('m'));
//                        $unassigned_leads_count_month = $this->Lead->get_leads($action,$table,$select,$whereMonth,$join_assign,$group_by = array(),$order_by = array());
//                        $unassigned_leads_count_year = $this->Lead->get_leads($action,$table,$select,$whereYear,$join_assign,$group_by = array(),$order_by = array());
//                        $result[$i]['Year'] = $unassigned_leads_count_year;
//                        $result[$i]['Month'] = $unassigned_leads_count_month;
//                        $result[$i]['status'] = 'Unassigned Leads';
                        $whereArray = array(Tbl_LeadAssign . '.employee_id' => $employee_id,'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'), Tbl_LeadAssign . '.is_updated' => 1);
                        $total_assigned = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');
                        $whereArrayMonth = array(Tbl_LeadAssign . '.employee_id' => $employee_id,'MONTH(' . Tbl_Leads . '.created_on)' => date('m'), Tbl_LeadAssign . '.is_updated' => 1);
                        $total_assigned_month = $this->Lead->get_leads($action, $table, '', $whereArrayMonth, $join, '', '');
                    }
                    $res = array('result' => True,
                        'data' => $result,
                        'total_assigned' =>$total_assigned,
                        'total_assigned_month' =>$total_assigned_month);
                    returnJson($res);
                    break;
                case 'ZM':
                    $table = Tbl_LeadAssign;
                    $action = 'count';
                    $branch_id = $params['id'];

                    if (!empty($status)) {
                        $i = 0;
                        foreach ($status as $key => $value) {
                            $whereArray = array(Tbl_LeadAssign . '.branch_id' => $branch_id, 'status' => $key, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'), Tbl_LeadAssign . '.is_updated' => 1);
                            $result[$i]['Year'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');

                            $whereArray = array(Tbl_LeadAssign . '.branch_id' => $branch_id, 'status' => $key, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'), Tbl_LeadAssign . '.is_updated' => 1);
                            $result[$i]['Month'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');
                            $result[$i]['status'] = $value;
                            $i++;
                        }
                        $whereArray = array(Tbl_LeadAssign . '.branch_id' => $branch_id,'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'), Tbl_LeadAssign . '.is_updated' => 1);
                        $result['total_assigned'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');
                    }
                    $res = array('result' => True,
                        'data' => $result);
                    returnJson($res);
                    break;
                case 'GM':
                    $table = Tbl_LeadAssign;
                    $action = 'count';
                    $zone_id = $params['id'];

                    if (!empty($status)) {
                        $i = 0;
                        foreach ($status as $key => $value) {
                            $whereArray = array(Tbl_LeadAssign . '.zone_id' => $zone_id, 'status' => $key, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'), Tbl_LeadAssign . '.is_updated' => 1);
                            $result[$i]['Year'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');

                            $whereArray = array(Tbl_LeadAssign . '.zone_id' => $zone_id, 'status' => $key, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'), Tbl_LeadAssign . '.is_updated' => 1);
                            $result[$i]['Month'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');
                            $result[$i]['status'] = $value;
                            $i++;
                        }
                        $whereArray = array(Tbl_LeadAssign . '.zone_id' => $zone_id,'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'), Tbl_LeadAssign . '.is_updated' => 1);
                        $result['total_assigned'] = $this->Lead->get_leads($action, $table, '', $whereArray, $join, '', '');
                    }
                    $res = array('result' => True,
                        'data' => $result);
                    returnJson($res);
                    break;
            }
        }
        $error = array(
            "result" => False,
            "data" => array("Missing Parameters.")
        );
        returnJson($error);
    }

    public function generated_conversion_post()
    {
        $params = $this->input->post();
        if ((isset($params) && !empty($params) && isset($params['hrms_id']) && !empty($params['hrms_id']))) {
            $login_user = get_session();
            $action = 'count';
            $select = array();
            $join = array();
            //For Generated Leads Count
            $table = Tbl_Leads;

            //Year till date
            $where = array(Tbl_Leads . '.created_by' => $params['hrms_id']);
            $yr_start_date=date('Y').'-04-01 00:00:00';
            $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
            $where[Tbl_Leads.".created_on >='".$yr_start_date."'"] = NULL;
            $where[Tbl_Leads.".created_on <='".$yr_end_date."'"] = NULL;
            $result['total_generated'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by = array(), $order_by = array());

            //For Converted Leads Count
            $table = Tbl_LeadAssign;
            $join[] = array('table' => Tbl_Leads, 'on_condition' => Tbl_Leads . '.id = ' . Tbl_LeadAssign . '.lead_id', 'type' => '');
            //year till date
            $whereArray = array(Tbl_Leads . '.created_by' => $params['hrms_id'], Tbl_LeadAssign . '.status' => 'Converted', Tbl_LeadAssign . '.is_updated' => 1);
            $yr_start_date=date('Y').'-04-01 00:00:00';
            $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
            $whereArray[Tbl_Leads.".created_on >='".$yr_start_date."'"] = NULL;
            $whereArray[Tbl_Leads.".created_on <='".$yr_end_date."'"] = NULL;
            $result['total_converted'] = $this->Lead->get_leads($action, $table, $select, $whereArray, $join, $group_by = array(), $order_by = array());
            $res = array('result' => True,
                'data' => $result);
            returnJson($res);
        }
        $error = array(
            "result" => False,
            "data" => array("Missing Parameters.")
        );
        returnJson($error);
    }


}