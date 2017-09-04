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
        'zone_id' => '1234',
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
    if($select_option == true){
        $result[''] = 'Select';
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
