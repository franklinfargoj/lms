<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_category extends CI_Controller {

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
          $admin = ucwords(strtolower($this->session->userdata('admin_type')));
            if ($admin != 'Super Admin'){
                redirect('dashboard');
            }
          $this->load->model('Master_model','master');
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
          $this->make_bread->add('Product Category', '', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          $arrData['categorylist'] = $this->master->view_product_category();
		return load_view("Products/Category/view",$arrData);
	}

     /*
     * add
     * Display Category Insert form and functionality for Insert.
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function add()
     {
          /*Create Breadcumb*/
          $this->make_bread->add('Product Category', 'product_category', 0);
          $this->make_bread->add('Add', '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          if($this->input->post()){
               $this->form_validation->set_rules('title','Product category name','trim|required|callback_alphaNumeric|callback_isTaken');
               if ($this->form_validation->run() == FALSE)
               {    $arrData['has_error'] = 'has-error';
                    return load_view("Products/Category/add",$arrData);
               }else{
                    $insert = array(
                         'title' => ucwords(strtolower($this->input->post('title'))),
                         'status' => strtolower($this->input->post('status')),
                         'created_by' => loginUserId()
                    );
                    $response = $this->master->add_product_category($insert);
                    if($response['status'] == 'error'){
                         $this->session->set_flashdata('error','Failed to add product category');
                         redirect('product_category/add');
                    }else{
                         $this->session->set_flashdata('success','Product category added successfully.');
                         redirect('product_category');
                    }
               }
          }else{
               return load_view("Products/Category/add",$arrData);
          }
     }

     /*
     * edit
     * Display Category Edit form and functionality for Update.
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function edit($id)
     {    if(!$id){
               $this->session->set_flashdata('error','Invalid access');
               redirect('product_category');
          }
          $id = decode_id($id);
          /*Create Breadcumb*/
          $this->make_bread->add('Product Category', 'product_category', 0);
          $this->make_bread->add('Edit', '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          $arrData['categoryDetail'] = $this->master->view_product_category($id);


          if(count($arrData['categoryDetail']) > 1){
               $this->session->set_flashdata('error','Invalid access');
               redirect('product_category');
          }
          if($this->input->post()){
               if(ucwords(strtolower($this->input->post('title'))) != $arrData['categoryDetail'][0]['title']){
                    $is_unique = '|callback_isTaken';

               }else{
                    $is_unique = '';
               }
               $this->form_validation->set_rules('title','Product category name','trim|required|callback_alphaNumeric'.$is_unique);
               if ($this->form_validation->run() == FALSE){    
                    $arrData['has_error'] = 'has-error';
                    return load_view("Products/Category/edit",$arrData);
               }else{
                    $update = array(
                         'title' => ucwords(strtolower($this->input->post('title'))),
                         'status' => strtolower($this->input->post('status')),
                         'modified_by' => loginUserId(),
                         'modified_on' => date('y-m-d H:i:s')
                    );
                    $response = $this->master->edit_product_category($id,$update);
                    if($response['status'] == 'error'){
                         $this->session->set_flashdata('error','Failed to edit product category');
                         redirect('product_category/edit/'.encode_id($id));
                    }else{
                         $this->session->set_flashdata('success','Product category updated successfully.');
                         redirect('product_category');
                    }
                    
               }
          }else{
               return load_view("Products/Category/edit",$arrData);
          }
     }

     /*
     * delete
     * Delete category (Soft Delete)
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function delete($id){
          if(!$id){
               $this->session->set_flashdata('error','Invalid access');
               redirect('product_category');
          }
          $id = decode_id($id);
          $soft_deleted = $this->master->delete_product_category($id);
          if($soft_deleted > 0){
               $this->session->set_flashdata('success','Product category deleted successfully.');
          }else{
               $this->session->set_flashdata('error','Failed to delete product category');
          }
          redirect('product_category');
     }

     /*public function activate(){
          if($this->input->post('id')){
               $id = $this->input->post('id');
               //$updateData = array('status' => )
               $this->db->where('id', $id);
               if($this->db->update(Tbl_Category,$updateData)){
                    return true;
               }else{
                    return false;
               }
          }
     }

     public function deactivate(){
          if($this->input->post('id')){
               $id = $this->input->post('id');
               $this->db->where('id', $id);
               if($this->db->update(Tbl_Category,$updateData)){
                    return true;
               }else{
                    return false;
               }
          }
     }*/
     

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

    /*
    * isTaken
    * Checks title unique or not
    * @author Gourav Thatoi
    * @access public
    * @param none
    * @return boolean
    */
     public function isTaken($title){
         $table = Tbl_Category;
         $where = array('title'=>ucwords(strtolower($title)));
         $is_taken = isTaken($title,$table,$where);
         if($is_taken > 0){
             $this->form_validation->set_message('isTaken', '%s already exists');
             return FALSE;
         }
         return TRUE;
     }
}
