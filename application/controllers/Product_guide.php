<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_guide extends CI_Controller {

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
     * Display product description
     * @author Ashok Jadhav
	* @access public
     * @param none
     * @return void
     */
	public function index($productId)
	{
          $arrData['product'] = $this->master->view_product($productId);

          /*Create Breadcumb*/
          $this->make_bread->add('Product', 'product', 0);
          $this->make_bread->add($arrData['product'][0]['title'], '', 0);
          $this->make_bread->add('Description', '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          $arrData['productguidelist'] = $this->master->view_product_guide($productId);
          asort($arrData['productguidelist']);
          return load_view("Products/Product_guide/view",$arrData);
	}

     /*
     * add
     * Add Description for product
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function add($productId)
     {
          $arrData['product'] = $this->master->view_product($productId);
          $arrData['titleList'] = $this->master->get_enum_values(Tbl_ProductDetails,'title');

          /*Create Breadcumb*/
          $this->make_bread->add('Product', 'product', 0);
          $this->make_bread->add($arrData['product'][0]['title'], 'product_guide/index/'.$productId, 0);
          $this->make_bread->add('Add Description', '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          if($this->input->post()){
               $product_id = $this->input->post('product_id');
               $this->form_validation->set_rules('title','Title', 'required');
               $this->form_validation->set_rules('description_text','Description', 'required');
               if ($this->form_validation->run() == FALSE){    
                    $arrData['has_error'] = 'has-error';
                    return load_view("Products/Product_guide/add",$arrData);
               }else{
                    $insert = array(
                         'product_id' => $productId,
                         'title' => $this->input->post('title'),
                         'description_text' => $this->input->post('description_text'),
                         'created_by' => loginUserId()
                    );
                    $status = $this->master->add_product_guide($insert);
                    if($status['status'] == 'error'){
                         $this->session->set_flashdata('error',$this->input->post('title').' details already added for this product.');
                         redirect('product_guide/add/'.$productId);
                    }else{
                         $this->session->set_flashdata('success','Product Details added successfully.');
                         redirect('product_guide/index/'.$productId);
                    }
               }
          }else{
               return load_view("Products/Product_guide/add",$arrData);
          }
     }

     /*
     * edit
     * Edit Product Description
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function edit()
     {    
          if($this->input->post()){
               //$admin_id =  $this->session->userdata('admin_id');
               $admin_id =  1;
               $id = $this->input->post('id');
               $productId = $this->input->post('product_id');
               $arrData['product'] = $this->master->view_product($productId);
               //$this->form_validation->set_rules('title','Title', 'required');
               $this->form_validation->set_rules('description_text','Description', 'required');
               if ($this->form_validation->run() == FALSE){

                    /*Create Breadcumb*/
                    $this->make_bread->add('Product', 'product', 0);
                    $this->make_bread->add($arrData['product'][0]['title'], '', 0);
                    $this->make_bread->add('Description', '', 1);
                    $arrData['breadcrumb'] = $this->make_bread->output();
                    /*Create Breadcumb*/

                    $arrData['has_error'] = 'has-error';
                    $arrData['productguidelist'] = $this->master->view_product_guide($productId);
                    return load_view("Products/Product_guide/view",$arrData);
               }else{
                    $update = array(
                         //'title' => $this->input->post('title'),
                         'description_text' => $this->input->post('description_text'),
                         'modified_by' => loginUserId(),
                         'modified_on' => date('y-m-d H:i:s')
                    );
                    $this->master->edit_product_guide($id,$update);
                    $this->session->set_flashdata('success','Product Details updated successfully.');
                    redirect('product_guide/index/'.$productId);
               }
          }else{
               redirect('product');
          }
     }
}
