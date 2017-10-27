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
                $arrData = $this->$action('converted',$arrData);
                $arrData = $this->combine($arrData);
            }else{
                $arrData = $this->$action($arrData);
            }
            if(!empty($arrData['product_category_id'])){
                $product_list = $this->Lead->get_all_products(array('category_id' => $arrData['product_category_id'],'is_deleted' => 0,'status' => 'active'));
                $arrData['product_list'] = dropdown($product_list,'All');
            }
            if($export == 'yes'){
                $this->export_to_excel($action,$arrData);
            }
        }else{
            $d = new DateTime('first day of this month');
            $arrData['start_date'] = str_replace('-', '-', $d->format('d-m-Y'));
            $arrData['end_date']   = str_replace('-', '-',date('d-m-Y'));
            if($action == 'leads_generated_vs_converted'){
                $arrData = $this->$action('generated',$arrData);
                $arrData = $this->$action('converted',$arrData);
                $arrData = $this->combine($arrData);
            }else{
                $arrData = $this->$action($arrData);
            }
        }
        
        //Get All dropdown
        $category_list = $this->Lead->get_all_category(array('is_deleted' => 0,'status' => 'active'));
        $arrData['category_list'] = dropdown($category_list,'All');
        $arrData['lead_sources'] = $this->Lead->get_enum(Tbl_Leads,'lead_source');
        $arrData['breadcrumb'] = $this->make_bread->output();
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
        $select = array('COUNT(la.lead_id) as count','la.status');
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

        if(($arrData['view'] == 'employee') || ($arrData['national'] == 'yes')){
            $this->make_bread->add('Pendancy Leads', 'reports/index/pendancy_leads_reports', 0);
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
            $this->make_bread->add('Pendancy Leads', 'reports/index/pendancy_leads_reports', 0);
            $this->make_bread->add('Branch View', '', 0);
            $viewName = 'BM';
        }else{
            $this->make_bread->add('Pendancy Leads', '', 0);
            $viewName = $login_user['designation_name'];
            if($viewName == 'GM'){
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
        $list = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE);
        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'count DESC');
        //pe($this->db->last_query());
        $arrData['leads'] = array();
        $arrData['Total'] = 0;
        if($list){
            $Lead['userId'] = array();
            foreach ($leads as $key => $value) {
                //Employee Login
                if($viewName == 'EM'){
                    $index = $value['employee_id'];
                    $Lead['userId'][] = $value['employee_id'];
                }

                //Branch Manager Login
                if($viewName == 'BM'){
                    $index = $value['branch_id'];
                    $Lead['userId'][] = $value['branch_id'];
                }

                //Zone Manager Login
                if($viewName == 'ZM'){
                    $index = $value['zone_id'];
                    $Lead['userId'][] = $value['zone_id'];
                }
                $Lead[$index]['status'][$value['status']] = $value['count'];
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
        }
        return $arrData;
    }

    private function leads_type_reports($arrData){
        $login_user = get_session();
        $lead_type = array_keys($this->config->item('lead_type'));
        //Build Input Parameter
        $action = 'list';
        $select = array('COUNT(l.id) as count','l.lead_identification');
        $table = Tbl_Leads.' as l';
        $where  = array(/*'la.is_deleted' => 0,'la.is_updated' => 1,*/'l.lead_identification IN ("'.str_replace(',','","',implode(',',$lead_type)).'")' => NULL);
        $join = array();
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
            $this->make_bread->add('Leads Identification', 'reports/index/leads_type_reports', 0);
            $this->make_bread->add('Branch View', '', 0);
            $viewName = 'BM';
        }else{
            $this->make_bread->add('Leads Identification', '', 0);
            $viewName = $login_user['designation_name'];
            if($viewName == 'GM'){
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
        $list = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE);

        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'count DESC');
        //pe($this->db->last_query());
        $arrData['leads'] = array();
        $arrData['Total'] = 0;
        if($list){
            $Lead['userId'] = array();
            foreach ($leads as $key => $value) {
                //Employee Login
                if($viewName == 'EM'){
                    $index = $value['employee_id'];
                    $Lead['userId'][] = $value['employee_id'];
                }
                //Branch Manager Login
                if($viewName == 'BM'){
                    $index = $value['branch_id'];
                    $Lead['userId'][] = $value['branch_id'];
                }
                //Zone Manager Login
                if($viewName == 'ZM'){
                    $index = $value['zone_id'];
                    $Lead['userId'][] = $value['zone_id'];
                }
                $Lead[$index]['lead_identification'][$value['lead_identification']] = $value['count'];
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
                    $arrData['leads'][$index]['lead_identification'] = array();
                }else{
                    $arrData['leads'][$index]['total'] = array_sum($Lead[$index]['lead_identification']);
                    $arrData['leads'][$index]['lead_identification'] = $Lead[$index]['lead_identification'];
                }
                $arrData['Total'] += $arrData['leads'][$index]['total'];
            }
        }
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
        $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
        $group_by = array('la.status');

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
            if($viewName == 'GM'){
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
            //Get Data for Branch
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
            //Get Data for Branch
            $select[] = 'l.zone_id';
            $group_by[] = 'l.zone_id';

            //Get Listing for branch
            $SELECT = array('zone_id','zone_name'); 
            //$WHERE['designation'] = 'ZD';
            $GROUP_BY = array('zone_id');
        }

        $TABLE  = 'employee_dump';
        $list = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE);

        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'count DESC');
        /*pe($this->db->last_query());
        exit;*/
        $arrData['leads'] = array();
        $arrData['Total'] = 0;
        if($list){
            $Lead['userId'] = array();
            foreach ($leads as $key => $value) {
                //Employee Login
                if($viewName == 'EM'){
                    $index = $value['employee_id'];
                    $Lead['userId'][] = $value['employee_id'];
                }

                //Branch Manager Login
                if($viewName == 'BM'){
                    $index = $value['branch_id'];
                    $Lead['userId'][] = $value['branch_id'];
                }

                //Zone Manager Login
                if($viewName == 'ZM'){
                    $index = $value['zone_id'];
                    $Lead['userId'][] = $value['zone_id'];
                }

                $Lead[$index]['status'][$value['status']] = $value['count'];
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
        }
        return $arrData;
    }

    private function leads_assigned($arrData){
        $login_user = get_session();
        $lead_status = array_keys($this->config->item('lead_status'));
        //Build Input Parameter
        $action = 'list';
        $select = array('COUNT(la.lead_id) as count','la.status');
        $table = Tbl_Leads.' as l';
        $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'la.status IN ("'.str_replace(',','","',implode(',',$lead_status)).'")' => NULL);
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

        if(($arrData['view'] == 'employee') || ($arrData['national'] == 'yes')){
            $this->make_bread->add('Leads Assigned',  'reports/index/leads_assigned', 0);
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
            $this->make_bread->add('Leads Assigned',  'reports/index/leads_assigned', 0);
            $this->make_bread->add('Branch View', '', 0);
            $viewName = 'BM';
        }else{
            $this->make_bread->add('Leads Assigned', '', 0);
            $viewName = $login_user['designation_name'];
            if($viewName == 'GM'){
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
        $list = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE);

        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'count DESC');
        //pe($this->db->last_query());
        $arrData['leads'] = array();
        $arrData['Total'] = 0;
        if($list){
            $Lead['userId'] = array();
            foreach ($leads as $key => $value) {
                //Employee Login
                if($viewName == 'EM'){
                    $index = $value['employee_id'];
                    $Lead['userId'][] = $value['employee_id'];
                }

                //Branch Manager Login
                if($viewName == 'BM'){
                    $index = $value['branch_id'];
                    $Lead['userId'][] = $value['branch_id'];
                }

                //Zone Manager Login
                if($viewName == 'ZM'){
                    $index = $value['zone_id'];
                    $Lead['userId'][] = $value['zone_id'];
                }
                $Lead[$index]['status'][$value['status']] = $value['count'];
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
        }else{
            $select = array('COUNT(la.lead_id) as converted_count');
            $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'la.status' => 'Converted');
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
            $alias = 'la';
        }
        $group_by = array();


        //If Start date selected
        if(!empty($arrData['start_date'])){
            $where['DATE_FORMAT('.$alias.'.created_on,"%Y-%m-%d") >='] = date('Y-m-d',strtotime($arrData['start_date']));
        }
        //If End date selected
        if(!empty($arrData['end_date'])){
            $where['DATE_FORMAT('.$alias.'.created_on,"%Y-%m-%d") <='] = date('Y-m-d',strtotime($arrData['end_date']));
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
            if($viewName == 'GM'){
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
                $where[$alias.'.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
                if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                    if(!empty($arrData['branch_id'])){
                        $where[$alias.'.branch_id'] = $arrData['branch_id'];
                    }
                }else{
                    $where[$alias.'.branch_id'] = $login_user['branch_id'];
                }
            }
            if($type == 'generated'){
                $group_by[]  =  'l.created_by';
            }else{
                $group_by[]  =  'la.employee_id';
            }

            //Get Listing for employees
            $SELECT = array('hrms_id as employee_id','name as employee_name','branch_id','branch_name','zone_id','zone_name','designation');
            if(isset($where[$alias.'.zone_id'])){
                $WHERE['zone_id'] = $where[$alias.'.zone_id'];
            }
            if(isset($where[$alias.'.branch_id'])){
                $WHERE['branch_id'] = $where[$alias.'.branch_id'];   
            }
            //$WHERE['designation'] = 'HD';
            $GROUP_BY = array('hrms_id');
        }

        //Branch Manager Login
        if($viewName == 'BM'){
            //Get Data for branch
            $select[] = $alias.'.branch_id';
            $where[$alias.'.zone_id'] = !empty($arrData['zone_id']) ? $arrData['zone_id'] : $login_user['zone_id'];
            $group_by[] = 'l.branch_id';

            //Get Listing for branch
            $SELECT = array('branch_id','branch_name','zone_id','zone_name'); 
            if(isset($where[$alias.'.zone_id'])){
                $WHERE['zone_id'] = $where[$alias.'.zone_id'];
            }
            //$WHERE['designation'] = 'BR';
            $GROUP_BY = array('branch_id');

        }

        //Zone Manager Login
        if($viewName == 'ZM'){
            //Get Data for zone
            $select[] = $alias.'.zone_id';
            $group_by[] = $alias.'.zone_id';
            
            //Get Listing for branch
            $SELECT = array('zone_id','zone_name'); 
            $WHERE = array();
            //$WHERE['designation'] = 'ZD';
            $GROUP_BY = array('zone_id');
        }

        $TABLE  = 'employee_dump';
        if(!isset($arrData['list'])){
            $arrData['list'] = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE);
        }

        $arrData[$type] = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array());
        //pe($this->db->last_query());
        return $arrData;
    }

    private function combine($arrData){
        $login_user = get_session();
        $viewName = $arrData['viewName'];
        if($arrData['view'] == 'employee'){
            $this->make_bread->add('Leads Generated Vs Converted', 'reports/index/leads_generated_vs_converted', 0);
            if((!empty($arrData['zone_id'])) || (!empty($arrData['branch_id']))){
                if(!empty($arrData['branch_id'])){
                    if($login_user['designation_name'] != 'BM'){
                        $this->make_bread->add('Branch View', 'reports/index/leads_generated_vs_converted/branch/'.encode_id($arrData['zone_id']), 0);
                    }
                }
            }
            $this->make_bread->add('Employee View', '', 0);
        }else if($arrData['view']){
            $this->make_bread->add('Leads Generated Vs Converted', 'reports/index/leads_generated_vs_converted', 0);
            $this->make_bread->add('Branch View', '', 0);
        }else{
            $this->make_bread->add('Leads Generated Vs Converted', '', 0);
        }
        $arrData['G_Total'] = $arrData['C_Total'] = 0;    
        $arrData['leads'] = array();
        $leads = array_merge($arrData['generated'],$arrData['converted']);
        if($arrData['list']){
            $Lead['userId'] = array();
            foreach ($leads as $key => $value) {
                //Employee Login
                if($viewName == 'EM'){
                    $index = $value['employee_id'];
                    $Lead['userId'][] = $value['employee_id'];
                }

                //Branch Manager Login
                if($viewName == 'BM'){
                    $index = $value['branch_id'];
                    $Lead['userId'][] = $value['branch_id'];
                }

                //Zone Manager Login
                if($viewName == 'ZM'){
                    $index = $value['zone_id'];
                    $Lead['userId'][] = $value['zone_id'];
                }

                if(isset($value['generated_count'])){
                    if(isset($Lead[$index]['generated_count'])){
                        $Lead[$index]['generated_count'] += $value['generated_count'];

                    }else{
                        $Lead[$index]['generated_count'] = $value['generated_count'];
                    }
                }
                if(isset($value['converted_count'])){
                    if(isset($Lead[$index]['converted_count'])){
                        $Lead[$index]['converted_count'] += $value['converted_count'];
                    }else{
                        $Lead[$index]['converted_count'] = $value['converted_count'];
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
                }else{
                    $arrData['leads'][$index]['generated_count'] = isset($Lead[$index]['generated_count']) ? $Lead[$index]['generated_count'] : 0;
                    $arrData['leads'][$index]['converted_count'] = isset($Lead[$index]['converted_count']) ? $Lead[$index]['converted_count'] : 0;
                }
                $arrData['G_Total'] += $arrData['leads'][$index]['generated_count'];
                $arrData['C_Total'] += $arrData['leads'][$index]['converted_count'];
            }
        }
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
            if($viewName == 'GM'){
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
        $list = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE);

        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'lead_ticket_range DESC');

        $arrData['leads'] = array();
        $arrData['Total'] = 0;
        if($list){
            $Lead['userId'] = array();
            foreach ($leads as $key => $value) {
                //Employee Login
                if($viewName == 'EM'){
                    $index = $value['employee_id'];
                    $Lead['userId'][] = $value['employee_id'];
                }

                //Branch Manager Login
                if($viewName == 'BM'){
                    $index = $value['branch_id'];
                    $Lead['userId'][] = $value['branch_id'];
                }

                //Zone Manager Login
                if($viewName == 'ZM'){
                    $index = $value['zone_id'];
                    $Lead['userId'][] = $value['zone_id'];
                }
                $Lead[$index]['ticket'] = $value['lead_ticket_range'];
                $Lead[$index]['amount'] = $value['amount'];
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
            if($viewName == 'GM'){
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
        $list = $this->Lead->get_employee_dump($SELECT,$WHERE,$GROUP_BY,$TABLE);
        /*pe($this->db->last_query());
        exit;*/

        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = 'count DESC');

        $arrData['leads'] = array();
        $arrData['Total'] = 0;   
        if($list){
            $Lead['userId'] = array();
            foreach ($leads as $key => $value) {
                //Employee Login
                if($viewName == 'EM'){
                    $index = $value['employee_id'];
                    $Lead['userId'][] = $value['employee_id'];
                }

                //Branch Manager Login
                if($viewName == 'BM'){
                    $index = $value['branch_id'];
                    $Lead['userId'][] = $value['branch_id'];
                }

                //Zone Manager Login
                if($viewName == 'ZM'){
                    $index = $value['zone_id'];
                    $Lead['userId'][] = $value['zone_id'];
                }
                $Lead[$index]['total'] = $value['count'];
            }
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
            if($this->session->userdata('admin_type') == 'BM'){
                    array_unshift($arrData['leads'], $arrData['leads'][$this->session->userdata('branch_id')]);
                    unset($arrData['leads'][$this->session->userdata('branch_id')]);
            }
            if($this->session->userdata('admin_type') == 'ZM'){
                    array_unshift($arrData['leads'], $arrData['leads'][$this->session->userdata('zone_id')]);
                    unset($arrData['leads'][$this->session->userdata('branch_id')]);
            }

        }
        return $arrData;
    }


    public function export_to_excel($action,$arrData){
        /*pe($arrData);
        exit;*/
        if($arrData['viewName'] == 'EM'){
            $header_value = array('Sr.No','Zone','Branch','HRMS ID','Employee Name','Designation','Source Type','Category Name','Product Name');
        }else if($arrData['viewName'] == 'BM'){
            $header_value = array('Sr.No','Zone','Branch','Source Type','Category Name','Product Name');   
        }else{
            $header_value = array('Sr.No','Zone','Source Type','Category Name','Product Name');
        }
        switch ($action) {
            case 'pendancy_leads_reports':
                $pendancy_leads_col = array('Total Pending Leads','Not Connected','Interested /Follow Up','Documents Collected','Drop/Not Inserted','Cannot be contacted');
                $header_value = array_merge($header_value,$pendancy_leads_col);
                break;
            case 'leads_type_reports':
                $leads_type_col = array('Total Leads','Highly Interested','Medium Interested','Low Interested');
                $header_value = array_merge($header_value,$leads_type_col);
                break;
            case 'leads_generated':
                $leads_generated_col = array('Total Generated Leads','Not Connected','Interested /Follow Up','Documents Collected','Account Opened','Converted','Drop/Not Inserted','Cannot be contacted','Closed');
                $header_value = array_merge($header_value,$leads_generated_col);
                break;
            case 'leads_assigned':
                $leads_assigned_col = array('Total Assigned Leads','Not Connected','Interested /Follow Up','Documents Collected','Account Opened','Converted','Drop/Not Inserted','Cannot be contacted','Closed');
                $header_value = array_merge($header_value,$leads_assigned_col);
                break;
            case 'leads_generated_vs_converted':
                $leads_g_vs_c_col = array('Total Leads Generated','Total Leads Converted');
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
                break;

        }
        $this->create_excel($action,$header_value,$arrData);
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
        foreach ($data['leads'] as $key => $value) {
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
            $objSheet->getCell($excel_alpha[++$col].$i)->setValue(!empty($data['lead_source']) ? ucwords($data['lead_source']) : 'All');
            $objSheet->getCell($excel_alpha[++$col].$i)->setValue(isset($data['category']) ? ucwords($data['category']) : 'All');
            $objSheet->getCell($excel_alpha[++$col].$i)->setValue(isset($data['product']) ? ucwords($data['product']) : 'All');
            if($action == 'leads_generated_vs_converted'){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(isset($value['generated_count']) ? $value['generated_count'] : 0);
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(isset($value['converted_count']) ? $value['converted_count'] : 0);
            }else if($action == 'leads_classification'){
                $objSheet->getCell($excel_alpha[++$col].$i)->setValue(isset($value['ticket']) ? $value['ticket'] : 0);
            }else if($action == 'usage'){
                if($data['viewName'] == 'EM'){
                    $objSheet->getCell($excel_alpha[++$col].$i)->setValue($value['total']);
                }else{
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
                            if(($action == 'pendancy_leads_reports') && (in_array($v1,array('AO','Converted','Closed')))){
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
        //return $file_name;
        make_upload_directory('uploads');
        make_upload_directory('uploads/excel_list');
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $objWriter->save('php://output');
    }

}