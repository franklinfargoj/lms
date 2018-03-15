<?php

/**
 * Master_model Class
 *
 * @author Franklin Fargoj
 *
 */
class Ccemail_model extends CI_Model{

    /**
     * construct
     * initializes
     * @author Franklin Fargoj
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


    /**
     * add_cc_email
     * Inserts cc email to the database
     * @author Franklin Fargoj
     * @access public
     * @param $data
     * @return boolean
     */
    public function add_cc_email($data = array())
    {
        if (!empty($data)) {
            $this->db->insert(email_cc, $data);
            return true;
        }
        return false;
    }

    /**
     * list_cc_email
     * List cc email from the database
     * @author Franklin Fargoj
     * @access public
     * @param none
     * @return array
     */
    public function list_cc_email(){
        $this->db->select('*');
        $this->db->order_by("id", "DESC");
        $query =  $this->db->get('email_cc');
        return $query->result_array();
    }


    /**
     * view_cc_email
     * retreives the data as per the id for displaying in edit section
     * @author Franklin Fargoj
     * @access public
     * @param $id
     * @return array
     */
    public function view_cc_email($id){
        $this->db->select('*');
        $this->db->where('id',$id);
        $query =  $this->db->get('email_cc');
        return $query->result_array();
    }


    /**
     * edit_cc_email
     * Edit the cc_email as per the specific id
     * @author Franklin Fargoj
     * @access public
     * @param $id,$update
     * @return array
     */
    public function edit_cc_email($id,$update){
        $this->db->where('id', $id);
        $this->db->update('email_cc', $update);

        $errors = $this->db->error();
        if($errors['code']){
            $response['status'] = 'error';
            $response['code'] = $errors['code'];
        }else{
            $response['status'] = 'success';
            $response['affected_rows'] = $this->db->affected_rows();
        }
        return $response;
    }

    /**
     * active_cc_mail
     * Gets mail address and name with sctive status
     * @author Franklin Fargoj
     * @access public
     * @param none
     * @return array
     */
    public function active_cc_mail(){
        $this->db->select('name,email');
        $this->db->where('status', "active");
        $query = $this->db->get('email_cc');
        return $query->result_array();
    }

}
