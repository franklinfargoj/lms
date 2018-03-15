<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Change_password extends CI_Controller {

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
		// Initialization of class
		parent::__construct();
          is_logged_in();     //check login
        $admin = ucwords(strtolower($this->session->userdata('admin_type')));
        if ($admin != 'Super Admin'){
            redirect('dashboard');
        }

          $this->load->model('Login_model','master');
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
          $this->make_bread->add('Change Password', '', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          return load_view("change_password",$arrData);
	}

     /*
     * add
     * Add product name under category.
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function reset_password()
     {
          if($this->input->post()){
               /*Create Breadcumb*/
               $this->make_bread->add('Change Password', '', 0);
               $arrData['breadcrumb'] = $this->make_bread->output();
               /*Create Breadcumb*/

               $where = array('hrms_id' => loginUserId());
               $get_admin_details = $this->master->get_admin_details($where);
               $this->form_validation->set_rules('current_pwd','Current Password', 'trim|required');
               $this->form_validation->set_rules('new_pwd','New Password', 'trim|required|matches[re_pwd]');
               $this->form_validation->set_rules('re_pwd','Re-type New Password', 'trim|required');
               if($this->input->post('current_pwd') != '' && md5($this->input->post('current_pwd')) != $get_admin_details[0]['password']){
                    $this->session->set_flashdata('error', 'Current password entered is wrong');
                    redirect('change_password');
               }
               if ($this->form_validation->run() == FALSE)
               {    
                    $arrData['has_error'] = 'has-error';
                    return load_view("change_password",$arrData);
               }else{
                    $checkInput = array(
                         'password'      => md5($this->input->post('new_pwd'))
                    );
                    $updateFlag = $this->master->reset_password($where,$checkInput);
                    if($updateFlag){
                         $this->session->set_flashdata('success','Password reset successfully');
                         redirect('change_password');
                    }else{
                         $this->session->set_flashdata('error','Failed to reset password');
                         redirect('change_password');
                    }
               }
          }
     }



}
