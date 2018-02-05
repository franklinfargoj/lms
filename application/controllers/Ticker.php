<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ticker extends CI_Controller {

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
          $this->load->model('Ticker_model','master'); //load models
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
          $this->make_bread->add('Tickers', '', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          $arrData['tickerlist'] = $this->master->view_record();
          return load_view("Ticker/view",$arrData);
	}

     /*
     * add
     * Add Ticker
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function add()
     {    
          /*Create Breadcumb*/
          $this->make_bread->add('Tickers', 'ticker', 0);
          $this->make_bread->add('Add', '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/
          
          if($this->input->post()){
               $this->form_validation->set_rules('title','Title', 'trim|required|is_unique['.Tbl_Ticker.'.title]');
               $this->form_validation->set_rules('description_text','Description', 'trim|required');
               $this->form_validation->set_message('is_unique', '%s is already taken');
               if ($this->form_validation->run() == FALSE)
               {    $arrData['has_error'] = 'has-error';
                    return load_view("Ticker/add",$arrData);
               }else{
                    $insert = array(
                         'title' => $this->input->post('title'),
                         'description_text' => $this->input->post('description_text'),
                         'status' => strtolower($this->input->post('status')),
                         'created_by' => loginUserId()
                    );
                    $response = $this->master->add_record($insert);
                    if($response['status'] == 'error'){
                         $this->session->set_flashdata('error','Failed to add ticker information');
                         redirect('ticker/add');
                    }else{
                         $this->session->set_flashdata('success','Ticker Information added successfully.');
                         redirect('ticker');
                    }
               }
          }else{
               return load_view("Ticker/add",$arrData);
          }
     }

     /*
     * edit
     * Edit Ticker
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function edit($id)
     {    
          if(!$id){
               $this->session->set_flashdata('error','Invalid access');
               redirect('ticker');
          }
          $id = decode_id($id);
          /*Create Breadcumb*/
          $this->make_bread->add('Tickers', 'ticker', 0);
          $this->make_bread->add('Edit', '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          $arrData['tickerDetail'] = $this->master->view_record($id);
          if(count($arrData['tickerDetail']) > 1){
               $this->session->set_flashdata('error','Invalid access');
               redirect('ticker');
          }
          if($this->input->post()){
               if($this->input->post('title') != $arrData['tickerDetail'][0]['title']){
                    $is_unique = '|is_unique['.Tbl_Ticker.'.title]';
                    $this->form_validation->set_message('is_unique', '%s is already taken');
               }else{
                    $is_unique = '';
               }
               $this->form_validation->set_rules('title','Title', 'trim|required'.$is_unique);
               $this->form_validation->set_rules('description_text','Description', 'trim|required');
               if ($this->form_validation->run() == FALSE){    
                    $arrData['has_error'] = 'has-error';
                    return load_view("Ticker/edit",$arrData);
               }else{
                    $update = array(
                         'title' => $this->input->post('title'),
                         'description_text' => $this->input->post('description_text'),
                         'status' => strtolower($this->input->post('status')),
                         'modified_by' => loginUserId(),
                         'modified_on' => date('y-m-d H:i:s')
                    );
                    $response = $this->master->edit_record($id,$update);
                    if($response['status'] == 'error'){
                         $this->session->set_flashdata('error','Failed to edit ticker information');
                         redirect('ticker/edit/'.encode_id($id));
                    }else{
                         $this->session->set_flashdata('success','Ticker information updated successfully.');
                         redirect('ticker');
                    }
               }
          }else{
               return load_view("Ticker/edit",$arrData);
          }
     }

     /*
     * delete
     * Delete Ticker (Soft Delete)
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function delete($id){
          if(!$id){
               $this->session->set_flashdata('error','Invalid access');
               redirect('ticker');
          }
          $id = decode_id($id);
          $soft_deleted = $this->master->delete_record($id);
          if($soft_deleted > 0){
               $this->session->set_flashdata('success','Ticker information deleted successfully.');
          }else{
               $this->session->set_flashdata('eroor','Failed to delete ticker information');
          }
          redirect('ticker');
     }

     /*
     * view
     * View Details
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function view($id){
          if(!$id){
               $this->session->set_flashdata('error','Invalid access');
               redirect('ticker');
          }

          $id = decode_id($id);
          $arrData['tickerDetail'] = $this->master->view_record($id);
          if(count($arrData['tickerDetail']) > 1){
               $this->session->set_flashdata('error','Invalid access');
               redirect('ticker');
          }
          /*Create Breadcumb*/
          $this->make_bread->add('Tickers', 'ticker', 0);
          $this->make_bread->add($arrData['tickerDetail'][0]['title'], '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          return load_view("Ticker/detail",$arrData);
     }
}
