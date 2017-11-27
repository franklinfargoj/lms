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
	public function index()
	{
          /*Create Breadcumb*/
          $this->make_bread->add('RAPC', '', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/
            $select=array('*');
            $table=Tbl_processing_center;
            $arrData['list'] = $this->master->view($select,$where=array(),$table,$join = array(),$order_by = array());
            return load_view("Rapc/list",$arrData);
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
        $this->make_bread->add('RAPC Upload', '', 0);
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
        $this->make_bread->add('Analytics Lead Route', '', 0);
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
}
