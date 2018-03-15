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
 	 * Check for login
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

	/**
	 * get_admin_details
	 * Get login admin details
	 * @author Ashok Jadhav
	 * @access public
	 * @param $where
	 * @return array
	 */
	public function get_admin_details($where){
		return $this->db->select('id,full_name,password')
					->from(Tbl_Admin)
					->where($where)
					->get()
					->result_array();
	}

	/**
	 * reset_password
	 * Reset Password
	 * @author Ashok Jadhav
	 * @access public
	 * @param $where,$data
	 * @return int
	 */
	public function reset_password($where,$data){
		$this->db->where($where)
				 ->update(Tbl_Admin,$data);
		return $this->db->affected_rows();

	}

	/**
	 * insert_login_log
	 * Inserts login log to the database
	 * @author Gourav Thatoi
	 * @access public
	 * @param $data
	 * @return boolean
	 */
	public function insert_login_log($data = array())
	{
		if (!empty($data)) {
			$this->db->insert(Tbl_LoginLog, $data);
			return true;
		}
		return false;
	}
    /**
     * insert_login_log
     * Inserts login log to the database
     * @author Gourav Thatoi
     * @access public
     * @param $data
     * @return boolean
     */
    public function insert_email($data = array())
    {
        if (!empty($data)) {
            $this->db->insert(email_cc, $data);
            return true;
        }
        return false;
    }


}
