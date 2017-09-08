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
        $this->load->model('Login_model','master');
        // load the BotDetect Captcha library and set its parameter
        $this->load->library('botdetect/BotDetectCaptcha', array(
            'captchaConfig' => 'ExampleCaptcha'
        ));
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
        $isLoggedIn = $this->session->userdata('isLoggedIn');
        if (!empty($isLoggedIn)) { redirect('dashboard'); }
        // make Captcha Html accessible to View code
        $arrData['captchaHtml'] = $this->botdetectcaptcha->Html();
        $arrData['captchaValidationMessage'] = '';
          //Get tickers title
          $this->load->model('Ticker_model','ticker');
          $select = array('id','title');
          $where['is_deleted'] = 0;
          $arrData['tickers'] = $this->ticker->view($select,$where,Tbl_Ticker,array(),array(),$limit = 2);
          return $this->load->view("login",$arrData);
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
               {    
                    $this->session->set_flashdata('error','Incorrect login details');
                    redirect('login');
               }else{
                    // validate the user-entered Captcha code when the form is submitted
                    $code = $this->input->post('CaptchaCode');
                    $isHuman = $this->botdetectcaptcha->Validate($code);
                    if ($isHuman) {
                        // Captcha validation passed
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
                             $this->session->set_flashdata('error','Incorrect Login Details');
                             redirect('login');
                        }
                    } else {
                        // Captcha validation failed, return an error message
                        $this->session->set_flashdata('error','Invalid Security Code');
                        redirect('login');            
                    }
               }
          }else{
            redirect('login');
          }
     }
     public function logOut(){
          $login_user = array("admin_name" => null,"site_user"=>null,"admin_id"=>null,"isLoggedIn" => FALSE);
          $this->session->set_userdata($login_user);
          redirect('login');
     }

     public function view_tickers($id)
     {
          $id = decode_id($id);
          //Get tickers title
          $this->load->model('Ticker_model','ticker');
          $select = array('id','title','description_text');
          $where = array('is_deleted' => 0,'id' => $id);
          $arrData['tickerDetails'] = $this->ticker->view($select,$where,Tbl_Ticker,array(),array(),$limit = 0);
          return $this->load->view("view_tickers",$arrData);
     }

     public function view_faqs()
     {
          //Get FAQ details
          $this->load->model('Faq_model','Faq');
          $select = array('id','question','answer');
          $where = array('is_deleted' => 0);
          $arrData['faqs'] = $this->Faq->view($select,$where,Tbl_Faq,array(),array(),$limit = 0);
          return $this->load->view("view_faqs",$arrData);
     }


     private function set_session($data){
          $login_user = array("admin_name" => $data['name'],"admin_type"=>$data['admin_type'],"admin_id"=>$data['id'],"isLoggedIn"=>TRUE);
          $this->session->set_userdata($login_user);
     }


     
}
