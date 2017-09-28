<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Charts extends CI_Controller
{


    /*
     * __construct
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
        is_logged_in();
        $this->load->model('Lead');
        $this->load->model('Master_model','Master');
    }

    /*
     * index
     * Common function which make call to requested function.
     * @author Ashok Jadhav (AJ)
     * @access public
     * @param $action,$view,$zone_id,$branch_id,$export = 'no'
     * @return void
     *
     */
    public function index($action,$view = null,$zone_id = null,$branch_id = null,$export = 'no')
    {
        $this->make_bread->add('Charts', '', 0);
        /*$d = new DateTime('first day of this month');
        $arrData['start_date'] = str_replace('-', '-', $d->format('d-m-Y'));
        $arrData['end_date']   = str_replace('-', '-',date('d-m-Y'));*/
        if($action == 'leads_generated_vs_converted'){
            $arrData = $this->$action('generated',array());
            $arrData = $this->$action('converted',$arrData);
            $arrData = $this->combine($arrData);
        }else{
            $arrData = $this->$action();
        }
        $arrData['breadcrumb'] = $this->make_bread->output();
        /*pe($arrData);
        exit;*/
        return load_view('Charts/'.$action,$arrData);
    }

    private function pendancy_leads_reports(){
        $this->make_bread->add('Pendancy Leads', '', 0);
        $lead_status = array_keys($this->config->item('lead_status'));

        //Get Listing for branch
        $SELECT = array('zone_id','zone_name');
        $WHERE = array();
        //$WHERE['designation'] = 'ZD';
        $GROUP_BY = array('zone_id');
        $TABLE  = 'employee_dump';
        $LIST = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE);

        //Build Input Parameter
        $action = 'list';
        $select = array('la.zone_id','COUNT(la.lead_id) as count','la.status');
        $table = Tbl_Leads.' as l';
        $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'la.status NOT IN ("AO","Converted","Closed")' => NULL);
        $join = array();
        $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
        $group_by = array('la.zone_id','la.status');
        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'count DESC');
        /*pe($this->db->last_query());
        exit;*/

        $arrData['Total'] = 0;
        if($LIST){
            foreach ($leads as $key => $value) {
                $zone['ids'][] = $value['zone_id'];
                //$arrData['Total'] += $value['count'];
                $zone['status'][$value['zone_id']][$value['status']] = $value['count'];
            }
            foreach ($LIST as $key => $value) {
                $index = $value->zone_id;
                $arrData['zone_id'][] = $value->zone_id;
                $arrData['zone_name'][] = $value->zone_name;
                if(!in_array($value->zone_id,$zone['ids'])){
                    foreach ($lead_status as $k => $v){
                        if(!in_array($v,array("AO","Converted","Closed"))){
                            $arrData['status'][$v][] = 0;
                        }
                    }
                }else{
                    foreach ($lead_status as $k => $v){
                        if(!in_array($v,array("AO","Converted","Closed"))) {
                            $arrData['status'][$v][] = isset($zone['status'][$index][$v]) ? $zone['status'][$index][$v] : 0;
                        }
                    }
                }
            }
        }
        return $arrData;
    }

    private function leads_type_reports(){
        $this->make_bread->add('Leads Identification', '', 0);
        $lead_type = array_keys($this->config->item('lead_type'));

        //Get Listing for Zone
        $SELECT = array('zone_id','zone_name');
        $WHERE = array();
        //$WHERE['designation'] = 'ZD';
        $GROUP_BY = array('zone_id');
        $TABLE  = 'employee_dump';
        $LIST = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE);

        //Build Input Parameter
        $action = 'list';
        $select = array('l.zone_id','COUNT(la.lead_id) as count','l.lead_identification');
        $table = Tbl_Leads.' as l';
        $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'l.lead_identification IN ("'.str_replace(',','","',implode(',',$lead_type)).'")' => NULL);
        $join = array();
        $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
        $group_by = array('l.zone_id','l.lead_identification');
        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'count DESC');

        $arrData['Total'] = 0;
        if($LIST){
            foreach ($leads as $key => $value) {
                $zone['ids'][] = $value['zone_id'];
                //$arrData['Total'] += $value['count'];
                $zone['lead_identification'][$value['zone_id']][$value['lead_identification']] = $value['count'];
            }
            foreach ($LIST as $key => $value) {
                $index = $value->zone_id;
                $arrData['zone_id'][] = $value->zone_id;
                $arrData['zone_name'][] = $value->zone_name;
                if(!in_array($value->zone_id,$zone['ids'])){
                    foreach ($lead_type as $k => $v){
                        $arrData['lead_identification'][$v][] = 0;
                    }
                }else{
                    foreach ($lead_type as $k => $v){
                        $arrData['lead_identification'][$v][] = isset($zone['lead_identification'][$index][$v]) ? $zone['lead_identification'][$index][$v] : 0;
                    }
                }
            }
        }
        return $arrData;
    }

    private function leads_generated(){
        $this->make_bread->add('Leads Generated', '', 0);
        $lead_status = array_keys($this->config->item('lead_status'));
        //Get Listing for branch
        $SELECT = array('zone_id','zone_name');
        $WHERE = array();
        //$WHERE['designation'] = 'ZD';
        $GROUP_BY = array('zone_id');
        $TABLE  = 'employee_dump';
        $LIST = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE);

        //Build Input Parameter
        $action = 'list';
        $select = array('l.zone_id','COUNT(l.id) as count','la.status');
        $table = Tbl_Leads.' as l';
        $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'la.status IN ("'.str_replace(',','","',implode(',',$lead_status)).'")' => NULL);
        $join = array();
        $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
        $group_by = array('l.zone_id','la.status');
        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'count DESC');
        //pe($this->db->last_query());

        $arrData['Total'] = 0;
        if($LIST){
            foreach ($leads as $key => $value) {
                $zone['ids'][] = $value['zone_id'];
                //$arrData['Total'] += $value['count'];
                $zone['status'][$value['zone_id']][$value['status']] = $value['count'];
            }
            foreach ($LIST as $key => $value){
                $index = $value->zone_id;
                $arrData['zone_id'][] = $value->zone_id;
                $arrData['zone_name'][] = $value->zone_name;
                if(!in_array($value->zone_id,$zone['ids'])){
                    foreach ($lead_status as $k => $v){
                        $arrData['status'][$v][] = 0;
                    }
                }else{
                    foreach ($lead_status as $k => $v){
                        $arrData['status'][$v][] = isset($zone['status'][$index][$v]) ? $zone['status'][$index][$v] : 0;

                    }
                }
            }
        }
        return $arrData;
    }

    private function leads_assigned(){
        $this->make_bread->add('Leads Assigned', '', 0);
        $lead_status = array_keys($this->config->item('lead_status'));
        //Get Listing for ZOne
        $SELECT = array('zone_id','zone_name');
        $WHERE = array();
        //$WHERE['designation'] = 'ZD';
        $GROUP_BY = array('zone_id');
        $TABLE  = 'employee_dump';
        $LIST = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE);

        //Build Input Parameter
        $action = 'list';
        $select = array('la.zone_id','COUNT(la.lead_id) as count','la.status');
        $table = Tbl_Leads.' as l';
        $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'la.status IN ("'.str_replace(',','","',implode(',',$lead_status)).'")' => NULL);
        $join = array();
        $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
        $group_by = array('la.zone_id','la.status');
        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'count DESC');
        //pe($this->db->last_query());

        $arrData['Total'] = 0;
        if($LIST){
            foreach ($leads as $key => $value) {
                $zone['ids'][] = $value['zone_id'];
                //$arrData['Total'] += $value['count'];
                $zone['status'][$value['zone_id']][$value['status']] = $value['count'];
            }
            foreach ($LIST as $key => $value) {
                $index = $value->zone_id;
                $arrData['zone_id'][] = $value->zone_id;
                $arrData['zone_name'][] = $value->zone_name;
                if(!in_array($value->zone_id,$zone['ids'])){
                    foreach ($lead_status as $k => $v){
                        $arrData['status'][$v][] = 0;
                    }
                }else{
                    foreach ($lead_status as $k => $v){
                        $arrData['status'][$v][] = isset($zone['status'][$index][$v]) ? $zone['status'][$index][$v] : 0;

                    }
                }
            }
        }
        return $arrData;
    }

    private function leads_generated_vs_converted($type,$arrData){
        $lead_status = array_keys($this->config->item('lead_status'));
        //Build Input Parameter
        $action = 'list';
        $table = Tbl_Leads.' as l';
        $join = array();
        if($type == 'generated'){
            $select = array('COUNT(l.id) as generated_count');
            $where  = array();
            $alias = 'l';
        }else{
            $select = array('COUNT(la.lead_id) as converted_count');
            $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'la.status' => 'Converted');
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
            $alias = 'la';
        }
        $group_by = array();
        $select[] = $alias.'.zone_id';
        $group_by[] = $alias.'.zone_id';

        //Get Listing for branch
        $SELECT = array('zone_id','zone_name');
        $WHERE = array();
        //$WHERE['designation'] = 'ZD';
        $GROUP_BY = array('zone_id');
        $TABLE  = 'employee_dump';
        if(!isset($arrData['list'])){
            $arrData['list'] = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE);
        }

        $arrData[$type] = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array());
        //pe($this->db->last_query());
        return $arrData;
    }

    private function combine($arrData){
        $this->make_bread->add('Leads Generated Vs Converted', '', 0);
        $arrData['G_Total'] = $arrData['C_Total'] = 0;

        $leads = array_merge($arrData['generated'],$arrData['converted']);
        if($arrData['list']){
            foreach ($leads as $key => $value) {
                $zone['ids'][] = $value['zone_id'];
                if(isset($value['generated_count'])){
                    if(isset($zone['generated_count'][$value['zone_id']])){
                        $zone['generated_count'][$value['zone_id']] += $value['generated_count'];
                    }else{
                        $zone['generated_count'][$value['zone_id']] = $value['generated_count'];
                    }
                    //$arrData['G_Total'] += $value['generated_count'];
                }
                if(isset($value['converted_count'])){
                    if(isset($zone['converted_count'][$value['zone_id']])){
                        $zone['converted_count'][$value['zone_id']] += $value['converted_count'];
                    }else{
                        $zone['converted_count'][$value['zone_id']] = $value['converted_count'];
                    }
                    //$arrData['C_Total'] += $value['converted_count'];
                }
            }
            foreach ($arrData['list'] as $key => $value) {
                $index = $value->zone_id;
                $arrData['zone_id'][] = $value->zone_id;
                $arrData['zone_name'][] = $value->zone_name;
                if(!in_array($value->zone_id,$zone['ids'])){
                    $arrData['generated_count'][] = 0;
                    $arrData['converted_count'][] = 0;
                }else{
                    $arrData['generated_count'][] = isset($zone['generated_count'][$index]) ? $zone['generated_count'][$index] : 0;
                    $arrData['converted_count'][] = isset($zone['converted_count'][$index]) ? $zone['converted_count'][$index] : 0;
                }
            }
        }
        return $arrData;
    }

    private function leads_classification(){
        $this->make_bread->add('Leads Classification', '', 0);
        //Get Listing for branch
        $SELECT = array('zone_id','zone_name');
        $WHERE = array();
        //$WHERE['designation'] = 'ZD';
        $GROUP_BY = array('zone_id');
        $TABLE  = 'employee_dump';
        $LIST = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE);

        //Build Input Parameter
        $action = 'list';
        $select = array('l.zone_id','SUM(l.lead_ticket_range) as lead_ticket_range');
        $table = Tbl_Leads.' as l';
        $where  = array();
        $join = array();
        $group_by = array('l.zone_id',/*'l.lead_ticket_range'*/);
        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'lead_ticket_range DESC');

        $arrData['Total'] = 0;
        if($LIST){
            foreach ($leads as $key => $value) {
                $zone['ids'][] = $value['zone_id'];
                //$arrData['Total'] += $value['lead_ticket_range'];
                $zone['ticket'][$value['zone_id']] = $value['lead_ticket_range'];
            }
            foreach ($LIST as $key => $value){
                $index = $value->zone_id;
                $arrData['zone_id'][] = $value->zone_id;
                $arrData['zone_name'][] = $value->zone_name;
                if(!in_array($value->zone_id,$zone['ids'])){
                    $arrData['ticket'][] = 0;
                }else{
                    $arrData['ticket'][] = isset($zone['ticket'][$index]) ? $zone['ticket'][$index] : 0;
                }
            }
        }
        return $arrData;
    }

    private function usage(){
        $this->make_bread->add('Usage', '', 0);

        //Get Listing for Zone
        $SELECT = array('zone_id','zone_name','COUNT(hrms_id) as total_user');
        $WHERE = array();
        //$WHERE['designation'] = 'ZD';
        $GROUP_BY = array('zone_id');
        $TABLE  = 'employee_dump';
        $LIST = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE);

        //Build Input Parameter
        $action = 'list';
        $select = array('COUNT(DISTINCT(l.employee_id)) as count','l.zone_id');
        $table = Tbl_LoginLog.' as l';
        $where  = array();
        $join = array();
        $group_by = array('l.zone_id');
        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'count DESC');

        $arrData['Total'] = 0;
        if($LIST){
            foreach ($leads as $key => $value) {
                //Zone Manager Login
                $zone['ids'][] = $value['zone_id'];
                $zone['logged_in'][$value['zone_id']] = $value['count'];
            }
            foreach ($LIST as $key => $value) {
                $index = $value->zone_id;
                $arrData['zone_id'][] = $value->zone_id;
                $arrData['zone_name'][] = $value->zone_name;

                if(!in_array($value->zone_id,$zone['ids'])){
                    $arrData['logged_in'][] = 0;
                }else{
                    $arrData['logged_in'][] = $zone['logged_in'][$index];
                }

                if(isset($value->total_user)){
                    $arrData['Total'] += $value->total_user;
                    $arrData['not_logged_in'][] = ($value->total_user -  isset($zone['logged_in'][$index]) ? $zone['logged_in'][$index] : 0);
                }
            }
        }
        return $arrData;
    }
}