<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

   /**
     * construct
     * constructor method
     * @author Ashok Jadhav
	 * @access private
     * @param none
     * @return void
     * 
     */
	function __construct()
	{
		// Initialization of class
		parent::__construct();
          is_logged_in();     //check login
          $this->load->model('Lead','master');
          
	}

	/**
     * index
     * Index Page for this controller.
     * @author Ashok Jadhav
	 * @access public
     * @param none
     * @return void
     *
     */
	public function index()
	{
        //Get session data
        $input = get_session();
        $middle = '';
        //Create Breadcumb
        /*$arrData['breadcrumb'] = $this->make_bread->output();*/
        $arrData['breadcrumb'] = '';


        //Get session data
        $input = get_session();
        $leads = array();

        if(isset($input['designation_name']) && !empty($input['designation_name'])){
            switch ($input['designation_name']){
                case 'EM':
                    //Parameters buiding for sending to get_leads function.
                    $action = 'count';
                    $select = array();
                    $join = array();

                    //For Generated Leads Count
                    $table = Tbl_Leads;

                        //Month till date
                        $where = array(Tbl_Leads.'.created_by' => $input['hrms_id'],'MONTH('.Tbl_Leads.'.created_on)' => date('m'));
                        $leads['generated_mtd'] = $this->master->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());

                        //Year till date
                        $where  = array(Tbl_Leads.'.created_by' => $input['hrms_id'],'YEAR('.Tbl_Leads.'.created_on)' => date('Y'));
                        $leads['generated_ytd'] = $this->master->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());

                    //For converted leads Count
                    $table = Tbl_LeadAssign;

                        //Month till date
                        $where = array(Tbl_LeadAssign.'.employee_id' => $input['hrms_id'],Tbl_LeadAssign.'.status' => 'Converted',Tbl_LeadAssign.'.is_deleted' => 0,'MONTH('.Tbl_LeadAssign.'.created_on)' => date('m'));
                        $leads['converted_mtd'] = $this->master->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());


                        //Year till date
                        $where  = array(Tbl_LeadAssign.'.employee_id' => $input['hrms_id'],Tbl_LeadAssign.'.status' => 'Converted',Tbl_LeadAssign.'.is_deleted' => 0,'YEAR('.Tbl_LeadAssign.'.created_on)' => date('Y'));
                        $leads['converted_ytd'] = $this->master->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());

                    //For assigned leads Count
                    $table = Tbl_LeadAssign;

                        //Year till date
                        $where  = array(Tbl_LeadAssign.'.employee_id' => $input['hrms_id'],Tbl_LeadAssign.'.is_deleted' => 0,'YEAR('.Tbl_LeadAssign.'.created_on)' => date('Y'));
                        $leads['assigned_leads'] = $this->master->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());

                    $arrData['leads'] = $leads;

                    //Middle view
                    $middle = "dashboard";
                    break;
                case 'BM':
                    $branch_id = $input['branch_id'];
                    $arrData['leads'] = $this->bm_view($branch_id);
                    $middle = "Leads/view/bm_view";
                    break;
                case 'ZM':
                    $zone_id = $input['zone_id'];
                    $arrData['leads'] = $this->zm_view($zone_id);
                    $middle = "Leads/view/zm_view";
                    break;
                case 'RM':
                    $arrData['leads'] = $this->gm_view();
                    $middle = "Leads/view/gm_view";
                    break;
            }

        }
        return load_view($middle,$arrData);
	}

    /**
     * bm_view
     * loads the branch manager view
     * @author Gourav Thatoi
     */
    public function bm_view($branch_id){

        //for generated lead
        $where_month_Array = array('branch_id' => $branch_id,
            'MONTH(created_on)' => date('m'));
        $generated_value = $this->master->get_generated_lead_bm_zm($where_month_Array);
        //for converted lead
        $final = array();
        $login_user = get_session();
        $result = get_details($login_user['designation_name']);
        foreach ($result['employee_list'] as $key =>$value){
            $generated_key_value = array();
            foreach ($generated_value as $k => $v) {
                $generated_key_value[$v['created_by']] = $v['total'];
            }
            if (!array_key_exists($value['id'], $generated_key_value)) {
                $push_generated = array(
                    'created_by' => $value['id'],
                    'created_by_name' => $value['full_name'],
                    'total_generated' => 0);
            } else {
                $push_generated = array(
                    'created_by' => $value['id'],
                    'created_by_name' => $value['full_name'],
                    'total_generated' => $generated_key_value[$value['id']]);
            }
            $final[$value['id']] = $push_generated;
        }
        foreach ($final as $id => $value) {

            $where_month_Array = array('employee_id' => $value['created_by'],
                'MONTH(created_on)' => date('m'),
                'status' => 'converted');
            $converted = $this->master->get_converted_lead_bm_zm($where_month_Array);
            if (!empty($converted)) {
                $converted = 0;
            }
            $final[$value['created_by']]['total_converted'] = $converted;
        }
        return $final;
    }

    /**
     * zm_view
     * loads the zonal manager view
     * @author Gourav Thatoi
     */
    public function zm_view($zone_id){

        $where_month_Array = array(
            'zone_id' => $zone_id,
            'MONTH(created_on)' => date('m') - 1
        );
        $generated = $this->master->get_generated_lead_bm_zm($where_month_Array);
        $generated_key_value = array();
        $final = array();
        $login_user = get_session();
        $result = get_details($login_user['designation_name']);
        foreach ($generated as $k => $v) {
            $generated_key_value[$v['branch_id']] = $v['total'];
        }
        /*pe($this->db->last_query());
        exit;*/
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
            $converted = $this->master->get_converted_lead_bm_zm($where_month_Array);
            if (!empty($converted)) {
                $converted = 0;
            }
            $final[$value['created_by_branch_id']]['total_converted'] = $converted;
        }
        return $final;
    }

    /**
     * zm_view
     * loads the zonal manager view
     * @author Gourav Thatoi
     */
    public function gm_view(){

        $where_month_Array = array(
            'zone_id !=' => NULL,
            'MONTH(created_on)' => date('m') - 1
        );
        $generated = $this->master->get_generated_lead_bm_zm($where_month_Array);
        $generated_key_value = array();
        $final = array();
        $login_user = get_session();
        $result = get_details($login_user['designation_name']);
        foreach ($generated as $k => $v) {
            $generated_key_value[$v['zone_id']] = $v['total'];
        }
        
        foreach ($result['zone_list'] as $key => $val) {
            if (!array_key_exists($val['id'], $generated_key_value)) {
                $push_generated = array(
                    'created_by_zone_id' => $val['id'],
                    'created_by_zone_name' => $val['full_name'],
                    'total_generated' => 0
                );
            } else {
                $push_generated = array(
                    'created_by_zone_id' => $val['id'],
                    'created_by_zone_name' => $val['full_name'],
                    'total_generated' => $generated_key_value[$val['id']]
                );
            }
            $final[$val['id']] = $push_generated;
        }
        /*pe($final);
        exit;*/
        //for converted
        foreach ($final as $id => $value) {
            $where_month_Array = array(
                'zone_id' => $value['created_by_zone_id'],
                'MONTH(created_on)' => date('m'),
                'status' => 'converted'
            );
            $converted = $this->master->get_converted_lead_bm_zm($where_month_Array);
            if (!empty($converted)) {
                $converted = 0;
            }
            $final[$value['created_by_zone_id']]['total_converted'] = $converted;
        }
        return $final;
    }


    /**
     * leads_performance
     * loads the performance of employee
     * @author Gourav Thatoi
     */
    public function leads_performance($id=''){
        $input = get_session();;
        $branch_id = decode_id($id);
        $created_by = decode_id($id);
        $source = $this->config->item('lead_source');
        if($this->session->userdata('admin_type')=='EM')
        $created_by = $input['hrms_id'];
        $action = 'count';
        $table = Tbl_Leads;
        $result = array();
        $join[] = array('table' => Tbl_LeadAssign, 'on_condition' => Tbl_Leads . '.id = ' . Tbl_LeadAssign . '.lead_id', 'type' => '');
        $select = array();
        $middle = "Leads/view/performance";
        $this->make_bread->add('My Lead Performance', '', 0);
        $result['breadcrumb'] = $this->make_bread->output();

        if ($this->session->userdata('admin_type') == 'ZM') {

            foreach ($source as $key => $lead_source){
                $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => $lead_source);
                $result['lead_assigned_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');
                $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => $lead_source);
                $result['month_lead_assigned_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

                $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), Tbl_Leads . '.lead_source' => $lead_source,
                    Tbl_LeadAssign . '.status' => 'Converted');
                $result['lead_converted_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

                $where = array(Tbl_LeadAssign . '.branch_id' => $branch_id, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'), Tbl_Leads . '.lead_source' => $lead_source,
                    Tbl_LeadAssign . '.status' => 'Converted');
                $result['month_lead_converted_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');
            }

        }if($this->session->userdata('admin_type') =='BM' || $this->session->userdata('admin_type') =='EM'){

            foreach ($source as $key => $lead_source){
            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'),
                Tbl_Leads . '.lead_source' => $lead_source);
            $result['lead_assigned_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'),
                Tbl_Leads . '.lead_source' => $lead_source);
            $result['month_lead_assigned_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'),
                Tbl_Leads . '.lead_source' => $lead_source,Tbl_LeadAssign . '.status' => 'Converted');
            $result['lead_converted_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            $where = array(Tbl_LeadAssign . '.employee_id' => $created_by, Tbl_LeadAssign . '.is_deleted' => 0, 'MONTH(' . Tbl_LeadAssign . '.created_on)' => date('m'),
                Tbl_Leads . '.lead_source' => $lead_source,Tbl_LeadAssign . '.status' => 'Converted');
            $result['month_lead_converted_'.$key] = $this->master->get_leads($action, $table, $select, $where, $join, '', '');

            }
        }
        load_view($middle,$result);

    }

    /**
     * leads_status
     * loads the status of employee
     * @author Gourav Thatoi
     */
   public function leads_status($id='',$name=''){
        $result = array();
        $status = $this->config->item('lead_status');
        $designation_type = $this->session->userdata('admin_type');
        $join[] = array('table' => Tbl_Leads, 'on_condition' => Tbl_Leads . '.id = ' . Tbl_LeadAssign . '.lead_id', 'type' => '');
        
        if(!empty($designation_type) && $designation_type == 'ZM'){
            $table = Tbl_LeadAssign;
            $action = 'count';
            $id=$this->uri->segment(3);
            $branch_id = decode_id($id);
            $result['branch_id'] = $branch_id;
            $this->make_bread->add('Generated Leads', '', 0);
//            $this->make_bread->add($branch_id, '', 0);  //Put Branch name Here

            if(!empty($status)){
                foreach ($status as $key => $value) {
                    $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => $key, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'));
                    $result[$key]['Year'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');

                    $whereArray = array(Tbl_Leads.'.branch_id' => $branch_id, 'status' => $key, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'));
                    $result[$key]['Month'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');
                }
            }
        }

        if(!empty($designation_type) && $designation_type == 'RM'){
            $table = Tbl_LeadAssign;
            $action = 'count';
            $id=$this->uri->segment(3);
            $zone_id = decode_id($id);
            $result['zone_id'] = $zone_id;
            $this->make_bread->add('Generated Leads', '', 0);
//            $this->make_bread->add($branch_id, '', 0);  //Put Branch name Here

            if(!empty($status)){
                foreach ($status as $key => $value) {
                    $whereArray = array(Tbl_Leads.'.zone_id' => $zone_id, 'status' => $key, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'));
                    $result[$key]['Year'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');

                    $whereArray = array(Tbl_Leads.'.zone_id' => $zone_id, 'status' => $key, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'));
                    $result[$key]['Month'] = $this->master->get_leads($action, $table, '', $whereArray, $join, '', '');
                }
            }
        }
        
        if(!empty($designation_type) && ($designation_type == 'BM' || $designation_type == 'EM')){
            $table = Tbl_LeadAssign;
            $action = 'count';
            $employee_id = decode_id($id);
            $result['employee_id'] = $employee_id;
            $result['employee_name'] = $name;
            if($designation_type == 'EM'){
                $input = get_session();
                $employee_id = $input['hrms_id'];
                $result['employee_id'] = $employee_id;
                $result['employee_name'] = $input['full_name'];
            }
            $this->make_bread->add('Generated Leads', '', 0);
            //$this->make_bread->add($employee_id, '', 0);  //Put Employee name Here
            if(!empty($status)){
                foreach ($status as $key => $value) {
                    $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>$key, 'YEAR(' . Tbl_Leads . '.created_on)' => date('Y'));
                    $result[$key]['Year'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');

                    $whereArray = array(Tbl_Leads.'.created_by'=>$employee_id,'status'=>$key, 'MONTH(' . Tbl_Leads . '.created_on)' => date('m'));
                    $result[$key]['Month'] = $this->master->get_leads($action,$table,'',$whereArray,$join,'','');        
                }
            }
        }
        $result['breadcrumb'] = $this->make_bread->output();
        $middle = "Leads/view/status";
        load_view($middle,$result);

   }
    public function emi_calculator(){
        $this->make_bread->add('EMI Calculator', '', 0);
        $result['breadcrumb'] = $this->make_bread->output();
        $middle = '/emi_calculator';
        load_view($middle,$result);
    }

    /**
     * export_excel
     * Excel export
     * @author Gourav Thatoi
     * @access public
     * @paramas none
     * @return  void
     */
    public function export_excel(){
        $this->load->library('excel');
        $file_name = time().'data.xls';
        $excel_alpha = unserialize(EXCEL_ALPHA);
        $objPHPExcel = $this->excel;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
        $objPHPExcel->getDefaultStyle()->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($excel_alpha[0])->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($excel_alpha[1])->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($excel_alpha[2])->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($excel_alpha[3])->setAutoSize(true);
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
        $objSheet = $objPHPExcel->getActiveSheet();
        $objSheet->getStyle($excel_alpha[0].'1')
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle($excel_alpha[1].'1')
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle($excel_alpha[2].'1')
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle($excel_alpha[3].'1')
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(-1);
        $objSheet->getCell($excel_alpha[0].'1')->setValue('Sr.No');
        $objSheet->getCell($excel_alpha[1].'1')->setValue('Employee Name');
        $objSheet->getCell($excel_alpha[2].'1')->setValue('Generated Leads(This Month)');
        $objSheet->getCell($excel_alpha[3].'1')->setValue('Converted Leads(This Month)');

        $login_user = get_session();
        $branch_id = $login_user['branch_id'];
        $branch_data = $this->bm_view($branch_id);
        $i=2;$j=1;
        foreach ($branch_data as $key => $value){
            $objSheet->getStyle($excel_alpha[0].$i)
                ->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getStyle($excel_alpha[1].$i)
                ->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $objSheet->getStyle($excel_alpha[2].$i)
                ->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getStyle($excel_alpha[3].$i)
                ->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getCell($excel_alpha[0].$i)->setValue($j);
            $objSheet->getCell($excel_alpha[1].$i)->setValue($value['created_by_name']);
            $objSheet->getCell($excel_alpha[2].$i)->setValue($value['total']);
            $objSheet->getCell($excel_alpha[3].$i)->setValue($value['converted_leads']);
            $i++;$j++;
        }

        //downloads excel
        make_upload_directory('uploads');
        make_upload_directory('uploads/excel_list');
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $objWriter->save('php://output');

    }
    
}
