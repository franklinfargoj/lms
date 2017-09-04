<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends CI_Controller {

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
          $this->load->model('Notification_model','notification');
     
	}

	/**
     * index
     * Index Page for this controller.
     * @author Ashok Jadhav
	 * @access public
     * @param none
     * @return void
     *
     */
	public function index()
	{
        //Get session data
        $input = get_session();
        $middle = '';
        /*Create Breadcumb*/
          $this->make_bread->add('Notification', '', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
        /*Create Breadcumb*/
        $arrData =  $this->get_notification_data($action = 'list',$arrData);
        return load_view('notification',$arrData);
	}

    public function mark_as_read(){
        if($this->input->post()){
            $where = array(
                'id' => $this->input->post('id'),
                'is_read' => 0
            );
            $data = array(
                'is_read' => 1
            );
            $is_update = $this->notification->update($where,Tbl_Notification,$data);
            if($is_update > 0) {
                echo  "true";   
            }else{
                echo "fasle";   
            }
        }
    }

    public function get_notification_data($action,$arrData){
        //Get session data
        $input = get_session();
        $table = Tbl_Notification.' as n';
        $select= array('n.*');
        $unread_where  = array('n.notification_to' => $input['hrms_id'],'n.is_read' => 0);
        $order_by = "n.priority ASC";
        $arrData['unread'] = $this->notification->get_notifications($action,$select,$unread_where,$table,$join = array(),$order_by);

        $read_where  = array('n.notification_to' => $input['hrms_id'],'n.is_read' => 1);
        $arrData['read'] = $this->notification->get_notifications($action,$select,$read_where,$table,$join = array(),$order_by);
        return $arrData;
    }

    
}
