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
        /*Create Breadcumb*/
          $this->make_bread->add('Add Leads', '', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/
        if ($this->input->post("Submit") == "Submit") {
            $this->form_validation->set_error_delimiters('<label class = "error">', '</label>');
            $this->form_validation->set_rules('is_existing_customer', 'Customer', 'required');
            $this->form_validation->set_rules('customer_name', 'Customer Name', 'required|callback_alphaNumeric');
            $this->form_validation->set_rules('contact_no', 'Phone No.', 'required|max_length[10]|min_length[10]|numeric');
            $this->form_validation->set_rules('product_category_id', 'Product Category', 'required');
            $this->form_validation->set_rules('product_id', 'Product','required');
            $this->form_validation->set_rules('department_id', 'Department Id', 'required');
            $this->form_validation->set_rules('department_name', 'Department Name', 'required');
            $this->form_validation->set_rules('remark', 'Remark', 'required');
            $this->form_validation->set_rules('is_own_branch', 'Branch', 'required');
            $this->form_validation->set_rules('lead_identification', 'Lead Identification', 'required');

            $input = get_session();

            $lead_data['state_id'] = $lead_data['created_by_state_id'] = $input['state_id'];
            $lead_data['branch_id'] = $lead_data['created_by_branch_id'] = $input['branch_id'];
            $lead_data['district_id'] = $lead_data['created_by_district_id'] = $input['district_id'];

            if ($this->input->post('is_own_branch') == '0') {
                $this->form_validation->set_rules('state_id', 'State', 'required');
                $this->form_validation->set_rules('branch_id', 'Branch', 'required');
                $this->form_validation->set_rules('district_id', 'District', 'required');

                $lead_data['state_id'] = $this->input->post('state_id');
                $lead_data['branch_id'] = $this->input->post('branch_id');
                $lead_data['district_id'] = $this->input->post('district_id');

            }

            if ($this->form_validation->run() === FALSE) {

                $middle = 'Leads/add_lead';
                $arrData['products'] = '';
                $arrData['category_selected'] = '';
                if ($this->input->post('product_category_id') != '') {
                    $arrData['category_selected'] = $this->input->post('product_category_id');
                    $whereArray = array("category_id" => $arrData['category_selected']);
                    $arrData['products'] = $this->Lead->get_all_products($whereArray);
                }
                $arrData['category'] = $this->Lead->get_all_category();
                return load_view($middle, $arrData);
            }

            
            $keys = array('is_existing_customer','lead_ticket_range','customer_name','contact_no','product_category_id','product_id','department_id',
                'department_name','lead_identification','is_own_branch','remark','lead_ticket_range');
            foreach ($keys as $k => $value){
                $lead_data[$value] = $this->input->post($value);
                
            }
            $lead_data['lead_name'] = $this->input->post('customer_name');
            $lead_id = $this->Lead->add_leads($lead_data);
            

            $assign_to = $this->Lead->get_product_assign_to($lead_data['product_id']);
            if($assign_to == 'self'){
                $lead_assign['lead_id'] = $lead_id;
                $lead_assign['employee_id']=$input['hrms_id'];
                $lead_assign['employee_name']=$input['full_name'];
                $lead_assign['branch_id']=$input['branch_id'];
                $lead_assign['district_id']=$input['district_id'];
                $lead_assign['state_id']=$input['state_id'];
                $lead_assign['zone_id']=$input['zone_id'];
                $lead_assign['created_by']=$input['hrms_id'];
                $lead_assign['created_by_name']=$input['full_name'];
                $this->Lead->insert_assign($lead_assign);
            }
            $this->session->set_flashdata('success', "Lead Added Successfully");
            redirect(base_url('Leads/add'), 'refresh');
        } else {
            $middle = 'Leads/add_lead';
            $arrData['products'] = '';
            $arrData['category_selected'] = '';
            $arrData['product_selected'] = '';
            $arrData['category'] = $this->Lead->get_all_category();
            return load_view($middle, $arrData);
        }

    }

    ##################################
    /*Private Functions*/
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
            $whereArray = array('category_id' => $category_id,'is_deleted' => 0);
            $products = $this->Lead->get_all_products($whereArray);
            $product_extra = 'class="form-control" id="product_id"';
            if (!empty($products)) {
                $options[''] = 'Select Product';
                foreach ($products as $key => $value) {
                    $options[$value['id']] = $value['title'];
                }
                $html = '<label>Product</label>';
                $html .= form_dropdown('product_id', $options, '', $product_extra);
            } else {
                $options[''] = 'Select Product';
                $html = '<label>Product</label>';
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

    public function upload()
    {
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
                    $this->session->set_flashdata('message', $msg);
                    redirect('leads/upload');
                } else {
                    set_time_limit(0);
                    ini_set('memory_limit', '-1');
                    $keys = ['customer_name', 'contact_no', 'is_existing_customer', 'is_own_branch', 'branch_id', 'zone_id', 'state_id', 'district_id', 'product_category_id', 'product_id', 'remark', 'lead_identification'];

                    $excelData = fetch_range_excel_data($file['full_path'], 'A2:L', $keys);
                    $validation = $this->validate_leads_data($excelData,$lead_source);

                    if (!empty($validation['insert_array'])) {
                        $insert_count = $this->Lead->insert_uploaded_data('db_leads', $validation['insert_array']);

                    }
                    if ($validation['type'] == 'error') {
                        make_upload_directory('./uploads/errorlog');
                        $target_path = './uploads/errorlog/';
                        $target_file = $file['raw_name'] . '_error_log_' . date('Y-m-d-H-i-s') . $file['file_ext'];
                        create_excel_error_file($validation['data'], $target_path.$target_file,$target_file);
                        $data = array(
                            'file_name' => $target_file,
                            'status' => 'failed'
                        );
                        $this->Lead->uploaded_log('uploaded_leads_log', $data);
                        $download_url = base_url('uploads/errorlog/'.$target_file);
                        $msg = notify('<span style="color: green">'.$validation['total_inserted'] . ' rows inserted sucessfully.</span> Error occured in ' . $validation['total_error_rows'] . ' rows.Please refer log file <a href="'.$download_url.'">here</a>.', 'danger');
                        $this->session->set_flashdata('message', $msg);
                        redirect(base_url('leads/upload'), 'refresh');
                    }
                    $data = array(
                        'file_name' => $file['file_name'],
                        'status' => 'success'
                    );
//                    unlink($file['full_path']);
                    $this->Lead->uploaded_log('uploaded_leads_log', $data);
                    $msg = notify('File Uploaded Successfully.' . $validation['total_inserted'] . ' rows inserted. ', 'success');
                    $this->session->set_flashdata('message', $msg);
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
            $whereArray = array('title'=>strtolower(trim($prod_cat_title)));
            $prod_category_id = $this->Lead->fetch_product_category_id($whereArray);
            if($prod_category_id == false){
                $error[$key] = 'Category does not exist.';

            }else{
                if($value['branch_id'] == ''){
                    $error[$key] = 'Branch id missing.';

                }else{
                    $all_product = $this->Lead->all_products_under_category($prod_category_id);
                    $prod_title = preg_replace('!\s+!', ' ', $value['product_id']);
                    if(in_array(strtolower(trim($prod_title)),$all_product)){

                        $whereArray = array('title'=>strtolower(trim($prod_title)));

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
                        $prod_id = $this->Lead->fetch_product_id($whereArray);
                        $value['product_category_id']=$prod_category_id;
                        $value['product_id']=$prod_id['product_id'];
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

    /*
     * unassigned_leads
     * Loads the listing page for unassigned leads.
     * @author Gourav Thatoi
     * @access public
     * @param none
     * @return none
     */
    public function unassigned_leads(){
        /*Create Breadcumb*/
          $this->make_bread->add('Unassign Leads', '', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/

        $arrData['unassigned_leads'] = $this->Lead->unassigned_leads();
        $middle = "Leads/unassigned_list";
        load_view($middle,$arrData);
    }

    /**
     * leads_list
     * Get Leads generated by and converted as well as assigned to login user (monthwise/yearwise) 
     * @author Ashok Jadhav
     * @access public
     * @param $type,$till
     * @return array
     */
    public function leads_list($type,$till){
        //Call to helper function to fetch Page title as we are using same list view for all lead list
        $title = get_lead_title($type,$till);
        $arrData['title'] = $title;
        $arrData['type'] = $type;
        $arrData['till'] = $till;

        //Create Breadcumb
        $this->make_bread->add($title, '', 0);
        $arrData['breadcrumb'] = $this->make_bread->output();

        //Get session data
        $input = get_session();

        if(isset($input['designation_name']) && !empty($input['designation_name'])){
            switch ($input['designation_name']){
                case 'EM':
                    //Parameters buiding for sending to list function.
                    $action = 'list';
                    $table = Tbl_Leads;
                    $join = array();
                    $join[] = array('table' => Tbl_Products,'on_condition' => Tbl_Leads.'.product_id = '.Tbl_Products.'.id','type' => '');
                    if($type == 'generated'){
                        $select = array(Tbl_Leads.'.id',Tbl_Leads.'.customer_name',Tbl_Leads.'.lead_identification',Tbl_Leads.'.created_on',Tbl_Leads.'.lead_source',Tbl_Products.'.title',Tbl_LeadAssign.'.status');
                        if($till == 'mtd'){
                            $where = array(Tbl_Leads.'.created_by' => $input['hrms_id'],'MONTH('.Tbl_Leads.'.created_on)' => date('m'));
                        }
                        if($till == 'ytd'){
                            $where  = array(Tbl_Leads.'.created_by' => $input['hrms_id'],'YEAR('.Tbl_Leads.'.created_on)' => date('Y'));
                        }
                        $join[] = array('table' => Tbl_LeadAssign,'on_condition' => Tbl_LeadAssign.'.lead_id = '.Tbl_Leads.'.id','type' => 'left');
                    }
                    if($type == 'converted'){
                        $select = array(Tbl_Leads.'.id',Tbl_Leads.'.customer_name',Tbl_Leads.'.lead_identification',Tbl_Leads.'.created_on',Tbl_Leads.'.lead_source',Tbl_Products.'.title',Tbl_LeadAssign.'.status');
                        if($till == 'mtd'){
                            $where = array(Tbl_LeadAssign.'.employee_id' => $input['hrms_id'],Tbl_LeadAssign.'.status' => 'converted',Tbl_LeadAssign.'.is_deleted' => 0,'MONTH('.Tbl_LeadAssign.'.created_on)' => date('m'));
                        }
                        if($till == 'ytd'){
                            $where  = array(Tbl_LeadAssign.'.employee_id' => $input['hrms_id'],Tbl_LeadAssign.'.status' => 'converted',Tbl_LeadAssign.'.is_deleted' => 0,'YEAR('.Tbl_LeadAssign.'.created_on)' => date('Y'));
                        }
                        $join[] = array('table' => Tbl_LeadAssign,'on_condition' => Tbl_LeadAssign.'.lead_id = '.Tbl_Leads.'.id','type' => '');
                    }
                    if($type == 'assigned'){
                        $select = array(Tbl_Leads.'.id',Tbl_Leads.'.customer_name',Tbl_Leads.'.lead_identification',Tbl_Leads.'.created_on',Tbl_Leads.'.lead_source',Tbl_Products.'.title',Tbl_LeadAssign.'.status');
                        if($till == 'ytd'){
                            $where  = array(Tbl_LeadAssign.'.employee_id' => $input['hrms_id'],Tbl_LeadAssign.'.is_deleted' => 0,'YEAR('.Tbl_LeadAssign.'.created_on)' => date('Y'));
                        }
                        $join[] = array('table' => Tbl_LeadAssign,'on_condition' => Tbl_LeadAssign.'.lead_id = '.Tbl_Leads.'.id','type' => '');
                    }

                    $arrData['leads'] = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());
                    break;
            }
            
        }
        return load_view($middle = "Leads/view",$arrData);
    }

    /**
     * details
     * Get Leads details based on type of lead (Generated,Converted,Assigned)
     * @author Ashok Jadhav
     * @access public
     * @param $type,$till,$lead_id
     * @return array
     */
    public function details($type,$till,$lead_id){
        $lead_id = decode_id($lead_id);
        $title = get_lead_title($type,$till);
        $arrData['title'] = $title;
        $arrData['type'] = $type;
        $arrData['till'] = $till;

        /*Create Breadcumb*/
          $this->make_bread->add($title, 'leads/leads_list/'.$type.'/'.$till, 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/

        $input = get_session();

        if(isset($input['designation_name']) && !empty($input['designation_name'])){
            switch ($input['designation_name']){
                case 'EM':
                    //Parameters buiding for sending to list function.
                    $action = 'list';
                    $table = Tbl_Leads;
                    $where  = array(Tbl_Leads.'.id' => $lead_id);
                    $join = array();
                    $join[] = array('table' => Tbl_Products,'on_condition' => Tbl_Leads.'.product_id = '.Tbl_Products.'.id','type' => '');
                    $join[] = array('table' => Tbl_Category,'on_condition' => Tbl_Leads.'.product_category_id = '.Tbl_Category.'.id','type' => '');

                    if($type == 'generated'){
                        $select = array(Tbl_Leads.'.id',Tbl_Leads.'.customer_name',Tbl_Leads.'.lead_identification',Tbl_Leads.'.lead_source',Tbl_Leads.'.contact_no',Tbl_Leads.'.product_id',Tbl_Products.'.title AS product_title',Tbl_Category.'.title AS category_title',Tbl_Leads.'.product_category_id',Tbl_LeadAssign.'.status');
                        $join[] = array('table' => Tbl_LeadAssign,'on_condition' => Tbl_LeadAssign.'.lead_id = '.Tbl_Leads.'.id','type' => 'left');
                    }

                    if($type == 'converted'){
                        $select = array(Tbl_Leads.'.id',Tbl_Leads.'.customer_name',Tbl_Leads.'.lead_identification',Tbl_Leads.'.lead_source',Tbl_Leads.'.contact_no',Tbl_Leads.'.product_id',Tbl_Products.'.title AS product_title',Tbl_Category.'.title AS category_title',Tbl_Leads.'.product_category_id',Tbl_LeadAssign.'.status');
                        $join[] = array('table' => Tbl_LeadAssign,'on_condition' => Tbl_LeadAssign.'.lead_id = '.Tbl_Leads.'.id','type' => '');
                    }

                    if($type == 'assigned'){
                        //SELECT COLUMNS
                        $select = array(Tbl_Leads.'.id',Tbl_Leads.'.remark',Tbl_Leads.'.customer_name',Tbl_Leads.'.lead_identification',Tbl_Leads.'.lead_source',Tbl_Leads.'.contact_no',Tbl_Leads.'.product_id',Tbl_Products.'.title AS product_title',Tbl_Category.'.title AS category_title',Tbl_Leads.'.product_category_id',Tbl_LeadAssign.'.status',Tbl_LeadAssign.'.employee_id',Tbl_Reminder.'.remind_on',Tbl_Reminder.'.reminder_text');

                        //JOIN CONDITIONS
                        $join[] = array('table' => Tbl_LeadAssign,'on_condition' => Tbl_LeadAssign.'.lead_id = '.Tbl_Leads.'.id','type' => '');
                        $join[] = array('table' => Tbl_Reminder,'on_condition' => Tbl_LeadAssign.'.lead_id = '.Tbl_Reminder.'.lead_id AND '.Tbl_Reminder.'.is_cancelled = "No"','type' => 'left');


                        //Only for assign leads show lead status dropdown as he can update status of only assigned leads
                        $arrData['lead_status'] = $this->Lead->lead_status(Tbl_LeadAssign,'status');
                        $category_list = $this->Lead->get_all_category(array('is_deleted' => 0));
                        $arrData['category_list'] = $this->dropdown($category_list,true);
                    }

                    $arrData['leads'] = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by = array(),$order_by = array());
                    break;

                case 'BM':
                    //Parameters buiding for sending to list function.
                    
            }
            /*pe($arrData['leads']);
            exit;*/
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

            $lead_id = decode_id($this->input->post('lead_id'));
            $lead_status = $this->input->post('lead_status');
            
            $interested = $this->input->post('interested');
            if($interested == 1){
                $product_category_id = $this->input->post('product_category_id');
                $product_id = $this->input->post('product_id');
                //Function call for add new leads in selected product category hierarchy
                $this->add_leads_other_product($lead_id,$product_category_id,$product_id);

            }
            $where = array('lead_id' => $lead_id);
            $data = array('status' => $lead_status);

            $response = $this->Lead->update_lead_status($where,$data);
            if($response['status'] == 'error'){
                 $this->session->set_flashdata('error','Failed to update lead status');
                 redirect('dashboard');
            }else{
                if($lead_status == 'Interested/Follow up'){
                    $remindData = array(
                        'lead_id' => $lead_id,
                        'remind_on' => date('y-m-d-H-i-s',strtotime($this->input->post('remind_on'))),
                        'remind_to' => $this->input->post('remind_to'),
                        'reminder_text' => $this->input->post('reminder_text') 
                    );
                    //This will add entry into reminder scheduler for status (Interested/Follow up)
                    $response = $this->Lead->add_reminder($remindData);
                }
                $this->session->set_flashdata('success','Lead status updated successfully');
                redirect('dashboard');
            }
        }
    }

     /**
     * add_leads_other_product
     * Add leads for other product 
     * @author Ashok Jadhav
     * @access public
     * @param $lead_id,$product_category_id,$product_id
     * @return boolean
     */
    public function add_leads_other_product($lead_id,$product_category_id,$product_id){
        //Building input parameters for function to get_leads
        $action = 'list';
        $table = Tbl_Leads;
        $select = array(Tbl_Leads.'.*');
        $where  = array(Tbl_Leads.'.id' => $lead_id);

        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join = array(),$group_by = array(),$order_by = array());
        $leads_data = $leads[0];
        $leads_data['product_category_id'] = $product_category_id;
        $leads_data['product_id'] = $product_id;
        $keys_to_remove = array('id','created_on','modified_by','modified_by_name','modified_on');
        foreach ($keys_to_remove as $key => $value) {
            unset($leads_data[$value]);
        }
        return $this->Lead->add_leads($leads_data);
    }

    /*Private Functions*/

    private function dropdown($data,$select_option){
        $result = array();
        if($select_option == true){
            $result[''] = 'Select';
        }
        foreach ($data as $key => $value) {
            $result[$value['id']] =  $value['title'];  
        }
        return $result;
    }
}