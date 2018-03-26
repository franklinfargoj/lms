<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cron extends CI_Controller
{
    /*
     * construct
     * constructor method
     * @author Ashok Jadhav (AJ)
     * @access private
     * @param none
     * @return void
     * 
     */
    function __construct()
    {
        // Initialization of class
        parent::__construct();
       is_cli() OR show_404();
        $this->load->model('Lead');
    }
function index(){
    echo "hello";
}
    /*
     * gm_consolidated_mail
     * Zone wise leads generated,converted,unassigned and pending count
     * @author Ashok Jadhav (AJ)
     * @access public
     * @param none
     * @return void
     * 
     */
    public function gm_consolidated_mail()
    {
        $cc =1;
        $GM_list = $this->Lead->get_employee_dump(array('hrms_id','name','designation','email_id','zone_id','zone_name'),array('designation_id' => 570701,'zone_id NOT IN(009999)' => NULL),array(),'employee_dump');
//        echo "<pre>";
//        print_r($GM_list);die;
        foreach ($GM_list as $k => $v) {
            $final = array();
            //For GENERAL MANAGER
            $general_manager = array('generated' => array(), 'converted' => array(), 'unassigned' => array(),'pending_before' => array(), 'pending' => array());
            $gm = $general_manager;
            $zone_list = $this->Lead->get_employee_dump(array('DISTINCT(zone_id) as zone_id', 'zone_name'), array('designation like' => '%ZONAL MANAGER%','supervisor_id'=>$v->hrms_id), array(), 'employee_dump');
//            echo "<pre>";
//        print_r($zone_list);die;
            $general_manager['generated'] = $this->get_leads(array('type' => 'generated', 'till' => 'mtd', 'user_type' => 'ZM'));
            $general_manager['converted'] = $this->get_leads(array('type' => 'converted', 'till' => 'mtd', 'user_type' => 'ZM'));
            $general_manager['unassigned'] = $this->get_leads(array('type' => 'unassigned', 'till' => '', 'user_type' => 'ZM'));
            $general_manager['pending_before']   = $this->get_leads(array('type'=>'pending_before','till'=>'','user_type'=>'ZM'));
            $general_manager['pending'] = $this->get_leads(array('type' => 'pending', 'till' => 'TAT', 'user_type' => 'ZM'));
//            echo "<pre>";
//        print_r($general_manager);//die;

            //$general_manager = call_user_func_array('array_merge', $general_manager);
            $general_manager = array_merge($general_manager);


            //$general_manager = array_merge($general_manager);
            $total = array();
//            echo "<pre>";
//            print_r($general_manager);
            foreach (array_keys($gm) as $key => $value) {
//                echo "<pre>";
//                print_r($general_manager[$value]);
                if(!empty($general_manager[$value])){
                    $total[$value] = array_column($general_manager[$value], $value, 'zone_id');
                }

            }
//        pe($total);
            $unique_zone_ids = array_unique(array_column($zone_list, 'zone_id'));
            //pe($unique_zone_ids);die;
            foreach ($zone_list as $key => $value) {


                    $final['general_manager'][$value->zone_id]['generated'] = isset($total['generated'][$value->zone_id]) ? $total['generated'][$value->zone_id] : 0;
                    $final['general_manager'][$value->zone_id]['converted'] = isset($total['converted'][$value->zone_id]) ? $total['converted'][$value->zone_id] : 0;
                    $final['general_manager'][$value->zone_id]['unassigned'] = isset($total['unassigned'][$value->zone_id]) ? $total['unassigned'][$value->zone_id] : 0;
                    $final['general_manager'][$value->zone_id]['pending_before'] = isset($total['pending_before'][$value->zone_id]) ? $total['pending_before'][$value->zone_id] : 0;
                    $final['general_manager'][$value->zone_id]['pending'] = isset($total['pending'][$value->zone_id]) ? $total['pending'][$value->zone_id] : 0;

                $final['general_manager'][$value->zone_id]['zone_id'] = $value->zone_id;
                $final['general_manager'][$value->zone_id]['zone_name'] = $value->zone_name;
            }
            //echo "<pre>";
          // print_r($final['general_manager']);die;
            //For GENERAL MANAGER

            $attachment_file = $this->export_to_excel('gm_consolidated_mail', $final['general_manager']);
            $attachment_file = array($attachment_file);
            $to = array('email' => $v->email_id,'name' => $v->name);
            $subject = 'Pending Leads under Dena Sampark for follow up';
            $message = $this->gm_msg();
            sendMail($to, $subject, $message, $attachment_file,$cc);
        }
    }

     /*
     * zm_consolidated_mail
     * Branch wise leads generated,converted,unassigned and pending count
     * @author Ashok Jadhav (AJ)
     * @access public
     * @param none
     * @return void
     * 
     */
    public function zm_consolidated_mail(){
        $cc =0;
        $zone_list = $this->Lead->get_employee_dump(array('hrms_id','name','designation','email_id','zone_id','zone_name'),array('designation like' => '%ZONAL MANAGER%'),array(),'employee_dump');
//        echo "<pre>";
//        print_r($zone_list);die;
        foreach ($zone_list as $k => $v) {
            $final = array();
            $zonal_manager=array();
            $branch_list=array();

            //FOR ZONAL MANAGER
            $zonal_manager = array('generated' => array(),'converted' => array(),'unassigned' => array(),'pending_before' => array(),'pending' => array());
            $gm = $zonal_manager;
            $branch_list = $this->Lead->get_employee_dump(array('DISTINCT(branch_id) as branch_id','branch_name'),array('zone_id' => $v->zone_id),array(),'employee_dump');
//            echo "<pre>";
////            echo $v->zone_id;
//           print_r($branch_list);die;
            $zonal_manager['generated']  = $this->get_leads(array('type'=>'generated','till'=>'mtd','user_type'=>'BM','zone_id' => $v->zone_id));
            $zonal_manager['converted']  = $this->get_leads(array('type'=>'converted','till'=>'mtd','user_type'=>'BM','zone_id' => $v->zone_id));
            $zonal_manager['unassigned'] = $this->get_leads(array('type'=>'unassigned','till'=>'','user_type'=>'BM','zone_id' => $v->zone_id));
            $zonal_manager['pending_before']   = $this->get_leads(array('type'=>'pending_before','till'=>'','user_type'=>'BM','zone_id' => $v->zone_id));
            $zonal_manager['pending']    = $this->get_leads(array('type'=>'pending','till'=>'TAT','user_type'=>'BM','zone_id' => $v->zone_id));

            //$zonal_manager = call_user_func_array('array_merge', $zonal_manager);
            $zonal_manager = array_merge($zonal_manager);
            //pe($zonal_manager);
            $total = array();
            foreach (array_keys($gm) as $key => $value) {
                if(!empty($zonal_manager[$value])){
                    $total[$value] = array_column($zonal_manager[$value], $value,'branch_id');
                }
            }
//            pe($total);die;
            $unique_branch_ids = array_unique(array_column($zonal_manager, 'branch_id'));
            foreach ($branch_list as $key => $value) {

                    $final['zonal_manager'][$value->branch_id]['generated'] = isset($total['generated'][$value->branch_id]) ? $total['generated'][$value->branch_id] : 0;
                    $final['zonal_manager'][$value->branch_id]['converted'] = isset($total['converted'][$value->branch_id]) ? $total['converted'][$value->branch_id] : 0;
                    $final['zonal_manager'][$value->branch_id]['unassigned'] = isset($total['unassigned'][$value->branch_id]) ? $total['unassigned'][$value->branch_id] : 0;
                    $final['zonal_manager'][$value->branch_id]['pending_before'] = isset($total['pending_before'][$value->branch_id]) ? $total['pending_before'][$value->branch_id] : 0;
                    $final['zonal_manager'][$value->branch_id]['pending'] = isset($total['pending'][$value->branch_id]) ? $total['pending'][$value->branch_id] : 0;

                $final['zonal_manager'][$value->branch_id]['branch_id'] = $value->branch_id;
                $final['zonal_manager'][$value->branch_id]['branch_name'] = $value->branch_name;
            }
           //echo "<pre>";
//            echo $v->zone_id;
          // pe($final['zonal_manager']);die;
            //FOR ZONAL MANAGER
            $subject = 'Pending Leads under Dena Sampark for follow up';
            $first_attachment_file = $this->export_to_excel('zm_consolidated_mail',$final['zonal_manager']);
            $second_attachment_file = $this->zm_consolidated_mail_for_advances($v->zone_id);
            $attachment_file = array($first_attachment_file,$second_attachment_file);
            $to = array('email' => $v->email_id,'name' => $v->name);
            $message = $this->zm_msg();
            sendMail($to,$subject,$message,$attachment_file,$cc);
//die;
        }
    }       

    /*
     * bm_consolidated_mail
     * Employee wise  leads generated,converted,unassigned and pending count 
     * @author Ashok Jadhav (AJ)
     * @access public
     * @param none
     * @return void
     * 
     */
    public function bm_consolidated_mail(){
        $cc =0;
        $branch_list = $this->Lead->get_employee_dump(array('hrms_id','name','designation','email_id','branch_id','branch_name'),array('designation like' => '%BRANCH MANAGER%'),array(),'employee_dump');
//        echo "<pre>";
//        print_r($branch_list);die;
        foreach ($branch_list as $k => $v) {
            $final = array();
            //FOR EMPLOYEE
            $branch_manager = array('generated' => array(),'converted' => array(),'pending_before' => array(),'pending' => array());
            $gm = $branch_manager;
            $employee_list = $this->Lead->get_employee_dump(array('hrms_id','name'),array('branch_id' => $v->branch_id),array(),'employee_dump');
            
            $branch_manager['generated']  = $this->get_leads(array('type'=>'generated','till'=>'mtd','user_type'=>'EM','branch_id' => $v->branch_id));
            $branch_manager['converted']  = $this->get_leads(array('type'=>'converted','till'=>'mtd','user_type'=>'EM','branch_id' => $v->branch_id));
            $branch_manager['pending_before']   = $this->get_leads(array('type'=>'pending_before','till'=>'','user_type'=>'EM','branch_id' => $v->branch_id));
            $branch_manager['pending']    = $this->get_leads(array('type'=>'pending','till'=>'TAT','user_type'=>'EM','branch_id' => $v->branch_id));
//            if(!empty($branch_manager)){
//                $branch_manager = call_user_func_array('array_merge', $branch_manager);
//            }
            $branch_manager = array_merge($branch_manager);
           // pe($branch_manager);
            $total = array();
            foreach (array_keys($gm) as $key => $value) {
                if(!empty($branch_manager[$value])) {
                    $total[$value] = array_column($branch_manager[$value], $value, 'hrms_id');
                }
            }
            //pe($total);die;
            //$unique_hrms_ids = array_unique(array_column($branch_manager, 'hrms_id'));
            foreach ($employee_list as $key => $value) {

                    $final['branch_manager'][$value->hrms_id]['generated'] = isset($total['generated'][$value->hrms_id]) ? $total['generated'][$value->hrms_id] : 0;
                    $final['branch_manager'][$value->hrms_id]['converted'] = isset($total['converted'][$value->hrms_id]) ? $total['converted'][$value->hrms_id] : 0;
                    $final['branch_manager'][$value->hrms_id]['pending_before'] = isset($total['pending_before'][$value->hrms_id]) ? $total['pending_before'][$value->hrms_id] : 0;
                    $final['branch_manager'][$value->hrms_id]['pending'] = isset($total['pending'][$value->hrms_id]) ? $total['pending'][$value->hrms_id] : 0;

                $final['branch_manager'][$value->hrms_id]['hrms_id'] = $value->hrms_id;
                $final['branch_manager'][$value->hrms_id]['employee_name'] = $value->name;
            }
            //FOR EMPLOYEE
            $attachment_file = $this->export_to_excel('bm_consolidated_mail',$final['branch_manager']);
            $attachment_file = array($attachment_file);
            $to = array('email' => $v->email_id,'name' => $v->name);
            $subject = 'Pending Leads under Dena Sampark for follow up';
            $message = $this->bm_msg();
            sendMail($to,$subject,$message,$attachment_file,$cc);
            //die;
        }
    }

    /*
     * bm_inactive_leads
     * Employee wise  Inactive leads count
     * @author Ashok Jadhav (AJ)
     * @access public
     * @param none
     * @return void
     * 
     */
    public function bm_inactive_leads(){
        //Branch list for sending mail

        $branch_list = $this->Lead->get_employee_dump(array('hrms_id','name','email_id','branch_id','branch_name'),array('designation like' => '%BRANCH MANAGER%'),array(),'employee_dump');
        foreach ($branch_list as $k => $v) {
            $final = array();
            //FOR BRANCH MANAGER
            $employee_list = $this->Lead->get_employee_dump(array('hrms_id','name'),array('branch_id' => $v->branch_id),array(),'employee_dump');
//            echo "<pre>";
//            print_r($employee_list);die;
            $branch_manager['inactive']  = $this->get_leads(array('type'=>'inactive','till'=>'days','days_count'=>2,'user_type'=>'EM','branch_id' => $v->branch_id));
            if(!empty($branch_manager)){
                $branch_manager = call_user_func_array('array_merge', $branch_manager);
            }
            
            $total['inactive'] = array_column($branch_manager,'inactive','hrms_id'); 
            $unique_hrms_ids = array_unique(array_column($branch_manager, 'hrms_id'));
            $total_count = 0;
            foreach ($employee_list as $key => $value) {
                if(!in_array($value->hrms_id,$unique_hrms_ids)){
                    $final['branch_manager'][$value->hrms_id]['inactive'] = 0;
                }else{
                    $final['branch_manager'][$value->hrms_id]['inactive'] = isset($total['inactive'][$value->hrms_id]) ? $total['inactive'][$value->hrms_id] : 0;
                    $total_count += $final['branch_manager'][$value->hrms_id]['inactive'];
                }
                $final['branch_manager'][$value->hrms_id]['hrms_id'] = $value->hrms_id;
                $final['branch_manager'][$value->hrms_id]['employee_name'] = $value->name;
            }
            //FOR BRANCH MANAGER

            //Notification Code
//            $title = 'Total no. of inactive leads for the Branch';
//            $description = 'Total no. of inactive leads : '.$total_count;
//            $priority = 'Normal';
//            $notification_to = $v->hrms_id;
//            notification_log($title,$description,$priority,$notification_to);
            //Notification Code

            //Mail Code
            $attachment_file = $this->export_to_excel('bm_inactive_leads',$final['branch_manager']);
            $to = array('email' => $v->email_id,'name' => $v->name);
            $subject = 'Inactive Leads - '.$v->branch_name;
            $message = 'Please Find an attachment';
            sendMail($to,$subject,$message,$attachment_file);
            die;
            //Mail Code
        }
    }
    /*
     * zm_inactive_leads
     * Branch wise  Inactive leads count
     * @author Ashok Jadhav (AJ)
     * @access public
     * @param none
     * @return void
     * 
     */
    public function zm_inactive_leads(){
        //zone list for sending mail

        $zone_list = $this->Lead->get_employee_dump(array('hrms_id','name','email_id','zone_id','zone_name'),array('designation like' => '%ZONAL MANAGER%'),array(),'employee_dump');
        foreach ($zone_list as $k => $v) {
            $final = array();
            //FOR ZONE MANAGER
            $branch_list = $this->Lead->get_employee_dump(array('branch_id','branch_name'),array('zone_id' => '009846'),array(),'employee_dump');
            
            $zonal_manager['inactive']  = $this->get_leads(array('type'=>'inactive','till'=>'TAT','user_type'=>'BM','zone_id' => '009846'));
            if(!empty($zonal_manager)) {
                $zonal_manager = call_user_func_array('array_merge', $zonal_manager);
            }
            
            $total['inactive'] = array_column($zonal_manager,'inactive','branch_id'); 
            $unique_branch_ids = array_unique(array_column($zonal_manager, 'branch_id'));
            $total_count = 0;
            foreach ($branch_list as $key => $value) {
                if(!in_array($value->branch_id,$unique_branch_ids)){
                    $final['zonal_manager'][$value->branch_id]['inactive'] = 0;
                }else{
                    $final['zonal_manager'][$value->branch_id]['inactive'] = isset($total['inactive'][$value->branch_id]) ? $total['inactive'][$value->branch_id] : 0;
                    $total_count += $final['zonal_manager'][$value->branch_id]['inactive'];
                }
                $final['zonal_manager'][$value->branch_id]['branch_id'] = $value->branch_id;
                $final['zonal_manager'][$value->branch_id]['branch_name'] = $value->branch_name;
            }
            //FOR ZONE MANAGER

            //Notification Code
            $title = 'Total no. of inactive leads for the Zone';
            $description = 'Total no. of inactive leads : '.$total_count;
            $priority = 'Normal';
            $notification_to = $v->hrms_id;    
            notification_log($title,$description,$priority,$notification_to);
            //Notification Code

            //Mail Code
            $attachment_file = $this->export_to_excel('zm_inactive_leads',$final['zonal_manager']);
            $subject = 'Inactive Leads - '.$v->zone_id;
            $to = array('email' => $v->email_id,'name' => $v->name);
            $message = 'Please Find an attachment';
            sendMail($to,$subject,$message,$attachment_file);
            die;
            //Mail code
        }
    }

    /*
     * zm_unassigned_leads
     * Branch wise  Unassigned leads count
     * @author Ashok Jadhav (AJ)
     * @access public
     * @param none
     * @return void
     * 
     */
    public function zm_unassigned_leads(){
        //zone list for sending mail

        $zone_list = $this->Lead->get_employee_dump(array('hrms_id','name','email_id','zone_id','zone_name'),array('designation like' => '%ZONAL MANAGER%'),array(),'employee_dump');
        foreach ($zone_list as $k => $v) {
            $final = array();
            //FOR ZONE MANAGER
            $branch_list = $this->Lead->get_employee_dump(array('branch_id','branch_name'),array('zone_id' => '009846'),array(),'employee_dump');
            
            $zonal_manager['unassigned']  = $this->get_leads(array('type'=>'unassigned_noti','till'=>'','user_type'=>'BM','zone_id' => '009846'));

            if(!empty($zonal_manager['unassigned'])) {
                $zonal_manager = call_user_func_array('array_merge', $zonal_manager);
            }
            
            $total['unassigned'] = array_column($zonal_manager,'unassigned','branch_id'); 
            $unique_hrms_ids = array_unique(array_column($zonal_manager, 'branch_id'));
            $total_count = 0;
            foreach ($branch_list as $key => $value) {
                if(!in_array($value->branch_id,$unique_hrms_ids)){
                    $final['zonal_manager'][$value->branch_id]['unassigned'] = 0;
                }else{
                    $final['zonal_manager'][$value->branch_id]['unassigned'] = isset($total['unassigned'][$value->branch_id]) ? $total['unassigned'][$value->branch_id] : 0;
                    $total_count += $final['zonal_manager'][$value->branch_id]['unassigned'];
                }
                $final['zonal_manager'][$value->branch_id]['branch_id'] = $value->branch_id;
                $final['zonal_manager'][$value->branch_id]['branch_name'] = $value->branch_name;
            }
            //FOR ZONE MANAGER

            //Notification Code
            $title = 'Total no. of unassigned leads for the Zone';
            $description = 'Total no. of unassigned leads : '.$total_count;
            $priority = 'Normal';
            $notification_to = $v->hrms_id;    
            notification_log($title,$description,$priority,$notification_to);
            //Notification Code

            //Mail Code
            //pe($final['zonal_manager']);die;
            $subject = 'Unassigned Leads - '.$v->zone_id;
            $attachment_file = $this->export_to_excel('zm_unassigned_leads',$final['zonal_manager']);
            $to = array('email' => $v->email_id,'name' => $v->name);
            $message = 'Please Find an attachment';
            sendMail($to,$subject,$message,$attachment_file);
            die;
            //Mail Code
        }
    }

    /*
     * gm_unassigned_leads
     * Branch wise  Unassigned leads count
     * @author Ashok Jadhav (AJ)
     * @access public
     * @param none
     * @return void
     *
     */
    public function gm_unassigned_leads(){
        //zone list for sending mail


        $GM_list = $this->Lead->get_employee_dump(array('hrms_id','name','designation','email_id','zone_id','zone_name'),array('designation like' => '%GENERAL MANAGER%'),array(),'employee_dump');
        foreach ($GM_list as $k => $v) {
            $final = array();
            //FOR ZONE MANAGER
            $zone_list = $this->Lead->get_employee_dump(array('DISTINCT(zone_id)', 'zone_name'), array(), array(), 'employee_dump');
            foreach ($zone_list as $key => $value) {
            $zonal_manager['unassigned']  = $this->get_leads(array('type'=>'unassigned_noti','till'=>'','user_type'=>'ZM','zone_id' => $value->zone_id));
                //pe($zonal_manager['unassigned']);
                if(!empty($zonal_manager['unassigned'])){
                    $zonal_manager = call_user_func_array('array_merge', $zonal_manager);
                }


            $total['unassigned'] = array_column($zonal_manager,'unassigned','zone_id');
            $total_count = 0;
                $final['zonal_manager'][$value->zone_id]['unassigned'] = isset($total['unassigned'][$value->zone_id]) ? $total['unassigned'][$value->zone_id] : 0;
                $total_count += $final['zonal_manager'][$value->zone_id]['unassigned'];
                $final['zonal_manager'][$value->zone_id]['zone_id'] = $value->zone_id;
                $final['zonal_manager'][$value->zone_id]['zone_name'] = $value->zone_name;
            }
            //pe($final['zonal_manager']);die;
            //FOR ZONE MANAGER

            //Notification Code
            $title = 'Total no. of unassigned leads for the Zone';
            $description = 'Total no. of unassigned leads : '.$total_count;
            $priority = 'Normal';
            $notification_to = $v->hrms_id;
            notification_log($title,$description,$priority,$notification_to);
            //Notification Code

            //Mail Code
            $subject = 'Unassigned Leads';
            $attachment_file = $this->export_to_excel('gm_unassigned_leads',$final['zonal_manager']);
            $to = array('email' => $v->email_id,'name' => $v->name);
            $message = 'Please Find an attachment';
            sendMail($to,$subject,$message,$attachment_file);
            //Mail Code
        }
    }


    /*
     * get_leads
     * Get leads count along with name
     * @author Ashok Jadhav (AJ)
     * @access private
     * @param $type,$till,$user_type
     * @return array
     * 
     */
    private function get_leads($data){
        $type = $data['type'];
        $till = $data['till'];
        $zone_id = '';
        $user_type = $data['user_type'];
        if(isset($data['zone_id'])){
            $zone_id = $data['zone_id'];
        }
        if(isset($data['branch_id'])){
            $branch_id = $data['branch_id'];
        }


        //Parameters buiding for sending to list function.
        $action = 'list';
        $table = Tbl_Leads.' as l';
        $join = array();
        if($type == 'generated'){
            //Generated Leads
            $select = array('COUNT(l.id) as generated');
            if($till == 'mtd'){
                $where = array('MONTH(l.created_on)' => date('m'),'YEAR(l.created_on)' => date('Y')); //Month till date filter

            }
            if($user_type == 'ZM'){
                $select[] = 'l.created_by_zone_id  as zone_id';
                if($zone_id != ''){
                    $where['l.created_by_zone_id'] = $zone_id;
                }
                $where['l.created_by_zone_id !='] = NULL;
                $group_by = array('l.created_by_zone_id');
            }elseif($user_type == 'BM'){
                $select[] = 'l.created_by_branch_id  as branch_id';
                $where['l.created_by_zone_id'] = $zone_id;
                $where['l.created_by_branch_id !='] = NULL;
                $group_by = array('l.created_by_branch_id');
            }elseif($user_type == 'EM'){
                $select[] = 'l.created_by as hrms_id';
                $where['l.created_by_branch_id'] = $branch_id;
                $where['l.created_by !='] = NULL;
                $group_by = array('l.created_by');
            }
        }elseif($type == 'unassigned'){
            //Unassigned Leads
            $select = array('COUNT(l.id) as unassigned');
            $where  = array('la.lead_id' => NULL);
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => 'left');
            if($user_type == 'ZM'){
                $select[] = 'l.created_by_zone_id as zone_id';
                if($zone_id != ''){
                    $where['l.created_by_zone_id'] = $zone_id;
                }
                $where['l.created_by_zone_id !='] = NULL;
                $group_by = array('l.zone_id');
            }elseif($user_type == 'BM'){
                $select[] = 'l.created_by_branch_id  as branch_id';
                $where['l.created_by_zone_id'] = $zone_id;
                $where['l.created_by_branch_id !='] = NULL;
                $group_by = array('l.created_by_branch_id');
            }
        }elseif($type == 'unassigned_noti'){
            //Unassigned Leads
            $select = array('COUNT(l.id) as unassigned');
            $where  = array('la.lead_id' => NULL);
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => 'left');
            if($user_type == 'ZM'){
                $select[] = 'l.zone_id';
                if($zone_id != ''){
                    $where['l.zone_id'] = $zone_id;
                }
                $day = date( 'Y-m-d', strtotime( date('Y-m-d') . ' -4 day' ) );
                $where['l.created_on <'] = $day.' 00:00:00';
                $group_by = array('l.zone_id');
            }elseif($user_type == 'BM'){
                $select[] = 'l.branch_id';
                $where['l.zone_id'] = $zone_id;
                $day = date( 'Y-m-d', strtotime( date('Y-m-d') . ' -4 day' ) );
                $where['l.created_on <'] = $day.' 00:00:00';
                $group_by = array('l.branch_id');
            }
        }elseif($type == 'adv_total'){
            //Generated Leads
            $select = array('COUNT(l.id) as generated');
            if($till == 'mtd'){
                $where = array('MONTH(l.created_on)' => date('m'),'YEAR(l.created_on)' => date('Y')); //Month till date filter

            }
            $where['l.product_category_id'] = 12;
            if($user_type == 'ZM'){
                $select[] = 'l.created_by_zone_id  as zone_id';
                if($zone_id != ''){
                    $where['l.created_by_zone_id'] = $zone_id;
                }
                $where['l.created_by_zone_id !='] = NULL;
                $group_by = array('l.created_by_zone_id');
            }elseif($user_type == 'BM'){
                $select[] = 'l.created_by_branch_id  as branch_id';
                $where['l.created_by_zone_id'] = $zone_id;
                $where['l.created_by_branch_id !='] = NULL;
                $group_by = array('l.created_by_branch_id');
            }elseif($user_type == 'EM'){
                $select[] = 'l.created_by as hrms_id';
                $where['l.created_by_branch_id'] = $branch_id;
                $where['l.created_by !='] = NULL;
                $group_by = array('l.created_by');
            }
        }elseif($type == 'adv_converted'){
            //Converted Leads
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');

            $select = array('COUNT(l.id) as converted');

            if($till == 'mtd'){
                $where = array('MONTH(la.modified_on)' => date('m'),'YEAR(l.modified_on)' => date('Y')); //Month till date filter

            }
            $where['la.status']  = 'Converted';
            $where['l.product_category_id'] = 12;
            if($user_type == 'ZM'){
                $select[] = 'la.zone_id';
                if($zone_id != ''){
                    $where['la.zone_id'] = $zone_id;
                }
                $group_by = array('la.zone_id');
            }elseif($user_type == 'BM'){
                $select[] = 'la.branch_id';
                $where['la.zone_id'] = $zone_id;
                $group_by = array('la.branch_id');
            }elseif($user_type == 'EM'){
                $select[] = 'la.employee_id as hrms_id';
                $where['la.branch_id'] = $branch_id;
                $group_by = array('la.employee_id');
            }
        }elseif($type == 'adv_assigned'){
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');

            $select = array('COUNT(l.id) as assigned');

            if($till == 'mtd'){
                $where = array('MONTH(la.created_on)' => date('m'),'YEAR(l.created_on)' => date('Y')); //Month till date filter

            }
            $where['l.product_category_id'] = 12;
            if($user_type == 'ZM'){
                $select[] = 'la.zone_id as zone_id';
                if($zone_id != ''){
                    $where['la.zone_id'] = $zone_id;
                }
                $where['la.zone_id !='] = NULL;
                $group_by = array('la.zone_id');
            }elseif($user_type == 'BM'){
                $select[] = 'la.branch_id  as branch_id';
                $where['la.zone_id'] = $zone_id;
                $where['la.branch_id !='] = NULL;
                $group_by = array('la.branch_id');
            }
        }elseif($type == 'adv_unassigned'){
            //Unassigned Leads
            $select = array('COUNT(l.id) as unassigned');
            $where  = array('la.lead_id' => NULL);
            $where['l.product_category_id'] = 12;
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => 'left');
            if($user_type == 'ZM'){
                $select[] = 'l.zone_id as zone_id';
                if($zone_id != ''){
                    $where['l.zone_id'] = $zone_id;
                }
                $where['l.zone_id !='] = NULL;
                $group_by = array('l.zone_id');
            }elseif($user_type == 'BM'){
                $select[] = 'l.branch_id  as branch_id';
                $where['l.zone_id'] = $zone_id;
                $where['l.branch_id !='] = NULL;
                $group_by = array('l.branch_id');
            }
        }else{
            //Assigned Leads
            $where  = array('la.is_deleted' => 0,'la.is_updated' => 1);
            if($till == 'mtd'){
                $where['MONTH(la.created_on)']  = date('m');
                $where['YEAR(la.created_on)']  = date('Y');
            }
            if($till == 'days'){
                $days_count = $data['days_count'];
                $where['DATEDIFF(CURDATE(),la.created_on) >']  = $days_count;   
            }
            if($till == 'TAT'){
                $where['p.turn_around_time < DATEDIFF(CURDATE(),la.created_on)']  = NULL;   
                $join[] = array('table' => Tbl_Products.' as p','on_condition' => 'l.product_id = p.id AND l.product_category_id = p.category_id','type' => '');
            }
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
            
            if($type == 'converted'){
                $select = array('COUNT(l.id) as converted');
                $where['la.status']  = 'Converted';
            }
            if($type == 'pending_before'){
if($user_type == 'ZM'){
$pending_days = 6;
}
if($user_type == 'BM'){
$pending_days = 4;
}
if($user_type == 'EM'){
$pending_days = 2;
}

                $select = array('COUNT(l.id) as pending_before');
                $where['la.status IN ("NC","FU","DC")']  = NULL;
                $day = date( 'Y-m-d', strtotime( date('Y-m-d') . ' -'.$pending_days.' day' ) ).' 00:00:00';
                $where["CASE WHEN la.status = 'NC' THEN la.modified_on < '$day' WHEN la.status = 'FU' THEN la.followup_date < '$day' END"]=NULL;
               // $join[] = array('table' => Tbl_Reminder.' as fr','on_condition' => 'fr.lead_id = l.id','type' => '');
                $join[] = array('table' => Tbl_Products.' as p','on_condition' => 'p.id = l.product_id','type' => '');
            }
            if($type == 'inactive'){
                $select = array('COUNT(l.id) as inactive');
                $where['la.status NOT IN ("Converted")']  = NULL;
            }

            if($type == 'pending'){
                $select = array('COUNT(l.id) as pending');
                $where['la.status']  = 'DC';   
            }
            if($user_type == 'ZM'){
                $select[] = 'la.zone_id';
                if($zone_id != ''){
                    $where['l.zone_id'] = $zone_id;
                }
                $group_by = array('la.zone_id');
            }elseif($user_type == 'BM'){
                $select[] = 'la.branch_id';
                $where['la.zone_id'] = $zone_id;
                $group_by = array('la.branch_id');
            }elseif($user_type == 'EM'){
                $select[] = 'la.employee_id as hrms_id';
                $where['la.branch_id'] = $branch_id;
                $group_by = array('la.employee_id');
            }
        }
        return $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array());
    }

    /*
     * export_to_excel
     * Creating columns header for excel
     * @author Ashok Jadhav (AJ)
     * @access private
     * @param $action,$arrData
     * @return string
     * 
     */
    private function export_to_excel($action,$arrData){
        $export_data = array();
        switch ($action) {
            case 'gm_consolidated_mail':
                $header_value = array('Zone Id','Zone Name','Lead Generated (MTD)','Lead Converted (MTD)','No.of Unassigned Leads','No.of pending Leads before Documentation','No. of pending leads post Documentation');
                break;
            case 'zm_consolidated_mail':
            $header_value = array('Branch Id','Branch Name','Lead Generated (MTD)','Lead Converted (MTD)','No.of Unassigned Leads','No.of pending Leads before Documentation','No. of pending leads post Documentation');
                break;
            case 'zm_consolidated_mail_advances':
                $header_value = array('Branch Id','Branch Name','Total Lead Generated (MTD)','Total Lead Assigned (MTD)','Total Lead Converted (MTD)','No.of Unassigned Leads');
                break;
            case 'bm_consolidated_mail':
            $header_value = array('HRMS Id','Employee Name','Lead Generated (MTD)','Lead Converted (MTD)','No.of pending Leads before Documentation','No. of pending leads post Documentation');
                break;
            case 'bm_inactive_leads':
            $header_value = array('HRMS Id','Employee Name','Total Inactive Leads');
                break;
            case 'zm_inactive_leads':
            $header_value = array('Branch Id','Branch Name','Total Inactive Leads');
                break;
            case 'zm_unassigned_leads':
            $header_value = array('Branch Id','Branch Name','Total Unassigned Leads');
                break;
            case 'gm_unassigned_leads':
                $header_value = array('Zone Id','Zone Name','Total Unassigned Leads');
                break;
        }
        return $this->create_excel($action,$header_value,$arrData);
    }

    /*
     * create_excel
     * Create column values for excel
     * @author Ashok Jadhav (AJ)
     * @access private
     * @param $action,$header_value,$data
     * @return string
     * 
     */
    private function create_excel($action,$header_value,$data){
       // pe($data);die;
        $this->load->library('excel');
        $file_name = 'leads_under_Denasampark_for_follow_up-'.date('d-m-Y').'-'.time().'data.xls';
        if($action == 'zm_consolidated_mail_advances' ){
            $file_name = 'Advances_leads.xls';
        }
        $excel_alpha = unserialize(EXCEL_ALPHA);

        //$objPHPExcel = $this->excel;
          $objPHPExcel = new PHPExcel();

        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
        $objPHPExcel->getDefaultStyle()->getFont()->setBold(true);
        $objPHPExcel->getDefaultStyle()
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $fontArray = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 22
            ));
        $textfontArray = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11
            ));
        $text_bold_false = array(
            'font'  => array(
                'bold'  => false,
                'size'  => 11
            ));
        $fileType = 'Excel5';
        $time = time();
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");

        foreach ($header_value as $key=>$value ){
            $objPHPExcel->getActiveSheet()->getColumnDimension($excel_alpha[$key])->setAutoSize(true);
        }

        $objSheet = $objPHPExcel->getActiveSheet();
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(-1);

        foreach ($header_value as $key => $value){
            $objSheet->getStyle($excel_alpha[$key].'1')
                ->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objSheet->getCell($excel_alpha[$key].'1')->setValue($value);
        }
        
        $i=2;$j=1;
        foreach ($data as $key => $value) {
            foreach ($header_value as $k => $v) {
                $objSheet->getStyle($excel_alpha[$k] . $i)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objSheet->getStyle($excel_alpha[$k].($i))->applyFromArray($text_bold_false);
            }

            //$objSheet->getCell($excel_alpha[0].$i)->setValue($j);
            $col = -1;
            if($action == 'gm_consolidated_mail'){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['zone_id']));
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['zone_name']));
            }
            if(in_array($action,array('zm_consolidated_mail','zm_consolidated_mail_advances','zm_inactive_leads','zm_unassigned_leads'))){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['branch_id']));
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['branch_name']));
            }
            if(in_array($action,array('bm_consolidated_mail','bm_inactive_leads'))){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['hrms_id']));
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['employee_name']));
            }
            if(in_array($action,array('gm_consolidated_mail','zm_consolidated_mail','zm_consolidated_mail_advances','bm_consolidated_mail'))){
                if($action == 'zm_consolidated_mail_advances' ){
                    $objSheet->getCell($excel_alpha[++$col] . $i)->setValue(ucwords($value['generated']));
                    $objSheet->getCell($excel_alpha[++$col] . $i)->setValue(ucwords($value['assigned']));
                    $objSheet->getCell($excel_alpha[++$col] . $i)->setValue(ucwords($value['converted']));
                    $objSheet->getCell($excel_alpha[++$col] . $i)->setValue(ucwords($value['unassigned']));

                }else {
                    $objSheet->getCell($excel_alpha[++$col] . $i)->setValue(ucwords($value['generated']));
                    $objSheet->getCell($excel_alpha[++$col] . $i)->setValue(ucwords($value['converted']));
                    if ($action == 'bm_consolidated_mail') {
                        $objSheet->getCell($excel_alpha[++$col] . $i)->setValue(ucwords($value['pending_before']));
                    } else {
                        $objSheet->getCell($excel_alpha[++$col] . $i)->setValue(ucwords($value['unassigned']));
                        $objSheet->getCell($excel_alpha[++$col] . $i)->setValue(ucwords($value['pending_before']));
                    }
                    $objSheet->getCell($excel_alpha[++$col] . $i)->setValue(ucwords($value['pending']));
                }
            }
            if(in_array($action,array('bm_inactive_leads','zm_inactive_leads'))){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['inactive']));   
            }
            if(in_array($action,array('zm_unassigned_leads','gm_unassigned_leads'))){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['unassigned']));   
            }
            $i++;$j++;
        }
        $objWriter->save('uploads/excel_list/'.$file_name);
        return $file_name;
    }

    public function masters(){
        $url = HRMS_BRANCH_RECORD;
        $result = call_external_url($url);
        $result = json_decode($result,true);
        $final_zone = array();$final_state = array();$final_dist = array();$final_branch = array();
        if(!empty($result)){
            foreach ($result['branch_details']['zone'] as $key=> $val){
                $state1 = array();$dist1 = array();
                $zone['code'] = $val['zone_id'];
                $zone['name'] = $val['zone_name'];
                $final_zone[] = $zone;
                if(isset($val['state']['id'])){
                    $state1[] = $val['state'];
                }else{
                    $state1 = $val['state'];
                }
                foreach ($state1 as $sk =>$state){
                    $states = array();$dist1 = array();
                    $states['code'] = $state['id'];
                    $states['name'] = $state['name'];
                    $states['zone_code'] =  $zone['code'];
                    $final_state[] = $states;
                    if(isset($state['districts']['id'])){
                        $dist1[] = $state['districts'];
                    }else{
                        $dist1 = $state['districts'];
                    }
                    foreach ($dist1 as $dk => $dist){
                        $district = array();$branch1 = array();
                        $district['code'] = $dist['id'];
                        $district['name'] = $dist['name'];
                        $district['state_code'] =    $states['code'];
                        $final_dist[] = $district;
                        if(isset($dist['branches']['id'])){
                            $branch1[] = $dist['branches'];
                        }else{
                            $branch1 = $dist['branches'];
                        }
                        foreach ($branch1 as $bk => $branch){
                            $branches = array();
                            $branches['code'] = $branch['id'];
                            $branches['name'] = $branch['name'];
                            $branches['district_code'] = $district['code'];
                            $final_branch[] = $branches;
                        }
                    }
                }
            }
            /* foreach ($final_zone as $key => $value){
                $action = 'count';
                $table = Tbl_zone;
                $where = array('code'=>$value['code']);
                $count = $this->Lead->get_leads($action,$table,$select=array(),$where,$join=array(),$group_by=array(),$order_by=array());
                if($count > 0){
                    unset($final_zone[$key]);
                }
            }
            $available_states = array();
            foreach ($final_state as $key => $value){

                $action = 'count';
                $table = Tbl_state;
                $where = array('code'=>$value['code']);
                $count = $this->Lead->get_leads($action,$table,$select=array(),$where,$join=array(),$group_by=array(),$order_by=array());
                if($count > 0 || in_array($value['code'],$available_states)){
                    unset($final_state[$key]);
                }
                if(isset($value['code']) && !empty($value['code']))
                $available_states[$key] = $value['code'];
            }
            foreach ($final_dist as $key => $value){
                $action = 'count';
                $table = Tbl_district;
                $where = array('code'=>$value['code']);
                $count = $this->Lead->get_leads($action,$table,$select=array(),$where,$join=array(),$group_by=array(),$order_by=array());
                if($count > 0){
                    unset($final_dist[$key]);
                }
            }
            foreach ($final_branch as $key => $value){
                $action = 'count';
                $table = Tbl_branch;
                $where = array('code'=>$value['code']);
                $count = $this->Lead->get_leads($action,$table,$select=array(),$where,$join=array(),$group_by=array(),$order_by=array());
                if($count > 0){
                    unset($final_branch[$key]);
                }
            }
*/
  //          if(count($final_zone) > 0){

                $data= array('is_old' => 1);
                $this->Lead->update($where='1=1',Tbl_zone,$data);
                $this->db->insert_batch(Tbl_zone,$final_zone);
                $where=array('is_old' => 1);
                $this->Lead->delete($where,Tbl_zone);

                $data= array('is_old' => 1);
                $this->Lead->update($where='1=1',Tbl_state,$data);
                $this->db->insert_batch(Tbl_state,$final_state);
                $where=array('is_old' => 1);
                $this->Lead->delete($where,Tbl_state);

                $data= array('is_old' => 1);
                $this->Lead->update($where='1=1',Tbl_district,$data);
                $this->db->insert_batch(Tbl_district,$final_dist);
                $where=array('is_old' => 1);
                $this->Lead->delete($where,Tbl_district);

                $data= array('is_old' => 1);
                $this->Lead->update($where='1=1',Tbl_branch,$data);
                $this->db->insert_batch(Tbl_branch,$final_branch);
                $where=array('is_old' => 1);
                $this->Lead->delete($where,Tbl_branch);


                //$this->db->insert_batch(Tbl_zone,$final_zone);
                //$this->db->insert_batch(Tbl_state,$final_state);
                //$this->db->insert_batch(Tbl_district,$final_dist);
                //$this->db->insert_batch(Tbl_branch,$final_branch);
       }else{
            }

    //    }

    }


    public function employee_dump(){
        $url = HRMS_EMP_DUMP;
        $result = call_external_url($url);
        $result = json_decode($result,true);
        $columns = ['hrms_id', 'name', 'designation_id','designation', 'email_id','contact_no', 'branch_id', 'branch_name',
            'zone_id', 'zone_name','district_id', 'state_id', 'supervisor_id'];
        $insert = array();
        if(!empty($result)){
            $hrms_keys = ['hrms_id','Name','Designation_Id','designation','Email','Contact_No','Branch_Id',
                'Branch_Name','Zone_Id','Zone_Name','District','State','Supervisor_HRMS_Id'];
            $result = $result['dbk_lms_emp_pack']['dbk_lms_emp_all'];

            foreach ($result as $key => $value){
                foreach ($columns as $k => $column){

                        $insert[$key][$column] = trim($value[$hrms_keys[$k]]);
                }
            }
            if(!empty($insert)){
                $data= array('is_old' => 1);
                $this->Lead->update($where='1=1',Tbl_emp_dump,$data);
                $this->db->insert_batch(Tbl_emp_dump,$insert);
                $where=array('is_old' => 1);
                $this->Lead->delete($where,Tbl_emp_dump);
              }
        }
    }

    public function upload_rapc_mapping(){
        if($this->input->post('Submit')) {
            if (isset($_FILES['filename']) && !empty($_FILES['filename']['tmp_name'])) {
                make_upload_directory('./uploads');
                $file = upload_excel('./uploads', 'filename');
                if (!is_array($file)) {
                    $msg = notify($file, $type = "danger");
                    $this->session->set_flashdata('error', $msg);
                    redirect('leads/upload_employee');
                } else {
                    set_time_limit(0);
                    ini_set('memory_limit', '-1');
                    $keys = ['processing_center','branch_id','other_processing_center_id'];

                    $excelData = fetch_range_excel_data($file['full_path'], 'A2:C', $keys);
                    $this->Lead->insert_uploaded_data(Tbl_processing_center,$excelData);
                    $msg = notify('File Uploaded Successfully.','success');
                    $this->session->set_flashdata('success', $msg);
                    redirect(base_url('leads/upload_employee'), 'refresh');

                }
            }
            $msg = notify("Please upload a file",'danger');
            $this->session->set_flashdata('message', $msg);
            redirect('leads/upload_employee');
        }
    }

    /*
     * zm_consolidated_mail
     * Branch wise leads generated,converted,unassigned and pending count
     * @author Ashok Jadhav (AJ)
     * @access public
     * @param none
     * @return void
     *
     */
    private function zm_consolidated_mail_for_advances($zone_id){
            $final = array();
            //FOR ZONAL MANAGER
            $zonal_manager = array('generated' => array(),'converted' => array(),'assigned' => array(),'unassigned' => array());
            $gm = $zonal_manager;
            $branch_list = $this->Lead->get_employee_dump(array('DISTINCT(branch_id) as branch_id','branch_name'),array('zone_id' => $zone_id),array(),'employee_dump');
//            echo "<pre>";
////            echo $v->zone_id;
//           print_r($branch_list);die;
            $zonal_manager['generated']  = $this->get_leads(array('type'=>'adv_total','till'=>'mtd','user_type'=>'BM','zone_id' => $zone_id));
            $zonal_manager['converted']  = $this->get_leads(array('type'=>'adv_converted','till'=>'mtd','user_type'=>'BM','zone_id' => $zone_id));
            $zonal_manager['assigned']   = $this->get_leads(array('type'=>'adv_assigned','till'=>'mtd','user_type'=>'BM','zone_id' => $zone_id));
            $zonal_manager['unassigned'] = $this->get_leads(array('type'=>'adv_unassigned','till'=>'','user_type'=>'BM','zone_id' => $zone_id));

            //$zonal_manager = call_user_func_array('array_merge', $zonal_manager);
            $zonal_manager = array_merge($zonal_manager);
            //pe($zonal_manager);
            $total = array();
            foreach (array_keys($gm) as $key => $value) {
                if(!empty($zonal_manager[$value])){
                    $total[$value] = array_column($zonal_manager[$value], $value,'branch_id');
                }
            }
//            pe($total);die;
            $unique_branch_ids = array_unique(array_column($zonal_manager, 'branch_id'));
            foreach ($branch_list as $key => $value) {

                $final['zonal_manager'][$value->branch_id]['generated'] = isset($total['generated'][$value->branch_id]) ? $total['generated'][$value->branch_id] : 0;
                $final['zonal_manager'][$value->branch_id]['converted'] = isset($total['converted'][$value->branch_id]) ? $total['converted'][$value->branch_id] : 0;
                $final['zonal_manager'][$value->branch_id]['unassigned'] = isset($total['unassigned'][$value->branch_id]) ? $total['unassigned'][$value->branch_id] : 0;
                $final['zonal_manager'][$value->branch_id]['assigned'] = isset($total['assigned'][$value->branch_id]) ? $total['assigned'][$value->branch_id] : 0;

                $final['zonal_manager'][$value->branch_id]['branch_id'] = $value->branch_id;
                $final['zonal_manager'][$value->branch_id]['branch_name'] = $value->branch_name;
            }
        $attachment_file = $this->export_to_excel('zm_consolidated_mail_advances',$final['zonal_manager']);

        return $attachment_file;
    }

    private function bm_msg(){
        $msg = "Dear Sir/Madam,<br><br>
                Re: Pendency of Leads in Dena Sampark<br><br>
                Please find attach herewith leads details of Dena Sampark. These are the leads which have not been acted upon by respective employee of your branch in T+2 Days. The attachment contains following parameters:<br><br>
                1) Lead Generated (During the current month).<br>
                2) Lead Converted (During the current month).<br><br>
                Following points need your immediate intervention:<br><br>
                3) Leads pending at pre-documentation stage. (The leads which are not acted upon in due time at various stages).<br>
                4) Leads pending at post documentation stage. (The leads which are mark “Document collected” and are pending beyond defined TAT).<br><br>
                You are requested to kindly look into the pendency and take up with respective employee for immediate suitable disposal of the pending leads.<br><br>
                This is an auto generated e-mail escalated to you on account of pendency beyond defined TAT at branch level which will be further auto escalated to respective Zonal Manager and further to Field General Manager after two days of pendencies at each  level.<br><br>
                Regards,<br>Dena Sampark";
        return $msg;
    }

    private function zm_msg(){
        $msg = "Dear Sir/Madam,<br><br>
                Re: Pendency of Leads in Dena Sampark<br><br>
                Please find attach herewith leads details of Dena Sampark. These are the leads which have not been acted upon by respective BM in T+2 Days. The attachment contains following parameters:<br><br>
                1) Lead Generated (During the current month).<br>
                2) Lead Converted (During the current month).<br><br>
                Following points need your immediate intervention:<br><br>
                3) Number of Unassigned Leads.<br>
                4) Leads pending at pre-documentation stage. (The leads which are not acted upon in due time at various stages).<br>
                5) Leads pending at post documentation stage. (The leads which are mark “Document collected” and are pending beyond defined TAT).<br><br>
                You are requested to kindly look into the pendency and take up with respective BM’s for immediate suitable disposal of the pending leads.<br><br>
                This is an auto generated e-mail escalated to you on account of pendency beyond defined TAT at branch level which will be further auto escalated to respective Field General Manager after two days of pendencies at each  level.<br><br>
                Regards,<br>Dena Sampark";
        return $msg;
    }

    private function gm_msg(){
        $msg = "Dear Sir/Madam,<br><br>
                Re: Pendency of Leads in Dena Sampark<br><br>
                Please find attach herewith leads details of Dena Sampark. These are the leads which have not been acted upon by respective BM within defined TAT and the same were escalated to DZM/ZM for further action. The attachment contains following parameters:<br><br>
                1) Lead Generated (During the current month).<br>
                2) Lead Converted (During the current month).<br><br>
                Following points need your intervention:<br><br>
                3) Number of Unassigned Leads.<br>
                4) Leads pending at pre-documentation stage. (The leads which are not acted upon in due time at various stages).<br>
                5) Leads pending at post documentation stage. (The leads which are mark “Document collected” and are pending beyond defined TAT).<br><br>
                You are requested to kindly look into the pendency and take up the matter for suitable disposal of the pending leads.<br><br>
                This is an auto generated e-mail escalated to you on account of pendency beyond defined TAT at Branch/DZM/ZM level.<br><br>
                Regards,<br>Dena Sampark";
        return $msg;
    }

}
