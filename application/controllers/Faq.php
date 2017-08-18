<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faq extends CI_Controller {

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
          $this->load->model('Faq_model','master');
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
          $this->make_bread->add('FAQs', '', 0);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          $arrData['faqlist'] = $this->master->view_record();
          return load_view("Faq/view",$arrData);
	}

     /*
     * add
     * Add Faqs
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function add()
     {
          /*Create Breadcumb*/
          $this->make_bread->add('FAQs', 'faq', 0);
          $this->make_bread->add('Add', '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          if($this->input->post()){
               //$admin_id =  $this->session->userdata('admin_id');
               $admin_id =  1;
               $this->form_validation->set_rules('question','Question', 'required');
               $this->form_validation->set_rules('answer','Answer', 'required');
               if ($this->form_validation->run() == FALSE)
               {    $arrData['has_error'] = 'has-error';
                    return load_view("Faq/add",$arrData);
               }else{
                    $insert = array(
                         'question' => $this->input->post('question'),
                         'answer' => $this->input->post('answer'),
                         'created_by' => loginUserId()
                    );
                    $this->master->add_record($insert);
                    $this->session->set_flashdata('success','Questions added successfully.');
                    redirect('faq');
               }
          }else{
               return load_view("Faq/add",$arrData);
          }
     }

     /*
     * edit
     * Edit Faq
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function edit($id)
     {    /*Create Breadcumb*/
          $this->make_bread->add('FAQs', 'faq', 0);
          $this->make_bread->add('Edit', '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/

          $arrData['faqDetail'] = $this->master->view_record($id);
          if($this->input->post()){
               //$admin_id =  $this->session->userdata('admin_id');
               $admin_id =  1;
               $this->form_validation->set_rules('question','Question', 'required');
               $this->form_validation->set_rules('answer','Answer', 'required');
               if ($this->form_validation->run() == FALSE){    
                    $arrData['has_error'] = 'has-error';
                    return load_view("Faq/edit",$arrData);
               }else{
                    $update = array(
                         'question' => $this->input->post('question'),
                         'answer' => $this->input->post('answer'),
                         'modified_by' => loginUserId(),
                         'modified_on' => date('y-m-d H:i:s')
                    );
                    $this->master->edit_record($id,$update);
                    $this->session->set_flashdata('success','Faq updated successfully.');
                    redirect('Faq');
               }
          }else{
               return load_view("Faq/edit",$arrData);
          }
     }

     /*
     * delete
     * Delete Faq (Soft Delete)
     * @author Ashok Jadhav
     * @access public
     * @param none
     * @return void
     */
     public function delete($id){
          $soft_deleted = $this->master->delete_record($id);
          if($soft_deleted > 0){
               $this->session->set_flashdata('success','Faq deleted successfully.');
               redirect('faq');
          }
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
          $arrData['faqDetail'] = $this->master->view_record($id);

          /*Create Breadcumb*/
          $this->make_bread->add('FAQs', 'faq', 0);
          $this->make_bread->add($arrData['faqDetail'][0]['question'], '', 1);
          $arrData['breadcrumb'] = $this->make_bread->output();
          /*Create Breadcumb*/
          
          return load_view("Faq/detail",$arrData);
          
     }
}
