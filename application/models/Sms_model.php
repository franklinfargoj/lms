<?php

/**
 * Master_model Class
 *
 * @author Ashok Jadhav
 *
 */
class Sms_model extends CI_Model{
	
	/**
	 * construct
	 *
	 * initializes
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
	}

	#####################################
	/* FAQ Function*/
	#####################################

	/**
	 * add_record
	 * @author Ashok Jadhav
	 * @access public
	 * @param $data
	 * @return int
	 */
	public function get_sms_credentials(){
		return $this->db->get(Tbl_SmsAuth)->row_array();
	}
}
