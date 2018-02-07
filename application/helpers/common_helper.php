<?php

require_once('./application/libraries/PHPMailer/PHPMailerAutoload.php');
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
    if(!$middle)$middle = "blank";
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
    //die;
}

 function sendMail1($to, $message, $subject,$attachment) {
     $ci = &get_instance();
$ci->load->library('email');
     $record = $ci->db->from(Tbl_Mail)->get()->result();

     $config = Array(
         'protocol' => 'smtp',
          //'smtp_host' => "webmail.denabank.co.in",
	  'smtp_host' => $record[0]->host,
         'smtp_port' => $record[0]->port,
         'smtp_user' => $record[0]->username,
         'smtp_pass' => '',
         'mailtype' => 'html',
         //'charset' => 'utf-8',
         'wordwrap' => TRUE
         
      );
     
     $ci->email->initialize($config);
	$ci->email->set_newline("\r\n");
     $ci->email->from($record[0]->fromemail, $record[0]->from);
     //$ci->email->to($to);
     $ci->email->to(array('exchadmin@denabank.co.in'));
     $ci->email->subject($subject);
     $ci->email->message("test msg from LMS");
     if($attachment != '')
      {
       $ci->email->attach($attachment);
       }
     if ($ci->email->send()) {
echo " mail sent to ".$ci->email->to;
      exit();
  return true;
	

     } else {
         echo $ci->email->print_debugger();
     }


 }
/*Added by Ashok Jadhav on 17 August 2017*/

function is_logged_in() {
    // Get current CodeIgniter instance
    $CI =& get_instance();
    // We need to use $CI->session instead of $this->session
    $isLoggedIn = $CI->session->userdata('isLoggedIn');
   $authorisation_key = $CI->session->userdata('authorisation_key');
    //echo $authorisation_key;echo $CI->session->userdata('admin_id');die;
    $check_response = check_authorisation($authorisation_key,$CI->session->userdata('admin_id'));
    if($check_response != 'TRUE'){
        redirect('login/logout');
    }

    if ($isLoggedIn != 'TRUE') { redirect('login'); }
}

function loginUserId(){
    // Get current CodeIgniter instance
    $CI =& get_instance();
    // We need to use $CI->session instead of $this->session
    $admin_id = $CI->session->userdata('admin_id');
    return $admin_id ? $admin_id : 0;
}

/**
 * notify
 * Returns div having bootstrap class according to the message type with the message
 * @author Gourav Thatoi
 * @param $msg , $type
 * @access public
 * @return html
 */
if(!function_exists('notify')){

    function notify($msg,$type="success"){

        if(!empty($msg)){

            return "<div class='alert text-$type'>$msg</div>";

        } return false;
    }
}

/**
 * returnJsom
 * Returns converts data to json
 * @author Gourav Thatoi
 * @param $data
 * @access public
 * @return json
 */
if(!function_exists('returnJson')){

    function returnJson($data){

        if(!empty($data)){

            $jsonData = json_encode($data);
            echo $jsonData;
            exit;

        }
    }
}

/**
 * make_upload_directory
 * Creates a directory if it does not exist
 * @author Gourav Thatoi
 * @param $directory
 * @access public
 * @return none
 */

if(!function_exists('make_upload_directory')){

    function make_upload_directory($directory = ''){

        if(empty($directory)){
            return false;
        }
        if(!is_dir($directory)){
            $oldmask = umask(0);
            mkdir($directory,0777);
            umask($oldmask);
        }
    }
}

/**
 * upload_excel
 * Uploads excel file
 * @author Gourav Thatoi
 * @param $upload_path , $file_name
 * @access public
 * @return none
 */
if(!function_exists('upload_excel')){

    function upload_excel($upload_path,$file_name){
        $CI = & get_instance();

        $config['allowed_types'] = "xls|xlsx";
        $config['upload_path'] = $upload_path;
        $config['file_name'] = time().$_FILES['filename']['name'];

        $CI->upload->initialize($config);

        if(! $CI->upload->do_upload($file_name)){

            return $CI->upload->display_errors();
        }
        return $CI->upload->data();
    }
}

/**
 * fetch_range_excel_data
 * Reads the uploaded excel file and returns an array according to the keys given
 * @author Gourav Thatoi
 * @param $file_path, $range,$keys,$highestRow,$date_keys
 * @access public
 * @return array
 */
if (!function_exists('fetch_range_excel_data'))
{
    function fetch_range_excel_data($file_path,$range,$keys = null,$highestRow = NULL,$date_keys = NULL)
    {
        $CI = & get_instance();
        $CI->load->library('excel');
        $inputFileType = PHPExcel_IOFactory::identify($file_path);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($file_path);
        $objPHPExcel->setActiveSheetIndex(0);
        if (empty($highestRow))
        {
            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();
        }
        if(is_array($keys) && !empty($keys))
        {
            $excelData = $objPHPExcel->getActiveSheet()->rangeToArrayWithKeys($range.$highestRow,null,$keys,$date_keys);
        }
        else
        {
            $excelData = $objPHPExcel->getActiveSheet()->rangeToArray($range.$highestRow,null,true,true,true);
        }
        return $excelData;
    }
}

/**
 * create_excel_error_file
 * Creates an excel file which containes all the errors while uploading
 * @author Gourav Thatoi
 * @param $validation_errors,$target_file_path
 * @access public
 * @return void
 */

if (!function_exists('create_excel_error_file'))
{
    function create_excel_error_file($validation_errors,$target_file_path,$target_file)
    {
        $CI = & get_instance();
        $CI->load->library('excel');
        $objPHPExcelWriter = new PHPExcel();
        $objPHPExcelWriter->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true);
        $objPHPExcelWriter->setActiveSheetIndex(0)->setCellValue('A1', 'row no.');
        $objPHPExcelWriter->setActiveSheetIndex(0)->setCellValue('B1', 'Error');

        $cnt = 2;
        foreach ($validation_errors as $row => $errors)
        {
            $objPHPExcelWriter->setActiveSheetIndex(0)->setCellValue('A'.$cnt, $row);
            $objPHPExcelWriter->setActiveSheetIndex(0)->setCellValue('B'.$cnt, $errors);
            $cnt++;
        }

        foreach(['A','B'] as $columnID) {
            $objPHPExcelWriter->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcelWriter, 'Excel5');
        $objWriter->save($target_file_path);

        $target_file_path = base_url().'uploads/errorlog/'.$target_file;
        $CI->session->set_userdata('error_file_path',$target_file_path);
    }
}

if (!function_exists('send_push_notification')){
    function send_push_notification($data, $message)
    {
        $deviceToken = $data['device_token'];
        $deviceType = $data['device_type'];
        if (isset($deviceToken) && $deviceToken != '') {
            switch ($deviceType) {
                case 'ios':        //Ios
                    //Sending push notification to iOS device
                    $passphrase = '12345';
                    $apnsCert = FCPATH . 'assets/iphone/apns-pro.pem';
                    $apnsHost = 'gateway.push.apple.com';
                    $apnsPort = 2195;
                    $streamContext = stream_context_create();
                    stream_context_set_option($streamContext, 'ssl', 'allow_self_signed', true);
                    stream_context_set_option($streamContext, 'ssl', 'verify_peer', false);
                    stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
                    stream_context_set_option($streamContext, 'ssl', 'passphrase', $passphrase);
                    $apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
                    $body['aps'] = array(
                        'badge' => +1,
                        'alert' => $message,
                        'sound' => 'default'
                    );
                    $payload = json_encode($body);
                    $apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
                    // Send it to the server
                    $result = fwrite($apns, $apnsMessage);
                    fclose($apns);        // Close the connection to the server
                    return $result;
                    break;
                case 'android'://Android
                    //setup for android push notification
                    $API_ACCESS_KEY = "AIzaSyBsinqvQuP5sSNzwecJigkXgkZc67ucnQ0";
                    $headers = array(
                        'Authorization: key=' . $API_ACCESS_KEY,
                        'Content-Type: application/json'
                    );

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    //Sending push notification to Android devices
                    $registrationIds = array($deviceToken);

                    // prep the bundle
                    $msg = array(
                        'message' => $message,
                        'title' => '',
                        'subtitle' => '',
                        'tickerText' => '',
                        'vibrate' => 1,
                        'sound' => 1
                    );

                    $fields = array(
                        'registration_ids' => $registrationIds,
                        'data' => $msg
                    );

                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                    $result = curl_exec($ch);
                    curl_close($ch);         // Close the connection to the server
                    return $result;
                    break;
            }
        }
    }
}
function encode_id($id){
    // Get current CodeIgniter instance
    $CI =& get_instance();
    $enc_id = $CI->encrypt->encode($id);
    $enc_id = str_replace(array('+', '/', '='), array('-', '_', '~'), $enc_id);
    return $enc_id = !empty($enc_id) ? $enc_id : 0;

}


function decode_id($id){
    // Get current CodeIgniter instance
    $CI =& get_instance();
    $dec_id = str_replace(array('-', '_', '~'), array('+', '/', '='), $id);
    return $dec_id = !empty($CI->encrypt->decode($dec_id)) ? $CI->encrypt->decode($dec_id) : 0;
}

function get_session(){
    $CI =& get_instance();
    //return $CI->session->userdata();

    $designation = get_designation($CI->session->userdata('designation_id'));
    if($CI->session->userdata('admin_id') == '1111111'){
        $designation = $CI->session->userdata('admin_type');
    }
    $CI->session->set_userdata('admin_type',$designation);
    $input = array(
        /*'hrms_id' => '312',*/
        'hrms_id' => $CI->session->userdata('admin_id'),
        'dept_type_id' => $CI->session->userdata('dept_type_id'),
        'dept_type_name' => $CI->session->userdata('dept_type_name'),
        'branch_id' => $CI->session->userdata('branch_id'),
        'district_id' => $CI->session->userdata('district_id'),
        'state_id' => $CI->session->userdata('state_id'),
        'zone_id' => $CI->session->userdata('zone_id'),
        'full_name' => $CI->session->userdata('admin_name'),
        'supervisor_id' => $CI->session->userdata('supervisor_id'),
        'designation_id' => $CI->session->userdata('designation_id'),
        'designation_name' => $designation,
        'mobile' => $CI->session->userdata('mobile'),
        'email_id' => $CI->session->userdata('email_id')
    );
    return $input;
}


function get_lead_title($type){
    switch ($type) {
        case 'generated':
                $title = 'Leads Generated';
            break;
        case 'converted':
                $title = 'Leads Converted';
            break;
        case 'assigned':
                $title = 'Leads Assigned';
            break;
        case 'all':
                $title = 'Leads';
            break;
    }
    
    return $title;
}

function dropdown($data,$select_option){
    $result = array();
    if($select_option != ''){
        $result[''] = $select_option;
    }
    if($data){
        foreach ($data as $key => $value) {
            $result[$value['id']] =  ucwords($value['title']);
        }
    }
    return $result;
}

if(!function_exists('send_sms')){
    function send_sms($mobile = '',$message='') {

        if($mobile!='') {
            $CI =& get_instance();
            $CI->load->model('Sms_model','sms');
            $credentials = $CI->sms->get_sms_credentials();
            //pe($credentials);
            $password = $CI->encrypt->decode($credentials['password']);
            $url = $credentials['url'].'?userid='.$credentials['username'].'&password='.$password.'&mobileno='.$mobile.'&sendername=BKDENA&sendernumber=0000&message='.urlencode($message).'&category=2&subject=Lead';           
          
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $output = curl_exec($ch);
            curl_close($ch);
//            echo $output;
//            die;
//            $output = file_get_contents($url);
//            echo $output;die;
            return $output;

        }
    }
}

function sendPushNotification($emp_id,$message,$title)
{
    $CI =& get_instance();
    $CI->load->model('Lead');
    $select = array('device_token','device_type');
    $where = array('employee_id'=>$emp_id,'device_type'=>'ANDROID');
    $order_by = 'id desc';
    $limit = '1';
    $table = Tbl_LoginLog;
    $device_values = $CI->Lead->lists($table,$select,$where,'','',$order_by,$limit);
//pe($device_values);die;
    if(!empty($device_values)){
        $device_id = $device_values[0]['device_token'];
        $device_type = $device_values[0]['device_type'];
        if((!empty($device_type) || $device_type != NULL) &&
            ($device_id != NULL || !empty($device_id))){
            $header = array();
            $header[] = 'Content-type: application/json';
            $header[] = 'Authorization: key=AAAA-QhpGTY:APA91bE-AL5cp0mPgmxhm4M1pTPqzNVTl1a0PxS3ZSBmO4eA5crSstcDRsXOUR1JYp5mQsBUN7kgtPxCrsN0rx7BZ8aHDJzW5iJIcP6GU2hvCs_mu13rRfFHijeEoSwulG3A6OzrhNgP';

            $data = array(
                'body'=>$message,
                'title' => $title,
                "notification_type" => "message",
                'notificationId' => time(),
                "message"=>$message
            );

            $fields = json_encode(array('to' => $device_id, 'data' => array('notificationData'=>$data)));
            $proxy = '172.25.129.11:8080';
            $crl = curl_init();
            curl_setopt($crl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($crl, CURLOPT_POST,true);
            curl_setopt($crl, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($crl, CURLOPT_PROXY, $proxy);
            curl_setopt($crl, CURLOPT_POSTFIELDS, $fields );

            curl_setopt($crl, CURLOPT_RETURNTRANSFER, true );

            $rest = curl_exec($crl);
//pe(curl_error($crl));die;
//   echo $fields;
    //echo $rest;die;
            if ($rest === false) {
                return curl_error($crl);
            }
            curl_close($crl);
            return $rest;
        }
    }
}

 function notification_log($title,$description,$priority,$notification_to){
     $CI =& get_instance();
     $CI->load->model('Notification_model','notification');
        $notificationData = array(
            'title' => $title,
            'description_text' => $description,
            'notification_to' => $notification_to,
            'priority' => $priority
        );

        return $CI->notification->insert(Tbl_Notification,$notificationData);
}

function get_notification_count(){
    $CI =& get_instance();
    $CI->load->model('Notification_model','notification');
    $input = get_session();
    
    $notification_count = $CI->notification->get_notifications_count();
    return $notification_count;
}

function get_details($hrms_id = ''){
    if(!empty($hrms_id)){
        //$records_response = call_external_url(HRMS_API_URL_GET_RECORD.$result->DBK_LMS_AUTH->username);
        $records_response = call_external_url(HRMS_API_URL_GET_RECORD.'emplid='.$hrms_id);
        $records = json_decode($records_response);
        $result['basic_info'] = array(
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
        );
        $result['list']=$records->dbk_lms_emp_record1->DBK_LMS_COLL;
    }else{
        $CI =& get_instance();
        $result['list']=$CI->session->userdata('list');
    }

    $result['status'] = 'success';
    return $result;
}

/**
 * export_excel
 * Excel export of branch manager home
 * @author Gourav Thatoi
 */
function export_excel($header_value,$data,$type='',$lead_source=''){
    $CI = & get_instance();
    $CI->load->library('excel');
    $file_name = time().'data.xls';
    $excel_alpha = unserialize(EXCEL_ALPHA);
    $objPHPExcel = $CI->excel;
    $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
    $objPHPExcel->getDefaultStyle()->getFont()->setBold(true);
    foreach ($header_value as $key=>$value ){
        $objPHPExcel->getActiveSheet()->getColumnDimension($excel_alpha[$key])->setAutoSize(true);
    }
    $objPHPExcel->getDefaultStyle()
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $styleArray = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN
            )
        )
    );
    $fontArray = array(
        'font'  => array(
            'bold'  => true,
            'size'  => 22
        ));
    $textfontArray = array(
        'font'  => array(
            'bold'  => true,
            'size'  => 11
        ));
    $text_bold_false = array(
        'font'  => array(
            'bold'  => false,
            'size'  => 11
        ));
    $fileType = 'Excel5';
    $time = time();
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
    $objSheet = $objPHPExcel->getActiveSheet();
    $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(-1);
    foreach ($header_value as $key => $value){
        $objSheet->getStyle($excel_alpha[$key].'1')
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objSheet->getCell($excel_alpha[$key].'1')->setValue($value);
    }

    $i=2;$j=1;
    switch ($type){
        case '':
            foreach ($data as $key => $value){

                if(isset($value['created_by_name'])){
                    $name = $value['created_by_name'];
                }
                if(isset($value['created_by_branch_name'])){
                    $name = $value['created_by_branch_name'];
                }
                foreach ($header_value as $k => $v){
                    $objSheet->getStyle($excel_alpha[$k].$i)
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    if($k == 1){
                        $objSheet->getStyle($excel_alpha[1].$i)
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    }
                }
                $objSheet->getStyle($excel_alpha[0].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[1].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[2].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[3].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[4].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[5].($i))->applyFromArray($text_bold_false);

                $objSheet->getCell($excel_alpha[0].$i)->setValue($j);
                $objSheet->getCell($excel_alpha[1].$i)->setValue(ucwords($name));
                $objSheet->getCell($excel_alpha[2].$i)->setValue($value['total_generated_mtd']);
                $objSheet->getCell($excel_alpha[3].$i)->setValue($value['total_generated_ytd']);
                $objSheet->getCell($excel_alpha[4].$i)->setValue($value['total_converted_mtd']);
                $objSheet->getCell($excel_alpha[5].$i)->setValue($value['total_converted_ytd']);
                $i++;$j++;
            }
            break;
        case 'assigned':
            $lead_status = $CI->config->item('lead_status');
            $lead_type = $CI->config->item('lead_type');
            foreach ($data['leads'] as $key => $value){
                foreach ($header_value as $k => $v){
                    $objSheet->getStyle($excel_alpha[$k].$i)
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    if($k == 1){
                        $objSheet->getStyle($excel_alpha[1].$i)
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    }
                    if($k == 2){
                        $objSheet->getStyle($excel_alpha[2].$i)
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    }
                }
                $created_date = explode(' ',$value['created_on']);
                $now = date_create(date('Y-m-d')); // or your date as well
                $generated_date = date_create($created_date[0]);
                $datediff = date_diff($now,$generated_date);
                $elapse_date = $datediff->format("%a days");
                $follow_up_date = date('d-m-Y',strtotime($value['remind_on']));
                if($value['remind_on'] == NULL || empty($value['remind_on'])){
                    $follow_up_date = '';
                }

                $objSheet->getStyle($excel_alpha[0].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[1].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[2].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[3].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[4].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[5].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[6].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[7].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[8].($i))->applyFromArray($text_bold_false);

                $objSheet->getCell($excel_alpha[0].$i)->setValue($j);
                $objSheet->getCell($excel_alpha[1].$i)->setValue(ucwords($value['customer_name']));
                $objSheet->getCell($excel_alpha[2].$i)->setValue(ucwords($value['contact_no']));
                $objSheet->getCell($excel_alpha[3].$i)->setValue(ucwords($value['title']));
                $objSheet->getCell($excel_alpha[4].$i)->setValue($elapse_date);
                $objSheet->getCell($excel_alpha[5].$i)->setValue(ucwords($lead_status[$value['status']]));
                $objSheet->getCell($excel_alpha[6].$i)->setValue($follow_up_date);
                $objSheet->getCell($excel_alpha[7].$i)->setValue(ucwords($lead_type[$value['lead_identification']]));
                $objSheet->getCell($excel_alpha[8].$i)->setValue(ucwords($value['lead_source']));
                $i++;$j++;
            }
            break;
        case 'unassigned':
            foreach ($data as $key => $value) {
                foreach ($header_value as $k => $v) {
                    $objSheet->getStyle($excel_alpha[$k] . $i)
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    if ($k == 1) {
                        $objSheet->getStyle($excel_alpha[1] . $i)
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    }
                    if ($k == 2) {
                        $objSheet->getStyle($excel_alpha[2] . $i)
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    }
                }

                $created_date = explode(' ', $value['created_on']);

                $now = date_create(date('Y-m-d')); // or your date as well
                //echo $created_date[0];
                $generated_date = date_create($created_date[0]);
                $datediff = date_diff($now, $generated_date);

                $elapse_date = $datediff->format("%a days");

                $objSheet->getStyle($excel_alpha[0].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[1].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[2].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[3].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[4].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[5].($i))->applyFromArray($text_bold_false);

                $objSheet->getCell($excel_alpha[0].$i)->setValue($j);
                $objSheet->getCell($excel_alpha[1].$i)->setValue(ucwords($value['lead_name']));
                $objSheet->getCell($excel_alpha[2].$i)->setValue(ucwords($value['contact_no']));
                $objSheet->getCell($excel_alpha[3].$i)->setValue(ucwords($value['product_title']));
                $objSheet->getCell($excel_alpha[4].$i)->setValue($elapse_date);
                $objSheet->getCell($excel_alpha[5].$i)->setValue($lead_source);
                $i++;$j++;
            }

            case 'generated':
            foreach ($data['leads'] as $key => $value) {
                foreach ($header_value as $k => $v) {
                    $objSheet->getStyle($excel_alpha[$k] . $i)
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    if ($k == 1) {
                        $objSheet->getStyle($excel_alpha[1] . $i)
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    }
                    if ($k == 2) {
                        $objSheet->getStyle($excel_alpha[2] . $i)
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    }
                }

                $created_date = explode(' ', $value['created_on']);

                $now = date_create(date('Y-m-d')); // or your date as well
                //echo $created_date[0];
                $generated_date = date_create($created_date[0]);
                $datediff = date_diff($now, $generated_date);

                $elapse_date = $datediff->format("%a days");

                $objSheet->getStyle($excel_alpha[0].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[1].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[2].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[3].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[4].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[5].($i))->applyFromArray($text_bold_false);
                $objSheet->getStyle($excel_alpha[6].($i))->applyFromArray($text_bold_false);

                $objSheet->getCell($excel_alpha[0].$i)->setValue($j);
                $objSheet->getCell($excel_alpha[1].$i)->setValue(ucwords($value['customer_name']));
                $objSheet->getCell($excel_alpha[2].$i)->setValue(ucwords($value['contact_no']));
                $objSheet->getCell($excel_alpha[3].$i)->setValue(ucwords($value['title']));
                $objSheet->getCell($excel_alpha[4].$i)->setValue($elapse_date);
                $objSheet->getCell($excel_alpha[5].$i)->setValue($value['lead_identification']);
                $objSheet->getCell($excel_alpha[6].$i)->setValue($value['lead_source']);
                $i++;$j++;
            }

    }

    //downloads excel
    make_upload_directory('uploads');
    make_upload_directory('uploads/excel_list');
    header('Content-Type: application/vnd.ms-excel'); //mime type
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    //tell browser what's the file name
    header('Cache-Control: max-age=0'); //no cache
    $objWriter->save('php://output');

}

function call_external_url($url) {

    //return file_get_contents($url);die;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 0);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, '');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   // curl_setopt($ch, CURLOPT_POSTFIELDS, '');
//pe(curl_error($ch));die;
    curl_exec($ch);
    $result = curl_exec($ch);
//pe(curl_error($ch));die;
    curl_close($ch);
    return($result);
}

function dummy_branch_details(){
    $lead_status['branch_details'][] =
        array(
            'zone_id'=>1,
            'zone_name'=>'BBSR',
            'state'=>[array(
                'id'=>28,
                'name'=>'odisha',
                'districts'=>[array(
                    'id'=>583,
                    'name'=>'dist1',
                    'branches'=>[array(
                        'id'=>84,
                        'name'=>'Branch89'
                    ),
                        array(
                            'id'=>784,
                            'name'=>'Branch985'
                        )]
                ),
                    array(
                        'id'=>13,
                        'name'=>'dist41',
                        'branches'=>[array(
                            'id'=>48,
                            'name'=>'Branch68'
                        )]
                    )]
            ),
                array('id'=>6,
                    'name'=>'andhra',
                    'districts'=>[array(
                        'id'=>5,
                        'name'=>'dist1',
                        'branches'=>[array(
                            'id'=>4,
                            'name'=>'Branch89'
                        )]
                    )])]);
    $lead_status['branch_details'][] =
        array(
            'zone_id'=>2,
            'zone_name'=>'laxmisagar',
            'state'=>[array(
                'id'=>52,
                'name'=>'jammu',
                'districts'=>[array(
                    'id'=>535,
                    'name'=>'dist15',
                    'branches'=>[array(
                        'id'=>45,
                        'name'=>'Branch59'
                    ),
                        array(
                            'id'=>745,
                            'name'=>'Branch955'
                        )]
                ),
                    array(
                        'id'=>1534,
                        'name'=>'dist414',
                        'branches'=>[array(
                            'id'=>454,
                            'name'=>'Branch64'
                        )]
                    )]
            ),
                array('id'=>66,
                    'name'=>'kashmir',
                    'districts'=>[array(
                        'id'=>5,
                        'name'=>'dist19',
                        'branches'=>[array(
                            'id'=>40,
                            'name'=>'Branch99'
                        )]
                    )])]);
    $lead_status['branch_details'][] =
        array(
            'zone_id'=>3,
            'zone_name'=>'xyz',
            'state'=>[array(
                'id'=>23,
                'name'=>'maharastra',
                'districts'=>[array(
                    'id'=>53,
                    'name'=>'dist3',
                    'branches'=>[array(
                        'id'=>43,
                        'name'=>'Branch93'
                    ),
                        array(
                            'id'=>74,
                            'name'=>'Branch95'
                        )]
                ),
                    array(
                        'id'=>153,
                        'name'=>'dist41',
                        'branches'=>[array(
                            'id'=>445,
                            'name'=>'Branch63'
                        )]
                    )]
            ),
                array('id'=>69,
                    'name'=>'pune',
                    'districts'=>[array(
                        'id'=>5,
                        'name'=>'dist13',
                        'branches'=>[array(
                            'id'=>44,
                            'name'=>'Branch956'
                        )]
                    )])]);

    return $lead_status['branch_details'];
    }

    function get_designation($designation_id){

        //$bm=array(520299,530399,540499,550599,560315);
  	$bm=array(510199,520299,530399,540499,550599,560315);	
        $zm=array(550502,560602,540402,550503);
        $gm=array(560601,570701,540405,580801);

        $designation = false;
        if(in_array($designation_id,$bm)){
            $designation = 'BM';
        }elseif(in_array($designation_id,$zm)){
            $designation = 'ZM';
        }elseif(in_array($designation_id,$gm)){
            $designation = 'GM';
        }else{
            $designation = 'EM';
        }
        return $designation;
    }


function fix_keys($array) {

    foreach ($array as $k => $val) {

        if (is_array($val)) {
            $array[$k] = fix_keys($val);
        }else{
            return array_values($array);
        }
    }

    return $array;
}

function sendMail($to = array(),$subject,$message,$attachment_file,$cc){
    $CI=& get_instance();
    $CI->load->database();
    $config = $CI->db->from(Tbl_Mail)->get()->result();
    $mail = new PHPMailer; //Create a new PHPMailer instance
    $mail->isSMTP(); //Tell PHPMailer to use SMTP

    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;

    //Ask for HTML-friendly debug output
    $mail->Debugoutput = 'html';

    //Set the hostname of the mail server
    $mail->Host = $config[0]->host;
    // use
    // $mail->Host = gethostbyname('smtp.gmail.com');
    // if your network does not support SMTP over IPv6

    //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
    $mail->Port = 25;

    //Set the encryption system to use - ssl (deprecated) or tls
    ////$mail->SMTPSecure = 'ssl';

    //Whether to use SMTP authentication
    $mail->SMTPAuth = false;

    //Username to use for SMTP authentication - use full email address for gmail
    $mail->Username = $config[0]->username;

    //Password to use for SMTP authentication
    $mail->Password = '';

    $mail->SMTPOptions = array(
      'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
      )
    );

    //Set who the message is to be sent from
    $mail->setFrom($config[0]->fromemail, $config[0]->from);

    //Set an alternative reply-to address
    $mail->addReplyTo($config[0]->fromemail, $config[0]->from);

    //Set who the message is to be sent to
    //$mail->addAddress('mukesh.kurmi@wwindia.com','Mukesh Kurmi');
    $mail->addAddress($to['email'],$to['name']);
    // $mail->addAddress('pragati@denabank.co.in','Pragati Dena Bank');
    // $mail->addAddress('rahul.choubey@denabank.co.in','Pragati Dena Bank');
    //$mail->addAddress('jeet.gupta@denabank.co.in','Pragati Dena Bank');
   // $mail->addCC('sunmit@denabank.co.in','Pragati Dena Bank');
    if($cc == 1){
        $mail->addCC('rahul.choubey@denabank.co.in','Rahul Choubey');
    }



    //Set the subject line
    $mail->Subject = $subject;

    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    //$mail->msgHTML('Your New password for MLS Auto Dealers is'.$pwd['password']);
    $mail->msgHTML($message);

    //Attach an image file
    if(!empty($attachment_file)){
        $mail->addAttachment('uploads/excel_list/'.$attachment_file);
    }
    //send the message, check for errors
    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        //exit;
    } else {
//       echo "sent";
        unlink('uploads/excel_list/'.$attachment_file);
    }
}

if (!function_exists('random_number')){
    function random_number(){
        return mt_rand();
    }
}

if (!function_exists('check_authorisation')){
    function check_authorisation($key,$hrms_id,$device=0){
        $CI = & get_instance();
        $CI->load->model('Lead');
        $select = 'authorisation_key';
        $table = Tbl_LoginLog;
        $order_by = 'date_time desc';
         if($device){
         $where = array('employee_id'=>$hrms_id,'device_type'=>'ANDROID');
         }else{
        $where = array('employee_id'=>$hrms_id,'device_type'=> NULL);
        }
        $list = $CI->Lead->lists($table,$select,$where,$join=array(),$group_by=array(),$order_by,$limit=1);
        if(!empty($list) && $list[0]['authorisation_key'] == $key){
            return TRUE;
        }return false;
    }
}
if (!function_exists('verify_account')){
    function verify_account($acc_no){
        if($acc_no !=''){
            $url = FINACLE_ACCOUNT_RECORD.'/'.$acc_no;
            $response = call_external_url($url);
            return $response;
        }
    }
}
if (!function_exists('isTaken')){
    function isTaken($title='',$table='',$where=''){
        if($title !='' && $table !='' && $where !=''){
            $action = 'count';
            $select = '';
            $join = $group_by = $order_by = array();
            $CI = & get_instance();
            $CI->load->model('Lead');
            $response = $CI->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by);
            return $response;
        }
    }
}

if (!function_exists('sortBySubkey')){
function sortBySubkey(&$array, $subkey, $sortType = SORT_ASC) {
    foreach ($array as $subarray) {
        $keys[] = $subarray[$subkey];
    }
    array_multisort($keys, $sortType, $array);
    return $array;
    }
}

if(!function_exists('allMasters')){
    function allMasters($table,$whereArray='',$selectArray='',$order_by=''){
        $CI = & get_instance();
        $CI->load->model('Lead');
        $action='list';
        $select=array('DISTINCT(TRIM(`name`)) as name','TRIM(`code`) as code');
        if($selectArray !=''){
            $select = $selectArray;
        }
        $data = $CI->Lead->get_leads($action,$table,$select,$whereArray,'','',$order_by);

        return $data;
    }
}


if(!function_exists('unassignedLeadCount')){
    function unassignedLeadCount(){
        $login_user = get_session();
        $branch_id = $login_user['branch_id'];
        $CI = & get_instance();
        $CI->load->model('Lead');
        $action = 'count';
        $select = array();
        $table = Tbl_Leads;
        $where = array(Tbl_Leads . '.branch_id' => $branch_id);
        $where['('.Tbl_LeadAssign.'.lead_id IS NULL OR '.Tbl_LeadAssign.'.is_deleted = 1)'] = NULL;

        $yr_start_date=(date('Y')-1).'-04-01 00:00:00';
        $yr_end_date=(date('Y')).'-03-31 23:59:59';
        $current_month = date('n');
        if($current_month >=4){
            $yr_start_date=(date('Y')).'-04-01 00:00:00';
            $yr_end_date=(date('Y')+1).'-03-31 23:59:59';
        }
                $where[Tbl_Leads . ".created_on >='".$yr_start_date."'"] = NULL; 
$where[Tbl_Leads . ".created_on <='".$yr_end_date."'"] = NULL;       
$join[] = array('table' => Tbl_LeadAssign, 'on_condition' => Tbl_LeadAssign . '.lead_id = ' . Tbl_Leads . '.id', 'type' => 'left');
        $data = $CI->Lead->get_leads($action, $table, $select, $where, $join, $group_by = array(), $order_by = array());
        return $data;

    }
}


if(!function_exists('assignedLeadCount')){
    function assignedLeadCount($admin_type){
        $login_user = get_session();
        $created_id = $login_user['hrms_id'];
        $CI = & get_instance();
        $CI->load->model('Lead');
        $action = 'count';
        $select = array();
        $join = array();
        $table = Tbl_LeadAssign ;
        $where = array(Tbl_LeadAssign . '.employee_id' => $created_id, Tbl_LeadAssign . '.is_updated' => 1, Tbl_LeadAssign . '.is_deleted' => 0,Tbl_LeadAssign . '.view_status' => 0, 'YEAR(' . Tbl_LeadAssign . '.created_on)' => date('Y'), 'DATEDIFF( CURDATE( ) , ' . Tbl_LeadAssign . '.created_on) <=' => Elapsed_day);
        if($admin_type == 'BM'){
            $join[] = array('table' => Tbl_Leads.' as l','on_condition' => 'l.id = '.Tbl_LeadAssign.'.lead_id','type' => '');
            $join[] = array('table' => Tbl_Category.' as pc','on_condition' => 'l.product_category_id = pc.id','type' => '');
            $where = "(".Tbl_LeadAssign.".status='AO' OR ".Tbl_LeadAssign.".status='NI' OR (".Tbl_LeadAssign.".status = 'DC' AND pc.title = 'Fee Income')) AND ".Tbl_LeadAssign.".branch_id =".$login_user['branch_id']." AND (".Tbl_LeadAssign.".is_updated = 1 AND ".Tbl_LeadAssign.".is_deleted = 0 AND DATEDIFF( CURDATE( ) , ".Tbl_LeadAssign.".created_on) <=".Elapsed_day.")";
        }

        $data = $CI->Lead->get_leads($action, $table, $select, $where, $join, $group_by=array(), $order_by = array());
        return $data;

    }
}


if(!function_exists('get_array')){
    function get_array(){
        return $data;

    }
}

if(!function_exists('branchname')){
    function branchname($id){
        $CI = & get_instance();
        $CI->load->model('Master_model','master');
        $select=array('name');
        $where['code'] = $id;
        $data = $CI->master->get_branchname($select,$where);
        return $data;
    }
}

function convertCurrency($number)
{
    // Convert Price to Crores or Lakhs or Thousands
    $length = strlen($number);
    $currency = 0;

   /* if($length == 4 || $length == 5)
    {
        // Thousand
        $number = $number / 1000;
        $number = round($number,2);
        $ext = "Thousand";
        $currency = $number." ".$ext;
    }
    elseif($length == 6 || $length == 7)
    {
        // Lakhs
        $number = $number / 100000;
        $number = round($number,2);
        $ext = "Lac";
        $currency = $number." ".$ext;

    }
    elseif($length == 8 || $length == 9)
    {
        // Crores
        $number = $number / 10000000;
        $number = round($number,2);
        $ext = "Cr";
        $currency = $number.' '.$ext;
    }*/
    if($number > 0){
        // Lakhs
        $number = $number / 100000;
        $number = round($number,2);
        $ext = "Lac";
        $currency = $number;
    }

    return $currency;
}

function get_status($lead_id){
    $CI = & get_instance();
    $CI->load->model('Lead');
    $select=array('status');
    $where['lead_id'] = $lead_id;
    $where['is_updated'] = 1;
    $where['is_deleted'] = 0;
    $data = $CI->Lead->get_status($select,$where);
    return $data;
}

function get_salt()
{
  return encode_id(date('YmdHis'));
}

function aes_decode($encrypted_str){
    $key = 'RwcmlVpg';
    $method = 'aes-256-cbc';
    $key = substr(hash('sha256', $key, true), 0, 32);
    $iv = '4e5Wa71fYoT7MFEX';
    $decrypted_string = openssl_decrypt(base64_decode($encrypted_str), $method, $key, OPENSSL_RAW_DATA, $iv);
    return $decrypted_string;
}

if(!function_exists('zonename')){
    function zonename($id){
        $CI = & get_instance();
        $CI->load->model('Master_model','master');
        $select=array('name');
        $where['code'] = $id;
        $data = $CI->master->get_zonename($select,$where);
        return $data;
    }
}

if(!function_exists('designation_by_hrms_id')){
    function designation_by_hrms_id($id){
        $CI = & get_instance();
        $CI->load->model('Master_model','master');
        $select=array('designation');
        $where['hrms_id'] = $id;
        $data = $CI->master->designation_by_hrms_id($select,$where);
        return $data;
    }
}

if(!function_exists('get_bm')){
    function get_bm($id){
        $CI = & get_instance();
        $CI->load->model('Master_model','master');
        $select=array('hrms_id','name');
        $where["hrms_id = (select hrms_id FROM employee_dump WHERE designation like '%BRANCH MANAGER%' AND branch_id = ".$id.")"] = NULL;
        $data = $CI->master->get_bm($select,$where);
        return $data;
    }
}







