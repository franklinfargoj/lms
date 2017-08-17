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
          $arrData['categorylist'] = $this->master->view_product_category();
		return load_view($middle = "Masters/Category/view",$arrData, $template = "main");
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
          if($this->input->post()){
               //$admin_id =  $this->session->userdata('admin_id');
               $admin_id =  1;
               $this->form_validation->set_rules('title','Product category name', 'required');
               if ($this->form_validation->run() == FALSE)
               {    $arrData['has_error'] = 'has-error';
                    return load_view($middle = "Masters/Category/add",$arrData, $template = "main");
               }else{
                    $insert = array(
                         'title' => $this->input->post('title'),
                         'created_by' => $admin_id
                    );
                    $this->master->add_product_category($insert);
                    $this->session->flashData('success','Product category added successfully.');
                    redirect('product_category');
               }
          }else{
               return load_view($middle = "Masters/Category/add",$arrData = array(), $template = "main");
          }
     }

     /*
     * add
     * Display Category Insert form and functionality for Insert.
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function edit($id)
     {    
          $arrData['categoryDetail'] = $this->master->view_product_category($id);
          if($this->input->post()){
               //$admin_id =  $this->session->userdata('admin_id');
               $admin_id =  1;
               $this->form_validation->set_rules('title','Product category name', 'required');
               if ($this->form_validation->run() == FALSE){    
                    $arrData['has_error'] = 'has-error';
                    return load_view($middle = "Masters/Category/edit",$arrData, $template = "main");
               }else{
                    $update = array(
                         'title' => $this->input->post('title'),
                         'modified_by' => $admin_id
                    );
                    $this->master->edit_product_category($id,$update);
                    $this->session->flashData('success','Product category updated successfully.');
                    redirect('product_category');
               }
          }else{
               return load_view($middle = "Masters/Category/edit",$arrData, $template = "main");
          }
     }

     public function delete($id){
          $soft_deleted = $this->master->delete_product_category($id);
          if($soft_deleted > 0){
               redirect('product_category');
          }
     }
}
