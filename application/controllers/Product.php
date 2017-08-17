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
		// Initialization of class
		parent::__construct();
          global $admin_id;
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
          $arrData['productlist'] = $this->master->view_product();
          return load_view($middle = "Masters/Product/view",$arrData, $template = "main");
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
          $arrData['categorylist'] = $this->getCategoryList();
          if($this->input->post()){
               //$admin_id =  $this->session->userdata('admin_id');
               $admin_id =  1;
               $this->form_validation->set_rules('title','Product name', 'required');
               $this->form_validation->set_rules('category_id','Product Category', 'required');
               if ($this->form_validation->run() == FALSE)
               {    $arrData['has_error'] = 'has-error';
                    return load_view($middle = "Masters/Product/add",$arrData, $template = "main");
               }else{
                    $insert = array(
                         'title' => $this->input->post('title'),
                         'category_id' => $this->input->post('category_id'),
                         'created_by' => $admin_id
                    );
                    $this->master->add_product($insert);
                    $this->session->flashData('success','Product category added successfully.');
                    redirect('product');
               }
          }else{
               return load_view($middle = "Masters/Product/add",$arrData, $template = "main");
          }
     }

     /*
     * edit
     * Edit Product name and change Category.
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function edit($id)
     {    
          $arrData['categorylist'] = $this->getCategoryList();
          $arrData['productDetail'] = $this->master->view_product($id);
          if($this->input->post()){
               //$admin_id =  $this->session->userdata('admin_id');
               $admin_id =  1;
               $this->form_validation->set_rules('title','Product name', 'required');
               $this->form_validation->set_rules('category_id','Product Category', 'required');
               if ($this->form_validation->run() == FALSE){    
                    $arrData['has_error'] = 'has-error';
                    return load_view($middle = "Masters/Product/edit",$arrData, $template = "main");
               }else{
                    $update = array(
                         'title' => $this->input->post('title'),
                         'category_id' => $this->input->post('category_id'),
                         'modified_by' => $admin_id
                    );
                    $this->master->edit_product($id,$update);
                    $this->session->flashData('success','Product updated successfully.');
                    redirect('product_category');
               }
          }else{
               return load_view($middle = "Masters/Product/edit",$arrData, $template = "main");
          }
     }

     public function delete($id){
          $soft_deleted = $this->master->delete_product($id);
          if($soft_deleted > 0){
               redirect('product_category');
          }
     }

     private function getCategoryList(){
          $list = $this->master->view_product_category();
          foreach ($list as $key => $value) {
               $categorylist[$value['id']] = $value['title'];
          }
          return $categorylist;
     }
}
