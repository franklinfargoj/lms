<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Toemail extends CI_Controller {

    /*
     * construct
     * constructor method
     * @author Raj Kale
	* @access private
     * @param none
     * @return void
     *
     */
    function __construct()
    {
        // Initialization of class
        parent::__construct();
        is_logged_in();     //check login
        $admin = ucwords(strtolower($this->session->userdata('admin_type')));
        if ($admin != 'Super Admin'){
            redirect('dashboard');
        }
        $this->load->model(['Lead', 'Toemail_model']);
    }

    /*
     * index
     * Index Page for this controller.
     * @author Raj Kale
	 * @access public
     * @param none
     * @return void
     */
    public function index()
    {
        /*Create Breadcumb*/
        $this->make_bread->add('To Email', '', 0);
        $arrData['breadcrumb'] = $this->make_bread->output();

        $arrData['userData'] = $this->Lead->get_employee_dump(array('id', 'hrms_id','name','designation','email_id','email_status', 'zone_id','zone_name'),array('designation like' => '%ZONAL MANAGER%', 'email_status' => 'active'),array(),'employee_dump');

        return load_view("list_to_email",$arrData);
    }

    /*
     * index
     * edit()
     * @author Raj Kale
     * @access public
     * @param $id
     * @return void
     */
    public function edit($id){
        $id = decode_id($id);

        $this->make_bread->add('To Email', 'toemail', 0);
        $this->make_bread->add('Edit', '', 1);
        $arrData['breadcrumb'] = $this->make_bread->output();

        $arrData['to_email'] = $this->Toemail_model->view_to_email($id);

        if($this->input->post()){
            $this->form_validation->set_rules('email_status','Status', 'required');
            if ($this->form_validation->run() == FALSE){
                $arrData['has_error'] = 'has-error';
                return load_view("edit_to_email",$arrData);
            }else{
                $update = array(
                    'email_status' => $this->input->post('email_status')
                );

                $response = $this->Toemail_model->edit_to_email($id,$update);
                if($response['status'] == 'error'){
                    $this->session->set_flashdata('error','Failed to edit CC email');
                    redirect('edit_to_email'.encode_id($id));
                }else{
                    $this->session->set_flashdata('success','To email updated successfully.');
                    redirect('toemail');
                }
            }
        }else{
            return load_view("edit_to_email",$arrData);
        }
    }

}
