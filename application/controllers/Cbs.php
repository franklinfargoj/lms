<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cbs extends CI_Controller {

    /**
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
        is_logged_in();     //check login
    }

    public function verify_account($acc_no)
    {
        //echo $acc_no;die;
        $host    = "172.25.2.23";
        $port    = 11221;
        $strval='1200';
        $primary = chr(bindec('10110000'));
        $primary = $primary.chr(bindec('00110000'));
        $primary = $primary.chr(bindec('10000001'));
        $primary = $primary.chr(bindec('00000001'));
        $primary = $primary.chr(bindec('01000000'));
        $primary = $primary.chr(bindec('00100000'));
        $primary = $primary.chr(bindec('10000000'));
        $primary = $primary.chr(0);

        $sec = chr(0);
        $sec = $sec.chr(0);
        $sec = $sec.chr(0);
        $sec = $sec.chr(0);
        $sec = $sec.chr(bindec('00000100'));
        $sec = $sec.chr(0);
        $sec = $sec.chr(0);
        $sec = $sec.chr(bindec('00101000'));

        $field_3 = '970000';
        $field_4 = '0000000000000000';
        $field_11 = '000000'.date('His');
        $field_12 = date('YmdHis');
        $field_17 = date('Ymd');
        $field_24 = '200';
        $field_32 = '03018';
        $field_34 = '09000400463';
        //$field_41 = 'LMS             ';
        $field_43 = '08BANKAWAY';
        $field_49 = 'INR';
        $field_102 = '31018        0000    '.$acc_no;
        $field_123 = '003LMS';
        $field_125 = '009LMSMOBILE';

        //$message = $msg."970000000000000000000000000001010120170808072141201708082000301809000000000IVR             08BANKAWAYINR31018        0000    9158885659  003IVR009IVRMOBILE";

        $msg_header= $strval.$primary.$sec;
        $message = $msg_header.$field_3.$field_4.$field_11.$field_12.$field_17.$field_24.$field_32.$field_34.$field_43.$field_49.$field_102.$field_123.$field_125;

        // echo "Message To server :".$message;
        // create socket
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket\n");
        // connect to server
        $result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");
        //echo "Conection Established Successfully With IP - ".$host." And PORT - ".$port;
        //echo "<br>";
        // send string to server
        $cnt = socket_write($socket, $message, strlen($message)) or die("Could not send data to server\n");
        //echo "Message Sent To Server :".$message;
        //echo "<br>";
        //echo "Sent Message Length :" .$cnt;
        //echo "<br>";
        // get server response
        $result = socket_read ($socket, 2048) or die("Could not read server response\n");
        // echo "Reply From Server  :".$result;die;
        // close socket
        socket_close($socket);

        $response= array();
        if(strpos($result,'UNI000000') !== false)
        {
            $response_data = explode('LMS~',$result);
            $response['status']='True';
        }else{
            $response_data = explode('LMS~',$result);
            $response['status']='False';
        }
        $response['data'] = $response_data[1];
        echo json_encode($response);
    }
}
