<?php

/**
 * Master_model Class
 *
 * @author Raj Kale
 *
 */
class Othersource_model extends CI_Model{

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
     * add_cc_email
     * Inserts other source to the database
     * @author Raj Kale
     * @access public
     * @param $data
     * @return boolean
     */
    public function add_other_source($data = array())
    {
        if (!empty($data)) {
            $this->db->insert(other_sources, $data);
            return true;
        }
        return false;
    }

    /**
     * list_cc_email
     * List other source from the database
     * @author Raj Kale
     * @access public
     * @param none
     * @return array
     */
    public function list_other_source(){
        $this->db->select('*');
        $this->db->order_by("id", "DESC");
        $query =  $this->db->get('other_sources');
        return $query->result_array();
    }


    /**
     * view_other_source
     * retreives the data as per the id for displaying in edit section
     * @author Raj Kale
     * @access public
     * @param $id
     * @return array
     */
    public function view_other_source($id){
        $this->db->select('*');
        $this->db->where('id',$id);
        $query =  $this->db->get('other_sources');
        return $query->result_array();
    }


    /**
     * edit_other_source
     * Edit the other source as per the specific id
     * @author Raj Kale
     * @access public
     * @param $id,$update
     * @return array
     */
    public function edit_other_source($id,$update){
        $this->db->where('id', $id);
        $this->db->update('other_sources', $update);

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
     * delete_other_source
     * Delete the other source as per the specific id
     * @author Raj Kale
     * @access public
     * @param $id
     * @return array
     */
    public function delete_other_source($id){
        $this->db->where('id', $id);
        $this->db->delete('other_sources');

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
