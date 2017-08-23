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
            $this->form_validation->set_rules('customer_type', 'Customer', 'required');
          //$this->form_validation->set_rules('lead_name', 'Lead Name', 'required');
            $this->form_validation->set_rules('customer_name', 'Customer Name', 'required');
            $this->form_validation->set_rules('phone_no', 'Phone No.', 'required|max_length[10]|min_length[10]|numeric');
            $this->form_validation->set_rules('product_category', 'Product Category', 'required');
            $this->form_validation->set_rules('product', 'Product', 'required');
            $this->form_validation->set_rules('remark', 'Remark', 'required');
            $this->form_validation->set_rules('is_own_branch', 'Branch', 'required');
            $this->form_validation->set_rules('lead_identification', 'Lead Identification', 'required');
            if ($this->input->post('is_own_branch') == '0') {
                $this->form_validation->set_rules('state_id', 'State', 'required');
                $this->form_validation->set_rules('branch_id', 'Branch', 'required');
                $this->form_validation->set_rules('district', 'District', 'required');

                $lead_data['state_id'] = $this->input->post('state_id');
                $lead_data['branch_id'] = $this->input->post('branch_id');
                $lead_data['district_id'] = $this->input->post('district');
            }
            if ($this->form_validation->run() === FALSE) {
                $middle = 'Leads/add_lead';
                $arrData['products'] = '';
                $arrData['category_selected'] = '';
                if ($this->input->post('product_category') != '') {
                    $arrData['category_selected'] = $this->input->post('product_category');
                    $whereArray = array("category_id" => $arrData['category_selected']);
                    $arrData['products'] = $this->Lead->get_all_products($whereArray);
                }
                $arrData['product_selected'] = '';
                if ($this->input->post('product') != '') {
                    $arrData['product_selected'] = $this->input->post('product');
                }
                $arrData['category'] = $this->Lead->get_all_category();
                return load_view($middle, $arrData);
            }

            $lead_data['is_existing_customer'] = $this->input->post('customer_type');
            $lead_data['customer_name'] = $this->input->post('customer_name');
            $lead_data['contact_no'] = $this->input->post('phone_no');
            $lead_data['product_category_id'] = $this->input->post('product_category');
            $lead_data['product_id'] = $this->input->post('product');
            $lead_data['lead_name'] = $this->input->post('customer_name');
            $lead_data['lead_identification'] = $this->input->post('lead_identification');
            $lead_data['pan_no'] = $this->input->post('lead_name');
            $lead_data['aadhar_no'] = $this->input->post('aadhar_no');
            $lead_data['is_own_branch'] = $this->input->post('is_own_branch');
            $lead_data['account_id'] = $this->input->post('account_no');
            $lead_data['remark'] = $this->input->post('remark');
            $this->Lead->insert($lead_data);
            $this->session->set_flashdata('success_message', "Lead Added Successfully");
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
            $product_extra = 'class="form-control" id="product"';
            if (!empty($products)) {
                $options[''] = 'Select Product';
                foreach ($products as $key => $value) {
                    $options[$value['id']] = $value['title'];
                }
                $html = '<label>Select Product</label>';
                $html .= form_dropdown('product_id', $options, '', $product_extra);
            } else {
                $options[''] = 'Select';
                $html = '<label>Select Product</label>';
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
                redirect('Leads/upload');
            }
            if (isset($_FILES['filename']) && !empty($_FILES['filename']['tmp_name'])) {
                make_upload_directory('./uploads');
                $file = upload_excel('./uploads', 'filename');
                if (!is_array($file)) {
                    $msg = notify(strip_tags($file), $type = "danger");
                    $this->session->set_flashdata('message', $msg);
                } else {
                    set_time_limit(0);
                    ini_set('memory_limit', '-1');
                    $keys = ['customer_name', 'contact_no', 'is_existing_customer', 'account_id', 'is_own_branch', 'branch_id', 'zone_id', 'state_id', 'district_id', 'product_category_id', 'product_id', 'remark', 'lead_identification', 'created_by', 'created_by_name', 'created_by_branch_id', 'created_by_zone_id', 'created_by_state_id', 'created_by_district_id'];

                    $excelData = fetch_range_excel_data($file['full_path'], 'A2:S', $keys);

                    $validation = $this->validate_leads_data($excelData,$lead_source);


                    if (!empty($validation['insert_array'])) {
                        $insert_count = $this->Lead->insert_uploaded_data('db_leads', $validation['insert_array']);

                    }
                    if ($validation['type'] == 'error') {
                        make_upload_directory('./uploads/errorlog');
                        $target_path = './uploads/errorlog/';
                        $target_file = $file['raw_name'] . '_error_log_' . date('Y-m-d-H-i-s') . $file['file_ext'];
                        create_excel_error_file($validation['data'], $target_path.$target_file);
                        unlink($file['full_path']);
                        $data = array(
                            'file_name' => $target_file,
                            'status' => 'failed'
                        );
                        $this->Lead->uploaded_log('uploaded_leads_log', $data);
                        $msg = notify($validation['total_inserted'] . ' rows inserted sucessfully. Error occured in ' . $validation['total_error_rows'] . ' rows.Please refer latest log file.', 'danger');
                        $this->session->set_flashdata('message', $msg);
                        redirect(base_url('Leads/upload'), 'refresh');
                    }
                    $data = array(
                        'file_name' => $file['file_name'],
                        'status' => 'success'
                    );
                    $this->Lead->uploaded_log('uploaded_leads_log', $data);
                    $msg = notify('File Uploaded Successfully.' . $validation['total_inserted'] . ' rows inserted. ', 'success');
                    $this->session->set_flashdata('message', $msg);
                    redirect(base_url('Leads/upload'), 'refresh');

                }
            }
            $msg = notify("Please upload a file",'danger');
            $this->session->set_flashdata('message', $msg);
            redirect('Leads/upload');
        }
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


            $error[$key] = 'Product name and product category name does not match.';

            $whereArray = array('title'=>$value['product_category_id']);
            $prod_category_id = $this->Lead->fetch_product_category_id($whereArray);
            if($prod_category_id == false){
                $error[$key] = 'Category does not exist.';

            }else{
                $all_product = $this->Lead->all_products_under_category($prod_category_id);
                if(in_array($value['product_id'],$all_product)){

                    $whereArray = array('title'=>$value['product_id']);
                    $prod_id = $this->Lead->fetch_product_id($whereArray);
                    $value['product_category_id']=$prod_category_id;
                    $value['product_id']=$prod_id['product_id'];
                    $value['lead_name']=$value['customer_name'];
                    $value['lead_source']=$lead_source;
                    $error = array();
                    $insert_array[] = $value;
                    $total_inserted++;
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
        $login_user = get_session();

        if(isset($login_user['designation_name']) && !empty($login_user['designation_name'])){
            switch ($login_user['designation_name']){
                case 'EM':
                    //Parameters buiding for sending to list function.
                    $action = 'list';
                    $table = Tbl_Leads.' as l';
                    $join = array();
                    $join[] = array('table' => Tbl_Products.' as p','on_condition' => 'l.product_id = p.id','type' => '');
                    if($type == 'generated'){
                        $select = array('l.id','l.customer_name','l.lead_identification','l.created_on','l.lead_source','p.title','la.status');
                        if($till == 'mtd'){
                            $where = array('l.created_by' => $login_user['hrms_id'],'MONTH(l.created_on)' => date('m'));
                        }
                        if($till == 'ytd'){
                            $where  = array('l.created_by' => $login_user['hrms_id'],'YEAR(l.created_on)' => date('Y'));
                        }
                        $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => 'left');
                    }
                    if($type == 'converted'){
                        $select = array('l.id','l.customer_name','l.lead_identification','l.created_on','l.lead_source','p.title','la.status');
                        if($till == 'mtd'){
                            $where = array('la.employee_id' => $login_user['hrms_id'],'la.status' => 'converted','la.is_deleted' => 0,'MONTH(la.created_on)' => date('m'));
                        }
                        if($till == 'ytd'){
                            $where  = array('la.employee_id' => $login_user['hrms_id'],'la.status' => 'converted','la.is_deleted' => 0,'YEAR(la.created_on)' => date('Y'));
                        }
                        $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
                    }
                    if($type == 'assigned'){
                        $select = array('l.id','l.customer_name','l.lead_identification','l.created_on','l.lead_source','p.title','la.status','p1.title as interested_product_title');
                        if($till == 'ytd'){
                            $where  = array('la.employee_id' => $login_user['hrms_id'],'la.is_deleted' => 0,'YEAR(la.created_on)' => date('Y'));
                        }
                        $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
                        $join[] = array('table' => Tbl_Products.' as p1','on_condition' => 'l.interested_product_id = p1.id','type' => 'left');
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

        $login_user = get_session();

        if(isset($login_user['designation_name']) && !empty($login_user['designation_name'])){
            switch ($login_user['designation_name']){
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
                        $select = array(Tbl_Leads.'.id',Tbl_Leads.'.remark',Tbl_Leads.'.customer_name',Tbl_Leads.'.lead_identification',Tbl_Leads.'.lead_source',Tbl_Leads.'.contact_no',Tbl_Leads.'.product_id',Tbl_Products.'.title AS product_title'/*,Tbl_Leads.'.interested_product_id',Tbl_Products.'.title AS interested_product_title'*/,Tbl_Category.'.title AS category_title',Tbl_Leads.'.product_category_id',Tbl_LeadAssign.'.status',Tbl_LeadAssign.'.employee_id',Tbl_Reminder.'.remind_on',Tbl_Reminder.'.reminder_text');

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
                $this->form_validation->set_rules('product_category_id','Product Category', 'required');
                $this->form_validation->set_rules('product_id','Product', 'required');
                if ($this->form_validation->run() == FALSE)
                {    
                    /*$arrData['has_error'] = 'has-error';
                    return load_view("Products/Product/add",$arrData);*/
                    redirect('leads/leads_list/assigned/ytd');
                }
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
        $login_user = get_session();

        //Building input parameters for function to get_leads
        $action = 'list';
        $table = Tbl_Leads;
        $select = array(Tbl_Leads.'.*');
        $where  = array(Tbl_Leads.'.id' => $lead_id);

        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join = array(),$group_by = array(),$order_by = array());
        $leads_data = $leads[0];

        $leads_data['product_category_id']  = $product_category_id;
        $leads_data['product_id']   = $product_id;
        $leads_data['created_by']   = $login_user['hrms_id'];
        $leads_data['created_by_name']  = $login_user['full_name'];
        $leads_data['created_by_branch_id']     = $login_user['branch_id'];
        $leads_data['created_by_district_id']   = $login_user['district_id'];
        $leads_data['created_by_state_id']  = $login_user['state_id'];
        $leads_data['created_by_zone_id']   = $login_user['zone_id'];
        $keys_to_remove = array('id','created_on','modified_by','modified_by_name','modified_on');
        foreach ($keys_to_remove as $key => $value) {
            unset($leads_data[$value]);
        }
        if($this->Lead->add_leads($leads_data)){
            //link interested product id with current lead.
            $data = array('interested_product_id' => $product_id);
            $this->Lead->update($where,$table,$data);
            return true;
        }else{
            return false;
        }
        
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