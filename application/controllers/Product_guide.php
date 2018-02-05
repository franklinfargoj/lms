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
          //asort($arrData['productguidelist']);
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
          $arrData['category_list'] = dropdown($category_list,'Select');

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
               $arrData['category_list'] = dropdown($category_list,'Select');

               $product_list = $this->Lead->get_all_products(array('category_id' => $product_category_id,'is_deleted' => 0,'status' => 'active'));
               $arrData['product_list'] = dropdown($product_list,'Select');

               //Search for product description
               $arrData['searchResult'] = $this->master->view_product_guide($product_id);
              // asort($arrData['searchResult']);

               return load_view("Products/Product_guide/search",$arrData);
          }else{
               redirect('product_guide/view');
          }  
     }

     public function manage_points($productId)
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
          $this->make_bread->add('Manage Points', 'product_guide/view_points/'.encode_id($productId), 0);
          $this->make_bread->add('Add', '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          if($this->input->post()){
               $product_id = $this->input->post('product_id');
               $this->form_validation->set_rules('from_range','Min Range', 'trim|numeric|required');
               $this->form_validation->set_rules('to_range','Max Range', 'trim|numeric|required');
               $this->form_validation->set_rules('points','Points', 'trim|numeric|required');
               if ($this->form_validation->run() == FALSE){    
                    $arrData['has_error'] = 'has-error';
                    return load_view("Products/Product_guide/manage_points",$arrData);
               }else{
                    $insert = array(
                         'product_id' => $productId,
                         'from_range' => $this->input->post('from_range'),
                         'to_range' => $this->input->post('to_range'),
                         'points' => $this->input->post('points'),
                         'created_by' => loginUserId()
                    );
                    if($insert['from_range'] > $insert['to_range']){
                         $this->session->set_flashdata('error','Min range should be less than max range'); 
                        redirect('product_guide/manage_points/'.encode_id($productId));
                    }
                    $where = array('product_id' => $productId,'is_deleted !='=>'1');
                    $valid = $this->master->view_points($where);
                    if($valid){
                        foreach ($valid as $key => $valid_value){
                            if(($insert['from_range'] <= $valid[0]['to_range']) || ($insert['from_range'] > ($valid[0]['to_range'] + 1))){
                                $this->session->set_flashdata('error','Please enter range above ('.$valid[0]['from_range'].' - '.$valid[0]['to_range'].') starting from '.($valid[0]['to_range'] + 1));
                                redirect('product_guide/manage_points/'.encode_id($productId));
                            }
                        }
                    }
                    $response = $this->master->add_points($insert);
                    if($response['status'] == 'error'){
                         $this->session->set_flashdata('error','Failed to add points');
                    }else{
                         $this->session->set_flashdata('success','Points added successfully.');
                    }
                    redirect('product_guide/manage_points/'.encode_id($productId));
               }
          }else{
               return load_view("Products/Product_guide/manage_points",$arrData);
          }
     }


     public function points_distrubution($productId)
     {    
          if(!$productId){
               $this->session->set_flashdata('error','Invalid access');
               redirect('product');
          }
          $productId = decode_id($productId);
          $arrData['product'] = $this->master->view_product($productId);
          $where = array('product_id' => $productId,'active' => 1);
          $arrData['points_distrubution'] = $this->master->view_points_distrubute($where);
          if(count($arrData['product']) > 1){
               $this->session->set_flashdata('error','Invalid access');
               redirect('product');
          }
          /*Create Breadcumb*/
          $this->make_bread->add('Product', 'product', 0);
          $this->make_bread->add(ucwords($arrData['product'][0]['title']), '', 0);
          $this->make_bread->add('Points Distrubution', '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          if($this->input->post()){
               $product_id = $this->input->post('product_id');
               $this->form_validation->set_rules('generator_contrubution','Lead Generator Contribution', 'trim|numeric|required');
               $this->form_validation->set_rules('convertor_contrubution','Lead Convertor Contribution', 'trim|numeric|required');
               if ($this->form_validation->run() == FALSE){    
                    $arrData['has_error'] = 'has-error';
                    return load_view("Products/Product_guide/points_distrubution",$arrData);
               }else{
                    $insert = array(
                         'product_id' => $productId,
                         'generator_contrubution' => $this->input->post('generator_contrubution'),
                         'convertor_contrubution' => $this->input->post('convertor_contrubution'),
                         'created_by' => loginUserId()
                    );
                    $response = $this->master->points_distrubute($insert);
                    if($response['status'] == 'error'){
                         $this->session->set_flashdata('error','Failed to distribute points');
                    }else{
                         $this->session->set_flashdata('success','Points distribute successfully.');
                    }
                    redirect('product_guide/points_distrubution/'.encode_id($productId));
               }
          }else{
               return load_view("Products/Product_guide/points_distrubution",$arrData);
          }
     }

     public function view_points($productId)
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
          $this->make_bread->add('Manage Points', '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          $where = array('product_id' => $productId,'is_deleted !='=>'1');
          $arrData['pointsData'] = $this->master->view_points($where);

          return load_view("Products/Product_guide/view_points",$arrData);
     }

     public function delete_points($id,$product_id){
         if(!$id){
             $this->session->set_flashdata('error','Invalid access');
             redirect('product_guide/view_points');
         }
         $response = $this->master->delete_points(decode_id($id));
         if($response){
            $this->view_points($product_id);
         }

     }
}
