<?php
/**
 * Created by PhpStorm.
 * User: webwerks
 * Date: 26/2/18
 * Time: 1:09 PM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Other_api extends REST_Controller
{
    /*
     * construct
     * constructor method
     * @author Mukesh Kurmi
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

            $params = $this->input->post();

            if(!isset($params['token'])){
                $response = array(
                    'result'=>False,
                    'data'=>array('Security Token is missing.')
                );
                returnJson($response);
            }else{
                $token = $params['token'];
                if($token != 'dena!@#lms07'){
                    $response = array(
                        'result'=>False,
                        'data'=>array('Invalid Security Token.')
                    );
                    returnJson($response);
                }
            }
    }

    /**
     * unassigned_leads_list
     * leads list filtered by lead generation date
     * @autor Mukesh Kurmi
     * @accss public
     * @return array
     */
    public function rapc_leads_list_post()
    {
        $params = $this->input->post();
        if (!empty($params) && isset($params['date']) && !empty($params['date'])) {
            $date = $params['date'];
            $previous_date = date('Y-m-d', strtotime('-10 days',strtotime($date)));
//            echo $previous_date;die;
//            $now = time();
//            $your_date = strtotime($date);
//            $datediff = $now - $your_date;
//            $days =  round($datediff / (60 * 60 * 24));
//            if($days >30){
//                $res = array('result' => False,
//                    'data' => array('Date not more than 30 days old from today'));
//                returnJson($res);
//            }
            $action = 'list';
            $select = array('lead.id','lead.customer_name','lead.contact_no','lead.created_by_branch_id','lead.branch_id as current_branch_id','lead.created_by','lead.reroute_from_branch_id','lead.created_on','products.title as product','product_category.title as category','lead.id');
            $table = Tbl_Leads.' AS lead';
            $join[] = array('table' =>Tbl_Products.' AS products','on_condition'=>'products.id = lead.product_id','type'=>'left');
            $join[] = array('table' =>Tbl_Category.' AS product_category','on_condition'=>'product_category.id = lead.product_category_id','type'=>'left');
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = lead.id','type' => 'LEFT');
            $where = array('la.is_updated=1 AND la.status IN ("FU")' => NULL);
            $where["date(lead.created_on) <='".$date."' AND date(lead.created_on) >='".$previous_date."' AND products.id IN(23,24,25,26,29,30)"] = NULL;
            $order_by = 'lead.created_on DESC';
            $generated_leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by=array(),$order_by);
            if(!empty($generated_leads)) {
                $res = array('result' => True,
                    'data' => $generated_leads);
                returnJson($res);
            }else{
                $res = array('result' => False,
                    'data' => array('No Records Found'));
                returnJson($res);
            }
        } else {
            $res = array('result' => False,
                'data' => array('Date is missing'));
            returnJson($res);
        }

    }
}
