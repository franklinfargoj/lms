<?php
/**
 *
 * This is App Model
 *
 * @author	Gourav Thatoi
 * @subpackage	Model
 */
class App extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

    }
    public function insert_login_log($table,$data = array()){
        if(!empty($data)){
            $this->db->insert($table,$data);
            return true;
        } return false;
    }
}