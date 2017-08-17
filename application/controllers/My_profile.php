<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class My_profile extends CI_Controller {

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
          global $admin_id;
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
          $this->make_bread->add('My Profile', '', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          return load_view("my_profile",$arrData);
	}

     /*
     * add
     * Add product name under category.
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function change_password()
     {
          if($this->input->post()){
               $where = array('id' => loginUserId());
               $getAdminData = $this->master->getAdminData($where);
               $this->form_validation->set_rules('current_pwd','Current Password', 'trim|required');
               $this->form_validation->set_rules('new_pwd','New Password', 'trim|required|matches[re_pwd]');
               $this->form_validation->set_rules('re_pwd','Re-type New Password', 'trim|required');
               if($this->input->post('current_pwd') != '' && md5($this->input->post('current_pwd')) != $getAdminData[0]['password']){
                    $this->session->set_flashdata('error', 'Current password entered is wrong');
                    redirect('my_profile');
               }
               if ($this->form_validation->run() == FALSE)
               {    $arrData['has_error'] = 'has-error';
                    return $this->load->view("login",$arrData);
               }else{
                    $checkInput = array(
                         'username' => $this->input->post('username'),
                         'password' => md5($this->input->post('password')),
                         'password' => md5($this->input->post('password'))
                    );
                    $loginData = $this->master->check_login($checkInput);
                    if($loginData){
                         $this->set_session($loginData[0]);
                         $this->session->set_flashdata('success','Login success');
                         redirect('dashboard');
                    }else{
                         $this->session->set_flashdata('error','Incorrect login details');
                         redirect('login');
                    }
               }
          }else{
               return $this->load->view("login",$arrData = array());
          }
     }
     
}
