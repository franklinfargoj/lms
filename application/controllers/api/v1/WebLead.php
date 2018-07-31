<?php
/**
 * Created by PhpStorm.
 * User: webwerks1
 * Date: 17/8/17
 * Time: 4:40 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class WebLead extends REST_Controller
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
        $explode = explode('/',$_SERVER['HTTP_USER_AGENT']);
        $method = $this->router->method;
        $authorised_methods = $this->config->item('authorised_methods');
        $this->load->model('Lead');
    }


    public function add_lead_post()
    {
        $params = $this->input->post();

        $lead_data['customer_name'] =  ucwords(strtolower($params['customer_name']));
        $lead_data['contact_no'] = $params['contact_no'];
        $lead_data['branch_id'] = $params['branch_id'];
        $lead_data['zone_id'] = zoneid($params['branch_id']);
        $lead_data['district_id'] = $params['district_id'];
        $lead_data['state_id'] = $params['state_id'];
        $lead_data['product_id'] = $params['product_id'];
        $lead_data['product_category_id'] = $params['product_category_id'];
        $lead_data['lead_ticket_range'] = $params['lead_ticket_range'];

        $lead_data['lead_source'] = 'enquiry';
        $lead_data['other_source'] = 'website';
        $lead_data['created_by_name'] = 'website';

        $error = array();

        if (!empty($params)) {

            foreach ($params as $k => $value) {
                $validation_error = '';
                if ($k == 'customer_name') {
                    $validation_error = '|callback_alpha_dash_space["Customer Name"]';
                }
                if ($k == 'contact_no') {
                    $validation_error = '|numeric';
                }
                $this->form_validation->set_rules($k,ucwords(str_replace('_',' ',$k)), 'required' . $validation_error);
                if ($this->form_validation->run() == FALSE) {
                    $error[] = form_error($k);
                    $result = array('result' => FALSE,'msg'=>$error);
                    returnJson($result);
                }
            }

            if ($this->form_validation->run() == TRUE) {
                // check for duplicate entry
                $whereEx = array(
                            'customer_name'=>ucwords(strtolower($params['customer_name'])),
                            'contact_no'=> $params['contact_no'],
                            'product_id'=> $params['product_id'],
                            'DATEDIFF(CURDATE(),created_on) <=' => 180
                            );

                $is_exsits = $this->Lead->is_exsits($whereEx);
                if($is_exsits){
                    $result = array('result' => FALSE,
                                    'msg' => array('Record Already Added'));
                    returnJson($result);
                }

                $lead_id = $this->Lead->add_leads($lead_data);
                if ($lead_id != FALSE) {
                    $branch_manager_id = $this->Lead->branch_manager_id($params['branch_id']);
                    $push_message = "New Lead is been Assigned to your branch through website.";
                    $title = 'New Lead Assigned to your branch';
                    // sendPushNotification($branch_manager_id,$push_message,$title);
                    $result = array('result' => TRUE,'msg'=>'Lead added through website.');
                    returnJson($result);
                } else {
                    $result = array('result' => FALSE,'msg' => array('Record not Added'));
                    returnJson($result);
                }
            }
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
}
