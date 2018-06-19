<?php

/**
 * Master_model Class
 *
 * @author Raj Kale
 *
 */
class Toemail_model extends CI_Model{

    /**
     * construct
     * initializes
     * @author Raj Kale
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
     * view_to_email
     * retreives the data as per the id for displaying in edit section
     * @author Raj Kale
     * @access public
     * @param $id
     * @return array
     */
    public function view_to_email($id){
        $this->db->select('*');
        $this->db->where('id',$id);
        $query =  $this->db->get('employee_dump');
        return $query->result_array();
    }


    /**
     * edit_cc_email
     * Edit the cc_email as per the specific id
     * @author Raj Kale
     * @access public
     * @param $id,$update
     * @return array
     */
    public function edit_to_email($id,$update){
        $this->db->where('id', $id);
        $this->db->update('employee_dump', $update);

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

}
