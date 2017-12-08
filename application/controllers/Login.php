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
        $this->load->library('captcha');
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
        //Get tickers title
            $this->load->model('Ticker_model','ticker');
            $select = array('id','title');
            $where['is_deleted'] = 0;
            $arrData['tickers'] = $this->ticker->view($select,$where,Tbl_Ticker,array(),array(),$limit = 2);
        //Get tickers title    
        if($this->input->post()){
            $this->form_validation->set_rules('username','Username', 'trim|required');
            $this->form_validation->set_rules('password','Password', 'trim|required');
            $this->form_validation->set_rules('captext','Security code', 'trim|required|callback_check_captcha');
            if ($this->form_validation->run() == FALSE)
            {    
                $arrData['has_error'] = 'has-error';
                //Generate Captcha
                $arrData['capimage'] = $this->load_captcha();
                //Generate Captcha
                return $this->load->view("login",$arrData);
            }else{
//echo $this->input->post('password');die;
              $pwd = base64_decode($this->input->post('password'));
                // Captcha validation passed
                if($this->input->post('username') == '1111111'){
                    $checkInput = array(
                        'hrms_id' => $this->input->post('username'),
                        'password' => md5($pwd)
                    );
                    $loginData = $this->master->check_login($checkInput);
                    if($loginData){
                        $authorisation_key= random_number();
                        $data = array('device_token' => NULL,
                            'employee_id' => $this->input->post('username'),
                            'branch_id' => 0,
                            'zone_id' => 0,
                            'device_type' => NULL,
                            'authorisation_key'=> $authorisation_key
                        );
                        $this->master->insert_login_log($data); // login log
                        $loginData[0]['authorisation_key']=$authorisation_key;
                        $this->set_session($loginData[0]);
                        if(!empty($this->input->post('remember_me'))) {
                            setcookie ("member_login",$this->input->post('username'),time()+ (10 * 365 * 24 * 60 * 60));
                            setcookie ("member_password",$this->input->post('password'),time()+ (10 * 365 * 24 * 60 * 60));
                        } else {
                            if(isset($_COOKIE["member_login"])) {
                                setcookie ("member_login","");
                            }
                            if(isset($_COOKIE["member_password"])) {
                                setcookie ("member_password","");
                            }
                        }
                        $this->session->set_flashdata('success','Login success');
                        redirect('dashboard');
                    }else{
                        $this->session->set_flashdata('error','Incorrect Login Details');
                        redirect('login');
                    }
                }else{
                    $hrms_id = $this->input->post('username');
                    //$password = $this->input->post('password');
                   $password = $pwd;

                    //$auth_response = call_external_url(HRMS_API_URL_AUTH.'?username='.$user_id.'?password='.$password);
                    $auth_response = call_external_url(HRMS_API_URL_AUTH.'username='.$hrms_id.'&password='.$password);
                    $auth = json_decode($auth_response);
//echo "<pre>";
//print_r($auth);die;
                    if ($auth->DBK_LMS_AUTH->password == 'True') {
                        // $records_response = call_external_url(HRMS_API_URL_GET_RECORD.$result->DBK_LMS_AUTH->username);
                        //$records_response = call_external_url(HRMS_API_URL_GET_RECORD.'emplid='.$auth->DBK_LMS_AUTH->username);
                        $records_response = call_external_url(HRMS_API_URL_GET_RECORD.'hrms_id='.$auth->DBK_LMS_AUTH->username);
                        $records = json_decode($records_response);
                       // echo "<pre>";print_r($records);die;
                        $authorisation_key= random_number();
                        $data = array('device_token' => NULL,
                            'employee_id' => $records->dbk_lms_emp_record1->EMPLID,
                            'branch_id' => $records->dbk_lms_emp_record1->deptid,
                            'zone_id' => $records->dbk_lms_emp_record1->dbk_state_id,
                            'device_type' => NULL,
                            'authorisation_key'=> $authorisation_key

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
                            'authorisation_key' => $authorisation_key,
                            'list'=>$records->dbk_lms_emp_record1->DBK_LMS_COLL
                        );
                        $this->set_session($result);
                        if(!empty($this->input->post('remember_me'))) {
                            setcookie ("member_login",$this->input->post('username'),time()+ (10 * 365 * 24 * 60 * 60));
                            setcookie ("member_password",$this->input->post('password'),time()+ (10 * 365 * 24 * 60 * 60));
                        } else {
                            if(isset($_COOKIE["member_login"])) {
                                setcookie ("member_login","");
                            }
                            if(isset($_COOKIE["member_password"])) {
                                setcookie ("member_password","");
                            }
                        }
                        $this->session->set_flashdata('success','Login success');
                        redirect('dashboard');
                    }else{
                        $this->session->set_flashdata('error','Invalid login details.Kindly contact HRMS Admin.');
                        redirect('login');
                    }
                }
            }
        }else{
        //Generate Captcha
        $arrData['capimage'] = $this->load_captcha();
        //Generate Captcha
        }
        return $this->load->view("login",$arrData);
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
                 'authorisation_key' => $data['authorisation_key'],
                 'list'=>$data['list']
             );

          $this->session->set_userdata($login_user);
     }


    // callback function to check the captcha
    public function check_captcha($input) {//echo $this->session->userdata('captchaWord');die;
        if ($this->session->userdata('captchaWord') != $input) {
            // set the validation error
            $this->form_validation->set_message('check_captcha', 'The entered Security Code is incorrect.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function load_captcha($type = 'display')
    {
        //Delete Previous captcha image
        $files = glob('captcha/*'); // get all file names
        foreach($files as $file){ // iterate files
          if(is_file($file))
            unlink($file); // delete file
        }
        $captcha = $this->captcha->generateCaptcha();
        //print_r( $captcha);die;
        $capimage = $captcha['image']; //echo $data['capimage'];
        $newdata = array(
            'captchaWord' => $captcha['word'],
            'captchaTime' => $captcha['time'],
        );

        $this->session->set_userdata($newdata);
        if($type == 'refresh'){
            echo $capimage;
        }else{
            return $capimage;
        }
    }

    /*
    * Validation for alphabetical letters
    * @param array $pwd,$dataArray
    * @return String
    */
    public function alphaNumeric($str)
    {
        if ( !preg_match('/^[a-zA-Z0-9\s]+$/i',$str) )
        {
            $this->form_validation->set_message('alphaNumeric', 'Password should contains only letters and numbers');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

}
