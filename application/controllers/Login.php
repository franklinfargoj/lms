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
                        if($this->input->post('username') == '1111111'){
                            $checkInput = array(
                                'hrms_id' => $this->input->post('username'),
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
                        }else{
                            $hrms_id = $this->input->post('username');
                            $password = $this->input->post('password');
                            //$auth_response = call_external_url(HRMS_API_URL_AUTH.'?username='.$user_id.'?password='.$password);
                            $auth_response = call_external_url(HRMS_API_URL_AUTH.'/'.$hrms_id.'/'.$password);
                            $auth = json_decode($auth_response);
                            if ($auth->DBK_LMS_AUTH->password == 'True') {
                                // $records_response = call_external_url(HRMS_API_URL_GET_RECORD.$result->DBK_LMS_AUTH->username);
                                $records_response = call_external_url(HRMS_API_URL_GET_RECORD.'/'.$auth->DBK_LMS_AUTH->username);
                                $records = json_decode($records_response);
                                $data = array('device_token' => NULL,
                                    'employee_id' => $records->dbk_lms_emp_record1->EMPLID,
                                    'device_type' => NULL
                                );
                                $this->master->insert_login_log($data); // login log

                                $result = array(
                                    'hrms_id' => $records->dbk_lms_emp_record1->EMPLID,
                                    'dept_id' => $records->dbk_lms_emp_record1->deptid,
                                    'dept_type_id' => $records->dbk_lms_emp_record1->dbk_dept_type,
                                    'dept_type_name' => $records->dbk_lms_emp_record1->dept_discription,
                                    'branch_id' => $records->dbk_lms_emp_record1->deptid,
                                    'district_id' => $records->dbk_lms_emp_record1->district,
                                    'state_id' => $records->dbk_lms_emp_record1->state,
                                    'zone_id' => $records->dbk_lms_emp_record1->dbk_state_id,
                                    'full_name' => $records->dbk_lms_emp_record1->name,
                                    'supervisor_id' => $records->dbk_lms_emp_record1->supervisor,
                                    'designation_id' => $records->dbk_lms_emp_record1->designation_id,
                                    'designation_name' => $records->dbk_lms_emp_record1->designation_descr,
                                    'mobile' => $records->dbk_lms_emp_record1->phone,
                                    'email_id' => $records->dbk_lms_emp_record1->email,
                                    'list'=>$records->dbk_lms_emp_record1->DBK_LMS_COLL
                                );
                                $this->set_session($result);
                                $this->session->set_flashdata('success','Login success');
                                redirect('dashboard');
                            }else{
                                $this->session->set_flashdata('error','Invalid Login Credential.Please Enter Again OR Contact Administrator');
                                redirect('login');
                            }
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
            //echo "<pre>";print_r($data);die;
             $login_user = array(
                 'admin_id' => $data['hrms_id'],
                 'dept_type_id' => $data['dept_type_id'],
                 'dept_type_name' => $data['dept_type_name'],
                 'branch_id' => $data['branch_id'],
                 'district_id' => $data['district_id'],
                 'state_id' => $data['state_id'],
                 'zone_id' => $data['zone_id'],
                 'admin_name' => $data['full_name'],
                 'supervisor_id' => $data['supervisor_id'],
                 'designation_id' => $data['designation_id'],
                 'admin_type' => $data['designation_name'],
                 'mobile' => $data['mobile'],
                 'email_id' => $data['email_id'],
                 'isLoggedIn' => TRUE,
                 'list'=>$data['list']
             );

          $this->session->set_userdata($login_user);
     }
}
