<?php
/**
 * Created by PhpStorm.
 * User: webwerks
 * Date: 21/2/17
 * Time: 5:14 PM
 */
    /**
     * Load view
     *
     * @param unknown_type $arrDatam
     * @param unknown_type $middle
     * @param unknown_type $template
     */
    function load_view($middle = "home",$arrData = array(), $template = "main"){
        if(!$middle)$middle = "login";
        $CI =& get_instance();
        $arrData['middle'] = "middle/".$middle;
        $CI->load->view("layout/".$template,$arrData);
    }



/**
 *
 * Global function to print array and die
 */

function pe($arr)
{
    echo "<pre>";
    print_r($arr);
    die;
}

function sendmail($to, $message, $subject,$company_name,$company_email) {
    //echo $to;"<br>";
//    echo $message;"<br>";
//    //echo $subject;"<br>";
//   // echo $company_name;"<br>";
//   // echo $company_email;"<br>";
//    die;
    $ci = &get_instance();
    $ci->load->model('customers_model', 'customer');
    $smtp_details = $ci->customer->get_smtp_details();
    foreach($smtp_details as $row){
        $smtp_arr[] = $row->setting_value;
    }

     $config = Array(
        'protocol' => $smtp_arr[0],
        'smtp_host' => $smtp_arr[1],
        'smtp_port' => $smtp_arr[2],
        'smtp_user' => $smtp_arr[4],
        'smtp_pass' => base64_decode(base64_decode($smtp_arr[3])),
        'mailtype' => 'html',
        'charset' => 'utf-8',
        'wordwrap' => TRUE
    );
    $ci = &get_instance();
    $ci->load->library('email');
    $ci->email->initialize($config);
    $ci->email->from($smtp_arr[4], $company_name);
    $ci->email->to($to);
    $ci->email->subject($subject);
    $ci->email->message($message);
    //return true;
    if ($ci->email->send()) {
        return true;
    } else {
        return false;
    }

    
}
/*Added by Ashok Jadhav on 17 August 2017*/

function is_logged_in() {
    // Get current CodeIgniter instance
    $CI =& get_instance();
    // We need to use $CI->session instead of $this->session
    $isLoggedIn = $CI->session->userdata('isLoggedIn');
    if (empty($isLoggedIn)) { redirect('login'); }
}

function loginUserId(){
    // Get current CodeIgniter instance
    $CI =& get_instance();
    // We need to use $CI->session instead of $this->session
    $admin_id = $CI->session->userdata('admin_id');
    return $admin_id ? $admin_id : 0;
}
