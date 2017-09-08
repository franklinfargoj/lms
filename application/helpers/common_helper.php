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

if(!function_exists('send_sms')){
    function send_sms($name='',$mobile='') {
        $feedid='';
        $username='';
        $pass='';
        $senderid='';
        $sms='';
        if($mobile!='') {
                $sms = "Thanks for showing interest with Dena Bank. We will contact you shortly";
            $url = "http://bulkpush.mytoday.com/BulkSms/SingleMsgApi?feedid=$feedid&username=$username&password=$pass&To=$mobile&Text=" . urlencode($sms) . "&senderid=$senderid";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $output = curl_exec($ch);
            curl_close($ch);

            $response = ((array) simplexml_load_string($output));
            return $response;

        }
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

    $input = array(
        /*'hrms_id' => '312',*/
        'hrms_id' => $CI->session->userdata('admin_id'),
        'dept_id' => '12',
        'dept_type_id' => '123',
        'dept_type_name' => 'BR',
        'branch_id' => '3',
        'district_id' => '1',
        'state_id' => '1',
        'zone_id' => '4',
        'full_name' => $CI->session->userdata('admin_name'),
        'supervisor_id' => '009',
        'designation_id' => '4',
        'designation_name' => $CI->session->userdata('admin_type'),
        'mobile' => '9975772432',
        'email_id' => 'mukesh.kurmi@wwindia.com'
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
    foreach ($data as $key => $value) {
        $result[$value['id']] =  ucwords($value['title']);  
    }
    return $result;
}

if(!function_exists('send_sms')){
    function send_sms($mobile = '',$message='') {
        
        if($mobile!='') {
            $CI =& get_instance();
            $CI->load->model('Sms_model','sms');
            $credentials = $CI->sms->get_sms_credentials();
            $password = $CI->encrypt->decode($credentials['password']);
            $url = $credentials['url'].'?username='.$credentials['username'].'&password='.$password.'&to='.$mobile.'&udh=0&from=DENABK&text='.$message;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $output = curl_exec($ch);
            curl_close($ch);

            $response = ((array) simplexml_load_string($output));
            return $response;

        }
    }
}

function sendNotificationSingleClient($device_id,$device_type,$message,$title=NULL)
{
    //$d_type = ($device_type==0)? "appNameAndroid" : "appNameIOS";
   // $collection = PushNotification::app($d_type)->to($device_id)->send($message);
   // return $response = $collection->pushManager->getAdapter()->getResponse();
   
    $url = 'https://fcm.googleapis.com/fcm/send';
    $server_key = 'AAAAJTxIDRs:APA91bGmPFIAFGn7ZMj1XX__Vw-ONFXBbUwsJp_F3qCBalPyYMhCWcRiNtj7l7PzuGKuwSyG950X8s1kYFMHQIVcyXhH-ylwcYBZzaPnpTGxKfB1yOeAVTEkyp69_jNc25QNroxb_b-Z';
    $to = $device_id;
    $notification_title = ($title==NULL) ? 'Notification' : $title;
    $data = array('body'=>$message, 'title' => $notification_title, "icon" => "myicon","notification_type"=>"action");

    $fields = json_encode(array('to' => $to, 'data' => $data));
    $headers = array(
        'Content-Type:application/json',
        'Authorization:key='.$server_key
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    $result = curl_exec($ch);

    if ($result === FALSE) {
       // die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);
    return $result;
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

function get_details($designation_name){
    //        $curl_handle = curl_init();
//        curl_setopt($curl_handle, CURLOPT_URL, 'http://10.0.11.33/payo_app/users/update_synapse_info');
//
//        if(!isset($params['user_id']) || !isset($params['password']) || ($params['user_id'] == NULL) ||  ($params['password'] == NULL)){
//            $err['result'] = false;
//            $err['data'] = "Invalid Request";
//            returnJson($err);
//        }
//
//        $user_id = $params['user_id'];
//        $password = $params['password'];
//        $device_token = $params['device_token'];
//
//        $curl_handle = curl_init();
//        curl_setopt($curl_handle, CURLOPT_URL, '');
//        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($curl_handle, CURLOPT_POST, 1);
//        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, array(
//            'user_id' => $user_id,
//            'password' => $password
//        ));
//
//        $buffer = curl_exec($curl_handle);
//        curl_close($curl_handle);
//
//        $result = json_decode($buffer);

    $result['basic_info'] = array(
        'hrms_id' => '12',
        'dept_id' => '12',
        'dept_type_id' => '123',
        'dept_type_name' => 'BR',
        'branch_id' => '12',
        'district_id' => '1234',
        'state_id' => '1234',
        'zone_id' => '1234',
        'full_name' => 'mukesh kurmi',
        'supervisor_id' => '009',
        'designation_id' => '4',
        'designation_name' => $designation_name,
        'mobile' => '9975772432',
        'email_id' => 'mukesh.kurmi@wwindia.com',
    );
    $result['employee_list'][] = array(
        'id' => '2',
        'full_name' => 'mukesh kurmi',
    );
    $result['employee_list'][] = array(
        'id' => '13',
        'full_name' => 'anup',
    );
    $result['employee_list'][] = array(
        'id' => '15',
        'full_name' => 'anup',
    );
    $result['branch_list'][] = array(
        'id' => '3',
        'full_name' => 'branch1',
    );
    $result['branch_list'][] = array(
        'id' => '13',
        'full_name' => 'branch2',
    );
    $result['zone_list'][] = array(
        'id' => '4',
        'full_name' => 'zone1',
    );
    $result['zone_list'][] = array(
        'id' => '13',
        'full_name' => 'zone2',
    );
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

                $objSheet->getCell($excel_alpha[0].$i)->setValue($j);
                $objSheet->getCell($excel_alpha[1].$i)->setValue(ucwords($name));
                $objSheet->getCell($excel_alpha[2].$i)->setValue($value['total_generated']);
                $objSheet->getCell($excel_alpha[3].$i)->setValue($value['total_converted']);
                $i++;$j++;
            }
            break;
        case 'assigned':
            $lead_status = $CI->config->item('lead_status');
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

                $objSheet->getCell($excel_alpha[0].$i)->setValue($j);
                $objSheet->getCell($excel_alpha[1].$i)->setValue(ucwords($value['customer_name']));
                $objSheet->getCell($excel_alpha[2].$i)->setValue(ucwords($value['title']));
                $objSheet->getCell($excel_alpha[3].$i)->setValue($elapse_date);
                $objSheet->getCell($excel_alpha[4].$i)->setValue(ucwords($lead_status[$value['status']]));
                $objSheet->getCell($excel_alpha[5].$i)->setValue($follow_up_date);
                $objSheet->getCell($excel_alpha[6].$i)->setValue($value['lead_identification']);
                $objSheet->getCell($excel_alpha[7].$i)->setValue(ucwords($value['lead_source']));
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

                $objSheet->getCell($excel_alpha[0].$i)->setValue($j);
                $objSheet->getCell($excel_alpha[1].$i)->setValue(ucwords($value['lead_name']));
                $objSheet->getCell($excel_alpha[2].$i)->setValue(ucwords($value['product_title']));
                $objSheet->getCell($excel_alpha[3].$i)->setValue($elapse_date);
                $objSheet->getCell($excel_alpha[4].$i)->setValue($lead_source);
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

                $objSheet->getCell($excel_alpha[0].$i)->setValue($j);
                $objSheet->getCell($excel_alpha[1].$i)->setValue(ucwords($value['customer_name']));
                $objSheet->getCell($excel_alpha[2].$i)->setValue(ucwords($value['title']));
                $objSheet->getCell($excel_alpha[3].$i)->setValue($elapse_date);
                $objSheet->getCell($excel_alpha[4].$i)->setValue($value['lead_identification']);
                $objSheet->getCell($excel_alpha[5].$i)->setValue($value['lead_source']);
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
    curl_exec($ch);
    $result = curl_exec($ch);
    curl_close($ch);
    return($result);
}