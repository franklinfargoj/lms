<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {

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
          $this->make_bread->add('Products', '', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          $arrData['categorylist'] = $this->getCategoryList('TITLE');
          $arrData['productlist'] = $this->master->view_product();
          return load_view("Products/Product/view",$arrData);
	}

     /*
     * add
     * Add product name under category.
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function add()
     {
          /*Create Breadcumb*/
          $this->make_bread->add('Product', 'product', 0);
          $this->make_bread->add('Add', '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/
          
          $arrData['categorylist'] = $this->getCategoryList();
          if($this->input->post()){
               $this->form_validation->set_rules('title','Product name', 'trim|required|callback_alphaNumeric|is_unique['.Tbl_Products.'.title]');
               $this->form_validation->set_rules('category_id','Product Category', 'required');
               $this->form_validation->set_message('is_unique', '%s is already taken');
               $this->form_validation->set_rules('turn_around_time','Turn around time', 'trim|required');
               if ($this->form_validation->run() == FALSE)
               {    $arrData['has_error'] = 'has-error';
                    return load_view("Products/Product/add",$arrData);
               }else{
                    $insert = array(
                         'title' => strtolower($this->input->post('title')),
                         'category_id' => $this->input->post('category_id'),
                         'default_assign' => $this->input->post('default_assign'),
                         'status' => strtolower($this->input->post('status')),
                         'turn_around_time' => strtolower($this->input->post('turn_around_time')),
                         'created_by' => loginUserId()
                    );
                    $response = $this->master->add_product($insert);
                    if($response['status'] == 'error'){
                         $this->session->set_flashdata('error','Failed to add product');
                         redirect('product/add');
                    }else{
                         $this->session->set_flashdata('success','Product added successfully.');
                         redirect('product');
                    }
               }
          }else{
               return load_view("Products/Product/add",$arrData);
          }
     }

     /*
     * edit
     * Edit Product name and change Category.
     * @author Ashok Jadhav
     * @access public
     * @param $id
     * @return void
     */
     public function edit($id)
     {    
          if(!$id){
               $this->session->set_flashdata('error','Invalid access');
               redirect('product');
          }
          $id = decode_id($id);
          /*Create Breadcumb*/
          $this->make_bread->add('Product', 'product', 0);
          $this->make_bread->add('Edit', '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          $arrData['categorylist'] = $this->getCategoryList();
          $arrData['productDetail'] = $this->master->view_product($id);
          if(count($arrData['productDetail']) > 1){
               $this->session->set_flashdata('error','Invalid access');
               redirect('product');
          }
          if($this->input->post()){
               $this->form_validation->set_rules('category_id','Product Category', 'trim|required');
               if(strtolower($this->input->post('title')) != $arrData['productDetail'][0]['title']){
                    $is_unique = '|is_unique['.Tbl_Products.'.title]';
               }else{
                    $is_unique = '';
               }
               $this->form_validation->set_rules('title','Product name', 'trim|required|callback_alphaNumeric'.$is_unique);
               $this->form_validation->set_message('is_unique', '%s is already taken');
               if ($this->form_validation->run() == FALSE){    
                    $arrData['has_error'] = 'has-error';
                    return load_view("Products/Product/edit",$arrData);
               }else{
                    $update = array(
                         'category_id' => $this->input->post('category_id'),
                         'title' => strtolower($this->input->post('title')),
                         'default_assign' => $this->input->post('default_assign'),
                         'status' => strtolower($this->input->post('status')),
                         'turn_around_time' => strtolower($this->input->post('turn_around_time')),
                         'modified_by' => loginUserId(),
                         'modified_on' => date('y-m-d H:i:s')
                         
                    );
                    $response = $this->master->edit_product($id,$update);
                    if($response['status'] == 'error'){
                         $this->session->set_flashdata('success','Product updated successfully.');
                         redirect('product/edit/'.encode_id($id));
                    }else{
                         $this->session->set_flashdata('success','Product updated successfully.');
                         redirect('product');
                    }
               }
          }else{
               return load_view("Products/Product/edit",$arrData);
          }
     }

     /*
     * delete
     * Delete product (Soft Delete)
     * @author Ashok Jadhav
     * @access public
     * @param $id
     * @return void
     */
     public function delete($id){
          if(!$id){
               $this->session->set_flashdata('error','Invalid access');
               redirect('product');
          }
          $id = decode_id($id);
          $soft_deleted = $this->master->delete_product($id);
          if($soft_deleted > 0){
               $this->session->set_flashdata('success','Product deleted successfully.');
          }else{
               $this->session->set_flashdata('error','Failed to delete product');
          }
          redirect('product');
     }

     /*
     * delete
     * Delete product (Soft Delete)
     * @author Ashok Jadhav
     * @access public
     * @param $id
     * @return void
     */
     public function manage_points(){
          
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

     /*
     * getCategoryList
     * Get Category List
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     private function getCategoryList($option = 'ID'){
          $list = $this->master->view_product_category($id = null);
          asort($list);
          $categorylist[''] = "Select Category";
          foreach ($list as $key => $value) {
               if($option == 'ID'){
                    $categorylist[$value['id']] = $value['title'];
               }
               if($option == 'TITLE'){
                    $categorylist[$value['title']] = $value['title'];
               }
          }
          return $categorylist;
     }
}
