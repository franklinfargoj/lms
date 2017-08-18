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
                $middle = 'add_lead';
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
            $lead_data['is_own_branch'] = $this->input->post('is_own_branch');
            $lead_data['account_id'] = $this->input->post('account_no');
            $lead_data['remark'] = $this->input->post('remark');
            $this->Lead->insert($lead_data);
            $this->session->set_flashdata('success_message', "Lead Added Successfully");
            redirect(base_url('Leads/add'), 'refresh');
        } else {
            $middle = 'add_lead';
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
            $whereArray = array('id' => $category_id);
            $products = $this->Lead->get_all_products($whereArray);
            $product_extra = 'class="form-control" id="product"';
            if (!empty($products)) {
                $options[''] = 'Select Product';
                foreach ($products as $key => $value) {
                    $options[$value['id']] = $value['title'];
                }
                $html = '<label>Product</label>';
                $html .= form_dropdown('product', $options, '', $product_extra);
            } else {
                $options[''] = 'Select Product';
                $html = '<label>Product</label>';
                $html .= form_dropdown('product', $options, '', $product_extra);
            }


            echo json_encode($html);
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
                    $msg = notify($file, $type = "danger");
                    $this->session->set_flashdata('message', $msg);
                    redirect('Leads/upload');
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
        $middle = "upload";
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

            $whereArray = array('title'=>$value['product_category_id']);
            $prod_category_id = $this->Lead->fetch_product_category_id($whereArray);
            if($prod_category_id == false){
                $error[$key] = 'Category does not exist.';

            }else{
                if($value['branch_id'] == ''){
                    $error[$key] = 'Branch id missing.';

                }else{
                    $all_product = $this->Lead->all_products_under_category($prod_category_id);
                    if(in_array($value['product_id'],$all_product)){

                        $whereArray = array('title'=>$value['product_id']);
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
        $middle = "unassigned_list";
        load_view($middle,$arrData);
    }
}