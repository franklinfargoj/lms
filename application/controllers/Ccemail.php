<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ccemail extends CI_Controller {

    /*
     * construct
     * constructor method
     * @author Franklin Fargoj
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
        $this->load->model('Ccemail_model');
    }

    /*
     * index
     * Index Page for this controller.
     * @author Franklin Fargoj
	 * @access public
     * @param none
     * @return void
     */
    public function index()
    {
        /*Create Breadcumb*/
        $this->make_bread->add('CC Email', '', 0);
        $arrData['breadcrumb'] = $this->make_bread->output();

        $arrData['emaillist'] = $this->Ccemail_model->list_cc_email();
        return load_view("list_ccemail",$arrData);
    }


    /*
     * index
     * add()
     * @author Franklin Fargoj
	 * @access public
     * @param none
     * @return void
     */
    public function add()
    {
        $this->make_bread->add('CC Email', 'ccemail', 0);
        $this->make_bread->add('Add', '', 1);
        $arrData['breadcrumb'] = $this->make_bread->output();

        if($this->input->post()){

            $this->form_validation->set_rules('ccname','Name', 'required');
            $this->form_validation->set_rules('ccemail','Email', 'required|is_unique[email_cc.email]|valid_email');
            $this->form_validation->set_message('is_unique', 'The Email is already taken');
            if ($this->form_validation->run() == FALSE){

                $arrData['has_error'] = 'has-error';
                return load_view("email_cc",$arrData);
            }else{

                $insert = array(
                    'name' => $this->input->post('ccname'),
                    'email' => $this->input->post('ccemail')
                );
                $this->Ccemail_model->add_cc_email($insert);
                redirect('ccemail');
            }
        }else{
            return load_view("email_cc",$arrData);
        }
    }

    /*
     * index
     * edit()
     * @author Franklin Fargoj
     * @access public
     * @param $id
     * @return void
     */
    public function edit($id){
        $id = decode_id($id);

        $this->make_bread->add('CC Email', 'ccemail', 0);
        $this->make_bread->add('Edit', '', 1);
        $arrData['breadcrumb'] = $this->make_bread->output();

        $arrData['cc_name_email'] = $this->Ccemail_model->view_cc_email($id);

        if($this->input->post()){
            $this->form_validation->set_rules('ccname','Name', 'required');
            $this->form_validation->set_rules('ccemail','Email', 'required|valid_email');
            if ($this->form_validation->run() == FALSE){
                $arrData['has_error'] = 'has-error';
                return load_view("edit_ccemail",$arrData);
            }else{
                $update = array(
                    'name' => $this->input->post('ccname'),
                    'email' => $this->input->post('ccemail'),
                    'status' => $this->input->post('status')
                );

                $response = $this->Ccemail_model->edit_cc_email($id,$update);
                if($response['status'] == 'error'){
                    $this->session->set_flashdata('error','Failed to edit CC email');
                    redirect('edit_ccemail'.encode_id($id));
                }else{
                    $this->session->set_flashdata('success','CC email updated successfully.');
                    redirect('ccemail');
                }
            }
        }else{
            return load_view("edit_ccemail",$arrData);
        }
    }

}
