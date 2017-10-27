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
        $this->load->model('Lead');
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
        $final = array();
        //For GENERAL MANAGER
        $general_manager = array('generated' => array(),'converted' => array(),'unassigned' => array(),'pending' => array());
        $gm = $general_manager;
        $zone_list = $this->Lead->get_employee_dump(array('hrms_id','zone_id','zone_name'),array('designation' => 'ZD'),array(),'employee_dump');
    
        $general_manager['generated']  = $this->get_leads(array('type'=>'generated','till'=>'mtd','user_type'=>'ZM'));
        $general_manager['converted']  = $this->get_leads(array('type'=>'converted','till'=>'mtd','user_type'=>'ZM'));
        $general_manager['unassigned'] = $this->get_leads(array('type'=>'unassigned','till'=>'','user_type'=>'ZM'));
        $general_manager['pending']    = $this->get_leads(array('type'=>'pending','till'=>'TAT','user_type'=>'ZM'));

        $general_manager = call_user_func_array('array_merge', $general_manager);
        $total = array();
        foreach (array_keys($gm) as $key => $value) {
            $total[$value] = array_column($general_manager, $value,'zone_id'); 
        }
        $unique_zone_ids = array_unique(array_column($general_manager, 'zone_id'));
        foreach ($zone_list as $key => $value) {
            if(!in_array($value->zone_id,$unique_zone_ids)){
                $final['general_manager'][$value->zone_id]['generated'] = 0;
                $final['general_manager'][$value->zone_id]['converted'] = 0;
                $final['general_manager'][$value->zone_id]['unassigned'] = 0;
                $final['general_manager'][$value->zone_id]['pending'] = 0;
            }else{
                $final['general_manager'][$value->zone_id]['generated'] = isset($total['generated'][$value->zone_id]) ? $total['generated'][$value->zone_id] : 0;
                $final['general_manager'][$value->zone_id]['converted'] = isset($total['converted'][$value->zone_id]) ? $total['converted'][$value->zone_id] : 0;
                $final['general_manager'][$value->zone_id]['unassigned'] = isset($total['unassigned'][$value->zone_id]) ? $total['unassigned'][$value->zone_id] : 0;
                $final['general_manager'][$value->zone_id]['pending'] = isset($total['pending'][$value->zone_id]) ? $total['pending'][$value->zone_id] : 0;
            }
            $final['general_manager'][$value->zone_id]['zone_id'] = $value->zone_id;
            $final['general_manager'][$value->zone_id]['zone_name'] = $value->zone_name;
        }
        //For GENERAL MANAGER

        $attachment_file = $this->export_to_excel('gm_consolidated_mail',$final['general_manager']);
        $to = array('email' => 'ashok.jadhav@wwindia.com','name' => 'Ashok Jadhav');
        $subject = 'General Manager Consolidated Format';
        $message = 'Please Find an attachment';
        sendMail($to,$subject,$message,$attachment_file);
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
        $subject = 'Zonal Manager Consolidated Format';
        $zone_list = $this->Lead->get_employee_dump(array('hrms_id','name','email_id','zone_id','zone_name'),array('designation' => 'ZD'),array(),'employee_dump');
        foreach ($zone_list as $k => $v) {
            $final = array();
            //FOR ZONAL MANAGER
            $zonal_manager = array('generated' => array(),'converted' => array(),'unassigned' => array(),'pending' => array());
            $gm = $zonal_manager;
            $branch_list = $this->Lead->get_employee_dump(array('branch_id','branch_name'),array('designation' => 'BR','zone_id' => $v->zone_id),array(),'employee_dump');
            
            $zonal_manager['generated']  = $this->get_leads(array('type'=>'generated','till'=>'mtd','user_type'=>'BM','zone_id' => $v->zone_id));
            $zonal_manager['converted']  = $this->get_leads(array('type'=>'converted','till'=>'mtd','user_type'=>'BM','zone_id' => $v->zone_id));
            $zonal_manager['unassigned'] = $this->get_leads(array('type'=>'unassigned','till'=>'','user_type'=>'BM','zone_id' => $v->zone_id));
            $zonal_manager['pending']    = $this->get_leads(array('type'=>'pending','till'=>'TAT','user_type'=>'BM','zone_id' => $v->zone_id));
            
            $zonal_manager = call_user_func_array('array_merge', $zonal_manager);
            $total = array();
            foreach (array_keys($gm) as $key => $value) {
                $total[$value] = array_column($zonal_manager, $value,'branch_id'); 
            }
            $unique_branch_ids = array_unique(array_column($zonal_manager, 'branch_id'));
            foreach ($branch_list as $key => $value) {
                if(!in_array($value->branch_id,$unique_branch_ids)){
                    $final['zonal_manager'][$value->branch_id]['generated'] = 0;
                    $final['zonal_manager'][$value->branch_id]['converted'] = 0;
                    $final['zonal_manager'][$value->branch_id]['unassigned'] = 0;
                    $final['zonal_manager'][$value->branch_id]['pending'] = 0;
                }else{
                    $final['zonal_manager'][$value->branch_id]['generated'] = isset($total['generated'][$value->branch_id]) ? $total['generated'][$value->branch_id] : 0;
                    $final['zonal_manager'][$value->branch_id]['converted'] = isset($total['converted'][$value->branch_id]) ? $total['converted'][$value->branch_id] : 0;
                    $final['zonal_manager'][$value->branch_id]['unassigned'] = isset($total['unassigned'][$value->branch_id]) ? $total['unassigned'][$value->branch_id] : 0;
                    $final['zonal_manager'][$value->branch_id]['pending'] = isset($total['pending'][$value->branch_id]) ? $total['pending'][$value->branch_id] : 0;
                }
                $final['zonal_manager'][$value->branch_id]['branch_id'] = $value->branch_id;
                $final['zonal_manager'][$value->branch_id]['branch_name'] = $value->branch_name;
            }
            //FOR ZONAL MANAGER
            $attachment_file = $this->export_to_excel('zm_consolidated_mail',$final['zonal_manager']);
            $to = array('email' => $v->email_id,'name' => $v->name);
            
            $message = 'Please Find an attachment';
            sendMail($to,$subject,$message,$attachment_file);
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
        $subject = 'Branch Manager Consolidated Format';
        $branch_list = $this->Lead->get_employee_dump(array('hrms_id','name','email_id','branch_id','branch_name'),array(),array(),'employee_dump');
        foreach ($branch_list as $k => $v) {
            $final = array();
            //FOR EMPLOYEE
            $branch_manager = array('generated' => array(),'converted' => array(),'pending_before' => array(),'pending' => array());
            $gm = $branch_manager;
            $employee_list = $this->Lead->get_employee_dump(array('hrms_id','name'),array('designation' => 'HD','branch_id' => $v->branch_id),array(),'employee_dump');
            
            $branch_manager['generated']  = $this->get_leads(array('type'=>'generated','till'=>'mtd','user_type'=>'EM','branch_id' => $v->branch_id));
            $branch_manager['converted']  = $this->get_leads(array('type'=>'converted','till'=>'mtd','user_type'=>'EM','branch_id' => $v->branch_id));
            $branch_manager['pending_before']   = $this->get_leads(array('type'=>'pending_before','till'=>'','user_type'=>'EM','branch_id' => $v->branch_id));
            $branch_manager['pending']    = $this->get_leads(array('type'=>'pending','till'=>'TAT','user_type'=>'EM','branch_id' => $v->branch_id));
            
            $branch_manager = call_user_func_array('array_merge', $branch_manager);
            
            $total = array();
            foreach (array_keys($gm) as $key => $value) {
                $total[$value] = array_column($branch_manager, $value,'hrms_id'); 
            }
            $unique_hrms_ids = array_unique(array_column($branch_manager, 'hrms_id'));
            foreach ($employee_list as $key => $value) {
                if(!in_array($value->hrms_id,$unique_hrms_ids)){
                    $final['branch_manager'][$value->hrms_id]['generated'] = 0;
                    $final['branch_manager'][$value->hrms_id]['converted'] = 0;
                    $final['branch_manager'][$value->hrms_id]['pending_before'] = 0;
                    $final['branch_manager'][$value->hrms_id]['pending'] = 0;
                }else{
                    $final['branch_manager'][$value->hrms_id]['generated'] = isset($total['generated'][$value->hrms_id]) ? $total['generated'][$value->hrms_id] : 0;
                    $final['branch_manager'][$value->hrms_id]['converted'] = isset($total['converted'][$value->hrms_id]) ? $total['converted'][$value->hrms_id] : 0;
                    $final['branch_manager'][$value->hrms_id]['pending_before'] = isset($total['pending_before'][$value->hrms_id]) ? $total['pending_before'][$value->hrms_id] : 0;
                    $final['branch_manager'][$value->hrms_id]['pending'] = isset($total['pending'][$value->hrms_id]) ? $total['pending'][$value->hrms_id] : 0;
                }
                $final['branch_manager'][$value->hrms_id]['hrms_id'] = $value->hrms_id;
                $final['branch_manager'][$value->hrms_id]['employee_name'] = $value->name;
            }
            //FOR EMPLOYEE
            $attachment_file = $this->export_to_excel('bm_consolidated_mail',$final['branch_manager']);
            $to = array('email' => $v->email_id,'name' => $v->name);
            
            $message = 'Please Find an attachment';
            sendMail($to,$subject,$message,$attachment_file);
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
        $subject = 'Branch Manager Inacvtive Leads';
        $branch_list = $this->Lead->get_employee_dump(array('hrms_id','name','email_id','branch_id','branch_name'),array('designation' => 'BR'),array(),'employee_dump');
        foreach ($branch_list as $k => $v) {
            $final = array();
            //FOR BRANCH MANAGER
            $employee_list = $this->Lead->get_employee_dump(array('hrms_id','name'),array('designation' => 'HD','branch_id' => $v->branch_id),array(),'employee_dump');
            
            $branch_manager['inactive']  = $this->get_leads(array('type'=>'inactive','till'=>'days','days_count'=>2,'user_type'=>'EM','branch_id' => $v->branch_id));
            $branch_manager = call_user_func_array('array_merge', $branch_manager);
            
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
            $title = 'Total no. of inactive leads for the Branch';
            $description = 'Total no. of inactive leads : '.$total_count;
            $priority = 'Normal';
            $notification_to = $v->hrms_id;    
            notification_log($title,$description,$priority,$notification_to);
            //Notification Code

            //Mail Code
            $attachment_file = $this->export_to_excel('bm_inactive_leads',$final['branch_manager']);
            $to = array('email' => $v->email_id,'name' => $v->name);
            $message = 'Please Find an attachment';
            sendMail($to,$subject,$message,$attachment_file);
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
        $subject = 'Zone Manager Inacvtive Leads';
        $zone_list = $this->Lead->get_employee_dump(array('hrms_id','name','email_id','zone_id','zone_name'),array('designation' => 'ZD'),array(),'employee_dump');
        foreach ($zone_list as $k => $v) {
            $final = array();
            //FOR ZONE MANAGER
            $branch_list = $this->Lead->get_employee_dump(array('branch_id','branch_name'),array('designation' => 'BR','zone_id' => $v->zone_id),array(),'employee_dump');
            
            $zonal_manager['inactive']  = $this->get_leads(array('type'=>'inactive','till'=>'TAT','user_type'=>'BM','zone_id' => $v->zone_id));
            $zonal_manager = call_user_func_array('array_merge', $zonal_manager);
            
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
            $to = array('email' => $v->email_id,'name' => $v->name);
            $message = 'Please Find an attachment';
            sendMail($to,$subject,$message,$attachment_file);
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
        $subject = 'Zone Manager Unassigned Leads';
        $zone_list = $this->Lead->get_employee_dump(array('hrms_id','name','email_id','zone_id','zone_name'),array('designation' => 'ZD'),array(),'employee_dump');
        foreach ($zone_list as $k => $v) {
            $final = array();
            //FOR ZONE MANAGER
            $branch_list = $this->Lead->get_employee_dump(array('branch_id','branch_name'),array('designation' => 'BR','zone_id' => $v->zone_id),array(),'employee_dump');
            
            $zonal_manager['unassigned']  = $this->get_leads(array('type'=>'unassigned','till'=>'','user_type'=>'BM','zone_id' => $v->zone_id));
            $zonal_manager = call_user_func_array('array_merge', $zonal_manager);
            
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
            $attachment_file = $this->export_to_excel('zm_unassigned_leads',$final['zonal_manager']);
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
                $where = array('MONTH(l.created_on)' => date('m')); //Month till date filter
            }
            if($user_type == 'ZM'){
                $select[] = 'l.zone_id';
                $group_by = array('l.zone_id');
            }elseif($user_type == 'BM'){
                $select[] = 'l.branch_id';
                $where['l.zone_id'] = $zone_id;
                $group_by = array('l.branch_id');
            }elseif($user_type == 'EM'){
                $select[] = 'l.created_by as hrms_id';
                $where['l.branch_id'] = $branch_id;
                $group_by = array('l.created_by');
            }
        }elseif($type == 'unassigned'){
            //Unassigned Leads
            $select = array('COUNT(l.id) as unassigned');
            $where  = array('la.lead_id' => NULL);
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => 'left');
            if($user_type == 'ZM'){
                $select[] = 'l.zone_id';
                $group_by = array('l.zone_id');
            }elseif($user_type == 'BM'){
                $select[] = 'l.branch_id';
                $where['l.zone_id'] = $zone_id;
                $group_by = array('l.branch_id');
            }
        }else{
            //Assigned Leads
            $where  = array('la.is_deleted' => 0,'la.is_updated' => 1);
            if($till == 'mtd'){
                $where['MONTH(la.created_on)']  = date('m');
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
                $select = array('COUNT(l.id) as pending_before');
                $where['la.status IN ("NC","FU")']  = NULL;
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
                $header_value = array('Zone Id','Zone Name','Lead Generated (MTD)','Lead Converted (MTD)','No.of Unassigned Leads','No. of pending leads post Documentation');
                break;
            case 'zm_consolidated_mail':
            $header_value = array('Branch Id','Branch Name','Lead Generated (MTD)','Lead Converted (MTD)','No.of Unassigned Leads','No. of pending leads post Documentation');
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
        $this->load->library('excel');
        $file_name = time().'data.xls';
        $excel_alpha = unserialize(EXCEL_ALPHA);
        $objPHPExcel = $this->excel;
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
            if(in_array($action,array('zm_consolidated_mail','zm_inactive_leads','zm_unassigned_leads'))){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['branch_id']));
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['branch_name']));
            }
            if(in_array($action,array('bm_consolidated_mail','bm_inactive_leads'))){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['hrms_id']));
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['employee_name']));
            }
            if(in_array($action,array('gm_consolidated_mail','zm_consolidated_mail','bm_consolidated_mail'))){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['generated']));
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['converted']));
                if($action == 'bm_consolidated_mail'){
                    $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['pending_before']));
                }else{
                    $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['unassigned']));
                }
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['pending']));
            }
            if(in_array($action,array('bm_inactive_leads','zm_inactive_leads'))){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['inactive']));   
            }
            if(in_array($action,array('zm_unassigned_leads'))){
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
            foreach ($final_zone as $key => $value){
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
            if(count($final_zone) > 0){
                $this->db->insert_batch(Tbl_zone,$final_zone);
                $this->db->insert_batch(Tbl_state,$final_state);
                $this->db->insert_batch(Tbl_district,$final_dist);
                $this->db->insert_batch(Tbl_branch,$final_branch);
                $this->session->set_flashdata('success','Successfully Inserated');
                redirect('dashboard','refresh');
            }else{
                $this->session->set_flashdata('success','All Duplicate entries found.');
                redirect('dashboard','refresh');
            }

        }

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
            $count = 1;
            foreach ($result as $key => $value){
                foreach ($columns as $k => $column){
                    $action = 'count';
                    $table = Tbl_emp_dump;
                    if($k == 0){
                        $whereArray = array('hrms_id ='=>$value[$hrms_keys[$k]]);
                        $count = $this->Lead->get_leads($action,$table,$select=array(),$whereArray,$join=array(),$group_by=array(),$order_by=array());
                    }
                    if($count == 0){
                        $insert[$key][$column] = trim($value[$hrms_keys[$k]]);
                    }
                }
            }
            if(!empty($insert)){
                $this->db->insert_batch(Tbl_emp_dump,$insert);
                redirect('dashboard','refresh');
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
}