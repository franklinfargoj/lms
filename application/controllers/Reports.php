<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Reports extends CI_Controller
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
        is_logged_in();
        $this->load->model('Lead');
        $this->load->model('Master_model','Master');
    }

    public function index($action = 'pendancy_leads_reports',$view = null,$zone_id = null,$branch_id = null)
    {
        $this->make_bread->add('Reports', '', 0);   
        $arrData['breadcrumb'] = $this->make_bread->output();
        $arrData['view'] = $view;
        $arrData['zone_id'] = decode_id($zone_id);
        $arrData['branch_id'] = decode_id($branch_id);
        if($this->input->post()){
            $arrData['start_date']     = $this->input->post('start_date');
            $arrData['end_date']   = $this->input->post('end_date');
            $arrData['product_category_id']    = $this->input->post('product_category_id');
            $arrData['product_id']     = $this->input->post('product_id');
            $arrData['lead_source'] = $this->input->post('lead_source');
            
            //
            $arrData['view'] = $this->input->post('view');
            $arrData['zone_id'] = decode_id($this->input->post('zone_id'));
            $arrData['branch_id'] = decode_id($this->input->post('branch_id'));
            $arrData = $this->$action($arrData);
            if(!empty($arrData['product_category_id'])){
                $product_list = $this->Lead->get_all_products(array('category_id' => $arrData['product_category_id'],'is_deleted' => 0,'status' => 'active'));
                $arrData['product_list'] = dropdown($product_list,'All');
            }
        }else{
            $arrData['start_date']  = date('Y-m-d',strtotime("-2 days"));;
            $arrData['end_date']   = date('Y-m-d');
            $arrData = $this->$action($arrData);
        }
        //Get All dropdown
        $category_list = $this->Lead->get_all_category(array('is_deleted' => 0,'status' => 'active'));
        $arrData['category_list'] = dropdown($category_list,'All');
        $arrData['lead_sources'] = $this->Lead->get_enum(Tbl_Leads,'lead_source');
        return load_view('Reports/'.$action,$arrData);
    }

    private function pendancy_leads_reports($arrData){

        $login_user = get_session();
        //Build Input Parameter
        $action = 'list';
        $select = array('COUNT(*) as count','la.status');
        $table = Tbl_Leads.' as l';
        $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'la.status NOT IN ("AO","Converted","Closed")' => NULL);
        $join = array();
        $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
        $group_by = array('la.status');

        //If Start date selected
        if(!empty($arrData['start_date'])){
            $where['DATE_FORMAT(la.created_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
        }
        //If End date selected
        if(!empty($arrData['end_date'])){
            $where['DATE_FORMAT(la.created_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
        }
        //If Category selected
        if(!empty($arrData['product_category_id'])){
            $where['l.product_category_id'] = $arrData['product_category_id'];

            $categoryData = $this->Master->view_product_category($arrData['product_category_id']);
            $arrData['category'] = $categoryData[0]['title'];  //Get Title
        }
        //If Product selected
        if(!empty($arrData['product_id'])){
            $where['l.product_id'] = $arrData['product_id'];

            $productData = $this->Master->view_product($arrData['product_id']);
            $arrData['product'] = $productData[0]['title'];   //Get Title
        }
        //If Lead Source selected
        if(!empty($arrData['lead_source'])){
            $where['l.lead_source'] = $arrData['lead_source'];
        }

        if($arrData['view'] == 'employee'){
            $viewName = 'EM';
        }else if($arrData['view']){
            $viewName = 'BM';
        }else{
            $viewName = $login_user['designation_name'];
        }
        
        //Employee Login
        if($viewName == 'EM'){
            $select[] = 'la.employee_id';
            $select[] = 'la.branch_id';
            $select[] = 'la.zone_id';

            $where['la.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
            if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                if(!empty($arrData['branch_id'])){
                    $where['la.branch_id'] = $arrData['branch_id'];
                }
            }else{
                $where['la.branch_id'] = $login_user['branch_id'];
            }

            $group_by[]  =  'la.employee_id';
        }

        //Branch Manager Login
        if($viewName == 'BM'){
            $select[] = 'la.branch_id';
            $select[] = 'la.zone_id';
            
            $where['la.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
            
            $group_by[] = 'la.branch_id';
        }

        //Zone Manager Login
        if($viewName == 'ZM'){
            $select[] = 'la.zone_id';
            $group_by[] = 'la.zone_id';
        }

        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array());
        //pe($this->db->last_query());
        $arrData['leads'] = array();

        foreach ($leads as $key => $value) {
            //Employee Login
            if($viewName == 'EM'){
                $index = $value['employee_id'];
                $arrData['leads'][$index]['employee_id'] = $value['employee_id'];
                $arrData['leads'][$index]['branch_id'] = $value['branch_id'];
                $arrData['leads'][$index]['zone_id'] = $value['zone_id'];
            }

            //Branch Manager Login
            if($viewName == 'BM'){
                $index = $value['branch_id'];
                $arrData['leads'][$index]['branch_id'] = $value['branch_id'];
                $arrData['leads'][$index]['zone_id'] = $value['zone_id'];
            }

            //Zone Manager Login
            if($viewName == 'ZM'){
                $index = $value['zone_id'];
                $arrData['leads'][$index]['zone_id'] = $value['zone_id'];
            }
            
            if(isset($arrData['leads'][$index]['total'])){
                $arrData['leads'][$index]['total'] += $value['count'];
            }else{
                $arrData['leads'][$index]['total'] = $value['count'];    
            }
            $arrData['leads'][$index]['status'][$value['status']] = $value['count'];
        }
        $arrData['viewName'] = $viewName;
        //pe($arrData);
        return $arrData;
    }


    private function leads_type_reports($arrData){

        $login_user = get_session();
        $lead_type = array_keys($this->config->item('lead_type'));
        //Build Input Parameter
        $action = 'list';
        $select = array('COUNT(*) as count','l.lead_identification');
        $table = Tbl_Leads.' as l';
        $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'l.lead_identification IN ("'.str_replace(',','","',implode(',',$lead_type)).'")' => NULL);
        $join = array();
        $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
        $group_by = array('l.lead_identification');

        //If Start date selected
        if(!empty($arrData['start_date'])){
            $where['DATE_FORMAT(la.created_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
        }
        //If End date selected
        if(!empty($arrData['end_date'])){
            $where['DATE_FORMAT(la.created_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
        }
        //If Category selected
        if(!empty($arrData['product_category_id'])){
            $where['l.product_category_id'] = $arrData['product_category_id'];

            $categoryData = $this->Master->view_product_category($arrData['product_category_id']);
            $arrData['category'] = $categoryData[0]['title'];  //Get Title
        }
        //If Product selected
        if(!empty($arrData['product_id'])){
            $where['l.product_id'] = $arrData['product_id'];

            $productData = $this->Master->view_product($arrData['product_id']);
            $arrData['product'] = $productData[0]['title'];   //Get Title
        }
        //If Lead Source selected
        if(!empty($arrData['lead_source'])){
            $where['l.lead_source'] = $arrData['lead_source'];
        }

        if($arrData['view'] == 'employee'){
            $viewName = 'EM';
        }else if($arrData['view']){
            $viewName = 'BM';
        }else{
            $viewName = $login_user['designation_name'];
        }
        
        //Employee Login
        if($viewName == 'EM'){
            $select[] = 'la.employee_id';
            $select[] = 'la.branch_id';
            $select[] = 'la.zone_id';

            $where['la.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
            if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                if(!empty($arrData['branch_id'])){
                    $where['la.branch_id'] = $arrData['branch_id'];
                }
            }else{
                $where['la.branch_id'] = $login_user['branch_id'];
            }

            $group_by[]  =  'la.employee_id';
        }

        //Branch Manager Login
        if($viewName == 'BM'){
            $select[] = 'la.branch_id';
            $select[] = 'la.zone_id';
            
            $where['la.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
            
            $group_by[] = 'la.branch_id';
        }

        //Zone Manager Login
        if($viewName == 'ZM'){
            $select[] = 'la.zone_id';
            $group_by[] = 'la.zone_id';
        }

        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array());
        //pe($this->db->last_query());
        $arrData['leads'] = array();

        foreach ($leads as $key => $value) {
            //Employee Login
            if($viewName == 'EM'){
                $index = $value['employee_id'];
                $arrData['leads'][$index]['employee_id'] = $value['employee_id'];
                $arrData['leads'][$index]['branch_id'] = $value['branch_id'];
                $arrData['leads'][$index]['zone_id'] = $value['zone_id'];
            }

            //Branch Manager Login
            if($viewName == 'BM'){
                $index = $value['branch_id'];
                $arrData['leads'][$index]['branch_id'] = $value['branch_id'];
                $arrData['leads'][$index]['zone_id'] = $value['zone_id'];
            }

            //Zone Manager Login
            if($viewName == 'ZM'){
                $index = $value['zone_id'];
                $arrData['leads'][$index]['zone_id'] = $value['zone_id'];
            }
            
            if(isset($arrData['leads'][$index]['total'])){
                $arrData['leads'][$index]['total'] += $value['count'];
            }else{
                $arrData['leads'][$index]['total'] = $value['count'];    
            }
            $arrData['leads'][$index]['lead_identification'][$value['lead_identification']] = $value['count'];
        }
        $arrData['viewName'] = $viewName;
        //pe($arrData);
        return $arrData;
    }

}