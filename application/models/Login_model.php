<?php

/**
 * Master_model Class
 *
 * @author Ashok Jadhav
 *
 */
class Login_model extends CI_Model{
	
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
	 * check_login
	 * @author Ashok Jadhav
	 * @access public
	 * @param $where
	 * @return array
	 */
	public function check_login($where){
		return $this->db->select('*')
					->from(Tbl_Admin)
					->where($where)
					->get()
					->result_array();
	}

	public function getAdminData($where){
		return $this->db->select('password')
					->from(Tbl_Admin)
					->where($where)
					->get()
					->result_array();
	}
}
