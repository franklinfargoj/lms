<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cron extends CI_Controller
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
    }


    public function gm_consolidated_mail()
    {   
        $final = array();
        //For GENERAL MANAGER
        $general_manager = array('generated' => array(),'converted' => array(),'unassigned' => array(),'pending' => array());
        $gm = $general_manager;
        $zone_list = $this->Lead->get_employee_dump(array('zone_id','zone_name'),array(),array('zone_id'),'employee_dump');
        
        $general_manager['generated']  = $this->get_leads('generated','mtd','ZM');
        $assigned   = $this->get_leads('assigned','mtd','ZM');
        foreach ($assigned as $key => $value) {
            if($value['status'] == 'Converted'){
                $data = array();
                $data['converted'] = $value['count'];
                $data['zone_id'] = $value['zone_id'];
                $general_manager['converted'][] = $data;
            }
            elseif(in_array($value['status'],array('AO','NI','CBC'))){
                $data = array();
                $data['pending'] = $value['count'];
                $data['zone_id'] = $value['zone_id'];
                $general_manager['pending'][] = $data;
            }
        }
        $general_manager['unassigned'] = $this->get_leads('unassigned','','ZM');
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
        $to = array('email' => 'mukesh.kurmi@wwindia.com','name' => 'Mukesh Kurmi');
        $subject = 'General Manager Consolidated Format';
        $message = 'Please Find an attachment';
        sendMail($to,$subject,$message,$attachment_file);
    }

    public function zm_consolidated_mail(){
        $final = array();
        //FOR ZONAL MANAGER
        $zonal_manager = array('generated' => array(),'converted' => array(),'unassigned' => array(),'pending' => array());
        $gm = $zonal_manager;
        $branch_list = $this->Lead->get_employee_dump(array('branch_id','branch_name'),array(),array('branch_id'),'employee_dump');
        
        $zonal_manager['generated']  = $this->get_leads('generated','mtd','BM');
        $assigned   = $this->get_leads('assigned','mtd','BM');
        foreach ($assigned as $key => $value) {
            if($value['status'] == 'Converted'){
                $data = array();
                $data['converted'] = $value['count'];
                $data['branch_id'] = $value['branch_id'];
                $zonal_manager['converted'][] = $data;
            }
            elseif(in_array($value['status'],array('AO','NI','CBC'))){
                $data = array();
                $data['pending'] = $value['count'];
                $data['branch_id'] = $value['branch_id'];
                $general_manager['pending'][] = $data;
            }
        }
        $zonal_manager['unassigned'] = $this->get_leads('unassigned','','BM');
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
        $to = array('email' => 'mukesh.kurmi@wwindia.com','name' => 'Mukesh Kurmi');
        $subject = 'Zonal Manager Consolidated Format';
        $message = 'Please Find an attachment';
        sendMail($to,$subject,$message,$attachment_file);
    }       

    public function bm_consolidated_mail(){
        $final = array();
        //FOR EMPLOYEE
        $branch_manager = array('generated' => array(),'converted' => array(),'unassigned' => array(),'pending' => array());
        $gm = $branch_manager;
        $employee_list = $this->Lead->get_employee_dump(array('hrms_id','name'),array(),array('hrms_id'),'employee_dump');
        
        $branch_manager['generated']  = $this->get_leads('generated','mtd','EM');
        $assigned   = $this->get_leads('assigned','mtd','EM');
        foreach ($assigned as $key => $value) {
            if($value['status'] == 'Converted'){
                $data = array();
                $data['converted'] = $value['count'];
                $data['hrms_id'] = $value['hrms_id'];
                $branch_manager['converted'][] = $data;
            }
            elseif(in_array($value['status'],array('AO','NI','CBC'))){
                $data = array();
                $data['pending'] = $value['count'];
                $data['hrms_id'] = $value['hrms_id'];
                $general_manager['pending'][] = $data;
            }
            elseif(in_array($value['status'],array('NC','FU','DC'))){
                $data = array();
                $data['pending_before'] = $value['count'];
                $data['hrms_id'] = $value['hrms_id'];
                $general_manager['pending'][] = $data;
            }
        }
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
                $final['branch_manager'][$value->hrms_id]['pending_before'] = isset($total['pending_before'][$value->hrms_id]) ? $total['unassigned'][$value->hrms_id] : 0;
                $final['branch_manager'][$value->hrms_id]['pending'] = isset($total['pending'][$value->hrms_id]) ? $total['pending'][$value->hrms_id] : 0;
            }
            $final['branch_manager'][$value->hrms_id]['hrms_id'] = $value->hrms_id;
            $final['branch_manager'][$value->hrms_id]['employee_name'] = $value->name;
        }
        //FOR EMPLOYEE
        $attachment_file = $this->export_to_excel('bm_consolidated_mail',$final['branch_manager']);
        $to = array('email' => 'mukesh.kurmi@wwindia.com','name' => 'Mukesh Kurmi');
        $subject = 'Branch Manager Consolidated Format';
        $message = 'Please Find an attachment';
        sendMail($to,$subject,$message,$attachment_file);
    }


    private function get_leads($type,$till,$user_type){
        //Parameters buiding for sending to list function.
        $action = 'list';
        $table = Tbl_Leads.' as l';
        $join = array();
        if($type == 'generated'){
            $select = array('COUNT(l.id) as generated');
            if($till == 'mtd'){
                $where = array('MONTH(l.created_on)' => date('m')); //Month till date filter
            }
            if($user_type == 'ZM'){
                $select[] = 'l.zone_id';
                $group_by = array('l.zone_id');
            }elseif($user_type == 'BM'){
                $select[] = 'l.branch_id';
                $group_by = array('l.branch_id');
            }elseif($user_type == 'EM'){
                $select[] = 'l.created_by as hrms_id';
                $group_by = array('l.created_by');
            }
        }
        if($type == 'assigned'){
            $select = array('COUNT(l.id) as count','la.status');
            if($till == 'mtd'){
                $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'MONTH(la.created_on)' => date('m'));
            }
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
            if($user_type == 'ZM'){
                $select[] = 'la.zone_id';
                $group_by = array('la.zone_id');
            }elseif($user_type == 'BM'){
                $select[] = 'la.branch_id';
                $group_by = array('la.branch_id');
            }elseif($user_type == 'EM'){
                $select[] = 'la.employee_id as hrms_id';
                $group_by = array('la.employee_id');
            }
            $group_by[] = 'la.status';
        }

        if($type == 'unassigned'){
            $select = array('COUNT(l.id) as unassigned');
            $where  = array('la.lead_id' => NULL);
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => 'left');
            if($user_type == 'ZM'){
                $select[] = 'l.zone_id';
                $group_by = array('l.zone_id');
            }elseif($user_type == 'BM'){
                $select[] = 'l.branch_id';
                $group_by = array('l.branch_id');
            }
        }
        
        return $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array());
        /*pe($this->db->last_query());
        echo "<hr>";*/
        //exit;
    }

    public function export_to_excel($action,$arrData){
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
        }
        return $this->create_excel($action,$header_value,$arrData);
    }


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
            if($action == 'zm_consolidated_mail'){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['branch_id']));
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['branch_name']));
            }
            if($action == 'bm_consolidated_mail'){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['hrms_id']));
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['employee_name']));
            }
            $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['generated']));
            $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['converted']));
            if($action == 'bm_consolidated_mail'){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['pending_before']));
            }else{
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['unassigned']));
            }
            $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['pending']));
           
            $i++;$j++;
        }
        $objWriter->save('uploads/excel_list/'.$file_name);
        return $file_name;
    }

    

    

    

}