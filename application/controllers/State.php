<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class State extends CI_Controller {

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
          is_logged_in();     //check login
          $this->load->model('State_model','master');
	}

    /*
     * index
     * Index Page for this controller.
     * @author Ashok Jadhav
	* @access public
     * @param none
     * @return void
     */
	public function index()
	{
          /*Create Breadcumb*/
          $this->make_bread->add('State', '', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          $arrData['statelist'] = $this->master->view_record();
		return load_view("State/view",$arrData);
	}

     /*
     * add
     * Display State Insert form and functionality for Insert.
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function add()
     {
          /*Create Breadcumb*/
          $this->make_bread->add('State', 'state', 0);
          $this->make_bread->add('Add', '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          if($this->input->post()){
               $this->form_validation->set_rules('title','State name','trim|required|callback_alphaNumeric');
               if ($this->form_validation->run() == FALSE)
               {    $arrData['has_error'] = 'has-error';
                    return load_view("State/add",$arrData);
               }else{
                    $insert = array(
                         'title' => $this->input->post('title'),
                         'created_by' => loginUserId()
                    );
                    $this->master->add_record($insert);
                    $this->session->set_flashdata('success','State added successfully.');
                    redirect('state');
               }
          }else{
               return load_view("State/add",$arrData);
          }
     }

     /*
     * edit
     * Display State Edit form and functionality for Update.
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function edit($id)
     {    
          /*Create Breadcumb*/
          $this->make_bread->add('State', 'state', 0);
          $this->make_bread->add('Edit', '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          $arrData['stateDetail'] = $this->master->view_record($id);
          if($this->input->post()){
               $this->form_validation->set_rules('title','State name','trim|required|callback_alphaNumeric');
               if ($this->form_validation->run() == FALSE){    
                    $arrData['has_error'] = 'has-error';
                    return load_view("State/edit",$arrData);
               }else{
                    $update = array(
                         'title' => $this->input->post('title'),
                         'modified_by' => loginUserId()
                    );
                    $this->master->edit_record($id,$update);
                    $this->session->set_flashdata('success','State updated successfully.');
                    redirect('state');
               }
          }else{
               return load_view("State/edit",$arrData);
          }
     }

     /*
     * delete
     * Delete state (Soft Delete)
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function delete($id){
          $soft_deleted = $this->master->delete_record($id);
          if($soft_deleted > 0){
               $this->session->set_flashdata('success','State deleted successfully.');
               redirect('state');
          }
     }

     ##################################
     /*Private Functions*/
     ##################################
     /*
     * Validation for alphabetical letters
     * @param array $pwd,$dataArray
     * @return String
     */
     public function alphaNumeric($str)
     {
          if ( !preg_match('/^[a-zA-Z0-9\s]+$/i',$str) )
          {
               $this->form_validation->set_message('alphaNumeric', 'Please enter only alpha numeric characters.');
               return FALSE;
          }
          else
          {
               return TRUE;
          }
     }
}
