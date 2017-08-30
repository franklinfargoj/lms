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
          $this->load->model('Lead','Lead');
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
          if(!$productId){
               $this->session->set_flashdata('error','Invalid access');
               redirect('product');
          }
          $productId = decode_id($productId);
          $arrData['product'] = $this->master->view_product($productId);
          if(count($arrData['product']) > 1){
               $this->session->set_flashdata('error','Invalid access');
               redirect('product');
          }

          /*Create Breadcumb*/
          $this->make_bread->add('Product', 'product', 0);
          $this->make_bread->add(ucwords($arrData['product'][0]['title']), '', 0);
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
          if(!$productId){
               $this->session->set_flashdata('error','Invalid access');
               redirect('product');
          }
          $productId = decode_id($productId);
          $arrData['product'] = $this->master->view_product($productId);
          if(count($arrData['product']) > 1){
               $this->session->set_flashdata('error','Invalid access');
               redirect('product');
          }
          $arrData['titleList'] = $this->master->get_enum_values(Tbl_ProductDetails,'title');

          /*Create Breadcumb*/
          $this->make_bread->add('Product', 'product', 0);
          $this->make_bread->add(ucwords($arrData['product'][0]['title']), 'product_guide/index/'.encode_id($productId), 0);
          $this->make_bread->add('Add Description', '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          if($this->input->post()){
               $product_id = $this->input->post('product_id');
               $this->form_validation->set_rules('title','Title', 'trim|required');
               $this->form_validation->set_rules('description_text','Description', 'trim|required');
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
                    $response = $this->master->add_product_guide($insert);
                    if($response['status'] == 'error'){
                         if($response['code'] == 1062){
                              $this->session->set_flashdata('error',$this->input->post('title').' details already added for this product.');
                         }else{
                              $this->session->set_flashdata('error','Failed to add product details');
                         }
                         redirect('product_guide/add/'.encode_id($productId));
                    }else{
                         $this->session->set_flashdata('success','Product Details added successfully.');
                         redirect('product_guide/index/'.encode_id($productId));
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
               $id = decode_id($this->input->post('id'));
               $productId = decode_id($this->input->post('product_id'));
               $arrData['product'] = $this->master->view_product($productId);
               if(count($arrData['product']) > 1){
                    $this->session->set_flashdata('error','Invalid access');
                    redirect('product');
               }
               //$this->form_validation->set_rules('title','Title', 'required');
               $this->form_validation->set_rules('description_text_'.$id,'Description', 'trim|required');
               if ($this->form_validation->run() == FALSE){

                    /*Create Breadcumb*/
                    $this->make_bread->add('Product', 'product', 0);
                    $this->make_bread->add(ucwords($arrData['product'][0]['title']), '', 0);
                    $this->make_bread->add('Description', '', 1);
                    $arrData['breadcrumb'] = $this->make_bread->output();
                    /*Create Breadcumb*/

                    $arrData['has_error'] = 'has-error';
                    $arrData['productguidelist'] = $this->master->view_product_guide($productId);
                    return load_view("Products/Product_guide/view",$arrData);
               }else{
                    $update = array(
                         //'title' => $this->input->post('title'),
                         'description_text' => $this->input->post('description_text_'.$id),
                         'modified_by' => loginUserId(),
                         'modified_on' => date('y-m-d H:i:s')
                    );
                    $response = $this->master->edit_product_guide($id,$update);
                    if($response['status'] == 'error'){
                         $this->session->set_flashdata('error','Failed to add product details');
                    }else{
                         $this->session->set_flashdata('success','Product details updated successfully.');
                    }
                    redirect('product_guide/index/'.encode_id($productId));
               }
          }else{
               redirect('product');
          }
     }

     /*
     * view
     * Display product description
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function view()
     {
          /*Create Breadcumb*/
          $this->make_bread->add('Product Guide', '', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          $category_list = $this->Lead->get_all_category(array('is_deleted' => 0,'status' => 'active'));
          $arrData['category_list'] = dropdown($category_list,true);

          return load_view("Products/Product_guide/search",$arrData);
     }

     /*
     * search
     * Search for product description
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function search()
     {
          /*Create Breadcumb*/
          $this->make_bread->add('Product Guide', '', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          if($this->input->post()){
               $this->form_validation->set_rules('product_category_id','Product Category', 'required');
               $this->form_validation->set_rules('product_id','Product', 'required');
               if ($this->form_validation->run() == FALSE)
               {
                    $arrData['has_error'] = 'has-error';
                    return load_view("Products/Product_guide/view",$arrData);    
               }
               $product_category_id = $this->input->post('product_category_id');
               $arrData['product_category_id'] = $product_category_id;
               $product_id = $this->input->post('product_id');
               $arrData['product_id'] = $product_id;

               $category_list = $this->Lead->get_all_category(array('is_deleted' => 0,'status' => 'active'));
               $arrData['category_list'] = dropdown($category_list,true);

               $product_list = $this->Lead->get_all_products(array('category_id' => $product_category_id,'is_deleted' => 0,'status' => 'active'));
               $arrData['product_list'] = dropdown($product_list,true);

               //Search for product description
               $arrData['searchResult'] = $this->master->view_product_guide($product_id);
               asort($arrData['searchResult']);

               return load_view("Products/Product_guide/search",$arrData);
          }else{
               redirect('product_guide/view');
          }  
     }
}
