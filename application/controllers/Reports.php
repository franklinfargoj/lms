<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Reports extends CI_Controller
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

        $param1 = $this->uri->segment(3,0);
        $admin = ucwords(strtolower($this->session->userdata('admin_type')));
        if ($admin == 'Em'){
            redirect('dashboard');
        }
        if ($admin != 'Super Admin'){
            if($param1 == 'dashboard'){
                redirect('dashboard');
            }
        }

        $this->load->model('Lead');
        $this->load->model('Master_model','Master');
    }

    /*
     * index
     * Commen function to call for each search and initial page load.
     * @author Ashok Jadhav (AJ)
     * @access public
     * @param $action,$view,$zone_id,$branch_id,$export = 'no'
     * @return void
     *
     */
    public function index($action,$view = null,$zone_id = null,$branch_id = null,$export = 'no')
    {
        $this->make_bread->add('Reports', '', 0);
        $arrData['view'] = $view;
        $arrData['zone_id'] = decode_id($zone_id);
        $arrData['branch_id'] = decode_id($branch_id);
        $arrData['national'] = '';
        if($this->input->post()){

            $arrData['start_date']  = str_replace('-', '-',$this->input->post('start_date'));
            $arrData['end_date']   = str_replace('-', '-',$this->input->post('end_date'));
            $arrData['product_category_id']    = $this->input->post('product_category_id');
            $arrData['product_id']     = $this->input->post('product_id');
            $arrData['lead_source'] = $this->input->post('lead_source');
            $export = $this->input->post('export');
            $arrData['national'] = $this->input->post('national');
            //
            $arrData['view'] = $this->input->post('view');
            $arrData['zone_id'] = decode_id($this->input->post('zone_id'));
            $arrData['branch_id'] = decode_id($this->input->post('branch_id'));
            if($action == 'leads_generated_vs_converted'){
                $arrData = $this->$action('generated',$arrData);
                $arrData = $this->$action('assigned',$arrData);
                $arrData = $this->$action('converted',$arrData);
                $arrData = $this->$action('actual_business',$arrData);
                //pe($arrData);die;
                $arrData = $this->combine($arrData);
                //pe($arrData);die;
            }else{
                $arrData = $this->$action($arrData);
//                pe($arrData);die;
            }
            if(!empty($arrData['product_category_id'])){
                $product_list = $this->Lead->get_all_products(array('category_id' => $arrData['product_category_id'],'is_deleted' => 0,'status' => 'active'));
                $arrData['product_list'] = dropdown($product_list,'All');
            }

            if($export == 'yes'){
                if(!empty($arrData['lead_source'])){
                    //pe($arrData);die;
                    $lead_sources = $this->config->item('lead_source');
                    $arrData['lead_source'] = $lead_sources[$arrData['lead_source']];
                }

                if($action != 'status_flow') {
                    $this->export_to_excel($action, $arrData);
                }else{
                    $this->export_to_excel_master($arrData);
                }
            }
        }else{

            $d = new DateTime('first day of this month');
            $arrData['start_date'] = str_replace('-', '-', $d->format('d-m-Y'));
            $arrData['end_date']   = str_replace('-', '-',date('d-m-Y'));
            if($action == 'leads_generated_vs_converted'){
                $arrData = $this->$action('generated',$arrData);
                $arrData = $this->$action('assigned',$arrData);
                $arrData = $this->$action('converted',$arrData);
                $arrData = $this->$action('actual_business',$arrData);
//pe($arrData);die;
                $arrData = $this->combine($arrData);
                //pe($arrData);die;
            }else{
                $arrData = $this->$action($arrData);

            }

        }

        //Get All dropdown
        $category_list = $this->Lead->get_all_category(array('is_deleted' => 0,'status' => 'active'));
        $arrData['category_list'] = dropdown($category_list,'All');
        $arrData['lead_sources'] = $this->Lead->get_enum(Tbl_Leads,'lead_source');
        $arrData['breadcrumb'] = $this->make_bread->output();
        $arrData['view'] = $view;
        return load_view('Reports/'.$action,$arrData);
    }

    public function view()
    {
        $this->make_bread->add('Reports', '', 0);
        $arrData['breadcrumb'] = $this->make_bread->output();
        return load_view('Reports/index',$arrData);
    }

    private function pendancy_leads_reports($arrData){
        $login_user = get_session();
        //Build Input Parameter
        $action = 'list';
        $select = array('COUNT(la.lead_id) as count','la.status','p.turn_around_time');
        $table = Tbl_Leads.' as l';
        $day = date( 'Y-m-d', strtotime( date('Y-m-d') . ' -2 day' ) ).' 00:00:00';
        $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'la.status NOT IN ("AO","Converted","Closed","NI")' => NULL);
        $where["CASE WHEN la.status = 'NC' THEN la.modified_on <'$day' WHEN la.status = 'FU' THEN la.followup_date < '$day' WHEN la.status = 'DC' THEN p.turn_around_time < DATEDIFF(CURDATE(),la.modified_on) END"]=NULL;
        $join = array();
        $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
        //$join[] = array('table' => Tbl_Reminder.' as fr','on_condition' => 'fr.lead_id = l.id','type' => '');
        $join[] = array('table' => Tbl_Products.' as p','on_condition' => 'p.id = l.product_id','type' => '');
        $group_by = array('la.status');

//        //If Start date selected
//        if(!empty($arrData['start_date'])){
//            $where['DATE_FORMAT(la.created_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
//        }
//        //If End date selected
//        if(!empty($arrData['end_date'])){
//            $where['DATE_FORMAT(la.created_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
//        }
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

        if(($arrData['view'] == 'employee') || ($arrData['national'] == 'yes')){
            $this->make_bread->add('Pendency Leads', 'reports/index/pendancy_leads_reports', 0);
            if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                if(!empty($arrData['branch_id'])){
                    if($login_user['designation_name'] != 'BM'){
                        $this->make_bread->add('Branch View', 'reports/index/pendancy_leads_reports/branch/'.encode_id($arrData['zone_id']), 0);
                    }
                }
            }
            $this->make_bread->add('Employee View', '', 0);
            $viewName = 'EM';
        }else if($arrData['view']){
            $this->make_bread->add('Pendency Leads', 'reports/index/pendancy_leads_reports', 0);
            $this->make_bread->add('Branch View', '', 0);
            $viewName = 'BM';
        }else{
            $this->make_bread->add('Pendency Leads', '', 0);
            $viewName = $login_user['designation_name'];
            if($viewName == 'GM' || $viewName == 'Super admin'){
                $viewName = 'ZM';
            }
        }

        $WHERE = array();
        //Employee Login
        if($viewName == 'EM'){
            //Get Data for employees
            $select[] = 'la.employee_id';
            if($arrData['national'] != 'yes'){
                $where['la.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
                if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                    if(!empty($arrData['branch_id'])){
                        $where['la.branch_id'] = $arrData['branch_id'];
                    }
                }else{
                    $where['la.branch_id'] = $login_user['branch_id'];
                }
            }
            $group_by[]  =  'la.employee_id';

            //Get Listing for employees
            $SELECT = array('hrms_id as employee_id','name as employee_name','branch_id','branch_name','zone_id','zone_name','designation');
            if(isset($where['la.zone_id'])){
                $WHERE['zone_id'] = $where['la.zone_id'];
            }
            if(isset($where['la.branch_id'])){
                $WHERE['branch_id'] = $where['la.branch_id'];
            }
            //$WHERE['designation'] = 'HD';
            $GROUP_BY = array('hrms_id');
        }

        //Branch Manager Login
        if($viewName == 'BM'){
            //Get Data for branch
            $select[] = 'la.branch_id';
            $where['la.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
            $group_by[] = 'la.branch_id';

            //Get Listing for branch
            $SELECT = array('branch_id','branch_name','zone_id','zone_name');
            if(isset($where['la.zone_id'])){
                $WHERE['zone_id'] = $where['la.zone_id'];
            }
            //$WHERE['designation'] = 'BR';
            $GROUP_BY = array('branch_id');
        }

        //Zone Manager Login
        if($viewName == 'ZM'){
            //Get Data for zone
            $select[] = 'la.zone_id';
            $group_by[] = 'la.zone_id';

            //Get Listing for branch
            $SELECT = array('zone_id','zone_name');
            $WHERE = array();
            //$WHERE['designation'] = 'ZD';
            $GROUP_BY = array('zone_id');
        }

        $TABLE  = 'employee_dump';
        $list = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE,$viewName);
        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'count DESC');
        //pe($this->db->last_query());
        $arrData['leads'] = array();
        $arrData['Total'] = 0;
        if($list){
            $Lead['userId'] = array();
            if(!empty($leads)) {
                foreach ($leads as $key => $value) {
                    //Employee Login
                    if ($viewName == 'EM') {
                        $index = $value['employee_id'];
                        $Lead['userId'][] = $value['employee_id'];
                    }

                    //Branch Manager Login
                    if ($viewName == 'BM') {
                        $index = $value['branch_id'];
                        $Lead['userId'][] = $value['branch_id'];
                    }

                    //Zone Manager Login
                    if ($viewName == 'ZM') {
                        $index = $value['zone_id'];
                        $Lead['userId'][] = $value['zone_id'];
                    }
                    $Lead[$index]['status'][$value['status']] = $value['count'];
                }
            }
            $arrData['viewName'] = $viewName;
            foreach ($list as $key => $value) {
                //Employee Login
                if($viewName == 'EM'){
                    $index = $value->employee_id;
                    $arrData['leads'][$index]['employee_id'] = $value->employee_id;
                    $arrData['leads'][$index]['employee_name'] = $value->employee_name;
                    $arrData['leads'][$index]['designation'] = $value->designation;
                    $arrData['leads'][$index]['branch_name'] = $value->branch_name;
                    $arrData['leads'][$index]['branch_id'] = $value->branch_id;
                }
                //Branch Manager Login
                if($viewName == 'BM'){
                    $index = $value->branch_id;
                    $arrData['leads'][$index]['branch_name'] = $value->branch_name;
                    $arrData['leads'][$index]['branch_id'] = $value->branch_id;
                }

                //Zone Manager Login
                if($viewName == 'ZM'){
                    $index = $value->zone_id;
                }
                $arrData['leads'][$index]['zone_name'] = $value->zone_name;
                $arrData['leads'][$index]['zone_id'] = $value->zone_id;
                if(!in_array($index,$Lead['userId'])){
                    $arrData['leads'][$index]['total'] = 0;
                    $arrData['leads'][$index]['status'] = array();
                }else{
                    $arrData['leads'][$index]['total'] = array_sum($Lead[$index]['status']);
                    $arrData['leads'][$index]['status'] = $Lead[$index]['status'];
                }
                $arrData['Total'] += $arrData['leads'][$index]['total'];
            }

            if($this->session->userdata('admin_type') == 'BM' && $arrData['view'] == ''){
                $arrData['leads'] = array($this->session->userdata('branch_id')=> $arrData['leads'][$this->session->userdata('branch_id')]) + $arrData['leads'];
            }
            if($this->session->userdata('admin_type') == 'ZM' && $arrData['view'] == ''){
                $arrData['leads'] = array($this->session->userdata('zone_id')=> $arrData['leads'][$this->session->userdata('zone_id')]) + $arrData['leads'];
            }
        }
        return $arrData;
    }

    private function leads_type_reports($arrData){
        $login_user = get_session();
        $lead_type = array_keys($this->config->item('lead_type'));
        //Build Input Parameter
        $action = 'list';
        $select = array('COUNT(l.id) as count','l.lead_identification','SUM(l.lead_ticket_range) as lead_ticket_range');
        $table = Tbl_Leads.' as l';
        $where  = array(/*'la.is_deleted' => 0,'la.is_updated' => 1,*/'l.lead_identification IN ("'.str_replace(',','","',implode(',',$lead_type)).'")' => NULL);
        $where['la.is_deleted = 0 AND la.is_updated =1 AND la.status IN ("FU")']= NULL;
        $join = array();
        $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');

        //$join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
        $group_by = array('l.lead_identification');

        //If Start date selected
        if(!empty($arrData['start_date'])){
            $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
        }
        //If End date selected
        if(!empty($arrData['end_date'])){
            $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
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

        if(($arrData['view'] == 'employee') || ($arrData['national'] == 'yes')){
            $this->make_bread->add('Leads Identification', 'reports/index/leads_type_reports', 0);
            if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                if(!empty($arrData['branch_id'])){
                    if($login_user['designation_name'] != 'BM'){
                        $this->make_bread->add('Branch View', 'reports/index/leads_type_reports/branch/'.encode_id($arrData['zone_id']), 0);
                    }
                }
            }
            $this->make_bread->add('Employee View', '', 0);
            $viewName = 'EM';
        }else if($arrData['view']){
            $this->make_bread->add('Interested Leads', 'reports/index/leads_type_reports', 0);
            $this->make_bread->add('Branch View', '', 0);
            $viewName = 'BM';
        }else{
            $this->make_bread->add('Interested Leads', '', 0);
            $viewName = $login_user['designation_name'];
            if($viewName == 'GM' || $viewName == 'Super admin'){
                $viewName = 'ZM';
            }
        }

        $WHERE = array();
        //Employee Login
        if($viewName == 'EM'){
            //Get Data for employees
            $select[] = 'l.created_by as employee_id';
            if($arrData['national'] != 'yes'){
                $where['l.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
                if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                    if(!empty($arrData['branch_id'])){
                        $where['l.branch_id'] = $arrData['branch_id'];
                    }
                }else{
                    $where['l.branch_id'] = $login_user['branch_id'];
                }
            }
            $group_by[]  =  'l.created_by';

            //Get Listing for employees
            $SELECT = array('hrms_id as employee_id','name as employee_name','branch_id','branch_name','zone_id','zone_name','designation');
            if(isset($where['l.zone_id'])){
                $WHERE['zone_id'] = $where['l.zone_id'];
            }
            if(isset($where['l.branch_id'])){
                $WHERE['branch_id'] = $where['l.branch_id'];
            }
            //$WHERE['designation'] = 'HD';
            $GROUP_BY = array('hrms_id');
        }

        //Branch Manager Login
        if($viewName == 'BM'){
            //Get Data for branch
            $select[] = 'l.branch_id';
            $where['l.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
            $group_by[] = 'l.branch_id';

            //Get Listing for branch
            $SELECT = array('branch_id','branch_name','zone_id','zone_name');
            if(isset($where['l.zone_id'])){
                $WHERE['zone_id'] = $where['l.zone_id'];
            }
            //$WHERE['designation'] = 'BR';
            $GROUP_BY = array('branch_id');
        }

        //Zone Manager Login
        if($viewName == 'ZM'){
            //Get Data for zone
            $select[] = 'l.zone_id';
            $group_by[] = 'l.zone_id';

            //Get Listing for branch
            $SELECT = array('zone_id','zone_name');
            $WHERE = array();
            //$WHERE['designation'] = 'ZD';
            $GROUP_BY = array('zone_id');
        }
        $TABLE  = 'employee_dump';
        $list = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE,$viewName);

        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'count DESC');
        //pe($this->db->last_query());
        //pe($leads);die;
        $arrData['leads'] = array();
        $arrData['Total'] = 0;
        if($list){
            $Lead['userId'] = array();
            if(!empty($leads)) {
                foreach ($leads as $key => $value) {
                    //Employee Login
                    if ($viewName == 'EM') {
                        $index = $value['employee_id'];
                        $Lead['userId'][] = $value['employee_id'];
                    }
                    //Branch Manager Login
                    if ($viewName == 'BM') {
                        $index = $value['branch_id'];
                        $Lead['userId'][] = $value['branch_id'];
                    }
                    //Zone Manager Login
                    if ($viewName == 'ZM') {
                        $index = $value['zone_id'];
                        $Lead['userId'][] = $value['zone_id'];
                    }
                    $Lead[$index]['lead_identification'][$value['lead_identification']] = $value['count'];
                    $Lead[$index]['lead_ticket_range'][$value['lead_ticket_range']] = $value['lead_ticket_range'];

                }
            }
            // pe($Lead);die;
            $arrData['viewName'] = $viewName;
            foreach ($list as $key => $value) {
                //Employee Login
                if($viewName == 'EM'){
                    $index = $value->employee_id;
                    $arrData['leads'][$index]['employee_id'] = $value->employee_id;
                    $arrData['leads'][$index]['employee_name'] = $value->employee_name;
                    $arrData['leads'][$index]['designation'] = $value->designation;
                    $arrData['leads'][$index]['branch_name'] = $value->branch_name;
                    $arrData['leads'][$index]['branch_id'] = $value->branch_id;
                }
                //Branch Manager Login
                if($viewName == 'BM'){
                    $index = $value->branch_id;
                    $arrData['leads'][$index]['branch_name'] = $value->branch_name;
                    $arrData['leads'][$index]['branch_id'] = $value->branch_id;
                }

                //Zone Manager Login
                if($viewName == 'ZM'){
                    $index = $value->zone_id;
                }
                $arrData['leads'][$index]['zone_name'] = $value->zone_name;
                $arrData['leads'][$index]['zone_id'] = $value->zone_id;
                if(!in_array($index,$Lead['userId'])){
                    $arrData['leads'][$index]['total'] = 0;
                    $arrData['leads'][$index]['lead_ticket_range'] = 0;
                    $arrData['leads'][$index]['lead_identification'] = array();
                }else{
                    $arrData['leads'][$index]['total'] = array_sum($Lead[$index]['lead_identification']);
                    $arrData['leads'][$index]['lead_ticket_range'] = array_sum($Lead[$index]['lead_ticket_range']);
                    $arrData['leads'][$index]['lead_identification'] = $Lead[$index]['lead_identification'];
                }
                $arrData['Total'] += $arrData['leads'][$index]['total'];
            }
            if($this->session->userdata('admin_type') == 'BM' && $arrData['view'] == ''){
                $arrData['leads'] = array($this->session->userdata('branch_id')=> $arrData['leads'][$this->session->userdata('branch_id')]) + $arrData['leads'];
            }
            if($this->session->userdata('admin_type') == 'ZM' && $arrData['view'] == ''){
                $arrData['leads'] = array($this->session->userdata('zone_id')=> $arrData['leads'][$this->session->userdata('zone_id')]) + $arrData['leads'];
            }
        }
        //pe($arrData);die;
        return $arrData;
    }

    private function leads_generated($arrData){
        $login_user = get_session();
        $lead_status = array_keys($this->config->item('lead_status'));
        //Build Input Parameter

        $action = 'list';
        $select = array('COUNT(l.id) as count','la.status');
        $table = Tbl_Leads.' as l';
        $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'la.status IN ("'.str_replace(',','","',implode(',',$lead_status)).'")' => NULL);
        $join = array();
        $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => 'LEFT');
        $group_by = array('la.status');

        $select1 = array('COUNT(l.id) as generated_count');
        $where1  = array();

        //If Start date selected
        if(!empty($arrData['start_date'])){
            $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
            $where1['DATE_FORMAT(l.created_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
        }
        //If End date selected
        if(!empty($arrData['end_date'])){
            $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
            $where1['DATE_FORMAT(l.created_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
        }
        //If Category selected
        if(!empty($arrData['product_category_id'])){
            $where['l.product_category_id'] = $arrData['product_category_id'];
            $where1['l.product_category_id'] = $arrData['product_category_id'];

            $categoryData = $this->Master->view_product_category($arrData['product_category_id']);
            $arrData['category'] = $categoryData[0]['title'];  //Get Title
        }
        //If Product selected
        if(!empty($arrData['product_id'])){
            $where['l.product_id'] = $arrData['product_id'];
            $where1['l.product_id'] = $arrData['product_id'];

            $productData = $this->Master->view_product($arrData['product_id']);
            $arrData['product'] = $productData[0]['title'];   //Get Title
        }
        //If Lead Source selected
        if(!empty($arrData['lead_source'])){
            $where['l.lead_source'] = $arrData['lead_source'];
            $where1['l.lead_source'] = $arrData['lead_source'];
        }

        if(($arrData['view'] == 'employee') || ($arrData['national'] == 'yes')){
            $this->make_bread->add('Leads Generated', 'reports/index/leads_generated', 0);
            if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                if(!empty($arrData['branch_id'])){
                    if($login_user['designation_name'] != 'BM'){
                        $this->make_bread->add('Branch View', 'reports/index/leads_generated/branch/'.encode_id($arrData['zone_id']), 0);
                    }
                }
            }
            $this->make_bread->add('Employee View', '', 0);
            $viewName = 'EM';
        }else if($arrData['view']){
            $this->make_bread->add('Leads Generated', 'reports/index/leads_generated', 0);
            $this->make_bread->add('Branch View', '', 0);
            $viewName = 'BM';
        }else{
            $this->make_bread->add('Leads Generated', '', 0);
            $viewName = $login_user['designation_name'];
            if($viewName == 'GM' || $viewName == 'Super admin'){
                $viewName = 'ZM';
            }
        }

        $WHERE = array();
        //Employee Login
        if($viewName == 'EM'){
            //Get Data for employees
            $select[] = 'l.created_by as employee_id';
            $select1[] = 'l.created_by as employee_id';
            if($arrData['national'] != 'yes'){
                $where['l.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
                if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                    if(!empty($arrData['branch_id'])){
                        $where['l.branch_id'] = $arrData['branch_id'];
                        $where1['l.branch_id'] = $arrData['branch_id'];
                    }
                }else{
                    $where['l.branch_id'] = $login_user['branch_id'];
                    $where1['l.branch_id'] = $login_user['branch_id'];
                }
            }
            $group_by[]  =  'l.created_by';

            //Get Listing for employees
            $SELECT = array('hrms_id as employee_id','name as employee_name','branch_id','branch_name','zone_id','zone_name','designation');
            if(isset($where['l.zone_id'])){
                $WHERE['zone_id'] = $where['l.zone_id'];
            }
            if(isset($where['l.branch_id'])){
                $WHERE['branch_id'] = $where['l.branch_id'];
            }
            //$WHERE['designation'] = 'HD';
            $GROUP_BY = array('hrms_id');
        }

        //Branch Manager Login
        if($viewName == 'BM'){
            //Get Data for Branch
            $select[] = 'l.created_by_branch_id as branch_id';
            $select1[] = 'l.created_by_branch_id as branch_id';
            $where['l.created_by_zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
            $where1['l.created_by_zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
            $group_by[] = 'l.created_by_branch_id';

            //Get Listing for branch
            $SELECT = array('branch_id','branch_name','zone_id','zone_name');
            if(isset($where['l.created_by_zone_id'])){
                $WHERE['zone_id'] = $where['l.created_by_zone_id'];
            }
            //$WHERE['designation'] = 'BR';
            $GROUP_BY = array('branch_id');
        }

        //Zone Manager Login
        if($viewName == 'ZM'){
            //Get Data for Branch
            $select[] = 'l.created_by_zone_id as zone_id';
            $select1[] = 'l.created_by_zone_id as zone_id';
            $group_by[] = 'l.zone_id';

            //Get Listing for branch
            $SELECT = array('zone_id','zone_name');
            //$WHERE['designation'] = 'ZD';
            $GROUP_BY = array('zone_id');
        }

        $TABLE  = 'employee_dump';
        $list = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE,$viewName);

        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'count DESC');

        $group_by1 = array_pop($group_by);

        $generated_leads = $this->Lead->get_leads($action,$table,$select1,$where1,$join=array(),$group_by1,$order_by = '');
//pe($leads);
//pe($generated_leads);//die;
        /*pe($this->db->last_query());
       exit;*/
        //pe($unassigned_leads_count);die;
//        $arrData['leads'] = array();
//        $table = Tbl_Leads.' as l';
//        $join = array();
//        $select = array('COUNT(l.id1) as generated_count');
//        $where  = array();
//        $alias = 'l';
//        $arrData[generated_count] = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array());
        $arrData['Total'] = 0;
        if($list){
            $Lead['userId'] = array();
            //$generatedLead['userId'] = array();

            if(!empty($generated_leads)) {
                foreach ($generated_leads as $key => $value) {
                    //Employee Login
                    if ($viewName == 'EM') {
                        $index = $value['employee_id'];
                        $generatedLead['userId'][] = $value['employee_id'];
                    }

                    //Branch Manager Login
                    if ($viewName == 'BM') {
                        $index = $value['branch_id'];
                        $generatedLead['userId'][] = $value['branch_id'];
                    }

                    //Zone Manager Login
                    if ($viewName == 'ZM') {
                        $index = $value['zone_id'];
                        $generatedLead['userId'][] = $value['zone_id'];
                    }

                    $generatedLead[$index]['generated'] = $value['generated_count'];
                }
            }
            if(!empty($leads)) {
                foreach ($leads as $key => $value) {
                    //Employee Login
                    if ($viewName == 'EM') {
                        $index = $value['employee_id'];
                        $Lead['userId'][] = $value['employee_id'];
                    }

                    //Branch Manager Login
                    if ($viewName == 'BM') {
                        $index = $value['branch_id'];
                        $Lead['userId'][] = $value['branch_id'];
                    }

                    //Zone Manager Login
                    if ($viewName == 'ZM') {
                        $index = $value['zone_id'];
                        $Lead['userId'][] = $value['zone_id'];
                    }

                    $Lead[$index]['status'][$value['status']] = $value['count'];
                }
            }
//            pe($Lead);
            //pe($generatedLead);die;
            $arrData['viewName'] = $viewName;
            foreach ($list as $key => $value) {
                //echo $index;echo "<br>";
                //Employee Login
                if($viewName == 'EM'){
                    $index = $value->employee_id;
                    $arrData['leads'][$index]['employee_id'] = $value->employee_id;
                    $arrData['leads'][$index]['employee_name'] = $value->employee_name;
                    $arrData['leads'][$index]['designation'] = $value->designation;
                    $arrData['leads'][$index]['branch_name'] = $value->branch_name;
                    $arrData['leads'][$index]['branch_id'] = $value->branch_id;
                }
                //Branch Manager Login
                if($viewName == 'BM'){
                    $index = $value->branch_id;
                    $arrData['leads'][$index]['branch_name'] = $value->branch_name;
                    $arrData['leads'][$index]['branch_id'] = $value->branch_id;
                }

                //Zone Manager Login
                if($viewName == 'ZM'){
                    $index = $value->zone_id;
                }
                $arrData['leads'][$index]['zone_name'] = $value->zone_name;
                $arrData['leads'][$index]['zone_id'] = $value->zone_id;
                if(!empty($Lead['userId']) && !empty($generatedLead['userId'])){

                    $generatedCnt = 0;
                    $status = array();
                    if(isset($generatedLead[$index])){
                        $generatedCnt = $generatedLead[$index]['generated'];
                    }
                    if(isset($Lead[$index])){
                        $status = $Lead[$index]['status'];
                    }
                    $arrData['leads'][$index]['total'] = $generatedCnt;
                    $arrData['leads'][$index]['status'] = $status;

                }
                if(empty($Lead['userId']) && !empty($generatedLead['userId'])){

                    $generatedCnt = 0;
                    if(isset($generatedLead[$index])){
                        $generatedCnt = $generatedLead[$index]['generated'];
                    }
                    $arrData['leads'][$index]['total'] = $generatedCnt;
                    $arrData['leads'][$index]['status'] = array();

                }
                if(!empty($Lead['userId']) && empty($generatedLead['userId'])){
                    if(!in_array($index,$Lead['userId'])){
                        $arrData['leads'][$index]['total'] = 0;
                        $arrData['leads'][$index]['status'] = array();
                    }else{
                        $arrData['leads'][$index]['total'] = 0;
                        $arrData['leads'][$index]['status'] = array();
                    }
                }

                $arrData['Total'] = $arrData['Total']+$generatedCnt;
            }
            if($this->session->userdata('admin_type') == 'BM' && $arrData['view'] == ''){
                $arrData['leads'] = array($this->session->userdata('branch_id')=> $arrData['leads'][$this->session->userdata('branch_id')]) + $arrData['leads'];
            }
            if($this->session->userdata('admin_type') == 'ZM' && $arrData['view'] == ''){
                $arrData['leads'] = array($this->session->userdata('zone_id')=> $arrData['leads'][$this->session->userdata('zone_id')]) + $arrData['leads'];
            }
        }
        //pe($arrData);die;
        return $arrData;
    }

    private function leads_assigned($arrData){
        $login_user = get_session();
        $lead_status = array_keys($this->config->item('lead_status'));
        //Build Input Parameter
        $action = 'list';
        $select = array('COUNT(DISTINCT(la.lead_id)) as count','la.status');
        $table = Tbl_LeadAssign.' as la';
        $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'la.status IN ("'.str_replace(',','","',implode(',',$lead_status)).'")' => NULL);
        //$where  = array('la.status IN ("'.str_replace(',','","',implode(',',$lead_status)).'")' => NULL);
        $join = array();
        $join[] = array('table' => Tbl_Leads.' as l','on_condition' => 'la.lead_id = l.id','type' => '');
        $group_by = array('la.status');

        //If Start date selected
        if(!empty($arrData['start_date'])){
            $where['DATE_FORMAT(la.modified_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
        }
        //If End date selected
        if(!empty($arrData['end_date'])){
            $where['DATE_FORMAT(la.modified_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
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

        if(($arrData['view'] == 'employee') || ($arrData['national'] == 'yes')){
            $this->make_bread->add('Current Location',  'reports/index/leads_assigned', 0);
            if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                if(!empty($arrData['branch_id'])){
                    if($login_user['designation_name'] != 'BM'){
                        $this->make_bread->add('Branch View', 'reports/index/leads_assigned/branch/'.encode_id($arrData['zone_id']), 0);
                    }
                }
            }
            $this->make_bread->add('Employee View', '', 0);
            $viewName = 'EM';
        }else if($arrData['view']){
            $this->make_bread->add('Current Location',  'reports/index/leads_assigned', 0);
            $this->make_bread->add('Branch View', '', 0);
            $viewName = 'BM';
        }else{
            $this->make_bread->add('Current Location', '', 0);
            $viewName = $login_user['designation_name'];
            if($viewName == 'GM' || $viewName == 'Super admin'){
                $viewName = 'ZM';
            }
        }

        $WHERE = array();
        //Employee Login
        if($viewName == 'EM'){
            //Get Data for employees
            $select[] = 'la.employee_id';
            if($arrData['national'] != 'yes'){
                $where['la.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
                if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                    if(!empty($arrData['branch_id'])){
                        $where['la.branch_id'] = $arrData['branch_id'];
                    }
                }else{
                    $where['la.branch_id'] = $login_user['branch_id'];
                }
            }
            $group_by[]  =  'la.employee_id';

            //Get Listing for employees
            $SELECT = array('hrms_id as employee_id','name as employee_name','branch_id','branch_name','zone_id','zone_name','designation');
            if(isset($where['la.zone_id'])){
                $WHERE['zone_id'] = $where['la.zone_id'];
            }
            if(isset($where['la.branch_id'])){
                $WHERE['branch_id'] = $where['la.branch_id'];
            }
            //$WHERE['designation'] = 'HD';
            $GROUP_BY = array('hrms_id');
        }

        //Branch Manager Login
        if($viewName == 'BM'){
            //Get Data for Branch
            $select[] = 'la.branch_id';
            $where['la.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
            $group_by[] = 'la.branch_id';

            //Get Listing for branch
            $SELECT = array('branch_id','branch_name','zone_id','zone_name');
            if(isset($where['la.zone_id'])){
                $WHERE['zone_id'] = $where['la.zone_id'];
            }
            //$WHERE['designation'] = 'BR';
            $GROUP_BY = array('branch_id');
        }

        //Zone Manager Login
        if($viewName == 'ZM'){
            //Get Data for Zone
            $select[] = 'la.zone_id';
            $group_by[] = 'la.zone_id';

            //Get Listing for branch
            $SELECT = array('zone_id','zone_name');
            $WHERE = array();
            //$WHERE['designation'] = 'ZD';
            $GROUP_BY = array('zone_id');

        }

        $TABLE  = 'employee_dump';
        $list = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE,$viewName);

        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'count DESC');
        //pe($this->db->last_query());
        $arrData['leads'] = array();
        $arrData['Total'] = 0;
        if($list){
            $Lead['userId'] = array();
            if(!empty($leads)) {
                foreach ($leads as $key => $value) {
                    //Employee Login
                    if ($viewName == 'EM') {
                        $index = $value['employee_id'];
                        $Lead['userId'][] = $value['employee_id'];
                    }

                    //Branch Manager Login
                    if ($viewName == 'BM') {
                        $index = $value['branch_id'];
                        $Lead['userId'][] = $value['branch_id'];
                    }

                    //Zone Manager Login
                    if ($viewName == 'ZM') {
                        $index = $value['zone_id'];
                        $Lead['userId'][] = $value['zone_id'];
                    }
                    $Lead[$index]['status'][$value['status']] = $value['count'];
                }
            }
            $arrData['viewName'] = $viewName;
            foreach ($list as $key => $value) {
                //Employee Login
                if($viewName == 'EM'){
                    $index = $value->employee_id;
                    $arrData['leads'][$index]['employee_id'] = $value->employee_id;
                    $arrData['leads'][$index]['employee_name'] = $value->employee_name;
                    $arrData['leads'][$index]['designation'] = $value->designation;
                    $arrData['leads'][$index]['branch_name'] = $value->branch_name;
                    $arrData['leads'][$index]['branch_id'] = $value->branch_id;
                }
                //Branch Manager Login
                if($viewName == 'BM'){
                    $index = $value->branch_id;
                    $arrData['leads'][$index]['branch_name'] = $value->branch_name;
                    $arrData['leads'][$index]['branch_id'] = $value->branch_id;
                }

                //Zone Manager Login
                if($viewName == 'ZM'){
                    $index = $value->zone_id;
                }
                $arrData['leads'][$index]['zone_name'] = $value->zone_name;
                $arrData['leads'][$index]['zone_id'] = $value->zone_id;
                if(!in_array($index,$Lead['userId'])){
                    $arrData['leads'][$index]['total'] = 0;
                    $arrData['leads'][$index]['status'] = array();
                }else{
                    $arrData['leads'][$index]['total'] = array_sum($Lead[$index]['status']);
                    $arrData['leads'][$index]['status'] = $Lead[$index]['status'];
                }
                $arrData['Total'] += $arrData['leads'][$index]['total'];
            }
            if($this->session->userdata('admin_type') == 'BM' && $arrData['view'] == ''){
                $arrData['leads'] = array($this->session->userdata('branch_id')=> $arrData['leads'][$this->session->userdata('branch_id')]) + $arrData['leads'];
            }
            if($this->session->userdata('admin_type') == 'ZM' && $arrData['view'] == ''){
                $arrData['leads'] = array($this->session->userdata('zone_id')=> $arrData['leads'][$this->session->userdata('zone_id')]) + $arrData['leads'];
            }
        }
        return $arrData;
    }

    private function leads_generated_vs_converted($type,$arrData){
        $login_user = get_session();
        $lead_status = array_keys($this->config->item('lead_status'));
        //Build Input Parameter
        $action = 'list';
        $table = Tbl_Leads.' as l';
        $join = array();
        if($type == 'generated'){
            $select = array('COUNT(l.id) as generated_count');
            $where  = array();
            $alias = 'l';
        }elseif($type == 'actual_business'){
            $select = array('SUM(rfc.amount) as amount');
            $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'la.status' => 'Converted');
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
            $join[] = array('table' => Tbl_cbs.' as rfc','on_condition' => 'rfc.lead_id = l.id','type' => '');
            $alias = 'la';

        }elseif($type == 'assigned'){
            $table = Tbl_LeadAssign.' as la';
            $select = array('COUNT(DISTINCT(la.lead_id)) as assigned_count');
            //$where  = array('la.status' => 'NC');
            $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'la.status IN ("'.str_replace(',','","',implode(',',$lead_status)).'")' => NULL);
            $join[] = array('table' => Tbl_Leads.' as l','on_condition' => 'l.id = la.lead_id','type' => '');
            $alias = 'la';
        }else{
            $select = array('COUNT(la.lead_id) as converted_count');
            $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'la.status' => 'Converted');
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
            $alias = 'la';
        }

        $group_by = array();


        //If Start date selected
        if(!empty($arrData['start_date'])){
            if($type == 'assigned'){
                $where['DATE_FORMAT('.$alias.'.modified_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
            }else{
                $where['DATE_FORMAT('.$alias.'.created_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
            }

        }
        //If End date selected
        if(!empty($arrData['end_date'])){
            if($type == 'assigned'){
                $where['DATE_FORMAT('.$alias.'.modified_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
            }else{
                $where['DATE_FORMAT('.$alias.'.created_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
            }

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

        if(($arrData['view'] == 'employee') || ($arrData['national'] == 'yes')){
            $viewName = 'EM';
        }else if($arrData['view']){
            $viewName = 'BM';
        }else{
            $viewName = $login_user['designation_name'];
            if($viewName == 'GM' || $viewName == 'Super admin'){
                $viewName = 'ZM';
            }
        }
        $arrData['viewName'] = $viewName;

        $WHERE = array();
        //Employee Login
        if($viewName == 'EM'){
            //Get Data for employees
            if($type == 'generated'){
                $select[] = 'l.created_by as employee_id';
            }else{
                $select[] = 'la.employee_id as employee_id';
            }
            if($arrData['national'] != 'yes'){
                if($type == 'generated'){
                    $where[$alias.'.created_by_zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
                }else{
                    $where[$alias.'.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
                }

                if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                    if(!empty($arrData['branch_id'])){
                        if($type == 'generated'){
                            $where[$alias.'.created_by_branch_id'] = $arrData['branch_id'];
                        }else{
                            $where[$alias.'.branch_id'] = $arrData['branch_id'];
                        }

                    }
                }else{
                    if($type == 'generated'){
                        $where[$alias.'.created_by_branch_id'] = $login_user['branch_id'];
                    }else{
                        $where[$alias.'.branch_id'] = $login_user['branch_id'];
                    }

                }
            }
            if($type == 'generated'){
                $group_by[]  =  'l.created_by';
            }else{
                $group_by[]  =  'la.employee_id';
            }

            //Get Listing for employees
            $SELECT = array('hrms_id as employee_id','name as employee_name','branch_id','branch_name','zone_id','zone_name','designation');
            if(isset($where[$alias.'.zone_id']) || isset($where[$alias.'.created_by_zone_id'])){
                $WHERE['zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
            }
            if(isset($where[$alias.'.branch_id']) || isset($where[$alias.'.created_by_branch_id'])){
                $WHERE['branch_id'] = !empty($arrData['branch_id']) ? $arrData['branch_id'] : $login_user['branch_id'];

            }
            //$WHERE['designation'] = 'HD';
            $GROUP_BY = array('hrms_id');
        }

        //Branch Manager Login
        if($viewName == 'BM'){
            //Get Data for branch
            if($type == 'generated') {
                $select[] = $alias . '.created_by_branch_id as branch_id';
                $where[$alias . '.created_by_zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
                $group_by[] = $alias . '.created_by_branch_id';
            }else{
                $select[] = $alias . '.branch_id';
                $where[$alias . '.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
                $group_by[] = $alias . '.branch_id';
            }
            //Get Listing for branch
            $SELECT = array('branch_id','branch_name','zone_id','zone_name');
            if(isset($where[$alias.'.zone_id']) || isset($where[$alias.'.created_by_zone_id'])){
                $WHERE['zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
            }
            //$WHERE['designation'] = 'BR';
            $GROUP_BY = array('branch_id');

        }

        //Zone Manager Login
        if($viewName == 'ZM'){
            //Get Data for zone
            if($type == 'generated') {
                $select[] = $alias . '.created_by_zone_id as zone_id';
                $select[] = $alias.'.created_by_zone_id';
                $group_by[] = $alias.'.created_by_zone_id';
            }else{
                $select[] = $alias.'.zone_id';
                $group_by[] = $alias.'.zone_id';
            }


            //Get Listing for branch
            $SELECT = array('zone_id','zone_name');
            $WHERE = array();
            //$WHERE['designation'] = 'ZD';
            $GROUP_BY = array('zone_id');
        }

        $TABLE  = 'employee_dump';
        if(!isset($arrData['list'])){
            $arrData['list'] = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE,$viewName);
        }

        $arrData[$type] = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array());
        // pe($this->db->last_query());
        //pe($arrData);die;
        return $arrData;
    }

    private function combine($arrData){
        //pe($arrData);die;
        $login_user = get_session();
        $viewName = $arrData['viewName'];
        if($arrData['view'] == 'employee'){
            $this->make_bread->add('Business Generated', 'reports/index/leads_generated_vs_converted', 0);
            if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                if(!empty($arrData['branch_id'])){
                    if($login_user['designation_name'] != 'BM'){
                        $this->make_bread->add('Branch View', 'reports/index/leads_generated_vs_converted/branch/'.encode_id($arrData['zone_id']), 0);
                    }
                }
            }
            $this->make_bread->add('Employee View', '', 0);
        }else if($arrData['view']){
            $this->make_bread->add('Business Generated', 'reports/index/leads_generated_vs_converted', 0);
            $this->make_bread->add('Branch View', '', 0);
        }else{
            $this->make_bread->add('Business Generated', '', 0);
        }
        $arrData['G_Total'] = $arrData['C_Total'] = 0;
        $arrData['leads'] = array();
//        if(!empty($arrData['generated']) && !empty($arrData['converted']) && !empty($arrData['actual_business'] && !empty($arrData['assigned']))) {
//            $leads = array_merge($arrData['generated'], $arrData['converted'],$arrData['actual_business'],$arrData['assigned']);
//        }
        $leads = array_merge((array)$arrData['generated'],(array) $arrData['converted'],(array)$arrData['actual_business'],(array)$arrData['assigned']);
        //pe($leads);die;
        if($arrData['list']) {
            $Lead['userId'] = array();
            if (!empty($leads)){
                foreach ($leads as $key => $value) {
                    //Employee Login
                    if ($viewName == 'EM') {
                        $index = $value['employee_id'];
                        $Lead['userId'][] = $value['employee_id'];
                    }

                    //Branch Manager Login
                    if ($viewName == 'BM') {
                        $index = $value['branch_id'];
                        $Lead['userId'][] = $value['branch_id'];
                    }

                    //Zone Manager Login
                    if ($viewName == 'ZM') {
                        $index = $value['zone_id'];
                        $Lead['userId'][] = $value['zone_id'];
                    }

                    if (isset($value['generated_count'])) {
                        if (isset($Lead[$index]['generated_count'])) {
                            $Lead[$index]['generated_count'] += $value['generated_count'];

                        } else {
                            $Lead[$index]['generated_count'] = $value['generated_count'];
                        }
                    }
                    if (isset($value['converted_count'])) {
                        if (isset($Lead[$index]['converted_count'])) {
                            $Lead[$index]['converted_count'] += $value['converted_count'];
                        } else {
                            $Lead[$index]['converted_count'] = $value['converted_count'];
                        }
                    }
                    if (isset($value['amount'])) {
                        if (isset($Lead[$index]['amount'])) {
                            $Lead[$index]['actual_business'] += $value['amount'];
                        } else {
                            $Lead[$index]['actual_business'] = $value['amount'];
                        }
                    }
                    if (isset($value['assigned_count'])) {
                        if (isset($Lead[$index]['assigned_count'])) {
                            $Lead[$index]['assigned_count'] += $value['assigned_count'];
                        } else {
                            $Lead[$index]['assigned_count'] = $value['assigned_count'];
                        }
                    }
                }
            }
            foreach ($arrData['list'] as $key => $value) {
                //Employee Login
                if($viewName == 'EM'){
                    $index = $value->employee_id;
                    $arrData['leads'][$index]['employee_id'] = $value->employee_id;
                    $arrData['leads'][$index]['employee_name'] = $value->employee_name;
                    $arrData['leads'][$index]['designation'] = $value->designation;
                    $arrData['leads'][$index]['branch_name'] = $value->branch_name;
                    $arrData['leads'][$index]['branch_id'] = $value->branch_id;
                }
                //Branch Manager Login
                if($viewName == 'BM'){
                    $index = $value->branch_id;
                    $arrData['leads'][$index]['branch_name'] = $value->branch_name;
                    $arrData['leads'][$index]['branch_id'] = $value->branch_id;
                }

                //Zone Manager Login
                if($viewName == 'ZM'){
                    $index = $value->zone_id;
                }
                $arrData['leads'][$index]['zone_name'] = $value->zone_name;
                $arrData['leads'][$index]['zone_id'] = $value->zone_id;
                if(!in_array($index,$Lead['userId'])){
                    $arrData['leads'][$index]['generated_count'] = 0;
                    $arrData['leads'][$index]['converted_count'] = 0;
                    $arrData['leads'][$index]['actual_business'] = 0;
                    $arrData['leads'][$index]['assigned_count'] = 0;
                }else{
                    $arrData['leads'][$index]['generated_count'] = isset($Lead[$index]['generated_count']) ? $Lead[$index]['generated_count'] : 0;
                    $arrData['leads'][$index]['converted_count'] = isset($Lead[$index]['converted_count']) ? $Lead[$index]['converted_count'] : 0;
                    $arrData['leads'][$index]['actual_business'] = isset($Lead[$index]['actual_business']) ? $Lead[$index]['actual_business'] : 0;
                    $arrData['leads'][$index]['assigned_count'] = isset($Lead[$index]['assigned_count']) ? $Lead[$index]['assigned_count'] : 0;
                }
                $arrData['G_Total'] += $arrData['leads'][$index]['generated_count'];
                $arrData['C_Total'] += $arrData['leads'][$index]['converted_count'];
            }
            if($this->session->userdata('admin_type') == 'BM' && $arrData['view'] == ''){
                $arrData['leads'] = array($this->session->userdata('branch_id')=> $arrData['leads'][$this->session->userdata('branch_id')]) + $arrData['leads'];
            }
            if($this->session->userdata('admin_type') == 'ZM' && $arrData['view'] == ''){
                $arrData['leads'] = array($this->session->userdata('zone_id')=> $arrData['leads'][$this->session->userdata('zone_id')]) + $arrData['leads'];
            }
        }
        //pe($arrData);die;
        return $arrData;
    }

    private function leads_classification($arrData){
        $login_user = get_session();
        //Build Input Parameter
        $action = 'list';
        $select = array('SUM(l.lead_ticket_range) as lead_ticket_range,SUM(rfc.amount) as amount');
        $table = Tbl_Leads.' as l';
        $where  = array();
        $join = array();
        $join[] = array('table' => Tbl_cbs.' as rfc','on_condition' => 'rfc.lead_id = l.id','type' => 'left');
        /*$group_by = array('l.lead_ticket_range');*/

        //If Start date selected
        if(!empty($arrData['start_date'])){
            $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
        }
        //If End date selected
        if(!empty($arrData['end_date'])){
            $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
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

        if(($arrData['view'] == 'employee') || ($arrData['national'] == 'yes')){
            $this->make_bread->add('Leads Classifcation', 'reports/index/leads_classification', 0);
            if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                if(!empty($arrData['branch_id'])){
                    if($login_user['designation_name'] != 'BM'){
                        $this->make_bread->add('Branch View', 'reports/index/leads_classification/branch/'.encode_id($arrData['zone_id']), 0);
                    }
                }
            }
            $this->make_bread->add('Employee View', '', 0);
            $viewName = 'EM';
        }else if($arrData['view']){
            $this->make_bread->add('Leads Classifcation', 'reports/index/leads_classification', 0);
            $this->make_bread->add('Branch View', '', 0);
            $viewName = 'BM';
        }else{
            $this->make_bread->add('Leads Classifcation','', 0);
            $viewName = $login_user['designation_name'];
            if($viewName == 'GM' || $viewName == 'Super admin'){
                $viewName = 'ZM';
            }
        }

        $WHERE = array();
        //Employee Login
        if($viewName == 'EM'){
            $select[] = 'l.created_by as employee_id';
            if($arrData['national'] != 'yes'){
                $where['l.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
                if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                    if(!empty($arrData['branch_id'])){
                        $where['l.branch_id'] = $arrData['branch_id'];
                    }
                }else{
                    $where['l.branch_id'] = $login_user['branch_id'];
                }
            }
            $group_by[]  =  'l.created_by';

            //Get Listing for employees
            $SELECT = array('hrms_id as employee_id','name as employee_name','branch_id','branch_name','zone_id','zone_name','designation');
            if(isset($where['l.zone_id'])){
                $WHERE['zone_id'] = $where['l.zone_id'];
            }
            if(isset($where['l.branch_id'])){
                $WHERE['branch_id'] = $where['l.branch_id'];
            }
            //$WHERE['designation'] = 'HD';
            $GROUP_BY = array('hrms_id');
        }

        //Branch Manager Login
        if($viewName == 'BM'){
            $select[] = 'l.branch_id';
            $where['l.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
            $group_by[] = 'l.branch_id';

            //Get Listing for branch
            $SELECT = array('branch_id','branch_name','zone_id','zone_name');
            if(isset($where['l.zone_id'])){
                $WHERE['zone_id'] = $where['l.zone_id'];
            }
            //$WHERE['designation'] = 'BR';
            $GROUP_BY = array('branch_id');
        }

        //Zone Manager Login
        if($viewName == 'ZM'){
            $select[] = 'l.zone_id';
            $group_by[] = 'l.zone_id';

            //Get Listing for branch
            $SELECT = array('zone_id','zone_name');
            $WHERE = array();
            //$WHERE['designation'] = 'ZD';
            $GROUP_BY = array('zone_id');
        }

        $TABLE  = 'employee_dump';
        $list = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE,$viewName);

        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'lead_ticket_range DESC');

        $arrData['leads'] = array();
        $arrData['Total'] = 0;
        if($list){
            $Lead['userId'] = array();
            if(!empty($leads)) {
                foreach ($leads as $key => $value) {
                    //Employee Login
                    if ($viewName == 'EM') {
                        $index = $value['employee_id'];
                        $Lead['userId'][] = $value['employee_id'];
                    }

                    //Branch Manager Login
                    if ($viewName == 'BM') {
                        $index = $value['branch_id'];
                        $Lead['userId'][] = $value['branch_id'];
                    }

                    //Zone Manager Login
                    if ($viewName == 'ZM') {
                        $index = $value['zone_id'];
                        $Lead['userId'][] = $value['zone_id'];
                    }
                    $Lead[$index]['ticket'] = $value['lead_ticket_range'];
                    $Lead[$index]['amount'] = $value['amount'];
                }
            }
            $arrData['viewName'] = $viewName;
            foreach ($list as $key => $value) {
                //Employee Login
                if($viewName == 'EM'){
                    $index = $value->employee_id;
                    $arrData['leads'][$index]['employee_id'] = $value->employee_id;
                    $arrData['leads'][$index]['employee_name'] = $value->employee_name;
                    $arrData['leads'][$index]['designation'] = $value->designation;
                    $arrData['leads'][$index]['branch_name'] = $value->branch_name;
                    $arrData['leads'][$index]['branch_id'] = $value->branch_id;
                }
                //Branch Manager Login
                if($viewName == 'BM'){
                    $index = $value->branch_id;
                    $arrData['leads'][$index]['branch_name'] = $value->branch_name;
                    $arrData['leads'][$index]['branch_id'] = $value->branch_id;
                }

                //Zone Manager Login
                if($viewName == 'ZM'){
                    $index = $value->zone_id;
                }
                $arrData['leads'][$index]['zone_name'] = $value->zone_name;
                $arrData['leads'][$index]['zone_id'] = $value->zone_id;
                if(!in_array($index,$Lead['userId'])){
                    $arrData['leads'][$index]['ticket'] = 0;
                    $arrData['leads'][$index]['amount'] = 0;
                }else{
                    $arrData['leads'][$index]['ticket'] = $Lead[$index]['ticket'];
                    $arrData['leads'][$index]['amount'] = isset($Lead[$index]['amount']) ? $Lead[$index]['amount'] : 0;
                }
                $arrData['Total'] += $arrData['leads'][$index]['ticket'];
            }
            if($this->session->userdata('admin_type') == 'BM' && $arrData['view'] == ''){
                $arrData['leads'] = array($this->session->userdata('branch_id')=> $arrData['leads'][$this->session->userdata('branch_id')]) + $arrData['leads'];
            }
            if($this->session->userdata('admin_type') == 'ZM' && $arrData['view'] == ''){
                $arrData['leads'] = array($this->session->userdata('zone_id')=> $arrData['leads'][$this->session->userdata('zone_id')]) + $arrData['leads'];
            }
        }
        return $arrData;
    }

    private function usage($arrData){
        $login_user = get_session();
        //Build Input Parameter
        $action = 'list';
        $select = array();
        $table = Tbl_LoginLog.' as l';
        $where  = array();
        $join = array();
        $group_by = array();

        //If Start date selected
        if(!empty($arrData['start_date'])){
            $where['DATE_FORMAT(l.date_time,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
        }
        //If End date selected
        if(!empty($arrData['end_date'])){
            $where['DATE_FORMAT(l.date_time,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
        }

        if(($arrData['view'] == 'employee') || ($arrData['national'] == 'yes')){
            $this->make_bread->add('Usage', 'reports/index/usage', 0);
            if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                if(!empty($arrData['branch_id'])){
                    if($login_user['designation_name'] != 'BM'){
                        $this->make_bread->add('Branch View', 'reports/index/usage/branch/'.encode_id($arrData['zone_id']), 0);
                    }
                }
            }
            $this->make_bread->add('Employee View', '', 0);
            $viewName = 'EM';
        }else if($arrData['view']){
            $this->make_bread->add('Usage', 'reports/index/usage', 0);
            $this->make_bread->add('Branch View', '', 0);
            $viewName = 'BM';
        }else{
            $this->make_bread->add('Usage', '', 0);
            $viewName = $login_user['designation_name'];
            if($viewName == 'GM' || $viewName == 'Super admin'){
                $viewName = 'ZM';
            }
        }

        $WHERE = array();

        //Employee Login
        if($viewName == 'EM'){
            $select[] = 'COUNT(l.employee_id) as count';
            $select[] = 'l.employee_id';
            if($arrData['national'] != 'yes'){
                $where['l.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
                if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                    if(!empty($arrData['branch_id'])){
                        $where['l.branch_id'] = $arrData['branch_id'];
                    }
                }else{
                    $where['l.branch_id'] = $login_user['branch_id'];
                }
            }
            $group_by[]  =  'l.employee_id';

            //Get Listing for employees
            $SELECT = array('hrms_id as employee_id','name as employee_name','branch_id','branch_name','zone_id','zone_name','designation');
            if(isset($where['l.zone_id'])){
                $WHERE['zone_id'] = $where['l.zone_id'];
            }
            if(isset($where['l.branch_id'])){
                $WHERE['branch_id'] = $where['l.branch_id'];
            }
            //$WHERE['designation'] = 'HD';
            $GROUP_BY = array('hrms_id');
        }

        //Branch Manager Login
        if($viewName == 'BM'){
            $select[] = 'COUNT(DISTINCT(l.employee_id)) as count';
            $select[] = 'l.branch_id';
            $where['l.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
            $group_by[] = 'l.branch_id';

            //Get Listing for branch
            $SELECT = array('branch_id','branch_name','zone_id','zone_name','COUNT(hrms_id) as total_user');
            if(isset($where['l.zone_id'])){
                $WHERE['zone_id'] = $where['l.zone_id'];
            }
            //$WHERE['designation'] = 'BR';
            $GROUP_BY = array('branch_id');
        }

        //Zone Manager Login
        if($viewName == 'ZM'){
            $select[] = 'COUNT(DISTINCT(l.employee_id)) as count';
            $select[] = 'l.zone_id';
            $group_by[] = 'l.zone_id';

            //Get Listing for branch
            $SELECT = array('zone_id','zone_name','COUNT(hrms_id) as total_user');
            $WHERE = array();
            //$WHERE['designation'] = 'ZD';
            $GROUP_BY = array('zone_id');
        }

        $TABLE  = 'employee_dump';
        // echo $arrData['national'];
        $list = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE,$viewName);
        //pe($this->db->last_query());
        //exit;

        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'count DESC');
//pe($leads);die;
        $arrData['leads'] = array();
        $arrData['Total'] = 0;
        if($list){
            $Lead['userId'] = array();
            if(!empty($leads)) {
                foreach ($leads as $key => $value) {
                    //Employee Login
                    if ($viewName == 'EM') {
                        $index = $value['employee_id'];
                        $Lead['userId'][] = $value['employee_id'];
                    }

                    //Branch Manager Login
                    if ($viewName == 'BM') {
                        $index = $value['branch_id'];
                        $Lead['userId'][] = $value['branch_id'];
                    }

                    //Zone Manager Login
                    if ($viewName == 'ZM') {
                        $index = $value['zone_id'];
                        $Lead['userId'][] = $value['zone_id'];
                    }
                    $Lead[$index]['total'] = $value['count'];
                }
            }
            //pe($Lead);//pe($list);//die;
            $arrData['viewName'] = $viewName;
            foreach ($list as $key => $value) {
                if($viewName == 'EM'){
                    $index = $value->employee_id;
                    $arrData['leads'][$index]['employee_id'] = $value->employee_id;
                    $arrData['leads'][$index]['employee_name'] = $value->employee_name;
                    $arrData['leads'][$index]['designation'] = $value->designation;
                    $arrData['leads'][$index]['branch_name'] = $value->branch_name;
                    $arrData['leads'][$index]['branch_id'] = $value->branch_id;
                    if(!in_array($index,$Lead['userId'])){
                        $arrData['leads'][$index]['total'] = 0;
                    }else{
                        $arrData['leads'][$index]['total'] = $Lead[$index]['total'];
                    }
                    $arrData['Total'] = count($list);
                }

                //Branch Manager Login
                if($viewName == 'BM'){
                    $index = $value->branch_id;
                    $arrData['leads'][$index]['branch_name'] = $value->branch_name;
                    $arrData['leads'][$index]['branch_id'] = $value->branch_id;
                    if(!in_array($index,$Lead['userId'])){
                        $arrData['leads'][$index]['total'] = 0;
                    }else{
                        $arrData['leads'][$index]['total'] = $Lead[$index]['total'];
                    }
                    if(isset($value->total_user)){
                        $arrData['leads'][$index]['total_user'] = $value->total_user;
                        $arrData['Total'] += $value->total_user;
                        $arrData['leads'][$index]['not_logged_in'] = ($arrData['leads'][$index]['total_user'] - $arrData['leads'][$index]['total']);
                    }
                }


                //Zone Manager Login
                if($viewName == 'ZM'){
                    $index = $value->zone_id;
                    if(!in_array($index,$Lead['userId'])){
                        $arrData['leads'][$index]['total'] = 0;
                    }else{
                        $arrData['leads'][$index]['total'] = $Lead[$index]['total'];
                    }
                    if(isset($value->total_user)){
                        $arrData['leads'][$index]['total_user'] = $value->total_user;
                        $arrData['Total'] += $value->total_user;
                        $arrData['leads'][$index]['not_logged_in'] = ($arrData['leads'][$index]['total_user'] - $arrData['leads'][$index]['total']);
                    }
                }
                $arrData['leads'][$index]['zone_name'] = $value->zone_name;
                $arrData['leads'][$index]['zone_id'] = $value->zone_id;
            }
            // echo $this->db->last_query();
            //pe($arrData);//die;
            if($arrData['national'] != 'yes') {
                if ($this->session->userdata('admin_type') == 'BM' && $arrData['view'] == '') {
                    $arrData['leads'] = array($this->session->userdata('branch_id') => $arrData['leads'][$this->session->userdata('branch_id')]) + $arrData['leads'];
                }
                if ($this->session->userdata('admin_type') == 'ZM' && $arrData['view'] == '') {
                    $arrData['leads'] = array($this->session->userdata('zone_id') => $arrData['leads'][$this->session->userdata('zone_id')]) + $arrData['leads'];
                }
            }
        }
//pe($arrData);//die;
        return $arrData;
    }


    public function export_to_excel($action,$arrData){
        set_time_limit(0);
        ini_set('memory_limit', '-1');
    //    echo $action;
    //   pe($arrData);die;
     //   exit;
        if($arrData['viewName'] == 'EM'){
            $header_value = array('Sr.No','Zone','Branch','HRMS ID','Employee Name','Designation','Source Type','Category Name','Product Name');
        }else if($arrData['viewName'] == 'BM'){
            $header_value = array('Sr.No','Zone','Branch','Source Type','Category Name','Product Name');
        }else{
            $header_value = array('Sr.No','Zone','Source Type','Category Name','Product Name');
        }
        if($arrData['national'] == 'yes' && $action == 'usage'){
            $header_value = array('Sr.No','Zone','Branch','HRMS ID','Employee Name','Designation');
        }

        if($arrData['viewName'] == 'ZM' && $action == 'usage'){
            $header_value = array('Sr.No','Zone');
        }
        if($arrData['viewName'] == 'BM' && $action == 'usage'){
            $header_value = array('Sr.No','Zone','Branch');
        }
        if($arrData['viewName'] == 'EM' && $action == 'usage'){
            $header_value = array('Sr.No','Zone','Branch','HRMS ID','Employee Name','Designation');
        }

        if($action == 'status_flow'){
            $header_value = array('Sr. No.','Lead Id','Customer Name','Contact Number','Product Category','Product','Ticket Size','Lead Source','Lead Generation Date','Lead Generated By','Designation of Lead Generated By','Lead Generation Branch ID','Lead Generation Branch Name','Lead Generation Zone Name','Current Location of Lead','HRMS ID of Lead Generator','Lead Assignment Date(1)','Lead Assigned To (1)','Lead Assigned to HRMS ID (1)','Lead Assigned To Branch ID (1)','Lead Assigned To Branch Name (1)','Lead Assignment Date(2)','Lead Assigned To (2)','Lead Assigned to HRMS ID (2)','Lead Assigned To Branch ID (2)','Lead Assigned To Branch Name (2)','Lead Marked as Interested Date','Lead Marked as Interested By','Lead Marked as Interested By HRMS ID','Lead Identified As','Lead Marked as Documents Collected Date','Lead Marked as Documents Collected By','Lead Marked as Documents Collected By HRMS ID','Lead Marked as Account Opened Date','Lead Marked as Account Opened By','Lead Marked as Account Opened By HRMS ID','Lead marked as Drop/Not Interested Date','Lead marked as Drop/Not Interested By','Lead marked as Drop/Not Interested By HRMS ID','Reason for Drop','Lead Marked as Converted Date','Lead Marked as Converted By','Lead Marked as Converted By HRMS ID','Account Number of lead converted','Account Balance of Lead Converted','Lead marked as Reject Date','Lead marked as Reject By','Lead marked as Reject By HRMS ID','Current Zone of Lead','Status');
        }

        switch ($action) {
            case 'pendancy_leads_reports':
                $pendancy_leads_col = array('Total Pending Leads','Not Connected','Interested','Documents Collected');
                $header_value = array_merge($header_value,$pendancy_leads_col);
                break;
            case 'leads_type_reports':
                $leads_type_col = array('Total Leads','Highly Interested','Medium Interested','Low Interested');
                $header_value = array_merge($header_value,$leads_type_col);
                break;
            case 'leads_generated':
                $leads_generated_col = array('Total Generated Leads','Not Connected','Interested','Documents Collected','Sanctioned','Account Opened','Converted','Drop/Not Inserted','Closed');
                $header_value = array_merge($header_value,$leads_generated_col);
                break;
            case 'leads_assigned':
                $leads_assigned_col = array('Total Assigned Leads','Not Connected','Interested','Documents Collected','Account Opened','Converted','Drop/Not Inserted','Closed');
                $header_value = array_merge($header_value,$leads_assigned_col);
                break;
            case 'leads_generated_vs_converted':
                $leads_g_vs_c_col = array('Total Leads Generated','Total Leads Assigned','Total Leads Converted','Actual Business (In Lacs)');
                $header_value = array_merge($header_value,$leads_g_vs_c_col);
                break;
            case 'leads_classification':
                $leads_classification_col = array('Ticket Size(Rs)');
                $header_value = array_merge($header_value,$leads_classification_col);
                break;
            case 'usage':
                if($arrData['viewName'] == 'EM'){
                    $usage_col = array('Logged in count');
                }else{
                    $usage_col = array('Total User','Logged in User','Not logged in User');
                }
                $header_value = array_merge($header_value,$usage_col);
                // pe($header_value); //die;
                break;

        }
        //$this->create_excel($action,$header_value,$arrData);
        if($action == 'status_flow'){
            $this->create_dump_excel($action,$header_value,$arrData);
        }else{
            $this->create_excel($action,$header_value,$arrData);
        }

    }


    private function create_excel($action,$header_value,$data){
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 1800);
//        echo $action;
//        pe($header_value);
        // pe($data);die;
        $this->load->library('excel');
        $file_name = $action.'-'.time().'data.xls';
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
        //pe($data['leads']);die;
        foreach ($data['leads'] as $key => $value) {
            //echo 'h-'.$value['zone_name'];
            foreach ($header_value as $k => $v) {
                $objSheet->getStyle($excel_alpha[$k] . $i)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objSheet->getStyle($excel_alpha[$k].($i))->applyFromArray($text_bold_false);
            }

            $objSheet->getCell($excel_alpha[0].$i)->setValue($j);
            $objSheet->getCell($excel_alpha[1].$i)->setValue(ucwords($value['zone_name']));
            $col = 1;
            if($data['viewName'] == 'EM'){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['branch_name']));
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['employee_id']));
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['employee_name']));
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['designation']));
            }
            if($data['viewName'] == 'BM'){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(ucwords($value['branch_name']));
            }
            if($action != 'usage'){

                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(!empty($data['lead_source']) ? ucwords($data['lead_source']) : 'All');
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(isset($data['category']) ? ucwords($data['category']) : 'All');
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(isset($data['product']) ? ucwords($data['product']) : 'All');
            }
            if($action == 'leads_generated_vs_converted'){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(isset($value['generated_count']) ? $value['generated_count'] : 0);
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(isset($value['assigned_count']) ? $value['assigned_count'] : 0);
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(isset($value['converted_count']) ? $value['converted_count'] : 0);
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(isset($value['actual_business']) ? convertCurrency($value['actual_business']) : 0);
            }else if($action == 'leads_classification'){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(isset($value['ticket']) ? $value['ticket'] : 0);
            }else if($action == 'usage'){
                //echo "jkj";die;
                //pe($data);
                if($data['viewName'] == 'EM'){
                    //echo "lo";//die;
                    $objSheet->getCell($excel_alpha[++$col].$i)->setValue($value['total']);
                }else{
                    //echo "ko";//die;
                    $objSheet->getCell($excel_alpha[++$col].$i)->setValue(isset($value['total_user']) ? $value['total_user'] : 0);
                    $objSheet->getCell($excel_alpha[++$col].$i)->setValue($value['total']);
                    $objSheet->getCell($excel_alpha[++$col].$i)->setValue(isset($value['not_logged_in']) ? $value['not_logged_in'] : 0);
                }
            }else{
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue($value['total']);
            }
            $m = $col;
            if(in_array($action,array('leads_generated','leads_assigned','pendancy_leads_reports'))){
                $lead_status = array_keys($this->config->item('lead_status'));
                foreach ($lead_status as $k1 => $v1) {
                    if(in_array($v1,array_keys($value['status']))){
                        $objSheet->getCell($excel_alpha[++$m].$i)->setValue($value['status'][$v1]);
                    }else{
                        if(($action == 'pendancy_leads_reports') && (in_array($v1,array('AO','Converted','Closed','NI')))){
                        }else{
                            $objSheet->getCell($excel_alpha[++$m].$i)->setValue(0);
                        }
                    }
                }
            }
            if($action == 'leads_type_reports'){
                $lead_type = array_keys($this->config->item('lead_type'));
                foreach ($lead_type as $k1 => $v1) {
                    if(in_array($v1,array_keys($value['lead_identification']))){
                        $objSheet->getCell($excel_alpha[++$m].$i)->setValue($value['lead_identification'][$v1]);
                    }else{
                        $objSheet->getCell($excel_alpha[++$m].$i)->setValue(0);
                    }
                }
            }
            $i++;$j++;
        }

        // pe($data);die;
        //echo $file_name;die;
        //return $file_name;
        make_upload_directory('uploads');
        make_upload_directory('uploads/excel_list');
        //echo $file_name;die;
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $objWriter->save('php://output');
    }



    private function status_flow($arrData){

        set_time_limit(0);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 1800);
        $login_user = get_session();
        $action = 'list';
        $select = array('l.id','l.customer_name','l.lead_source','l.contact_no','l.created_by_branch_id',
            'l.created_on AS generated_on','l.reroute_from_branch_id','l.modified_on','l.branch_id','l.zone_id','l.created_by_name as generated',
            'l.opened_account_no','l.created_by_zone_id','l.lead_identification','l.created_by as created_by_hrms_id','l.lead_ticket_range',
            'p.title as product','pc.title as product_category','l.opened_account_no','rfc.amount','la.employee_id','la.employee_name','la.branch_id as assignee_branch',
            'la.created_by_name','la.modified_on AS date','la.status','la.modified_by','la.modified_by_name','la.reason_for_drop');
        $table = Tbl_Leads.' as l';
        $where  = array();
        $join = array();
        $join[] = array('table' => Tbl_Products.' as p','on_condition' => 'l.product_id = p.id','type' => '');
        $join[] = array('table' => Tbl_Category.' as pc','on_condition' => 'l.product_category_id = pc.id','type' => '');
        $join[] = array('table' => Tbl_cbs.' as rfc','on_condition' => 'l.id = rfc.lead_id','type' => 'left');
        $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'l.id = la.lead_id','type' => 'left');

        //If Start date selected
        if(!empty($arrData['start_date'])){
            $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
        }
        //If End date selected
        if(!empty($arrData['end_date'])){
            $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
        }

        $order_by = 'l.created_on DESC';

        $arrData['generated_leads'] = $this->Lead->listMasters($action,$table,$select,$where,$join,$group_by=array(),$order_by);




        $lstArray = array();
        foreach($arrData['generated_leads'] as $data){

            $lstArray[$data['id']][] = $data;

        }
        return $lstArray;

    }


    private function export_to_excel_master($data){


        $this->load->library('excel');
        $file_name = 'Master_report-'.date('d-m-Y').'-'.time().'data.xls';
        $excel_alpha = unserialize(EXCEL_ALPHA);

        $header_value = array('Sr. No.','Lead Id','Customer Name','Contact Number','Product Category',
            'Product','Ticket Size','Lead Source','Lead Generation Date','Lead Generated By','Designation of Lead Generated By',
            'Lead Generation Branch ID','Lead Generation Branch Name','Lead Generation Zone Name','Current Location of Lead','Current Zone of Lead',
            'HRMS ID of Lead Generator','Lead Assignment Date(1)','Lead Assigned To (1)','Lead Assigned to HRMS ID (1)','Lead Assigned To Branch ID (1)','Lead Assigned To Branch Name (1)',
            'Lead Assignment Date(2)','Lead Assigned To (2)','Lead Assigned to HRMS ID (2)','Lead Assigned To Branch ID (2)','Lead Assigned To Branch Name (2)',
            'Lead Marked as Interested Date','Lead Marked as Interested By','Lead Marked as Interested By HRMS ID','Lead Identified As',
            'Lead Marked as Documents Collected Date','Lead Marked as Documents Collected By','Lead Marked as Documents Collected By HRMS ID',
            'Lead Marked as Account Opened Date','Lead Marked as Account Opened By','Lead Marked as Account Opened By HRMS ID','Lead marked as Drop/Not Interested Date',
            'Lead marked as Drop/Not Interested By','Lead marked as Drop/Not Interested By HRMS ID','Reason for Drop','Lead Marked as Converted Date',
            'Lead Marked as Converted By','Lead Marked as Converted By HRMS ID','Account Number of lead converted',
            'Account Balance of Lead Converted','Lead marked as Reject Date','Lead marked as Reject By','Lead marked as Reject By HRMS ID',
            'Status');


        $objPHPExcel = $this->excel;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
        $objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
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
                'bold'  => false,
                'size'  => 22
            ));
        $textfontArray = array(
            'font'  => array(
                'bold'  => false,
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

//        $designation_by_hrms_id = array();
        $designationByHrmsId = designation_by_hrms_id_key_value();
        $branchNameByKeyValue = branchNameKeyValue();
        $zoneNameByKeyValue = zonenameKeyValue();
        $zoneIdKeyValue = zoneIdKeyValue();


        $j = 1;$i=2;
        foreach($data as $key => $value){
            $count  = count($value);

//            foreach ($header_value as $k => $v) {
//                $objSheet->getStyle($excel_alpha[$k] . $i)
//                    ->getAlignment()
//                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//                $objSheet->getStyle($excel_alpha[$k] . ($i))->applyFromArray($text_bold_false);
//            }

            $objSheet->getCell($excel_alpha[0] . $i)->setValue($j);
            $objSheet->getCell($excel_alpha[1] . $i)->setValue(ucwords($value[0]['id']));
            $objSheet->getCell($excel_alpha[2] . $i)->setValue(ucwords($value[0]['customer_name']));
            $objSheet->getCell($excel_alpha[3] . $i)->setValue(ucwords($value[0]['contact_no']));
            $objSheet->getCell($excel_alpha[4] . $i)->setValue(ucwords($value[0]['product_category']));
            $objSheet->getCell($excel_alpha[5] . $i)->setValue(ucwords($value[0]['product']));
            $objSheet->getCell($excel_alpha[6] . $i)->setValue(ucwords($value[0]['id']));
            $objSheet->getCell($excel_alpha[7] . $i)->setValue(ucwords($value[0]['lead_source']));
            $objSheet->getCell($excel_alpha[8] . $i)->setValue(ucwords($value[0]['generated_on']));
            $objSheet->getCell($excel_alpha[9] . $i)->setValue(ucwords($value[0]['generated']));

            if(array_key_exists($value[0]['created_by_hrms_id'],$designationByHrmsId)){
                $designation = $designationByHrmsId[$value[0]['created_by_hrms_id']];
            }else{
                $designation = "";
            }
            $objSheet->getCell($excel_alpha[10] . $i)->setValue(ucwords($designation));//its from helper
            $objSheet->getCell($excel_alpha[11] . $i)->setValue(ucwords($value[0]['created_by_branch_id']));

            if(array_key_exists($value[0]['created_by_branch_id'],$branchNameByKeyValue)){
                $branchName1 = $branchNameByKeyValue[$value[0]['created_by_branch_id']];
            }else{
                $branchName1 = "";
            }
            $objSheet->getCell($excel_alpha[12] . $i)->setValue(ucwords($branchName1));

            if(array_key_exists($value[0]['created_by_zone_id'],$zoneNameByKeyValue)){
                $zoneName1 = $zoneNameByKeyValue[$value[0]['created_by_zone_id']];
            }else{
                $zoneName1 = "";
            }
            $objSheet->getCell($excel_alpha[13] . $i)->setValue(ucwords($zoneName1));

            if(array_key_exists($value[0]['branch_id'],$branchNameByKeyValue)){
                $branchName2 = $branchNameByKeyValue[$value[0]['branch_id']];
            }else{
                $branchName2 = "";
            }
            $objSheet->getCell($excel_alpha[14] . $i)->setValue(ucwords($branchName2));

            $objSheet->getCell($excel_alpha[16] . $i)->setValue(ucwords($value[$key]['created_by_hrms_id']));

            if(array_key_exists($value[0]['branch_id'],$zoneIdKeyValue)){
                $currentZoneId = $zoneIdKeyValue[$value[0]['branch_id']];
            }else{
                $currentZoneId = "";
            }
            if(array_key_exists($currentZoneId,$zoneNameByKeyValue)){
                $zoneName2 = $zoneNameByKeyValue[$currentZoneId];
            }else{
                $zoneName2 = "";
            }
            $objSheet->getCell($excel_alpha[15] . $i)->setValue(ucwords($zoneName2));
            $objSheet->getCell($excel_alpha[49] . $i)->setValue(ucwords('Unassigned'));

            if($count > 1){
                $lead_type = $this->config->item('lead_type');
                foreach($value as $index => $val){
                    if($index == 0){
                        continue;
                    }

                    if(isset($value[0]['reroute_from_branch_id'])) {
                        if ($val['status'] == 'NC' && $val['assignee_branch'] == $value[0]['reroute_from_branch_id']) {
                            $objSheet->getCell($excel_alpha[17] . $i)->setValue(ucwords($val['date']));
                            $objSheet->getCell($excel_alpha[18] . $i)->setValue(ucwords($val['employee_name']));
                            $objSheet->getCell($excel_alpha[19] . $i)->setValue(ucwords($val['employee_id']));
                            $objSheet->getCell($excel_alpha[20] . $i)->setValue(ucwords($value[0]['reroute_from_branch_id']));
                            if(array_key_exists($value[0]['reroute_from_branch_id'],$branchNameByKeyValue)){
                                $reRoutedBranchName1 = $branchNameByKeyValue[$value[0]['reroute_from_branch_id']];
                            }else{
                                $reRoutedBranchName1 = "";
                            }
                            $objSheet->getCell($excel_alpha[21] . $i)->setValue(ucwords($reRoutedBranchName1));
                        }

                        if ($value[$key]['status'] == 'NC' && $value[$key]['assignee_branch'] == $value[0]['branch_id']) {
                            $objSheet->getCell($excel_alpha[22] . $i)->setValue(ucwords($value[$key]['date']));
                            $objSheet->getCell($excel_alpha[23] . $i)->setValue(ucwords($value[$key]['employee_name']));
                            $objSheet->getCell($excel_alpha[24] . $i)->setValue(ucwords($value[$key]['employee_id']));
                            $objSheet->getCell($excel_alpha[25] . $i)->setValue(ucwords($value[0]['branch_id']));
                            $objSheet->getCell($excel_alpha[26] . $i)->setValue(ucwords($branchName2));
                        }

                    }else{
                        $objSheet->getCell($excel_alpha[17] . $i)->setValue(ucwords($val['date']));
                        $objSheet->getCell($excel_alpha[18] . $i)->setValue(ucwords($val['employee_name']));
                        $objSheet->getCell($excel_alpha[19] . $i)->setValue(ucwords($val['employee_id']));
                        $objSheet->getCell($excel_alpha[20] . $i)->setValue(ucwords($value[0]['branch_id']));
                        $objSheet->getCell($excel_alpha[21] . $i)->setValue(ucwords($branchName2));

                    }

                    if($val['status'] == 'FU'){
                        $objSheet->getCell($excel_alpha[27] . $i)->setValue(ucwords($val['date']));
                        $objSheet->getCell($excel_alpha[28] . $i)->setValue(ucwords($val['modified_by_name']));
                        $objSheet->getCell($excel_alpha[29] . $i)->setValue(ucwords($val['modified_by']));
                        $objSheet->getCell($excel_alpha[30] . $i)->setValue(ucwords($lead_type[$value[0]['lead_identification']]));
                    }

                    if($val['status'] == 'DC'){
                        $objSheet->getCell($excel_alpha[31] . $i)->setValue(ucwords($val['date']));
                        $objSheet->getCell($excel_alpha[32] . $i)->setValue(ucwords($val['modified_by_name']));
                        $objSheet->getCell($excel_alpha[33] . $i)->setValue(ucwords($val['modified_by']));
                    }

                    if($val['status'] == 'AO'){
                        $objSheet->getCell($excel_alpha[34] . $i)->setValue(ucwords($val['date']));
                        $objSheet->getCell($excel_alpha[35] . $i)->setValue(ucwords($val['modified_by_name']));
                        $objSheet->getCell($excel_alpha[36] . $i)->setValue(ucwords($val['modified_by']));
                        $objSheet->getCell($excel_alpha[44] . $i)->setValue(ucwords($value[0]['opened_account_no']));
                        $objSheet->getCell($excel_alpha[45] . $i)->setValue(ucwords($value[0]['amount']));
                    }

                    if($val['status'] == 'NI'){
                        $objSheet->getCell($excel_alpha[37] . $i)->setValue(ucwords($val['date']));
                        $objSheet->getCell($excel_alpha[38] . $i)->setValue(ucwords($val['modified_by_name']));
                        $objSheet->getCell($excel_alpha[39] . $i)->setValue(ucwords($val['modified_by']));
                        $objSheet->getCell($excel_alpha[40] . $i)->setValue(ucwords($val['reason_for_drop']));
                    }

                    if($val['status'] == 'Converted'){
                        $objSheet->getCell($excel_alpha[41] . $i)->setValue(ucwords($val['date']));
                        $objSheet->getCell($excel_alpha[42] . $i)->setValue(ucwords($val['modified_by_name']));
                        $objSheet->getCell($excel_alpha[43] . $i)->setValue(ucwords($val['modified_by']));
                    }

                    if($val['status'] == 'Closed'){
                        $objSheet->getCell($excel_alpha[46] . $i)->setValue(ucwords($val['date']));
                        $objSheet->getCell($excel_alpha[47] . $i)->setValue(ucwords($val['modified_by_name']));
                        $objSheet->getCell($excel_alpha[48] . $i)->setValue(ucwords($val['modified_by']));
                    }
                    $objSheet->getCell($excel_alpha[49] . $i)->setValue(ucwords('Assigned'));

                }
            }


            $j++;
            $i++;
//            exit;
        }


        make_upload_directory('uploads');
        make_upload_directory('uploads/excel_list');
        //echo $file_name;die;
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $objWriter->save('php://output');


    }

    private function status_flow_old($arrData){

        set_time_limit(0);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 1800);
        $login_user = get_session();
        $action = 'list';
        $select = array('l.id','l.lead_identification','l.created_by as created_by_hrms_id','l.lead_ticket_range','p.title as product','pc.title as product_category','l.opened_account_no');
        $table = Tbl_Leads.' as l';
        $where  = array();
        $join = array();
        $join[] = array('table' => Tbl_Products.' as p','on_condition' => 'l.product_id = p.id','type' => '');
        $join[] = array('table' => Tbl_Category.' as pc','on_condition' => 'l.product_category_id = pc.id','type' => '');

        //If Start date selected
        if(!empty($arrData['start_date'])){
            $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
        }
        //If End date selected
        if(!empty($arrData['end_date'])){
            $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
        }
        //$where['l.id']=432;
//        if(!empty($arrData['product_category_id'])){
//            $where['l.product_category_id'] = $arrData['product_category_id'];
//            $categoryData = $this->Master->view_product_category($arrData['product_category_id']);
//            $arrData['category'] = $categoryData[0]['title'];  //Get Title
//        }
//        //If Product selected
//        if(!empty($arrData['product_id'])){
//            $where['l.product_id'] = $arrData['product_id'];
//            $productData = $this->Master->view_product($arrData['product_id']);
//            $arrData['product'] = $productData[0]['title'];   //Get Title
//        }
//        //If Lead Source selected
//        if(!empty($arrData['lead_source'])){
//            $where['l.lead_source'] = $arrData['lead_source'];
//        }

        if($this->session->userdata('admin_type')=='BM'){
            $where['l.branch_id'] = $this->session->userdata('branch_id');
        }
        if($this->session->userdata('admin_type')=='ZM'){
            $where['l.zone_id'] = $this->session->userdata('zone_id');
        }

        $order_by = 'l.created_on DESC';
        $arrData['generated_leads'] = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by=array(),$order_by);
        //pe($arrData['generated_leads']);die;
        foreach($arrData['generated_leads'] as $row){
            $lead_id = $row['id'];
            $final_result = array();
            $action = 'list';
            $select = array('l.id','l.customer_name','l.lead_source','l.contact_no','l.created_by_branch_id','l.created_on AS generated_on','l.reroute_from_branch_id','l.modified_on','l.branch_id','l.zone_id','l.created_by_name as generated','rfc.amount','l.opened_account_no','l.created_by_zone_id');
            $table = Tbl_Leads.' as l';
            $join = array();
            $join[] = array('table' => Tbl_cbs.' as rfc','on_condition' => 'l.id = rfc.lead_id','type' => 'left');
            $where = array('l.id'=>$lead_id);
            $result = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by=array(),$order_by=array());
            //pe($result);die;
            if(!empty($result)){
                if($result[0]['reroute_from_branch_id'] !=''){
                    $final_result[] = array(
                        'id'=>$result[0]['id'],
                        'generated'=>$result[0]['generated'],
                        'generated_on'=>$result[0]['generated_on'],
                        'date'=>$result[0]['generated_on'],
                        'reroute_from_branch_id'=>$result[0]['reroute_from_branch_id'],
                        'customer_name'=>$result[0]['customer_name'],
                        'branch_id'=>$result[0]['branch_id'],
                        'zone_id'=>$result[0]['zone_id'],
                        'contact_no'=>$result[0]['contact_no'],
                        'lead_source'=>$result[0]['lead_source'],
                        'created_by_branch_id'=>$result[0]['created_by_branch_id'],
                        'product'=>trim($row['product']),
                        'product_category'=>$row['product_category'],
                        'ticket_size'=>$row['lead_ticket_range'],
                        'created_by_hrms_id'=>$row['created_by_hrms_id'],
                        'lead_identification'=>$row['lead_identification'],
                        'created_by_zone_id'=>$result[0]['created_by_zone_id'],
                        'amount'=>$result[0]['amount'],
                        'opened_account_no'=>$result[0]['opened_account_no']
                    );
                    $final_result[] = array(
                        'id'=>$result[0]['id'],
                        'reroute_from_branch_id'=>$result[0]['reroute_from_branch_id'],
                        'reroute_to_branch_id'=>$result[0]['branch_id'],
                        'modified_on'=>$result[0]['modified_on'],
                        'date'=>$result[0]['modified_on'],
                        'created_by_branch_id'=>$result[0]['created_by_branch_id'],
                        'product'=>trim($row['product']),
                        'product_category'=>$row['product_category'],
                        'ticket_size'=>$row['lead_ticket_range'],
                        'created_by_hrms_id'=>$row['created_by_hrms_id'],
                        'lead_identification'=>$row['lead_identification'],
                        'created_by_zone_id'=>$result[0]['created_by_zone_id'],
                        'amount'=>$result[0]['amount'],
                        'opened_account_no'=>$result[0]['opened_account_no']
                    );
                }else{
//                    $final_result = $result;
//
//                    $final_result[0]['product'] = $row['product'];
//                    $final_result[0]['product_category'] = $row['product_category'];
//                    $final_result[0]['ticket_size'] = $row['lead_ticket_range'];
//                    $final_result[0]['created_by_hrms_id'] = $row['created_by_hrms_id'];
//                    $final_result[0]['lead_identification'] = $row['lead_identification'];
//                    $final_result[0]['date'] = $result[0]['generated_on'];
//                    $final_result[0]['branch_id'] = $result[0]['branch_id'];
//                    $final_result[0]['created_by_branch_id'] = $result[0]['created_by_branch_id'];
                    $final_result[] = array(
                        'id'=>$result[0]['id'],
                        'generated'=>$result[0]['generated'],
                        'generated_on'=>$result[0]['generated_on'],
                        'date'=>$result[0]['generated_on'],
                        'reroute_from_branch_id'=>$result[0]['reroute_from_branch_id'],
                        'customer_name'=>$result[0]['customer_name'],
                        'branch_id'=>$result[0]['branch_id'],
                        'zone_id'=>$result[0]['zone_id'],
                        'contact_no'=>$result[0]['contact_no'],
                        'lead_source'=>$result[0]['lead_source'],
                        'created_by_branch_id'=>$result[0]['created_by_branch_id'],
                        'product'=>trim($row['product']),
                        'product_category'=>$row['product_category'],
                        'ticket_size'=>$row['lead_ticket_range'],
                        'created_by_hrms_id'=>$row['created_by_hrms_id'],
                        'lead_identification'=>$row['lead_identification'],
                        'created_by_zone_id'=>$result[0]['created_by_zone_id'],
                        'amount'=>$result[0]['amount'],
                        'opened_account_no'=>$result[0]['opened_account_no']
                    );
                    //pe($final_result);die;

                }
            }
            $select = array('la.employee_id','la.employee_name','la.branch_id as assignee_branch','la.created_by_name','la.modified_on AS date','la.status','la.modified_by','la.modified_by_name','la.reason_for_drop');
            $table = Tbl_LeadAssign.' as la';
            $where = array('lead_id'=>$lead_id);
            $order_by = 'date ASC';
            $assign_result = $this->Lead->get_leads($action,$table,$select,$where,$join=array(),$group_by=array(),$order_by);

            if(!empty($assign_result)){
                $final_result = array_merge($final_result,$assign_result);
            }
            if(!empty($final_result)){
                $final_result = sortBySubkey($final_result,'date');
            }
            $arrData['status_flow'][] = $final_result;

        }
        //pe($arrData);die;
        return $arrData;
    }


    private function create_dump_excel($action,$header_value,$data){
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 1800);
        $lead_type = $this->config->item('lead_type');
       // echo $action;
//        pe($header_value);
        // pe($data);die;
        $this->load->library('excel');
        $file_name = 'Master_report-'.date('d-m-Y').'-'.time().'data.xls';
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

        $i=2;$j=1;$im=2;
        //pe($data['status_flow']);die;
        foreach ($data['status_flow'] as $key => $value) {
            //pe($value);die;
            foreach ($value as $key => $value1) {
                if($key == 0) {
                    if ($value[$key]['lead_source'] == 'analytics') {
                        $generated_by = "Generated By : Analytics<br><br>";
                    } elseif ($value[$key]['lead_source']['lead_source'] == 'tie_ups') {
                        $generated_by = "Generated By : Others<br><br>";
                    } elseif ($value[$key]['lead_source']['lead_source'] == 'enquiry') {
                        $generated_by = "Generated By : Website,IVR<br><br>";
                    } else {
                        //echo $lead_data[0]['created_by_branch_id'];
                        $branchname = branchname($data['status_flow']['created_by_branch_id']);
                        $branchname = $branchname[0]['name'];
                        $generated_by = "Generated By : " . ucwords(strtolower($value[$key]['generated'])) . ' - ' . $branchname . '<br><br>';
                    }//die;

                    foreach ($header_value as $k => $v) {
                        $objSheet->getStyle($excel_alpha[$k] . $i)
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objSheet->getStyle($excel_alpha[$k] . ($i))->applyFromArray($text_bold_false);
                    }

                    $objSheet->getCell($excel_alpha[0] . $i)->setValue($j);
                    $objSheet->getCell($excel_alpha[1] . $i)->setValue(ucwords($value[$key]['id']));
                    $objSheet->getCell($excel_alpha[2] . $i)->setValue(ucwords($value[$key]['customer_name']));
                    $objSheet->getCell($excel_alpha[3] . $i)->setValue(ucwords($value[$key]['contact_no']));
                    $objSheet->getCell($excel_alpha[4] . $i)->setValue(ucwords($value[$key]['product_category']));
                    $objSheet->getCell($excel_alpha[5] . $i)->setValue(ucwords($value[$key]['product']));
                    $objSheet->getCell($excel_alpha[6] . $i)->setValue((int)($value[$key]['ticket_size']));
                    $objSheet->getCell($excel_alpha[7] . $i)->setValue(ucwords($value[$key]['lead_source']));
                    $objSheet->getCell($excel_alpha[8] . $i)->setValue(ucwords($value[$key]['generated_on']));
                    $objSheet->getCell($excel_alpha[9] . $i)->setValue(ucwords($value[$key]['generated']));

                    $designation11 = designation_by_hrms_id($value[$key]['created_by_hrms_id']);
                    $designation11 = $designation11[0]['designation'];

                    $objSheet->getCell($excel_alpha[10] . $i)->setValue($designation11);
                    $objSheet->getCell($excel_alpha[11] . $i)->setValue(ucwords($value[$key]['created_by_branch_id']));
                    $branchname11 = branchname($value[$key]['created_by_branch_id']);
                    $branchname11 = $branchname11[0]['name'];
                    $objSheet->getCell($excel_alpha[12] . $i)->setValue(ucwords($branchname11));
                    $zonename12 = zonename($value[$key]['created_by_zone_id']);
                    $zonename12 = $zonename12[0]['name'];
                    $objSheet->getCell($excel_alpha[13] . $i)->setValue(ucwords($zonename12));
                    $branchname13 = branchname($value[$key]['branch_id']);
                    $branchname13 = $branchname13[0]['name'];
                    $objSheet->getCell($excel_alpha[14] . $i)->setValue(ucwords($branchname13));
                    $objSheet->getCell($excel_alpha[15] . $i)->setValue(ucwords($value[$key]['created_by_hrms_id']));
                    $current_zone_id = zoneid($value[$key]['branch_id']);
                    $zonename122 = zonename($current_zone_id);
                    $zonename122 = $zonename122[0]['name'];
                    $objSheet->getCell($excel_alpha[48] . $i)->setValue(ucwords($zonename122));


                    /*  if(isset($value[$key]['reroute_from_branch_id'])) {

                          $objSheet->getCell($excel_alpha[13] . $i)->setValue(ucwords($value[$key]['date']));
                          $objSheet->getCell($excel_alpha[16] . $i)->setValue(ucwords($value[$key]['reroute_from_branch_id']));
                          $branchname1 = branchname($value[$key]['reroute_from_branch_id']);
                          $branchname1 = $branchname1[0]['name'];
                          $objSheet->getCell($excel_alpha[17] . $i)->setValue(ucwords($branchname1));
                          $objSheet->getCell($excel_alpha[18] . $i)->setValue(ucwords($value[$key]['date']));
                          $objSheet->getCell($excel_alpha[21] . $i)->setValue(ucwords($value[$key]['branch_id']));
                          $branchname2 = branchname($value[$key]['branch_id']);
                          $branchname2 = $branchname2[0]['name'];
                          $objSheet->getCell($excel_alpha[22] . $i)->setValue(ucwords($branchname2));
                      }else{
                          $objSheet->getCell($excel_alpha[13] . $i)->setValue(ucwords($value[$key]['date']));
                          $objSheet->getCell($excel_alpha[16] . $i)->setValue(ucwords($value[$key]['branch_id']));
                          $branchname1 = branchname($value[$key]['branch_id']);
                          $branchname1 = $branchname1[0]['name'];
                          $objSheet->getCell($excel_alpha[17] . $i)->setValue(ucwords($branchname1));
                      }
                  */
                    $objSheet->getCell($excel_alpha[49] . $i)->setValue(ucwords('Unassigned'));

                }

                if($key > 0){

                    if(isset($value[$key]['status']) && !empty($value[$key]['status'])){
                        $objSheet->getCell($excel_alpha[49] . $i)->setValue(ucwords('Assigned'));
                    }

                    if(isset($value[0]['reroute_from_branch_id'])) {
                        if ($value[$key]['status'] == 'NC' && $value[$key]['assignee_branch'] == $value[0]['reroute_from_branch_id']) {
                            $objSheet->getCell($excel_alpha[16] . $i)->setValue(ucwords($value[$key]['date']));
                            $objSheet->getCell($excel_alpha[17] . $i)->setValue(ucwords($value[$key]['employee_name']));
                            $objSheet->getCell($excel_alpha[18] . $i)->setValue(ucwords($value[$key]['employee_id']));
                            $objSheet->getCell($excel_alpha[19] . $i)->setValue(ucwords($value[0]['reroute_from_branch_id']));
                            $branchname1 = branchname($value[0]['reroute_from_branch_id']);
                            $branchname1 = $branchname1[0]['name'];
                            $objSheet->getCell($excel_alpha[20] . $i)->setValue(ucwords($branchname1));

                        }
                        if ($value[$key]['status'] == 'NC' && $value[$key]['assignee_branch'] == $value[0]['branch_id']) {
                            $objSheet->getCell($excel_alpha[21] . $i)->setValue(ucwords($value[$key]['date']));
                            $objSheet->getCell($excel_alpha[22] . $i)->setValue(ucwords($value[$key]['employee_name']));
                            $objSheet->getCell($excel_alpha[23] . $i)->setValue(ucwords($value[$key]['employee_id']));
                            $objSheet->getCell($excel_alpha[24] . $i)->setValue(ucwords($value[0]['branch_id']));
                            $branchname2 = branchname($value[0]['branch_id']);
                            $branchname2 = $branchname2[0]['name'];
                            $objSheet->getCell($excel_alpha[25] . $i)->setValue(ucwords($branchname2));
                        }
                    }else{
                        $objSheet->getCell($excel_alpha[16] . $i)->setValue(ucwords($value[$key]['date']));
                        $objSheet->getCell($excel_alpha[17] . $i)->setValue(ucwords($value[$key]['employee_name']));
                        $objSheet->getCell($excel_alpha[18] . $i)->setValue(ucwords($value[$key]['employee_id']));
                        $objSheet->getCell($excel_alpha[19] . $i)->setValue(ucwords($value[0]['branch_id']));
                        $branchname1 = branchname($value[0]['branch_id']);
                        $branchname1 = $branchname1[0]['name'];
                        $objSheet->getCell($excel_alpha[20] . $i)->setValue(ucwords($branchname1));
                    }

                    if($value[$key]['status'] == 'FU'){
                        $objSheet->getCell($excel_alpha[26] . $i)->setValue(ucwords($value[$key]['date']));
                        $objSheet->getCell($excel_alpha[27] . $i)->setValue(ucwords($value[$key]['modified_by_name']));
                        $objSheet->getCell($excel_alpha[28] . $i)->setValue(ucwords($value[$key]['modified_by']));
                        $objSheet->getCell($excel_alpha[29] . $i)->setValue(ucwords($lead_type[$value[0]['lead_identification']]));
                    }



                    if($value[$key]['status'] == 'DC'){
                        $objSheet->getCell($excel_alpha[30] . $i)->setValue(ucwords($value[$key]['date']));
                        $objSheet->getCell($excel_alpha[31] . $i)->setValue(ucwords($value[$key]['modified_by_name']));
                        $objSheet->getCell($excel_alpha[32] . $i)->setValue(ucwords($value[$key]['modified_by']));
                    }

                    if($value[$key]['status'] == 'AO'){
                        $objSheet->getCell($excel_alpha[33] . $i)->setValue(ucwords($value[$key]['date']));
                        $objSheet->getCell($excel_alpha[34] . $i)->setValue(ucwords($value[$key]['modified_by_name']));
                        $objSheet->getCell($excel_alpha[35] . $i)->setValue(ucwords($value[$key]['modified_by']));
                        $objSheet->getCell($excel_alpha[43] . $i)->setValue(ucwords($value[0]['opened_account_no']));
                        $objSheet->getCell($excel_alpha[44] . $i)->setValue(ucwords($value[0]['amount']));
                    }

                    if($value[$key]['status'] == 'NI'){
                        $objSheet->getCell($excel_alpha[36] . $i)->setValue(ucwords($value[$key]['date']));
                        $objSheet->getCell($excel_alpha[37] . $i)->setValue(ucwords($value[$key]['modified_by_name']));
                        $objSheet->getCell($excel_alpha[38] . $i)->setValue(ucwords($value[$key]['modified_by']));
                        $objSheet->getCell($excel_alpha[39] . $i)->setValue(ucwords($value[$key]['reason_for_drop']));
                    }

                    if($value[$key]['status'] == 'Converted'){
                        $objSheet->getCell($excel_alpha[40] . $i)->setValue(ucwords($value[$key]['date']));
                        $objSheet->getCell($excel_alpha[41] . $i)->setValue(ucwords($value[$key]['modified_by_name']));
                        $objSheet->getCell($excel_alpha[42] . $i)->setValue(ucwords($value[$key]['modified_by']));
                    }

                    if($value[$key]['status'] == 'Closed'){
                        $objSheet->getCell($excel_alpha[45] . $i)->setValue(ucwords($value[$key]['date']));
                        $objSheet->getCell($excel_alpha[46] . $i)->setValue(ucwords($value[$key]['modified_by_name']));
                        $objSheet->getCell($excel_alpha[47] . $i)->setValue(ucwords($value[$key]['modified_by']));
                    }
                }
            }
            $i++;
            $j++;
        }
// pe($data);die;
        // pe($data);die;
        //echo $file_name;die;
        //return $file_name;
        make_upload_directory('uploads');
        make_upload_directory('uploads/excel_list');
        //echo $file_name;die;
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $objWriter->save('php://output');
    }


    private function leads_unassigned($arrData){
        //pe(zoneid('001949'));die;
        $login_user = get_session();
        $lead_status = array_keys($this->config->item('lead_status'));
        //Build Input Parameter

        $action = 'list';
        $select = array('COUNT(DISTINCT(l.id)) as count');
        $table = Tbl_Leads. ' as l';
        $where=array();
        $join[] = array('table' =>Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id ','type' => 'left');
        $where['(la.lead_id IS NULL OR la.is_deleted = 1)'] = NULL;

        //If Start date selected
        if(!empty($arrData['start_date'])){
            $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
            }
        //If End date selected
        if(!empty($arrData['end_date'])){
            $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
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

        if(($arrData['view'] == 'employee') || ($arrData['national'] == 'yes')){
            $this->make_bread->add('Unassigned Leads', 'reports/index/leads_unassigned', 0);
            if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                if(!empty($arrData['branch_id'])){
                    if($login_user['designation_name'] != 'BM'){
                        $this->make_bread->add('Branch View', 'reports/index/leads_unassigned/branch/'.encode_id($arrData['zone_id']), 0);
                    }
                }
            }
            $this->make_bread->add('Employee View', '', 0);
            $viewName = 'EM';
        }else if($arrData['view']){
            $this->make_bread->add('Unassigned Leads', 'reports/index/leads_unassigned', 0);
            $this->make_bread->add('Branch View', '', 0);
            $viewName = 'BM';
        }else{
            $this->make_bread->add('Unassigned Leads', '', 0);
            $viewName = $login_user['designation_name'];
            if($viewName == 'GM' || $viewName == 'Super admin'){
                $viewName = 'ZM';
            }
        }

        $WHERE = array();
        //Employee Login
        if($viewName == 'EM'){
            //Get Data for employees
            $select[] = 'l.created_by as employee_id';
            $select1[] = 'l.created_by as employee_id';
            if($arrData['national'] != 'yes'){
                $where['l.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
                if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                    if(!empty($arrData['branch_id'])){
                        $where['l.branch_id'] = $arrData['branch_id'];
                    }
                }else{
                    $where['l.branch_id'] = $login_user['branch_id'];
                }
            }
            $group_by[]  =  'l.created_by';

            //Get Listing for employees
            $SELECT = array('hrms_id as employee_id','name as employee_name','branch_id','branch_name','zone_id','zone_name','designation');
            if(isset($where['l.zone_id'])){
                $WHERE['zone_id'] = $where['l.zone_id'];
            }
            if(isset($where['l.branch_id'])){
                $WHERE['branch_id'] = $where['l.branch_id'];
            }
            //$WHERE['designation'] = 'HD';
            $GROUP_BY = array('hrms_id');
        }

        //Branch Manager Login
        if($viewName == 'BM'){
            //Get Data for Branch
            $select[] = 'l.branch_id as branch_id';
            $select1[] = 'l.branch_id as branch_id';
            $where['l.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
            $group_by[] = 'l.branch_id';

            //Get Listing for branch
            $SELECT = array('branch_id','branch_name','zone_id','zone_name');
            if(isset($where['l.zone_id'])){
                $WHERE['zone_id'] = $where['l.zone_id'];
            }
            //$WHERE['designation'] = 'BR';
            $GROUP_BY = array('branch_id');
        }

        //Zone Manager Login
        if($viewName == 'ZM'){
            //Get Data for Branch
            $select[] = 'l.zone_id as zone_id';
            $select1[] = 'l.zone_id as zone_id';
            $group_by[] = 'l.zone_id';

            //Get Listing for branch
            $SELECT = array('zone_id','zone_name');
            //$WHERE['designation'] = 'ZD';
            $GROUP_BY = array('zone_id');
        }

        $TABLE  = 'employee_dump';
        $list = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE,$viewName);

        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'count DESC');
       // pe($leads);
        $arrData['Total'] = 0;
        if($list){
            $Lead['userId'] = array();
            //$generatedLead['userId'] = array();

            if(!empty($leads)) {
                foreach ($leads as $key => $value) {
                    //echo $value['count'];
                    //Employee Login
                    if ($viewName == 'EM') {
                        $index = $value['employee_id'];
                        $Lead['userId'][] = $value['employee_id'];
                    }

                    //Branch Manager Login
                    if ($viewName == 'BM') {
                        $index = $value['branch_id'];
                        $Lead['userId'][] = $value['branch_id'];
                    }

                    //Zone Manager Login
                    if ($viewName == 'ZM') {
                        $index = $value['zone_id'];
                        $Lead['userId'][] = $value['zone_id'];
                    }

                    $unassignedLead[$index]['unassigned_leads'] = $value['count'];
                }
            }
            //pe($Lead);die;
            //pe($list);
            //pe($generatedLead);die;
            $arrData['viewName'] = $viewName;
            foreach ($list as $key => $value) {
                //echo $index;echo "<br>";
                //Employee Login
                if($viewName == 'EM'){
                    $index = $value->employee_id;
                    $arrData['leads'][$index]['employee_id'] = $value->employee_id;
                    $arrData['leads'][$index]['employee_name'] = $value->employee_name;
                    $arrData['leads'][$index]['designation'] = $value->designation;
                    $arrData['leads'][$index]['branch_name'] = $value->branch_name;
                    $arrData['leads'][$index]['branch_id'] = $value->branch_id;
                }
                //Branch Manager Login
                if($viewName == 'BM'){
                    $index = $value->branch_id;
                    $arrData['leads'][$index]['branch_name'] = $value->branch_name;
                    $arrData['leads'][$index]['branch_id'] = $value->branch_id;
                }

                //Zone Manager Login
                if($viewName == 'ZM'){
                    $index = $value->zone_id;

                }
                $arrData['leads'][$index]['zone_name'] = $value->zone_name;
                $arrData['leads'][$index]['zone_id'] = $value->zone_id;

                if(!empty($unassignedLead[$index]['unassigned_leads'])){
                    $arrData['leads'][$index]['total']=$unassignedLead[$index]['unassigned_leads'];
                    $arrData['Total'] += $unassignedLead[$index]['unassigned_leads'];
                }else{
                    $arrData['leads'][$index]['total']=0;
                }
            }
            if($this->session->userdata('admin_type') == 'BM' && $arrData['view'] == ''){
                $arrData['leads'] = array($this->session->userdata('branch_id')=> $arrData['leads'][$this->session->userdata('branch_id')]) + $arrData['leads'];
            }
            if($this->session->userdata('admin_type') == 'ZM' && $arrData['view'] == ''){
                $arrData['leads'] = array($this->session->userdata('zone_id')=> $arrData['leads'][$this->session->userdata('zone_id')]) + $arrData['leads'];
            }
        }
        sksort($arrData['leads'], "total");
        //pe($arrData);die;

        return $arrData;
    }

    private function dashboard1($arrData){


        $login_user = get_session();
        //Build Input Parameter
        $action = 'list';
        $select = array('DISTINCT(hrms_id)');
        $table = Tbl_emp_dump;
        $where  = array();
        $join = array();
        $group_by = array();

        $arrData['total_employee_count'] = count($this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array()));

        $action = 'list';
        $select = array('DISTINCT(id)');
        $table = Tbl_branch;
        $where  = array('code !='=>'');
        $join = array();
        $group_by = array();

        $arrData['total_branch_count'] = count($this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array()));

        $action = 'list';
        $select = array('DISTINCT(employee_id)');
        $table = Tbl_LoginLog;
        $where  = array('branch_id !='=>0);
        $join = array();
        $group_by = array();

        //If Start date selected
        if(!empty($arrData['start_date'])){
            $where['DATE_FORMAT(date_time,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
        }
        //If End date selected
        if(!empty($arrData['end_date'])){
            $where['DATE_FORMAT(date_time,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
        }

        $arrData['unique_login_count'] = count($this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array()));

        $where=array('branch_id !='=>0);
        $where['DATE_FORMAT(date_time,"%Y-%m-%d") >='] = date('Y-m-d');
        $arrData['today_unique_login_count'] = count($this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array()));

        $action = 'list';
        $select = array('DISTINCT(created_by)');
        $table = Tbl_Leads;
        $where  = array();
        $join = array();
        $group_by = array();

        //If Start date selected
        if(!empty($arrData['start_date'])){
            $where['DATE_FORMAT(created_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
        }
        //If End date selected
        if(!empty($arrData['end_date'])){
            $where['DATE_FORMAT(created_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
        }

        $arrData['unique_leadcreator_employee_count'] = count($this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array()));

        $action = 'list';
        $select = array('DISTINCT(created_by_branch_id)');
        $table = Tbl_Leads;
        $where  = array();
        $join = array();
        $group_by = array();

        //If Start date selected
        if(!empty($arrData['start_date'])){
            $where['DATE_FORMAT(created_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
        }
        //If End date selected
        if(!empty($arrData['end_date'])){
            $where['DATE_FORMAT(created_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
        }

        $arrData['unique_leadcreator_branch_count'] = count($this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array()));

        $action = 'list';
        $select = array('id','title');
        $table = Tbl_Category;
        $where  = array('status'=>'active','is_deleted'=>0);
        $join = array();
        $group_by = array();

        $arrData['product_category'] = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array());

        $source = $this->config->item('lead_source');


        foreach ($source as $key=>$val) {
            $action = 'list';
            $select = array('count( l.id ) as total,SUM(l.lead_ticket_range) as total_estimated_business,
             c.id as category_id,c.title as product_category');
            $table = Tbl_Leads . ' as l';
            $where = array('l.lead_source'=>$key);
            $join = array();
            $join[] = array('table' => Tbl_Category.' as c','on_condition' => 'l.product_category_id = c.id','type' => '');
            $group_by = array('l.product_category_id');

            //If Start date selected
            if (!empty($arrData['start_date'])) {
                $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") >='] = date('Y-m-d', strtotime($arrData['start_date']));
            }
            //If End date selected
            if (!empty($arrData['end_date'])) {
                $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") <='] = date('Y-m-d', strtotime($arrData['end_date']));
            }

//            $arrData['leads'][$key]['generated'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());
            $arrData['leads'][$key]['generated'] = $this->Lead->getDataTable($table, $select, $where, $join, $group_by, $order_by = array());

            $action = 'list';
            $select = array('count( l.id ) as total ,c.id as category_id,c.title as product_category');
            $table = Tbl_Leads . ' as l';
            $where = array('l.lead_source'=>$key,'la.status'=>'Converted');
            $join = array();
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
            $join[] = array('table' => Tbl_Category.' as c','on_condition' => 'l.product_category_id = c.id','type' => '');
            $group_by = array('l.product_category_id');

            //If Start date selected
            if (!empty($arrData['start_date'])) {
                $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") >='] = date('Y-m-d', strtotime($arrData['start_date']));
            }
            //If End date selected
            if (!empty($arrData['end_date'])) {
                $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") <='] = date('Y-m-d', strtotime($arrData['end_date']));
            }

            $arrData['leads'][$key]['converted'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());

            $action = 'list';
            $select = array('SUM(l.lead_ticket_range) as total, c.id as category_id,c.title as product_category');
            $table = Tbl_Leads . ' as l';
            $where = array('l.lead_source'=>$key);
            $join = array();
            $join[] = array('table' => Tbl_Category.' as c','on_condition' => 'l.product_category_id = c.id','type' => '');
            $group_by = array('l.product_category_id');

            //If Start date selected
            if (!empty($arrData['start_date'])) {
                $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") >='] = date('Y-m-d', strtotime($arrData['start_date']));
            }
            //If End date selected
            if (!empty($arrData['end_date'])) {
                $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") <='] = date('Y-m-d', strtotime($arrData['end_date']));
            }

            $arrData['leads'][$key]['estimated_business'] = $this->Lead->get_leads($action, $table, $select, $where, $join, $group_by, $order_by = array());

            $select = array('SUM( t.amount ) as total, c.id as category_id, c.title as prodct_category_title FROM (SELECT DISTINCT (lead_id), amount FROM db_response_from_cbs)t');
            $table = Tbl_cbs . ' as t';
            $where = array('l.lead_source'=>$key);
            $join = array();
            $join[] = array('table' => Tbl_Leads.' as l','on_condition' => 't.lead_id = l.id','type' => '');
            $join[] = array('table' => Tbl_Category.' as c','on_condition' => 'l.product_category_id = c.id','type' => '');
            $group_by = array('l.product_category_id');

            //If Start date selected
            if (!empty($arrData['start_date'])) {
                $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") >='] = date('Y-m-d', strtotime($arrData['start_date']));
            }
            //If End date selected
            if (!empty($arrData['end_date'])) {
                $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") <='] = date('Y-m-d', strtotime($arrData['end_date']));
            }

            $arrData['leads'][$key]['actual_business'] = $this->Lead->actual_amt($table, $select, $where, $join, $group_by, $order_by = array());
        }


        foreach ($arrData['leads'] as $key=>$val){
            if(!empty($val)) {
                foreach ($val as $key2=>$val2) {
                    if(!empty($val2)) {
                        foreach ($val2 as $rec) {
                            $record[$key][$key2][$rec['category_id']] = $rec['total'];
                        }
                    }
                }
            }
        }
        $arrData['leads'] = $record;

        return $arrData;
    }




        private function dashboard($arrData){


            $login_user = get_session();
            //Build Input Parameter
            $action = 'list';
            $select = array('DISTINCT(hrms_id)');
            $table = Tbl_emp_dump;
            $where  = array();
            $join = array();
            $group_by = array();

            $arrData['total_employee_count'] = count($this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array()));

            $action = 'list';
            $select = array('DISTINCT(id)');
            $table = Tbl_branch;
            $where  = array('code !='=>'');
            $join = array();
            $group_by = array();

            $arrData['total_branch_count'] = count($this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array()));

            $action = 'list';
            $select = array('DISTINCT(employee_id)');
            $table = Tbl_LoginLog;
            $where  = array('branch_id !='=>0);
            $join = array();
            $group_by = array();

            //If Start date selected
            if(!empty($arrData['start_date'])){
                $where['DATE_FORMAT(date_time,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
            }
            //If End date selected
            if(!empty($arrData['end_date'])){
                $where['DATE_FORMAT(date_time,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
            }

            $arrData['unique_login_count'] = count($this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array()));

            $where=array('branch_id !='=>0);
            $where['DATE_FORMAT(date_time,"%Y-%m-%d") >='] = date('Y-m-d');
            $arrData['today_unique_login_count'] = count($this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array()));

            $action = 'list';
            $select = array('DISTINCT(created_by)');
            $table = Tbl_Leads;
            $where  = array();
            $join = array();
            $group_by = array();

            //If Start date selected
            if(!empty($arrData['start_date'])){
                $where['DATE_FORMAT(created_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
            }
            //If End date selected
            if(!empty($arrData['end_date'])){
                $where['DATE_FORMAT(created_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
            }
            $arrData['unique_leadcreator_employee_count'] = count($this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array()));

            //If previous End date selected
            if(!empty($arrData['end_date'])){
                $where['DATE_FORMAT(created_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime('-1 day',strtotime($arrData['end_date'])));
            }

            $arrData['unique_leadcreator_employee_count_prev'] = count($this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array()));


            $action = 'list';
            $select = array('DISTINCT(created_by_branch_id)');
            $table = Tbl_Leads;
            $where  = array();
            $join = array();
            $group_by = array();

            //If Start date selected
            if(!empty($arrData['start_date'])){
                $where['DATE_FORMAT(created_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
            }
            //If End date selected
            if(!empty($arrData['end_date'])){
                $where['DATE_FORMAT(created_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
            }

            $arrData['unique_leadcreator_branch_count'] = count($this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array()));

            $action = 'list';
            $select = array('id','title');
            $table = Tbl_Category;
            $where  = array('status'=>'active','is_deleted'=>0);
            $join = array();
            $group_by = array();

            $arrData['product_category'] = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array());
            $source = $this->config->item('lead_source');


            foreach ($source as $key=>$val) {
                $action = 'list';
                $select = array('count(l.id) as total,SUM(l.lead_ticket_range) as total_estimated_business, p.category_id as category_id','l.product_id','p.title as product');
                $table = Tbl_Leads . ' as l';
                $where = array('l.lead_source'=>$key);
                $join = array();
                $join[] = array('table' => Tbl_Products.' as p',
                    'on_condition' => 'l.product_id = p.id','type' => '');
                $group_by = array('l.product_category_id');

                //If Start date selected
                if (!empty($arrData['start_date'])) {
                    $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") >='] = date('Y-m-d', strtotime($arrData['start_date']));
                }
                //If End date selected
                if (!empty($arrData['end_date'])) {
                    $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") <='] = date('Y-m-d', strtotime($arrData['end_date']));
                }

                $arrData['leads'][$key]['generated'] = $this->Lead->getDataTable($table, $select, $where, $join, $group_by, $order_by = array());

                $action = 'list';
                $select = array('count( l.id ) as total ,p.category_id as category_id,p.id as product_id,p.title as product');
                $table = Tbl_Leads . ' as l';
                $where = array('l.lead_source'=>$key,'la.status'=>'Converted');
                $join = array();
                $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
                $join[] = array('table' => Tbl_Products.' as p','on_condition' => 'l.product_id = p.id','type' => '');


                //If Start date selected
                if (!empty($arrData['start_date'])) {
                    $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") >='] = date('Y-m-d', strtotime($arrData['start_date']));
                }
                //If End date selected
                if (!empty($arrData['end_date'])) {
                    $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") <='] = date('Y-m-d', strtotime($arrData['end_date']));
                }

                $arrData['leads'][$key]['converted'] = $this->Lead->getDataTable($table, $select, $where, $join, $group_by, $order_by = array());


                $action = 'list';
                $select = array('SUM(l.lead_ticket_range) as total,p.id as product_id,p.category_id as category_id,
                p.title as product');
                $table = Tbl_Leads . ' as l';
                $where = array('l.lead_source'=>$key);
                $join = array();
                $join[] = array('table' => Tbl_Products.' as p',
                    'on_condition' => 'l.product_id = p.id','type' => '');


                //If Start date selected
                if (!empty($arrData['start_date'])) {
                    $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") >='] = date('Y-m-d', strtotime($arrData['start_date']));
                }

                //If End date selected
                if (!empty($arrData['end_date'])) {
                    $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") <='] = date('Y-m-d', strtotime($arrData['end_date']));
                }
                $arrData['leads'][$key]['estimated_business'] = $this->Lead->getDataTable($table, $select, $where, $join, $group_by, $order_by = array());


                //If prev End date selected
                if (!empty($arrData['end_date'])) {
                    $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") <='] = date('Y-m-d', strtotime('-1 day',strtotime($arrData['end_date'])));
                }
                $arrData['leads'][$key]['estimated_business_prev'] = $this->Lead->getDataTable($table, $select, $where, $join, $group_by, $order_by = array());



                $select = array('SUM( t.amount ) as total,p.id as product_id,p.category_id as category_id, p.title as product FROM (SELECT DISTINCT (lead_id), amount FROM db_response_from_cbs)t');
                $table = Tbl_cbs . ' as t';
                $where = array('l.lead_source'=>$key);
                $join = array();
                $join[] = array('table' => Tbl_Leads.' as l','on_condition' => 't.lead_id = l.id','type' => '');
                $join[] = array('table' => Tbl_Products.' as p','on_condition' => 'l.product_id = p.id','type' => '');
                $group_by = array('l.product_category_id');

                //If Start date selected
                if (!empty($arrData['start_date'])) {
                    $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") >='] = date('Y-m-d', strtotime($arrData['start_date']));
                }
                //If End date selected
                if (!empty($arrData['end_date'])) {
                    $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") <='] = date('Y-m-d', strtotime($arrData['end_date']));
                }
                $arrData['leads'][$key]['actual_business'] = $this->Lead->actual_amt($table, $select, $where, $join, $group_by, $order_by = array());

                //If prev End date selected
                if (!empty($arrData['end_date'])) {
                    $where['DATE_FORMAT(l.created_on,"%Y-%m-%d") <='] = date('Y-m-d', strtotime('-1 day',strtotime($arrData['end_date'])));
                }
                $arrData['leads'][$key]['actual_business_prev'] = $this->Lead->actual_amt($table, $select, $where, $join, $group_by, $order_by = array());
            }


            foreach ($arrData['leads'] as $key=>$val){
                if(!empty($val)) {
                    foreach ($val as $key2=>$val2) {
                        if(!empty($val2)) {
                            foreach ($val2 as $rec) {
                                $record[$key][$key2][$rec['category_id']] = $rec['total'];
                            }
                        }
                    }
                }
            }


            foreach ($arrData['leads'] as $key1=>$val1){
                if(!empty($val1)) {
                    foreach ($val1 as $key21=>$val21) {
                        if(!empty($val21)) {
                            foreach ($val21 as $rec1) {
                                $record1[$key1][$key21][$rec1['product_id']] = $rec1['total'];
                            }
                        }
                    }
                }
            }

            $action = 'list';
            $select = array('id','title');
            $table = Tbl_Products;
            $whereIn = array(SAVING,CURRENT,TERM,RECURRING);
            $join = array();
            $group_by = array();

            $fetchProducts = $this->Lead->fetchProductData($table,$select,$whereIn,$join,$group_by,$order_by = array(),$limit="",$whereIn);

            $lstData = array();
            foreach($fetchProducts as $data){
                $lstData[$data['id']] = $data['title'];
            }
            $arrData['product'] = $lstData;
            if(isset($record1)) {
                $arrData['leadData'] = $record1;
            }else{
                $arrData['leadData'] = null;
            }

            if(isset($record)) {
                $arrData['leads'] = $record;
            }else{
                $arrData['leads'] = null;
            }



            return $arrData;
        }






}
