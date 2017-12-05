<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Leads extends CI_Controller
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
        $this->load->model('Master_model','master');
        /*
        echo $this->encrypt->encode('denabank1234');
        exit;*/
    }


    public function index()
    {

    }

    /*
     * add
     * Adds lead.
     * @author Gourav Thatoi
     * @access public
     * @param none
     * @return void
     */
    public function add()
    {
        if($this->session->userdata('admin_type') == 'Super admin'){
            redirect('/dashboard');
        }
        /*Create Breadcumb*/
          $this->make_bread->add('Add Leads', '', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/

        $arrData['category_selected'] = '';
        $arrData['product_selected'] = '';
        $arrData['products'] = '';

        $action = 'list';$table=Tbl_state;
        $select=array('TRIM(code) as code','TRIM(name) as name');
        $orderby = 'name ASC';
        $where=array('name !='=>'','code !='=>'');
        $arrData['states'] = $this->Lead->get_leads($action,$table,$select,$where,'','',$orderby);

        $action = 'list';$table=Tbl_district;$select=array('code','name');
        $where = array('code !='=>'');
        $arrData['districts'] = $this->Lead->get_leads($action,$table,$select,$where,'','','');

        $action = 'list';$table=Tbl_branch;$select=array('code','name');
        $where = array('code !='=>'');
        $arrData['branches'] = $this->Lead->get_leads($action,$table,$select,$where,'','','');


        $category_list = $this->Lead->get_all_category(array('is_deleted' => 0,'status' => 'active'));
        $arrData['category'] = dropdown($category_list,'Select');
        if ($this->input->post("Submit") == "Submit") {
            $this->form_validation->set_error_delimiters('<span class = "help-block">', '</span>');
            //$this->form_validation->set_rules('is_existing_customer', 'Customer', 'required');
            $this->form_validation->set_rules('customer_name', 'Customer Name', 'required|callback_alpha_dash_space');
            $this->form_validation->set_rules('contact_no', 'Phone No.', 'required|max_length[10]|min_length[10]|numeric');
            $this->form_validation->set_rules('lead_ticket_range', 'Range.', 'required|numeric');
            $this->form_validation->set_rules('product_category_id', 'Product Category', 'required');
            $this->form_validation->set_rules('product_id', 'Product','required');
            //$this->form_validation->set_rules('remark', 'Remark', 'required');
            $this->form_validation->set_rules('is_own_branch', 'Branch', 'required');
            //$this->form_validation->set_rules('lead_identification', 'Lead Identification', 'required');

            

            if ($this->input->post('is_own_branch') == '0') {
                $this->form_validation->set_rules('state_id', 'State', 'required');
                $this->form_validation->set_rules('branch_id', 'Branch', 'required');
                $this->form_validation->set_rules('district_id', 'District', 'required');
            }

            if ($this->form_validation->run() === FALSE) {
                $middle = 'Leads/add_lead';
                return load_view($middle, $arrData);
            }else{
                
                $login_user = get_session();
                $lead_data['state_id'] = $lead_data['created_by_state_id'] = $login_user['state_id'];
                $lead_data['branch_id'] = $lead_data['created_by_branch_id'] = $login_user['branch_id'];
                $lead_data['district_id'] = $lead_data['created_by_district_id'] = $login_user['district_id'];
                $lead_data['zone_id'] = $lead_data['created_by_zone_id'] = $login_user['zone_id'];
                $branch_id = $login_user['branch_id'];

                if($this->input->post('is_own_branch') == '0'){
                    $lead_data['state_id'] = $this->input->post('state_id');
                    $lead_data['branch_id'] = $this->input->post('branch_id');
                    $lead_data['district_id'] = $this->input->post('district_id');
                    $branch_id = $this->input->post('branch_id');
                }
                
                $lead_data['created_by'] = $login_user['hrms_id'];
                $lead_data['created_by_name'] = $login_user['full_name'];

                $keys = array('customer_name','contact_no','product_category_id','product_id',
                    'is_own_branch','remark','lead_ticket_range');
                foreach ($keys as $k => $value){
                    if($value=='customer_name'){
                        $lead_data[$value] = ucwords(strtolower($this->input->post($value)));
                    }else{
                        $lead_data[$value] = $this->input->post($value);
                    }

                }
                $action = 'list';
                $select = array('map_with','title');
                $table = Tbl_Products;
                $where = array('id'=>$lead_data['product_id']);
                $product_mapped_with = $this->Lead->get_leads($action,$table,$select,$where,'','','');
                $product_name = $product_mapped_with[0]['title'];
                $product_mapped_with=$product_mapped_with[0]['map_with'];
                $lead_data['department_name'] = $this->session->userdata('department_name');
                $lead_data['department_id'] = $this->session->userdata('department_id');
                $whereArray = array('processing_center'=>$product_mapped_with,'branch_id'=>$lead_data['branch_id']);
                $routed_id = $this->Lead->check_mapping($whereArray);
                if(!is_array($routed_id)){
                    $lead_data['reroute_from_branch_id'] = $branch_id;
                    $lead_data['branch_id'] = $routed_id;
                    $lead_data['modified_on'] = date('Y-m-d H:i:s',time()+5);
                }
                $lead_data['lead_name'] = $this->input->post('customer_name');
                $lead_id = $this->Lead->add_leads($lead_data);
                if($lead_id != false){

                    //send sms
                    $sms = 'Thanks for showing interest in '.ucwords($product_name).' with Dena Bank. We will contact you shortly.';
                    send_sms($this->input->post('contact_no'),$sms);

                    //Push notification
                    $emp_id = $login_user['hrms_id'];
                    $title = 'Lead Submitted Successfully';
                    $push_message = 'Lead Submitted Successfully '.ucwords($product_name);
                    sendPushNotification($emp_id,$push_message,$title);
                    //Save notification
                    $this->insert_notification($lead_data);
                }

                $assign_to = $this->Lead->get_product_assign_to($lead_data['product_id']);
                if($assign_to == 'self'){
                    $lead_assign['lead_id'] = $lead_id;
                    $lead_assign['employee_id']=$login_user['hrms_id'];
                    $lead_assign['employee_name']=$login_user['full_name'];
                    $lead_assign['branch_id']=$login_user['branch_id'];
                    $lead_assign['district_id']=$login_user['district_id'];
                    $lead_assign['state_id']=$login_user['state_id'];
                    $lead_assign['zone_id']=$login_user['zone_id'];
                    $lead_assign['created_by']=$login_user['hrms_id'];
                    $lead_assign['created_by_name']=$login_user['full_name'];
                    $lead_assign['created_on']=date('Y-m-d H:i:s');
                    $this->Lead->insert_assign($lead_assign);
                    //Push notification
                    $emp_id = $login_user['hrms_id'];
                    $title = 'New Lead Assigned';
                    $push_message = "New Lead Assigned to you";
                    sendPushNotification($emp_id,$push_message,$title);
                }
                $this->session->set_flashdata('success', "Lead Submitted Successfully");
                redirect(base_url('leads/add'), 'refresh');
            }
        } else {
            $middle = 'Leads/add_lead';
            return load_view($middle, $arrData);
        }

    }

    ##################################
    /*public Functions*/
    ##################################
    /*
    * Validation for alphabetical letters
    * @param array $pwd,$dataArray
    * @return String
    */
    public function alphaNumeric($str)
    {
        if ( !preg_match('/^[a-zA-Z0-9\s]+$/i',$str) )
        {
            $this->form_validation->set_message('alphaNumeric', 'Please enter only alpha numeric characters.');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function alpha_dash_space($str)
    {
        $check =  ( ! preg_match("/^([-a-z_ ])+$/i", $str)) ? FALSE : TRUE;
        if(!$check){
            $this->form_validation->set_message('alpha_dash_space', 'Please enter only alphabets.');
        }
        return $check;
    }


    private function insert_notification($lead_data){
        if(!empty($lead_data)){
            $this->load->model('Master_model','master');
            $productData = $this->master->view_product($lead_data['product_id']);

            $title = 'New lead added';
            $description = 'Lead For '.ucwords($lead_data['customer_name']).' submitted sucessfully';
            $priority = 'Normal';
            $notification_to = $lead_data['created_by'];    
            return notification_log($title,$description,$priority,$notification_to);
        }
    }

    /*
     * Productlist
     * Fetches products according to selected category.
     * @author Gourav Thatoi
     * @access public
     * @param none
     * @return json
     */
    public function Productlist()
    {
        if ($this->input->post()) {
            $category_id = $this->input->post("category_id");
            $select_label = $this->input->post("select_label");
            $whereArray = array('category_id' => $category_id,'is_deleted' => 0,'status' => 'active');
            $products = $this->Lead->get_all_products($whereArray);
            $product_extra = 'class="form-control" id="product_id"';
            if (!empty($products)) {
                $options[''] = $select_label;
                foreach ($products as $key => $value) {
                    $options[$value['id']] = ucwords($value['title']);
                }
                $html = '<label>Product:<span style="color:red;">*</span></label>';
                $html .= form_dropdown('product_id', $options, '', $product_extra);
            } else {
                $options[''] = $select_label;
                $html = '<label>Product:<span style="color:red;">*</span></label>';
                $html .= form_dropdown('product_id', $options, '', $product_extra);
            }
            echo $html;
        }
    }

    /*
     * upload
     * Does the excel file import.
     * @author Gourav Thatoi
     * @access public
     * @param none
     * @return void
     */

    public function upload($param = '')
    {
        $admin = ucwords(strtolower($this->session->userdata('admin_type')));
        if ($admin != 'Super Admin'){
            redirect('dashboard');
        }
        /*Create Breadcumb*/
          $this->make_bread->add('Leads Upload', '', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/

        if($this->input->post('Submit')) {
            $lead_source = $this->input->post('lead_source');
            $this->form_validation->set_rules('lead_source','Lead Source', 'required');
            if ($this->form_validation->run() === FALSE) {
                $msg = notify("Please Select Lead Source",'danger');
                $this->session->set_flashdata('message', $msg);
                redirect('leads/upload');
            }
            if (isset($_FILES['filename']) && !empty($_FILES['filename']['tmp_name'])) {
                make_upload_directory('./uploads');
                $file = upload_excel('./uploads', 'filename');
                if (!is_array($file)) {
                    $msg = notify($file, $type = "danger");
                    $this->session->set_flashdata('error', $msg);
                    redirect('leads/upload');
                } else {
                    set_time_limit(0);
                    ini_set('memory_limit', '-1');
                    $keys = ['customer_name', 'contact_no', 'is_own_branch', 'branch_id', 'zone_id', 'state_id', 'district_id', 'product_category_id', 'product_id', 'remark'];

                    $excelData = fetch_range_excel_data($file['full_path'], 'A2:J', $keys);
                    $validation = $this->validate_leads_data($excelData,$lead_source);
                    if (!empty($validation['insert_array'])) {
                        $insert_count = $this->Lead->insert_uploaded_data(Tbl_Leads, $validation['insert_array']);

                    }
                    if ($validation['type'] == 'error') {
                        make_upload_directory('./uploads/errorlog');
                        $target_path = './uploads/errorlog/';
                        $target_file = $file['file_name'];
                        create_excel_error_file($validation['data'], $target_path.$target_file,$target_file);
                        $data = array(
                            'file_name' => $target_file,
                            'status' => 'failed',
                            'lead_source'=>$lead_source
                        );
                        $this->Lead->uploaded_log('uploaded_leads_log', $data);
                        $download_url = base_url('uploads/errorlog/'.$target_file);
                        $msg = notify('<span style="color: green">'.$validation['total_inserted'] . ' rows inserted sucessfully.</span> Error occured in ' . $validation['total_error_rows'] . ' rows.Please refer log file <a href="'.$download_url.'">here</a>.', 'danger');
                        $this->session->set_flashdata('error', $msg);
                        redirect(base_url('leads/upload'), 'refresh');
                    }
                    $data = array(
                        'file_name' => $file['file_name'],
                        'status' => 'success',
                        'lead_source'=>$lead_source
                    );
                    $this->Lead->uploaded_log('uploaded_leads_log', $data);
                    $msg = notify('File Uploaded Successfully.' . $validation['total_inserted'] . ' rows inserted. ', 'success');
                    $this->session->set_flashdata('success', $msg);
                    redirect(base_url('leads/upload'), 'refresh');

                }
            }
            $msg = notify("Please upload a file",'danger');
            $this->session->set_flashdata('message', $msg);
            redirect('leads/upload');
        }
        $arrData['uploaded_logs'] = $this->Lead->get_uploaded_leads_logs();
        $middle = "Leads/upload";
        load_view($middle,$arrData);
    }

    /*
     * validate_leads_data
     * Does the excel validation.
     * @author Gourav Thatoi
     * @access public
     * @param none
     * @return array
     */
    private function validate_leads_data($excelData,$lead_source)
    {
        $total_inserted=0; $total_rows = count($excelData);
        $error = $insert_array = $update_array = array();

        foreach ($excelData as $key => $value){

            $prod_cat_title = preg_replace('!\s+!', ' ', $value['product_category_id']);
            $whereArray = array('title'=>ucwords(strtolower(trim($prod_cat_title))));
            $prod_category_id = $this->Lead->fetch_product_category_id($whereArray);
            if($prod_category_id == false){
                $error[$key] = 'Category does not exist.';

            }else{
                if($value['branch_id'] == ''){
                    $error[$key] = 'Branch id missing.';

                }else{
                    $all_product = $this->Lead->all_products_under_category($prod_category_id);
                    $prod_title = preg_replace('!\s+!', ' ', $value['product_id']);

                    if(in_array(ucwords(strtolower(trim($prod_title))),$all_product)){
                        $whereArray = array('title' => ucwords(strtolower(trim($prod_title))),'status'=>'active');
                        $prod_id = $this->Lead->fetch_product_id($whereArray);
                        $analytic_lead_route = $this->master->view_lead_route();
                        if(($lead_source == 'analytics' && $analytic_lead_route[0]['route_to'] == 1) ||
                            ($lead_source != 'analytics'))
                        {
                            $mapping_whereArray = array('processing_center' => $prod_id['map_with'], 'branch_id' => $value['branch_id']);
                            $routed_id = $this->Lead->check_mapping($mapping_whereArray);
                            if (!is_array($routed_id)){
                                $value['reroute_from_branch_id'] = $value['branch_id'];
                                $value['branch_id'] = $routed_id;
                            }else{
                                $value['reroute_from_branch_id'] = NULL;
                            }
                        }
                        $is_own_branch = '1';
                        $is_existing_customer = '0';
                        if($value['is_existing_customer'] == 'y'){
                            $is_existing_customer = '1';
                        }
                        if($value['is_own_branch'] == 'n'){
                            $is_own_branch = '0';
                        }
                        $value['is_own_branch'] = $is_own_branch;
                        $value['is_existing_customer'] = $is_existing_customer;
                        $value['product_category_id']=$prod_category_id;
                        $value['product_id']=$prod_id['id'];
                        $value['lead_name']=$value['customer_name'];
                        $value['lead_source']=$lead_source;
                        $insert_array[] = $value;
                        $total_inserted++;
                    }else{
                        $error[$key] = 'Product name and product category name does not match.';
                    }
                }

            }

        }

        if(!empty($error))
        {
            return ['type' => 'error','total_inserted'=>$total_inserted ,'total_error_rows'=>($total_rows-$total_inserted), 'data' => $error,'insert_array' => $insert_array, 'update_array' => $update_array];
        }
        return ['type' => 'success','total_inserted'=>$total_inserted, 'insert_array' => $insert_array, 'update_array' => $update_array];
    }

    public function download_error_log(){
        $this->load->library('excel');
        $objPHPExcelWriter = new PHPExcel();
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcelWriter, 'Excel5');
        // $objWriter->save($target_file_path);

        $file_name = time().'Error_log.xls';
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $objWriter->save('php://output');
    }


    public function unassigned_leads_list($lead_source=''){
        $lead_source = decode_id($lead_source);
        $source = $this->config->item('lead_source');
        /*Create Breadcumb*/
          $this->make_bread->add('Unassigned Leads', 'leads/unassigned_leads', 0);
          $this->make_bread->add(ucwords($source[$lead_source]),'', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/
        $unassigned_leads = $this->Lead->unassigned_leads($lead_source,'');
        $arrData['unassigned_leads'] = $unassigned_leads;
        $arrData['lead_source'] = $lead_source;
        $arrData['type'] = 'unassigned';
        $middle = "Leads/unassigned_list";
        load_view($middle,$arrData);
    }

    /**
     * unassigned_leads
     * loads the unassigned leads count filtered by lead source
     * @autor Gourav Thatoi
     * @accss public
     * @return array
     */
    public function unassigned_leads(){
        $login_user = get_session();

        /*Create Breadcumb*/
          $this->make_bread->add('Unassigned Leads', '', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/
        $select = array('db_leads.lead_source,COUNT(db_leads.lead_source) as total');
        $table = Tbl_Leads;
        $join = array('db_lead_assign','db_lead_assign.lead_id = db_leads.id ','left');
        $group_by = array('db_leads.lead_source');
        $where = array(Tbl_Leads . '.branch_id' => $login_user['branch_id']);
        $yr_start_date=date('Y').'-04-01 00:00:00';
        $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
        $where[Tbl_Leads.".created_on >='".$yr_start_date."' AND ".Tbl_Leads.".created_on <='".$yr_end_date."'"] = NULL;
        $where['('.Tbl_LeadAssign.'.lead_id IS NULL OR '.Tbl_LeadAssign.'.is_deleted = 1)'] = NULL;
        $arrData['unassigned_leads_count'] = $this->Lead->unassigned_status_count($select,$table,$join,$where,$group_by);
        $response = array();
        $keys=array('walkin'=>0,'analytics'=>0,'tie_ups'=>0,'enquiry'=>0);
       foreach ($arrData['unassigned_leads_count'] as $k => $v){
                $keys[$v['lead_source']] = $v['total'];

        }
        $arrData['unassigned_leads_count'] = $keys;
        $middle = "Leads/view/unassigned_view";
        load_view($middle,$arrData);
    }

    public function unassigned_leads_details($lead_source,$id){
        $id = decode_id($id);
        $lead_source = decode_id($lead_source);
        
        /*Create Breadcumb*/
        $this->make_bread->add('Unassigned Leads', 'leads/unassigned_leads', 0);
        $this->make_bread->add(ucwords($lead_source),'leads/unassigned_leads_list/'.encode_id($lead_source), 0);
        $this->make_bread->add('Lead Detail', '', 0);
        $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/

        $arrData['unassigned_leads'] = $this->Lead->unassigned_leads('',$id);
        $arrData['lead_source'] = ucwords($lead_source);
        $l_source = encode_id($lead_source);
        $arrData['backUrl'] = 'leads/unassigned_leads_list/'.$l_source;

        $middle = "Leads/unassigned_details";
        load_view($middle,$arrData);
    }

    /**
     * leads_list
     * Get Leads generated by and converted as well as assigned to login user (monthwise/yearwise) 
     * @author Ashok Jadhav
     * @access public
     * @param $type,$till,$status,$param,$lead_source
     * @return array
     */
    public function leads_list($type,$till,$status = null,$param = null,$lead_source = null){
        //Fixed Parameters
        $arrData['type'] = $type;
        $arrData['till'] = $till;
        if(($status != 'all') && ($status != null)){
            $lead_status = $this->config->item('lead_status');
            $arrData['status'] = $status;
        }
        if(($param != 'all') && ($param != null)){
            $arrData['param'] = decode_id($param);
        }
        if(($lead_source != 'all') && ($lead_source != null)){
            $arrData['lead_source'] = $lead_source;
        }
        /*Breadcumb Creation*/
        if($type == 'assigned'){
            $login_user = get_session();
            if($login_user['designation_name'] == 'EM'){
                $table2 = Tbl_LeadAssign;
                $where2 = array(Tbl_LeadAssign . '.employee_id' => $login_user['hrms_id']);
                $lead_status_data2 = array('view_status' => 1);
                $this->Lead->update_lead_data($where2, $lead_status_data2, $table2);
            }
            if(($status != null) && ($lead_source != null)){
                //Breadcumb creation for Lead Performance Source wise
                $this->make_bread->add('Lead Performance', 'dashboard/leads_performance/'.$type.'/'.$param, 0);   
                $this->make_bread->add($lead_source, 'dashboard/leads_status/'.$type.'/'.$param.'/'.$lead_source, 0);   
                $this->make_bread->add(ucwords($lead_status[$status]),'', 0);   
            }else{
                //Breadcumb creation for Assigned List
                $this->make_bread->add(ucwords($type).' Leads', '', 0);   
            }
        }
        if(($type == 'generated') && ($status != null)){
            //Breadcumb creation for Generated Leads Status wise
            $this->make_bread->add(ucwords($type).' Leads', 'dashboard/leads_status/'.$type.'/'.$param, 0);   
            $this->make_bread->add(ucwords($lead_status[$status]),'', 0);   
        }
        $arrData['breadcrumb'] = $this->make_bread->output();

        //Get session data
        $login_user = get_session();
        $arrData = $this->view($login_user,$arrData,$param);
        return load_view('Leads/view',$arrData);
    }

    /**
     * details
     * Get Leads details based on type of lead (Generated,Converted,Assigned)
     * @author Ashok Jadhav
     * @access public
     * @param $type,$till,$lead_id
     * @return array
     */
    public function details($type,$till,$lead_id,$status = null,$param = null,$lead_source = null){
        $lead_id = decode_id($lead_id);
        $arrData['type'] = $type;
        $arrData['till'] = $till;
        $lead_status = $this->config->item('lead_status');
        $table=Tbl_state;
        $where=array('name !='=>'','code !='=>'');
        $order_by = 'name ASC';
        $arrData['states'] = allMasters($table,$where,'',$order_by);
        $table=Tbl_district;
        $arrData['districts'] = allMasters($table,$where,'',$order_by);

        $table=Tbl_branch;
        $arrData['branches'] = allMasters($table,$where,'',$order_by);
        if($type == 'assigned'){
            if(($status != null) && ($lead_source != null)){
                //Breadcumb creation for Lead Performance Source wise
                $this->make_bread->add('Lead Performance', 'dashboard/leads_performance/'.$type.'/'.$param, 0);
                $this->make_bread->add($lead_source, 'dashboard/leads_status/'.$type.'/'.$param.'/'.$lead_source, 0);
                $this->make_bread->add(ucwords($lead_status[$status]),'leads/leads_list/'.$type.'/'.$till.'/'.$status.'/'.$param.'/'.$lead_source, 0);
                $arrData['backUrl'] = 'leads/leads_list/'.$type.'/'.$till.'/'.$status.'/'.$param.'/'.$lead_source;
            }else{
                //Breadcumb creation for Assigned List
                $this->make_bread->add(ucwords($type).' Leads', 'leads/leads_list/'.$type.'/'.$till, 0);
                $arrData['backUrl'] = 'leads/leads_list/'.$type.'/'.$till;
            }
        }

        if(($type == 'generated') && ($status != null)){
            //Breadcumb creation for Generated Leads Status wise
            $this->make_bread->add(ucwords($type).' Leads', 'dashboard/leads_status/'.$type.'/'.$param, 0);
            $this->make_bread->add(ucwords($lead_status[$status]),'leads/leads_list/'.$type.'/'.$till.'/'.$status.'/'.$param, 0);
            $arrData['backUrl'] = 'leads/leads_list/'.$type.'/'.$till.'/'.$status.'/'.$param;
        }
        $arrData['breadcrumb'] = $this->make_bread->output();


        $this->make_bread->add('Lead Detail','', 0);
        $arrData['breadcrumb'] = $this->make_bread->output();

        /*Create Breadcumb*/

        $login_user = get_session();

        if(isset($login_user['designation_name']) && !empty($login_user['designation_name'])){
            //Parameters buiding for sending to list function.
            $action = 'list';
            $table = Tbl_Leads.' as l';
            $where  = array('l.id' => $lead_id);
            $join = array();
            $join[] = array('table' => Tbl_Products.' as p','on_condition' => 'l.product_id = p.id AND l.product_category_id = p.category_id','type' => '');
            $join[] = array('table' => Tbl_Category.' as c','on_condition' => 'l.product_category_id = c.id','type' => '');

            if($type == 'generated'){
                $select = array('l.id','l.opened_account_no','l.customer_name','l.lead_identification','l.lead_source','l.contact_no','l.product_id','p.title AS product_title','c.title AS category_title','l.product_category_id','la.status','la.employee_name');
                $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => 'left');
            }
            if($type == 'assigned'){
                //SELECT COLUMNS
                $select = array('l.id','l.opened_account_no','l.remark','l.customer_name','l.lead_identification','l.lead_source','l.contact_no','l.product_id','p.title AS product_title'/*,'l.interested_product_id','p1.title AS interested_product_title'*/,'c.title AS category_title','l.product_category_id','la.status','la.employee_id','la.employee_name','la.reason_for_drop','r.remind_on','r.reminder_text');

                $where['la.is_deleted'] = 0;
                $where['la.is_updated'] = 1;

                //JOIN CONDITIONS
                $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
                $join[] = array('table' => Tbl_Reminder.' as r','on_condition' => 'la.lead_id = r.lead_id AND r.is_cancelled = "No"','type' => 'left');
                /*$join[] = array('table' => Tbl_Products.' as p1','on_condition' => 'l.interested_product_id = p1.id','type' => 'left');*/

                //Only for assign leads show lead status dropdown as he can update status of only assigned leads
//                $arrData['lead_status'] = $this->Lead->get_enum(Tbl_LeadAssign,'status');
                $arrData['lead_identification'] = $this->Lead->get_enum(Tbl_Leads,'lead_identification');
                $category_list = $this->Lead->get_all_category(array('is_deleted' => 0,'status' => 'active'));
                $arrData['category_list'] = dropdown($category_list,'Select');
                $arrData['previous_status'] = $this->Lead->lead_previous_status($lead_id);
            }
            $arrData['leads'] = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());
            if($type == 'assigned'){
                $all_status = $this->config->item('lead_status');

                if($this->session->userdata('admin_type') == 'BM'){
                    $bm_status = $all_status;
                    unset($bm_status['NC'],$bm_status['AO'],$bm_status['NI'],$bm_status['DC'],$bm_status['FU']);
                    $arrData['lead_status'] = $bm_status;
                }else{
                    if($arrData['leads'][0]['status'] == 'NC'){
                        $nc_status = $all_status;
                        unset($nc_status['NC'],$nc_status['Converted'],$nc_status['Closed']);
                        $arrData['lead_status'] = $nc_status;
                    }
                    if($arrData['leads'][0]['status'] == 'NI'){
                        $arrData['lead_status'] = array('Closed' => 'Reject');
                    }
                    if($arrData['leads'][0]['status'] == 'FU'){
                        $fu_status = $all_status;
                        unset($fu_status['NC'],$fu_status['Converted'],$fu_status['FU'],$fu_status['Closed']);
                        $arrData['lead_status'] = $fu_status;
                    }
                    if($arrData['leads'][0]['status'] == 'DC'){
                        $dc_status = $all_status;
                        unset($dc_status['NC'],$dc_status['DC'],$dc_status['Converted'],$dc_status['FU'],$dc_status['Closed']);
                        $arrData['lead_status'] = $dc_status;
                    }
                    if($arrData['leads'][0]['status'] == 'AO'){
                        $ao_status = $all_status;
                        if($login_user['designation_name'] == 'EM'){
                            unset($ao_status['NC'],$ao_status['DC'],$ao_status['AO'],$ao_status['FU'],$ao_status['NI']);
                        }else{
                            unset($ao_status['AO'],$ao_status['FU'],$ao_status['NI']);
                        }
                        $arrData['lead_status'] = $ao_status;
                    }
                }
            }

        }
        return load_view($middle = "Leads/detail",$arrData);
    }

    /**
     * update_lead_status
     * Only for assigned lead list able to change lead status / Add Follow Up details
     * @author Ashok Jadhav
     * @access public
     * @param empty
     * @return array
     */
    public function update_lead_status(){
        if($this->input->post()){
            $login_user = get_session();
            $lead_id = decode_id($this->input->post('lead_id'));
            $lead_type = $this->input->post('lead_type');
            /****************************************************************
                                For Assigned List
            *****************************************************************/
                if($lead_type == 'assigned'){

                    $lead_identification = $this->input->post('lead_identification');
                    $lead_status = $this->input->post('lead_status');
                    $drop_reason = array();
                    if($lead_status == 'NI'){
                        $drop_reason = $this->input->post('reason');
                        if(empty($drop_reason) || $drop_reason == NULL){
                            $this->form_validation->set_rules('reason','Reason For Drop', 'required');
                            if ($this->form_validation->run() === FALSE) {
                                $this->session->set_flashdata('error','Enter Reason For Drop.');
                                redirect('leads/details/assigned/ytd/'.encode_id($lead_id));
                            }
                        }
                    }
                    $employee_id = $this->input->post('reroute_to');

                    if ($this->input->post('is_own_branch') == '0') {
                        $action = 'list';
                        $table = Tbl_Leads;
                        $select = array(Tbl_Leads . '.*');
                        $where = array(Tbl_Leads . '.id' => $lead_id);
                        $leadsAssign = $this->Lead->get_leads($action, $table, $select, $where, $join = array(), $group_by = array(), $order_by = array());
                        $leads_data = $leadsAssign[0];
                        $id = $leads_data['id'];
                        if($leads_data['branch_id'] == $this->input->post('branch_id')){
                            $this->session->set_flashdata('error','Lead can not be reassigned to same branch');
                            redirect('leads/leads_list/assigned/ytd','refresh');
                        }else {
                            $update_lead_data['reroute_from_branch_id'] = $leads_data['branch_id'];
                            $update_lead_data['state_id'] = $this->input->post('state_id');
                            $update_lead_data['branch_id'] = $this->input->post('branch_id');
                            $update_lead_data['district_id'] = $this->input->post('district_id');
                            $date = date('Y-m-d H:i:s');
                            $update_lead_data['modified_on'] = $date;
                            $whereUpdate = array('id' => $id);
                            $this->Lead->update($whereUpdate, Tbl_Leads, $update_lead_data);
                            $whereUpdate = array('lead_id' => $id);
                            $table = Tbl_LeadAssign;
                            if (empty($drop_reason)) {
                                $data = array('is_updated' => 0,'is_deleted' => 1);
                            } else {
                                $data = array('is_updated' => 0,'is_deleted' => 1, 'reason_for_drop' => $drop_reason);
                            }
                            $this->Lead->update($whereUpdate, $table, $data);
                            $this->session->set_flashdata('success', 'Lead information updated successfully');
                            redirect('leads/leads_list/assigned/ytd', 'refresh');
                        }
                    }
                    else{

                    /*****************************************************************/

                    //Building input parameters for function to get_leads
                    $action = 'list';
                    $table = Tbl_LeadAssign;
                    $select = array(Tbl_LeadAssign.'.*');
                    $where = array(Tbl_LeadAssign.'.lead_id' => $lead_id,Tbl_LeadAssign.'.is_updated' => 1);
                    $leadsAssign = $this->Lead->get_leads($action,$table,$select,$where,$join = array(),$group_by = array(),$order_by = array());
                    $leads_data = $leadsAssign[0];

                    $response1['status'] = 'success';
                    if(((!empty($lead_status)) && ($leads_data['status'] != $lead_status)) || (isset($employee_id) && !empty($employee_id))){
                        if ($response1['status'] == 'success') {

                            //Set current entry as old (set is_updated = 0)
                            $lead_old_data = array('is_updated' => 0);

                            //Create new entry in table Lead Assign with changed status.

                            /****************************************************************
                             * Update Lead Status
                             *****************************************************************/
                            $lead_status_data = array(
                                'lead_id' => $leads_data['lead_id'],
                                'employee_id' => $leads_data['employee_id'],
                                'employee_name' => $leads_data['employee_name'],
                                'branch_id' => $leads_data['branch_id'],
                                'district_id' => $leads_data['district_id'],
                                'state_id' => $leads_data['state_id'],
                                'zone_id' => $leads_data['zone_id'],
                                'status' => $lead_status,
                                'is_updated' => 1,
                                'created_on' => $leads_data['created_on'],
                                'created_by' => $leads_data['created_by'],
                                'created_by_name' => $leads_data['created_by_name'],
                                'modified_on' => date('y-m-d-H-i-s'),
                                'modified_by' => $login_user['hrms_id'],
                                'modified_by_name' => $login_user['full_name']
                            );
                            if(!empty($drop_reason)){
                                $lead_status_data['reason_for_drop'] = $drop_reason;
                            }
//                            if($lead_status == 'FU'){
//                                $lead_status_data['followup_date'] = date('y-m-d-H-i-s',strtotime($this->input->post('remind_on')));
//                            }
                            /*****************************************************************/

                            /****************************************************************
                             * Reroute Lead
                             *****************************************************************/
                            if (isset($employee_id) && !empty($employee_id)) {
                                $lead_old_data['is_deleted'] = 1;
                                $explode_employee = explode('-', $employee_id);
                                $lead_status_data['employee_id'] = $explode_employee[0];
                                $lead_status_data['employee_name'] = $explode_employee[1];
                                if ($leads_data['status'] != $lead_status && $lead_status !='' && !empty($lead_status)) {
                                    $lead_status_data['status'] = $lead_status;
                                }
                                else {
                                    $lead_status_data['status'] = $leads_data['status'];
                                }
                                $title = 'Lead assigned';
                                $description = 'Lead assigned';
                                $priority = 'Normal';
                                $notification_to = $lead_status_data['employee_id'];
                                notification_log($title, $description, $priority, $notification_to);
                            }
                            /*****************************************************************/
                            $this->Lead->update_lead_data($where, $lead_old_data, Tbl_LeadAssign);
                            $this->Lead->insert_lead_data($lead_status_data, Tbl_LeadAssign);

                            if ($lead_status == 'FU') {
                                $action = 'list';
                                $table = Tbl_Leads;
                                $select = array(Tbl_Leads . '.*');
                                $where = array(Tbl_Leads . '.id' => $lead_id);
                                $leadsAssigned = $this->Lead->get_leads($action, $table, $select, $where, $join = array(), $group_by = array(), $order_by = array());
                                $leads_info = $leadsAssigned[0];
                                if ($leads_info['lead_source'] == 'analytics') {

                                    if ($leads_info['reroute_from_branch_id'] == '' || $leads_info['reroute_from_branch_id'] == NULL) {
                                        $action = 'list';
                                        $select = array('map_with');
                                        $table = Tbl_Products;
                                        $where = array('id' => $leads_info['product_id'],'status'=>'active');
                                        $product_mapped_with = $this->Lead->get_leads($action, $table, $select, $where, '', '', '');
                                        $product_mapped_with = $product_mapped_with[0]['map_with'];
                                        $whereArray = array('processing_center' => $product_mapped_with, 'branch_id' => $leads_data['branch_id']);
                                        $routed_id = $this->Lead->check_mapping($whereArray);
                                        if ($this->input->post('is_own_branch') != '0') {
                                            $branch_id = $leads_data['branch_id'];
                                        } else {
                                            $branch_id = $this->input->post('branch_id');
                                        }

                                        if (!is_array($routed_id)) {
                                            $update_data['reroute_from_branch_id'] = $branch_id;
                                            $update_data['branch_id'] = $routed_id;
                                            $date = date('Y-m-d H:i:s');
                                            $update_data['modified_on'] = $date;
                                            $where = array('id' => $lead_id);
                                            $table = Tbl_Leads;
                                            $this->Lead->update_lead_data($where, $update_data, $table);
                                            $whereUpdate = array('lead_id' => $lead_id);
                                            $table = Tbl_LeadAssign;
                                            $data = array('is_updated' => 0,'is_deleted' => 1);
                                            $order_by = "id DESC";
                                            $limit= 1;
                                            $this->Lead->update_routed_lead($whereUpdate, $table, $data,$order_by,$limit);

                                        }

                                    }
                                }
                            }
                        }
                    }
                        /*****************************************************************
                        Update Lead Identification
                         *****************************************************************/
                        $where = array('id' => $lead_id);
                        $lead_identification_data = array(
                            'lead_identification' => $lead_identification
                        );
                        $response2 = $this->Lead->update_lead_data($where,$lead_identification_data,Tbl_Leads);
                        /*****************************************************************/


                        if(($response1['status'] == 'error') || ($response2['status'] == 'error')){
                            $this->session->set_flashdata('error','Failed to update lead information');
                            redirect('leads/leads_list/assigned/ytd');
                        }else{
                            if($lead_status == 'FU'){
                                $wherefollowup = array(Tbl_LeadAssign.'.lead_id' => $lead_id,Tbl_LeadAssign.'.is_updated' => 1,Tbl_LeadAssign.'.status' => 'FU');
                                $followup_data = array('followup_date'=>date('Y-m-d-H-i-s',strtotime($this->input->post('remind_on'))));
                                $this->Lead->update_lead_data($wherefollowup, $followup_data, Tbl_LeadAssign);
                                $remindData = array(
                                    'lead_id' => $lead_id,
                                    'remind_on' => date('y-m-d-H-i-s',strtotime($this->input->post('remind_on'))),
                                    'remind_to' => $this->input->post('remind_to'),
                                    'reminder_text' => $this->input->post('reminder_text')
                                );
                                //This will add entry into reminder scheduler for status (Interested/Follow up)
                                $this->Lead->add_reminder($remindData);

                            }
                            if($lead_status == 'AO'){
                                $cbs_res = $this->input->post('response_data');
                                $split_cbs_resp = explode('~',$cbs_res);
                                $responseData = array(
                                    'lead_id' => $lead_id,
                                    'account_no'=>trim($this->input->post('accountNo')),
                                    'response_data' => $this->input->post('response_data'),
                                    'amount' => substr($split_cbs_resp[0], 3),
                                    'customer_name' => $split_cbs_resp[1],
                                    'customer_contact_no' => $split_cbs_resp[2],
                                    'email_id' => $split_cbs_resp[3],
                                );
                                //This will add entry into cbs response for status (Account Opened)
                                $this->Lead->insert_lead_data($responseData,Tbl_cbs);
                                $table = Tbl_Leads;
                                $where = array('id'=>$lead_id);
                                $data = array('opened_account_no'=>trim($this->input->post('accountNo')));
                                $this->Lead->update_lead_data($where,$data,$table);
                            }
                            if($lead_status == 'Converted'){
                                $this->points_distrubution($lead_id);
                            }


                            $cat_name = $this->input->post('cat_name');
                            $customer_name = $this->input->post('customer_name');
                            $statusNotification = array('AO','NI');
                            if(in_array($lead_status,$statusNotification) || ($cat_name == 'Fee Income' && $lead_status == 'DC')){
                                $title="Action Required";
                                $description="Lead for ".ucwords(strtolower($customer_name))." requires your action";
                                $notification_to = $leads_data['created_by'];
                                $priority="Normal";
                                notification_log($title,$description,$priority,$notification_to);
                                //push notification
                                $emp_id = $leads_data['created_by'];
                                sendPushNotification($emp_id,$description,$title);
                            }
                            $this->session->set_flashdata('success','Lead information updated successfully');
                            redirect('leads/leads_list/assigned/ytd');
                        }
                    }
                }
            /*****************************************************************/


            /*****************************************************************
                                    For Unassigned List
            *****************************************************************/
                if($lead_type == 'unassigned'){
                    $employee_id = $this->input->post('assign_to');
                    $this->assign_to($employee_id,$lead_id);
                    $this->session->set_flashdata('success','Lead Assigned Successfully.');
                    redirect('leads/unassigned_leads/'.encode_id('Walk-in'));
                }
            /*****************************************************************/
        }
    }

    /**
     * update_lead_product
     * Interested in other product
     * @author Ashok Jadhav
     * @access public
     * @param empty
     * @return array
     */
    public function update_lead_product($lead_id,$product_category_id,$product_id){
        $login_user = get_session();

        //Building input parameters
        $table = Tbl_Leads;
        $where  = array(Tbl_Leads.'.id' => $lead_id);
        //link interested product id with current lead.
        $data['product_category_id'] = $product_category_id; 
        $data['product_id']     = $product_id; 
        $response = $this->Lead->update($where,$table,$data);
        if($response['status'] == 'error'){
            return false;
        }else{
            return true;
        }

    }

    /**
     * assign_multiple
     * Assign all (Unassigned) leads to selected employee
     * @author Ashok Jadhav
     * @access public
     * @param empty
     * @return array
     */
    public function assign_multiple(){
        if($this->input->post()){
            $employee_id = $this->input->post('assign_to');
            $lead_source = $this->input->post('lead_source');
            $lead_ids = $this->input->post('lead_ids');
            if(empty($lead_ids)){
                $this->session->set_flashdata('error','Please select atleast one lead');    
            }else{
                $this->assign_to($employee_id,$lead_ids);
                $this->session->set_flashdata('success','Lead Assigned Successfully.');
            }
            redirect('leads/unassigned_leads_list/'.encode_id($lead_source));
        }
    }

    


    /*Private Functions*/

    private function view($login_user,$arrData,$param = null){
        $type = $arrData['type'];
        $till = $arrData['till'];
        //Parameters buiding for sending to list function.
        $action = 'list';
        $table = Tbl_Leads.' as l';
        $join = array();
        $join[] = array('table' => Tbl_Products.' as p','on_condition' => 'l.product_id = p.id AND l.product_category_id = p.category_id','type' => '');
        $join[] = array('table' => Tbl_Category.' as pc','on_condition' => 'l.product_category_id = pc.id','type' => '');
        if($type == 'generated'){
            $select = array('l.id','l.customer_name','l.contact_no','l.lead_identification','l.created_on','l.lead_source','p.title','la.status','r.remind_on','DATEDIFF(CURDATE( ),l.created_on) as elapsed_day');
            $where = array('la.is_deleted' => 0,'la.is_updated' => 1);
            if($till == 'mtd'){
               // $where['MONTH(l.created_on)'] = date('m'); //Month till date filter
                $where['MONTH(l.created_on)'] = date('m'); //Month till date filter
                $where['YEAR(l.created_on)'] = date('Y');
            }
            if($till == 'ytd'){
                //$where['YEAR(l.created_on)'] = date('Y'); //Year till date filter
                $yr_start_date=date('Y').'-04-01 00:00:00';
                $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
                $where["l.created_on >='".$yr_start_date."' AND l.created_on <='".$yr_end_date."'"] = NULL; //Year till date filter

            }
            if(!empty($param)){
                $arrData['param'] = $param;
                if(($param != 'all') && ($param != null) && $this->uri->segment(2) !='export_excel_listing'){
                    $arrData['param'] = decode_id($param);
                }
                if($login_user['designation_name'] == 'EM'){
                    $where['l.created_by']  =   $login_user['hrms_id']; //Employee wise filter
                }
                if($login_user['designation_name'] == 'BM'){
                    $where['l.created_by'] = $arrData['param']; //Employee wise filter for branch manager
                }
                if($login_user['designation_name'] == 'ZM'){
                    $where['l.branch_id'] = $arrData['param']; //Branch wise filter for zone manager
                }
                if($login_user['designation_name'] == 'GM'){
                    $where['l.zone_id'] = $arrData['param']; //Zone wise filter for zone manager
                }
            }
            if(!empty($arrData['status'])){
                $where['la.status'] = $arrData['status'];
            }
            if(!empty($arrData['lead_source'])){
                $where['l.lead_source'] = $arrData['lead_source'];
            }
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
            $order_by = "la.created_on DESC";
        }
        if($type == 'assigned'){
            $order_by='';
            $select = array('l.id','l.customer_name','l.contact_no','l.lead_identification','la.created_on','l.lead_source','p.title','pc.title as prod_cat','la.status'/*,'p1.title as interested_product_title'*/,'r.remind_on','DATEDIFF(CURDATE( ),la.created_on) as elapsed_day');
            $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'DATEDIFF( CURDATE( ) , la.created_on) <=' => Elapsed_day);
//            if($till == 'mtd'){
//                $where['MONTH(la.created_on)'] = date('m'); //Month till date filter
//                $where['YEAR(la.created_on)'] = date('Y');
//                if(empty($arrData['status'])) {
//                    //$where["la.status NOT IN('Closed','Converted')"] = NULL;
//                }
//            }
//            if($till == 'ytd'){
//                $yr_start_date=date('Y').'-04-01 00:00:00';
//                $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
//                $where["la.created_on >='".$yr_start_date."' AND la.created_on <='".$yr_end_date."'"] = NULL; //Year till date filter
//                if(empty($arrData['status'])) {
//                    //$where["la.status NOT IN('Closed','Converted')"] = NULL;
//                }
//            }
            if(!empty($arrData['param'])){
                if($login_user['designation_name'] == 'EM'){
                    $where['la.employee_id']  =   $login_user['hrms_id']; //Employee wise filter
                }
                if($login_user['designation_name'] == 'BM'){
                    $where['la.employee_id'] = $arrData['param']; //Employee wise filter for branch manager
                }
                if($login_user['designation_name'] == 'ZM'){
                    $where['la.branch_id'] = $arrData['param']; //Branch wise filter for zone manager
                }
                if($login_user['designation_name'] == 'GM'){
                    $where['la.zone_id'] = $arrData['param']; //Zone wise filter for zone manager
                }
            }else{
                //All Assigned List (According Login User).
                if($login_user['designation_name'] == 'EM'){
                    $where['la.employee_id'] = $login_user['hrms_id'];
                    $order_by = "la.created_on DESC , la.id DESC";
                }
                if($login_user['designation_name'] == 'BM'){
                    $where['la.branch_id'] = $login_user['branch_id'];
                    $order_by = "CASE WHEN la.status = 'AO' THEN 1 WHEN pc.title = 'Fee Income' && la.status = 'DC' THEN 2 WHEN la.status = 'NI' THEN 3 ELSE 4 END , elapsed_day ASC";
                    //$order_by = "elapsed_day ASC";
                }
            }
            if(!empty($arrData['status'])){
                $where['la.status'] = $arrData['status'];
            }
            if(!empty($arrData['lead_source'])){
                $where['l.lead_source'] = $arrData['lead_source'];
            }
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');

        }

        $join[] = array('table' => Tbl_Reminder.' as r','on_condition' => 'la.lead_id = r.lead_id AND r.is_cancelled = "No"','type' => 'left');
        $arrData['leads'] = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by);
        $arrData['lead_sources'] = $this->Lead->get_enum(Tbl_Leads,'lead_source');
        return $arrData;
    }
    private function assign_to($employee_id,$lead_ids)
    {
       $explode_employee = explode('-',$employee_id);
        if (!empty($lead_ids)) {
            $login_user = get_session();
            $insertData = array();
            $assign_data = array(
                'employee_id' => $explode_employee[0],
                'employee_name' => $explode_employee[1],
                'branch_id' => $login_user['branch_id'],
                'district_id' => $login_user['district_id'],
                'state_id' => $login_user['state_id'],
                'zone_id' => $login_user['zone_id'],
                'status' => 'NC',
                'created_on' => date('Y-m-d H:i:s'),
                'created_by' => $login_user['hrms_id'],
                'modified_by' => $login_user['hrms_id'],
                'modified_on' => date('Y-m-d H:i:s'),
                'created_by_name' => $login_user['full_name'],
                'modified_by_name' => $login_user['full_name']
            );
            if (is_array($lead_ids)) {
                $leads = $lead_ids;
            } else {
                $leads[] = $lead_ids;
            }
            foreach ($leads as $key => $value) {
                $dataArray = explode("-",$value);
                $assign_data['lead_id'] = $dataArray[0];
                $insertData = $assign_data;
                $response = $this->Lead->insert_lead_data($insertData,Tbl_LeadAssign);
                if($response['status']=='success'){
                    //Add Notification
                    $title="New Lead Assigned";
                    $description="Lead for ".ucwords(strtolower($dataArray[1]))." assigned to you for ".ucwords(strtolower($dataArray[2]));
                    $notification_to = $explode_employee[0];
                    $priority="Normal";
                    notification_log($title,$description,$priority,$notification_to);
                    //push notification
                    $emp_id = $explode_employee[0];
                    sendPushNotification($emp_id,$description,$title);
                }
            }
        }
    }

    /**
     * export_excel_listing
     * Excel export of listing
     * @author Gourav Thatoi
     * @access public
     * @paramas none
     * @return  void
     */
    public function export_excel_listing($type,$till,$status = null,$lead_source = null,$param=null)
    {
        $params = '';
        if($type == 'assigned'){
            $header_value = array('Sr.No','Customer Name','Contact No','Product Name','Elapsed Days',
                'Status','Followup Date','Lead Identified As','Lead Source');
            if(!empty($param)){
                $arrData['param'] = decode_id($param);
            }
        }
        if($type == 'generated'){
            if($lead_source!= null){
                $params = decode_id($lead_source);
            }
            if(($status != 'all') && ($status != null)){
                $arrData['status'] = $status;
            }
            $arrData['type'] = $type;
            $arrData['till'] = $till;
            $header_value = array('Sr.No','Customer Name','Contact No','Product Name','Elapsed Days',
                'Lead Identified As','Lead Source');
            $login_user = get_session();
            $data = $this->view($login_user,$arrData,$params);
            export_excel($header_value,$data,$type);
        }

        if($type == 'unassigned'){
            $header_value = array('Sr.No','Customer Name','Contact No','Product Name','Elapsed Days','Lead Source');
            $lead_source = decode_id($till);
            $data = $this->Lead->unassigned_leads($lead_source,'');
            export_excel($header_value,$data,$type,$lead_source);
        }

        $arrData['type'] = $type;
        $arrData['till'] = $till;

        if($lead_source != 'all'){
            $arrData['lead_source'] = $lead_source;
        }

        if(($status != 'all') && ($status != null)){
            $arrData['status'] = $status;
        }
        //Get session data
        $login_user = get_session();
        $data = $this->view($login_user,$arrData,$params);
        export_excel($header_value,$data,$type);
    }

    /*
     * district_list
     * Fetches districts according to selected state.
     * @author Gourav Thatoi
     * @access public
     * @param none
     * @return json
     */
    public function district_list()
    {
        if ($this->input->post()) {
            $state_id = $this->input->post("state_code");
            $select_label = $this->input->post("select_label");
            $whereArray = array('state_code'=> $state_id,'code !=' =>'','name !='=>'');
            $table=Tbl_district;
            $order_by = 'name ASC';
            $districts = allMasters($table,$whereArray,'',$order_by);
            $district_extra = 'id="district_id"';$branch_extra = 'id="branch_id"';
            if (!empty($districts)) {
                $options[''] = $select_label;
                foreach ($districts as $key => $value) {
                    $options[$value['code']] = ucwords($value['name']);
                }
                $html = '<label>City:<span style="color:red;">*</span></label>';
                $html .= form_dropdown('district_id', $options, '', $district_extra);
                $options_branch[''] = 'Select Branch';
                $html1 = '<label>Branch:<span style="color:red;">*</span></label>';
                $html1 .= form_dropdown('branch_id', $options_branch, '', $branch_extra);
            } else {
                $options[''] = $select_label;
                $html = '<label>City:<span style="color:red;">*</span></label>';
                $html .= form_dropdown('district_id', $options, '', $district_extra);
                $options_branch[''] = 'Select Branch';
                $html1 = '<label>Branch:<span style="color:red;">*</span></label>';
                $html1 .= form_dropdown('branch_id', $options_branch, '', $branch_extra);
            }
            $data['district'] = $html;
            $data['branch'] = $html1;
            echo json_encode($data);
        }
    }
    /*
     * branch_list
     * Fetches districts according to selected state.
     * @author Gourav Thatoi
     * @access public
     * @param none
     * @return json
     */
    public function branch_list()
    {
        if ($this->input->post()) {
            $district_code = $this->input->post("district_code");
            $select_label = $this->input->post("select_label");
            $whereArray = array('district_code'=> $district_code,'code !=' =>'','name !='=>'');
            $order_by = 'name ASC';
            $table=Tbl_branch;
            $branches = allMasters($table,$whereArray,'',$order_by);
            $branch_extra = 'id="branch_id"';
            if (!empty($branches)) {
                $options[''] = $select_label;
                foreach ($branches as $key => $value) {
                    $options[$value['code']] = ucwords($value['name']);
                }
                $html = '<label>Branch:<span style="color:red;">*</span></label>';
                $html .= form_dropdown('branch_id', $options, '', $branch_extra);
            } else {
                $options[''] = $select_label;
                $html = '<label>Branch:<span style="color:red;">*</span></label>';
                $html .= form_dropdown('branch_id', $options, '', $branch_extra);
            }
            echo json_encode($html);
        }
    }
    public function is_own_branch(){
        if ($this->input->post()) {
            $district_code = $this->input->post("district_id");
            $state_code = $this->input->post("state_id");
            $branch_code = $this->input->post("branch_id");
            $action='list';$table=Tbl_branch;$select=array('TRIM(code) as code','TRIM(name) as name');
            $orderby = 'name ASC';
            $where=array('name !='=>'','code !='=>'');
            $branches = $this->Lead->get_leads($action,$table,$select,$where,'','',$orderby);
            $table = Tbl_district;
            $districts = $this->Lead->get_leads($action,$table,$select,'','','','');
            $table = Tbl_state;
            $states = $this->Lead->get_leads($action,$table,$select,'','','','');
            $branch_extra = 'id="branch_id"';
            $district_extra = 'id="district_id"';
            $state_extra = 'id="state_id"';
            if (!empty($states)) {
                $options[''] = 'Select State';
                foreach ($states as $key => $value) {
                    $options[$value['code']] = ucwords($value['name']);
                }
                $html = '<label>State:<span style="color:red;">*</span></label>';
                $html .= form_dropdown('state_id', $options, $state_code, $state_extra);
            } else {
                $options[''] = 'Select State';
                $html = '<label>State:<span style="color:red;">*</span></label>';
                $html .= form_dropdown('state_id', $options, '', $branch_extra);
            }
            if (!empty($branches)) {
                $options[''] = 'Select Branch';
                foreach ($branches as $key => $value) {
                    $options[$value['code']] = ucwords($value['name']);
                }
                $html1 = '<label>Branch:<span style="color:red;">*</span></label>';
                $html1 .= form_dropdown('branch_id', $options, $branch_code, $branch_extra);
            } else {
                $options[''] = 'Select Branch';
                $html1 = '<label>Branch:<span style="color:red;">*</span></label>';
                $html1 .= form_dropdown('branch_id', $options, '', $branch_extra);
            }
            if (!empty($districts)) {
                $dist_options[''] = 'Select City';
                foreach ($districts as $key => $value) {
                    $dist_options[$value['code']] = ucwords($value['name']);
                }
                $html2 = '<label>City:<span style="color:red;">*</span></label>';
                $html2 .= form_dropdown('district_id', $dist_options, $district_code, $district_extra);
            } else {
                $dist_options[''] = 'Select City';
                $html2 = '<label>City:<span style="color:red;">*</span></label>';
                $html2 .= form_dropdown('district_id', $dist_options, '', $district_extra);
            }
            $data['branch'] = $html1;
            $data['state'] = $html;
            $data['district'] = $html2;
            echo json_encode($data);
        }
    }

    private function points_distrubution($lead_id){

        $action = 'list';
        
        //Get Amount Details
        $table = Tbl_cbs.' as a';
        $select = array('a.*');
        $where  = array('a.lead_id' => $lead_id);
        $join = array();
        $amount_data = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());
        if(!empty($amount_data)){
            $amount = $amount_data[0]['amount'];
            $table = Tbl_Leads.' as l';
            $select = array('l.id','l.product_id','l.created_by','la.employee_id','mp.points','pd.generator_contrubution','pd.convertor_contrubution');
            $where  = array('l.id' => $lead_id,'la.is_deleted' => 0,'la.is_updated' => 1,'la.status' => 'Converted','mp.from_range <=' => $amount,'mp.to_range >=' => $amount,'pd.active' => 1);
            $join = array();
            $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
            $join[] = array('table' => Tbl_Products.' as p','on_condition' => 'l.product_id = p.id AND l.product_category_id = p.category_id','type' => '');
            $join[] = array('table' => Tbl_Manage_Points.' as mp','on_condition' => 'mp.product_id = p.id','type' => '');
            $join[] = array('table' => Tbl_Points_Distributor.' as pd','on_condition' => 'pd.product_id = p.id','type' => '');
            $leadData = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());
            if(!empty($leadData)){
                $data = array('lead_id' => $leadData[0]['id'],'product_id' => $leadData[0]['product_id']);
                //Generator Contribution
                $generator_data = array('employee_id' => $leadData[0]['created_by'],'points' => ($leadData[0]['generator_contrubution'] * $leadData[0]['points'] * 0.01),'role_as' => 'Generator');
                $generator_data = array_merge($generator_data,$data);
                //Convertor Contribution
                $convertor_data = array('employee_id' => $leadData[0]['employee_id'],'points' => ($leadData[0]['convertor_contrubution'] * $leadData[0]['points'] * 0.01),'role_as' => 'Convertor');
                $convertor_data = array_merge($convertor_data,$data);
                
                $this->db->insert(Tbl_Points,$generator_data);
                $this->db->insert(Tbl_Points,$convertor_data);
            }
        }
    }

    public function is_other_branch(){
        $state_extra = 'id="state_id"';
        $branch_extra = 'id="branch_id"';
        $district_extra = 'id="district_id"';
            $table = Tbl_state;
            $where=array('name !='=>'','code !='=>'');
            $order_by = 'name ASC';
            $states = allMasters($table,$where,'',$order_by);
            if (!empty($states)) {
                $options[''] = 'Select State';
                foreach ($states as $key => $value) {
                    $options[$value['code']] = ucwords($value['name']);
                }
                $html = '<label>State:<span style="color:red;">*</span></label>';
                $html .= form_dropdown('state_id', $options,'', $state_extra);
            }else {
                $options[''] = 'Select State';
                $html = '<label>State:<span style="color:red;">*</span></label>';
                $html .= form_dropdown('state_id', $options, '', $state_extra);
            }
            $branch_options[''] = 'Select Branch';
            $html1 = '<label>Branch:<span style="color:red;">*</span></label>';
            $html1 .= form_dropdown('branch_id', $branch_options, '', $branch_extra);
            $dist_options[''] = 'Select City';
            $html2 = '<label>City:<span style="color:red;">*</span></label>';
            $html2 .= form_dropdown('district_id', $dist_options, '', $district_extra);
            $data['branch'] = $html1;
            $data['state'] = $html;
            $data['district'] = $html2;
            echo json_encode($data);
    }

    public function verify_account(){
        if($this->input->post('acc_no') != ''){
            $acc_no = $this->input->post('acc_no');
            $response = verify_account($acc_no);
            echo $response;
        }
    }
    public function upload_employee(){
        $admin = ucwords(strtolower($this->session->userdata('admin_type')));
        if ($admin != 'Super Admin'){
            redirect('dashboard');
        }
        /*Create Breadcumb*/
        $this->make_bread->add('Employee Upload', '', 0);
        $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/
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
                    $keys = ['hrms_id', 'name', 'designation_id','designation', 'email_id','contact_no', 'branch_id', 'branch_name',
                        'zone_id', 'zone_name','district_id', 'state_id', 'supervisor_id'];

                    $excelData = fetch_range_excel_data($file['full_path'], 'A2:K', $keys);
                    $this->Lead->insert_uploaded_data(Tbl_emp_dump,$excelData);
                    $msg = notify('File Uploaded Successfully.','success');
                    $this->session->set_flashdata('success', $msg);
                    redirect(base_url('leads/upload_employee'), 'refresh');

                }
            }
            $msg = notify("Please upload a file",'danger');
            $this->session->set_flashdata('message', $msg);
            redirect('leads/upload_employee');
        }

        $middle = "employee_upload";
        load_view($middle,$arrData);
    }

    public function lead_life_cycle($id=''){
        if($id){
            $lead_id = decode_id($id);
            $final_result = array();
            $this->make_bread->add('Lead Life Cycle', '', 0);
            $arrData['breadcrumb'] = $this->make_bread->output();
            $action = 'list';
            $select = array('l.id','l.customer_name','l.lead_source','l.contact_no','l.created_by_branch_id','l.created_on AS generated_on','l.reroute_from_branch_id','l.modified_on','l.branch_id','l.created_by_name as generated');
            $table = Tbl_Leads.' as l';
            $where = array('id'=>$lead_id);
            $result = $this->Lead->get_leads($action,$table,$select,$where,$join=array(),$group_by=array(),$order_by=array());
            //pe($result);die;
            if(!empty($result)){
                if($result[0]['reroute_from_branch_id'] !=''){
                    $final_result[] = array('id'=>$result[0]['id'],'generated'=>$result[0]['generated'],'generated_on'=>$result[0]['generated_on'],
                        'date'=>$result[0]['generated_on'],'reroute_from_branch_id'=>$result[0]['reroute_from_branch_id'],
                        'customer_name'=>$result[0]['customer_name'],'branch_id'=>$result[0]['branch_id'],
                        'contact_no'=>$result[0]['contact_no'],'lead_source'=>$result[0]['lead_source'],'created_by_branch_id'=>$result[0]['created_by_branch_id']);
                    $final_result[] = array('id'=>$result[0]['id'],'reroute_from_branch_id'=>$result[0]['reroute_from_branch_id'],
                        'reroute_to_branch_id'=>$result[0]['branch_id'],'modified_on'=>$result[0]['modified_on'],
                        'date'=>$result[0]['modified_on'],'created_by_branch_id'=>$result[0]['created_by_branch_id']);
                }else{
                    $final_result = $result;
                    $final_result[0]['date'] = $result[0]['generated_on'];

                }
            }
            $select = array('la.employee_id','la.employee_name','la.created_by_name','la.modified_on AS date','la.status','la.modified_by','la.modified_by_name');
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
            pe($final_result);
            $arrData['lead_data'] = $final_result;
            $middle = 'Leads/life_cycle';
            return load_view($middle,$arrData);

        }
    }


    public function generated(){
        /*Create Breadcumb*/
        $this->make_bread->add('Lead Generated', 'leads/lead_generated', 0);
        $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/
        $login_user = get_session();
        $user = $login_user['hrms_id'];
        $action = 'list';
        $select = array('lead.id','lead.lead_name','products.title','lead.id');
        $table = Tbl_Leads.' AS lead';
        $join[] = array('table' =>Tbl_Products.' AS products','on_condition'=>'products.id = lead.product_id','type'=>'left');
        $where = array('lead.created_by'=>$user);
        $order_by = 'lead.created_on DESC';
        $arrData['generated_leads'] = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by=array(),$order_by);
        $middle = "Leads/view/lead_generated";
        load_view($middle,$arrData);
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