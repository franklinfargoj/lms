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
               $this->form_validation->set_rules('title','Product category name','trim|required|callback_alphaNumeric|is_unique['.Tbl_Category.'.title]');
               $this->form_validation->set_message('is_unique', '%s is already taken');
               if ($this->form_validation->run() == FALSE)
               {    $arrData['has_error'] = 'has-error';
                    return load_view("Products/Category/add",$arrData);
               }else{
                    $insert = array(
                         'title' => $this->input->post('title'),
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
     {    
          /*Create Breadcumb*/
          $this->make_bread->add('Product Category', 'product_category', 0);
          $this->make_bread->add('Edit', '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          $arrData['categoryDetail'] = $this->master->view_product_category($id);
          if($this->input->post()){
               if($this->input->post('title') != $arrData['categoryDetail'][0]['title']){
                    $is_unique = '|is_unique['.Tbl_Category.'.title]';
                    $this->form_validation->set_message('is_unique', '%s is already taken');
               }else{
                    $is_unique = '';
               }
               $this->form_validation->set_rules('title','Product category name','trim|required|callback_alphaNumeric'.$is_unique);
               if ($this->form_validation->run() == FALSE){    
                    $arrData['has_error'] = 'has-error';
                    return load_view("Products/Category/edit",$arrData);
               }else{
                    $update = array(
                         'title' => $this->input->post('title'),
                         'modified_by' => loginUserId(),
                         'modified_on' => date('y-m-d H:i:s')
                    );
                    $response = $this->master->edit_product_category($id,$update);
                    if($response['status'] == 'error'){
                         $this->session->set_flashdata('error','Failed to edit product category');
                         redirect('product_category/edit/'.$id);
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
          $soft_deleted = $this->master->delete_product_category($id);
          if($soft_deleted > 0){
               $this->session->set_flashdata('success','Product category deleted successfully.');
               redirect('product_category');
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
