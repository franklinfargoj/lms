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
    function create_excel_error_file($validation_errors,$target_file_path)
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
    }
}