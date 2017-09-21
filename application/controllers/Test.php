<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller
{

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
        parent::__construct(); // Initialization of class

    }

    /*
     * index
     * Index Page for this controller.
     * @author Ashok Jadhav
	* @access public
     * @param none
     * @return void
     */
    public function client()
    {
        $host    = "127.0.0.1";
        $port    = 25003;
        $message = "Hello Server";
        echo "Message To server :".$message;
        // create socket
        $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
        // connect to server
        $result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");
        // send string to server
        socket_write($socket, $message, strlen($message)) or die("Could not send data to server\n");
        // get server response
        $result = socket_read ($socket, 1024) or die("Could not read server response\n");
        echo "Reply From Server  :".$result;
        // close socket
        socket_close($socket);
    }

    function sendPushNotification()
    {
        //$d_type = ($device_type==0)? "appNameAndroid" : "appNameIOS";
        // $collection = PushNotification::app($d_type)->to($device_id)->send($message);
        // return $response = $collection->pushManager->getAdapter()->getResponse();

        $url = 'https://fcm.googleapis.com/fcm/send';
////    $server_key = 'AAAAJTxIDRs:APA91bGmPFIAFGn7ZMj1XX__Vw-ONFXBbUwsJp_F3qCBalPyYMhCWcRiNtj7l7PzuGKuwSyG950X8s1kYFMHQIVcyXhH-ylwcYBZzaPnpTGxKfB1yOeAVTEkyp69_jNc25QNroxb_b-Z';
//        $server_key = 'AAAA-QhpGTY:APA91bE-AL5cp0mPgmxhm4M1pTPqzNVTl1a0PxS3ZSBmO4eA5crSstcDRsXOUR1JYp5mQsBUN7kgtPxCrsN0rx7BZ8aHDJzW5iJIcP6GU2hvCs_mu13rRfFHijeEoSwulG3A6OzrhNgP';
//
//        //$to = $device_id;
//        $to = 'dbEu5-lTFtQ:APA91bEfBDObFHTcwSrFcwqYYQZkUY2_ZBY4iYsmfp9QnMtNbO5xAndriCz5zB3P1fAqeYAc7-4a09aHhC8n1x569lSYDDcswHv_2vzvkVDaNDFZnISqzttIRyWnTdZbWmDg82FQnfqp';
//        $notification_title = 'Test Title';
//        $data = array('body'=>'hi mukesh', 'title' => $notification_title, "icon" => "myicon","notification_type"=>"action");
//
//        $fields = json_encode(array('to' => $to, 'data' => array('notificationData'=>$data)));
//        $headers = array(
//            'Content-Type:application/json',
//            'Content-Length: 0',
//            'Authorization:key='.$server_key
//        );
        $header = array();
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: key=AAAA-QhpGTY:APA91bE-AL5cp0mPgmxhm4M1pTPqzNVTl1a0PxS3ZSBmO4eA5crSstcDRsXOUR1JYp5mQsBUN7kgtPxCrsN0rx7BZ8aHDJzW5iJIcP6GU2hvCs_mu13rRfFHijeEoSwulG3A6OzrhNgP';

        $payload = [
            'to' => 'fDNebtSRNB4:APA91bFxBzve9cfwCQ3dUqVbDHt-yNsw_ZbFB6dnrP-pYhAXEH7zdnUQLQojS8DV85MT347Dm76dLkyIiHIFweKffs9qXY4iYRW4ZCyRBYzNzkKfq2UbnWmE5FTJjY6PcylwxvNhkGcC',
            'notification' => [
                'title' => "Portugal VS Germany",
                'body' => "1 to 2 3444"
            ]
        ];

        $crl = curl_init();
        curl_setopt($crl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($crl, CURLOPT_POST,true);
        curl_setopt($crl, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($crl, CURLOPT_POSTFIELDS, json_encode( $payload ) );

        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true );

        $rest = curl_exec($crl);
        echo $rest;die;
        if ($rest === false) {
            return curl_error($crl);
        }
        curl_close($crl);
        return $rest;
    }
}