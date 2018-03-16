<?php if (!defined('BASEPATH'))    exit('No direct access to script is allowed');

class Reminder_model  extends CI_Model
{

    /**
     * construct
     * initializes
     * @author Franklin Fargoj
     * @access private
     * @param none
     * @return void
     *
     */
    public function __construct()
    {
        parent::__construct();
        //$this->_tbl_db_leads = 'db_leads';
    }

    /**
     * get_current_schedule
     * Retreives the Employees having the reminder schedule for current date
     * @author Franklin Fargoj
     * @access public
     * @param none
     * @return array
     */
    public function get_current_schedule(){
        $this->db->select('remind_to,reminder_text');
        $this->db->from('db_reminder_scheduler');
        $this->db->where('remind_on',date('Y-m-d'));
        $this->db->where('is_cancelled',"No");
        $resultArray = $this->db->get()->result_array();
        return  $resultArray;
    }


}
