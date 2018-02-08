<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rapc extends CI_Controller {

    /*
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
		parent::__construct(); // Initialization of class
          is_logged_in();     //check login
        $admin = ucwords(strtolower($this->session->userdata('admin_type')));
        if ($admin != 'Super Admin'){
            redirect('dashboard');
        }
          $this->load->model('Lead');
        $this->load->model('Master_model','master');
	}

    /*
     * index
     * Index Page for this controller.
     * @author Ashok Jadhav
	* @access public
     * @param none
     * @return void
     */
//	public function index()
//	{
//          /*Create Breadcumb*/
//          $this->make_bread->add('RAPC', '', 0);
//          $arrData['breadcrumb'] = $this->make_bread->output();
//          /*Create Breadcumb*/
//            $select=array('*');
//            $table=Tbl_processing_center;
//            $arrData['list'] = $this->master->view($select,$where=array(),$table,$join = array(),$order_by = array());
//            return load_view("Rapc/list",$arrData);
//	}

    public function index()
    {
        /*Create Breadcumb*/
        $this->make_bread->add('Other Processing Center', '', 0);
        $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/
        $select=array('DISTINCT(processing_center) as id','processing_center as title');
        $table=Tbl_processing_center;
        $typelist = $this->master->view($select,$where=array(),$table,$join = array(),$order_by = array());
        //pe($arrData['typelist']);die;
        $arrData['typelist'] = dropdown($typelist,'Select');
        return load_view("Rapc/view",$arrData);
    }


     /*
     * delete
     * Delete product (Soft Delete)
     * @author Ashok Jadhav
     * @access public
     * @param $id
     * @return void
     */
     public function delete($id){
          if(!$id){
               $this->session->set_flashdata('error','Invalid access');
               redirect('rapc');
          }
          $id = decode_id($id);
          $soft_deleted = $this->master->delete_map($id);
          if($soft_deleted > 0){
               $this->session->set_flashdata('success','Record deleted successfully.');
          }else{
               $this->session->set_flashdata('error','Failed to delete record');
          }
          redirect('rapc');
     }


    /*
     * isTaken
     * Checks title unique or not
     * @author Gourav Thatoi
     * @access public
     * @param none
     * @return boolean
     */
    public function isTaken($title){
        $table = Tbl_Products;
        $where = array('title'=>ucwords(strtolower($title)));
        $is_taken = isTaken($title,$table,$where);
        if($is_taken > 0){
            $this->form_validation->set_message('isTaken', '%s already exists');
            return FALSE;
        }
        return TRUE;
    }

    public function upload($param = '')
    {
        /*Create Breadcumb*/
        $this->make_bread->add('Other Processing Center', '', 0);
        $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/

        if($this->input->post('Submit')) {
            if (isset($_FILES['filename']) && !empty($_FILES['filename']['tmp_name'])) {
                make_upload_directory('./uploads');
                $file = upload_excel('./uploads', 'filename');
                if (!is_array($file)) {
                    $msg = notify($file, $type = "danger");
                    $this->session->set_flashdata('error', $msg);
                    redirect('rapc/upload');
                } else {
                    set_time_limit(0);
                    ini_set('memory_limit', '-1');
                    $keys = ['processing_center','branch_id','other_processing_center_id'];

                    $excelData = fetch_range_excel_data($file['full_path'], 'A2:C', $keys);
                    $this->Lead->insert_uploaded_data(Tbl_processing_center,$excelData);
                    $msg = notify('File Uploaded Successfully.','success');
                    $this->session->set_flashdata('success', $msg);
                    redirect(base_url('rapc'), 'refresh');

                }
            }
            $msg = notify("Please upload a file",'danger');
            $this->session->set_flashdata('message', $msg);
            redirect('rapc/upload');
        }
        //$arrData['uploaded_logs'] = $this->Lead->get_uploaded_leads_logs();
        $middle = "Rapc/rapcupload";
        load_view($middle,$arrData);
    }

    public function route($param = '')
    {
        /*Create Breadcumb*/
        $this->make_bread->add('Lead Routing Mapping', '', 0);
        $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/
        $arrData['routeDetail'] = $this->master->view_lead_route();
        if($this->input->post('Submit')) {
            $this->form_validation->set_rules('default_assign','Default Assign', 'trim|required');
            if ($this->form_validation->run() == FALSE)
            {   $this->session->set_flashdata('error','Please Select Default Assign');
                return load_view("Rapc/route",$arrData);
            }else{
                $data = array(
                    'route_to' => $this->input->post('default_assign'),
                    'modified_by' => loginUserId(),
                    'modified_on' => date('Y-m-d H:i:s')
                );
                $where = array('route_to !=' => NULL);
                $response = $this->master->lead_route($where,$data);
                if($response['status'] == 'error'){
                    $this->session->set_flashdata('error','Failed to Add Record');
                    redirect('rapc/route');
                }else{
                    $this->session->set_flashdata('success','Record added successfully.');
                    redirect('rapc/route');
                }
            }
        }
        //$arrData['uploaded_logs'] = $this->Lead->get_uploaded_leads_logs();
        $middle = "Rapc/route";
        load_view($middle,$arrData);
    }


/* Center list
* Fetches products according to selected type.
* @author Gourav Thatoi
* @access public
* @param none
* @return json
*/
    public function centerlist()
    {
        if ($this->input->post()) {
            $type_id = $this->input->post("type_id");
            $select=array('DISTINCT(other_processing_center_id)');
            $whereArray = array('processing_center' => $type_id);
            $table=Tbl_processing_center;
            //$products = $this->Lead->get_all_products($whereArray);
            $list = $this->master->view($select,$whereArray,$table,$join = array(),$order_by = array());
//pe($list);
            $center_extra = 'class="form-control" id="center_id"';
            if (!empty($list)) {
                $options[''] = 'select';
                foreach ($list as $key => $value) {
                    $procc_name = branchname($value['other_processing_center_id']);
                    $options[$value['other_processing_center_id']] = ucwords(strtolower($procc_name[0]['name']));
                }
                $html = '<label>Processing Center:<span style="color:red;">*</span></label>';
                $html .= form_dropdown('center_id', $options, '', $center_extra);
            } else {
                $options[''] = 'select';
                $html = '<label>Processing Center:<span style="color:red;">*</span></label>';
                $html .= form_dropdown('center_id', $options, '', $center_extra);
            }
            echo $html;
        }
    }

    /*
     * search
     * Search for product description
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
    public function search()
    {
        /*Create Breadcumb*/
        $this->make_bread->add('Other Processing Center', '', 0);
        $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/

        if($this->input->post()){
            $this->form_validation->set_rules('type_id','Center Type', 'required');
            $this->form_validation->set_rules('center_id','Processing Center', 'required');
            if ($this->form_validation->run() == FALSE)
            {
                $this->session->set_flashdata('error', validation_errors());
                redirect(base_url('rapc'), 'refresh');
            }
            $type_id = $this->input->post('type_id');
            $arrData['type_id'] = $type_id;
            $center_id = $this->input->post('center_id');
            $arrData['center_id'] = $center_id;

            $select=array('*');
            $whereArray = array('processing_center' => $type_id,'other_processing_center_id' => $center_id);
            $table=Tbl_processing_center;
            $arrData['type_id'] = $type_id;
            $arrData['list'] = $this->master->view($select,$whereArray,$table,$join = array(),$order_by = array());;
            return load_view("Rapc/list",$arrData);
        }else{
            redirect('rapc');
        }
    }

    /*
     * mapping_list
     * mapping_list for this controller.
     * @author Ashok Jadhav
	* @access public
     * @param none
     * @return void
     */
    public function mapping_list()
    {
        /*Create Breadcumb*/
        $this->make_bread->add('Lead Routing Mapping', '', 0);
        $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/

        $arrData['mappinglist'] = $this->master->mapping_list();
        return load_view("Rapc/mapping_list",$arrData);
    }

    public function add_mapping($param = '')
    {
        /*Create Breadcumb*/
        $this->make_bread->add('Add Lead Routing Mapping', '', 0);
        $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/
        if($this->input->post('Submit')) {
            $this->form_validation->set_rules('lead_source','Lead Source', 'trim|required|callback_chkRecord');
            $this->form_validation->set_rules('default_assign','Default Assign', 'trim|required');
            if ($this->form_validation->run() == FALSE)
            {   $this->session->set_flashdata('error',validation_errors());
                return load_view("Rapc/route",$arrData);
            }else{
                $data = array(
                    'lead_source' => $this->input->post('lead_source'),
                    'route_to' => $this->input->post('default_assign'),
                    'created_by' => loginUserId(),
                    'modified_by' => loginUserId(),
                    'modified_on' => date('Y-m-d H:i:s')
                );
                $response = $this->master->add_lead_mapping($data);
                if($response['status'] == 'error'){
                    $this->session->set_flashdata('error','Failed to Add Record');
                    redirect('rapc/add_mapping');
                }else{
                    $this->session->set_flashdata('success','Record added successfully.');
                    redirect('rapc/mapping_list');
                }
            }
        }
        //$arrData['uploaded_logs'] = $this->Lead->get_uploaded_leads_logs();
        $middle = "Rapc/route";
        load_view($middle,$arrData);
    }

    public function chkRecord($lead_source)
    {
        $arrData['routeDetail'] = $this->master->chkRecord($lead_source);
        if($arrData['routeDetail'][0]['id']){
            $this->form_validation->set_message('chkRecord', 'Record already added for selected source');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    public function edit_mapping($lead_source)
    {
        $lead_source = decode_id($lead_source);
        /*Create Breadcumb*/
        $this->make_bread->add('Update Lead Routing Mapping', '', 0);
        $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/
        $arrData['routeDetail'] = $this->master->chkRecord($lead_source);
        $arrData['lead_source'] = $lead_source;
        if($this->input->post('Submit')) {
            $this->form_validation->set_rules('default_assign','Default Assign', 'trim|required');
            if ($this->form_validation->run() == FALSE)
            {   $this->session->set_flashdata('error','Please Select Default Assign');
                return load_view("Rapc/edit_map",$arrData);
            }else{
                $data = array(
                    'route_to' => $this->input->post('default_assign'),
                    'modified_by' => loginUserId(),
                    'modified_on' => date('Y-m-d H:i:s')
                );
                $where = array('lead_source' => $lead_source);
                $response = $this->master->lead_route($where,$data);
                if($response['status'] == 'error'){
                    $this->session->set_flashdata('error','Failed to Update Record');
                    redirect('rapc/mapping_list');
                }else{
                    $this->session->set_flashdata('success','Record Updated successfully.');
                    redirect('rapc/mapping_list');
                }
            }
        }
        //$arrData['uploaded_logs'] = $this->Lead->get_uploaded_leads_logs();
        $middle = "Rapc/edit_map";
        load_view($middle,$arrData);
    }

}
