<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

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
          return $this->load->view("login",$arrData = array());
	}

     /*
     * add
     * Add product name under category.
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function check_login()
     {
          if($this->input->post()){
               $this->form_validation->set_rules('username','Username', 'trim|required');
               $this->form_validation->set_rules('password','Password', 'trim|required');
               if ($this->form_validation->run() == FALSE)
               {    $arrData['has_error'] = 'has-error';
                    return $this->load->view("login",$arrData);
               }else{
                    $checkInput = array(
                         'username' => $this->input->post('username'),
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
     public function logOut(){
          $login_user = array("admin_name" => null,"site_user"=>null,"admin_id"=>null,"isLoggedIn" => FALSE);
          $this->session->set_userdata($login_user);
          redirect('login');
     }


     private function set_session($data){
          
          $login_user = array("admin_name" => $data['name'],"site_user"=>$data['username'],"admin_id"=>$data['id'],"isLoggedIn"=>TRUE);
          $this->session->set_userdata($login_user);
     }


     
}
