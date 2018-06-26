<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Othersource extends CI_Controller {

    /*
     * construct
     * constructor method
     * @author Raj Kale
	* @access private
     * @param none
     * @return void
     *
     */
    function __construct()
    {
        // Initialization of class
        parent::__construct();
        is_logged_in();     //check login
        $admin = ucwords(strtolower($this->session->userdata('admin_type')));
        if ($admin != 'Super Admin'){
            redirect('dashboard');
        }
        $this->load->model('Othersource_model');
    }

    /*
     * index
     * Index Page for this controller.
     * @author Raj Kale
	 * @access public
     * @param none
     * @return void
     */
    public function index()
    {
        /*Create Breadcumb*/
        $this->make_bread->add('Other Agent', '', 0);
        $arrData['breadcrumb'] = $this->make_bread->output();
        $arrData['other_source_list'] = $this->Othersource_model->list_other_source();
        return load_view("other_source",$arrData);
    }


    /*
     * index
     * add()
     * @author Raj Kale
	 * @access public
     * @param none
     * @return void
     */
    public function add()
    {
        $this->make_bread->add('Other Source', 'othersource', 0);
        $this->make_bread->add('Add', '', 1);
        $arrData['breadcrumb'] = $this->make_bread->output();

        if($this->input->post()){

            $this->form_validation->set_rules('title','Title', 'required');
            $this->form_validation->set_rules('status','Status', 'required');

            if ($this->form_validation->run() == FALSE){

                $arrData['has_error'] = 'has-error';
                return load_view("add_other_source",$arrData);
            }else{

                $insert = array(
                    'title' => $this->input->post('title'),
                    'status' => $this->input->post('status'),
                    'created_by' => loginUserId()
                );
                $this->Othersource_model->add_other_source($insert);
                redirect('othersource');
            }
        }else{
            return load_view("add_other_source",$arrData);
        }
    }

    /*
     * index
     * edit()
     * @author Raj Kale
     * @access public
     * @param $id
     * @return void
     */
    public function edit($id){
        $id = decode_id($id);

        $this->make_bread->add('Other Source', 'othersource', 0);
        $this->make_bread->add('Edit', '', 1);
        $arrData['breadcrumb'] = $this->make_bread->output();

        $arrData['get_other_source'] = $this->Othersource_model->view_other_source($id);

        if($this->input->post()){
            $this->form_validation->set_rules('title','Title', 'required');
            $this->form_validation->set_rules('status','Status', 'required');

            if ($this->form_validation->run() == FALSE){
                $arrData['has_error'] = 'has-error';
                return load_view("edit_other_source",$arrData);
            }else{
                $update = array(
                    'title' => $this->input->post('title'),
                    'status' => $this->input->post('status'),
                    'modified_by' => loginUserId(),
                    'modified_on' => date('y-m-d H:i:s')
                );

                $response = $this->Othersource_model->edit_other_source($id,$update);
                if($response['status'] == 'error'){
                    $this->session->set_flashdata('error','Failed to edit Other Source');
                    redirect('edit_other_source'.encode_id($id));
                }else{
                    $this->session->set_flashdata('success','Other Source updated successfully.');
                    redirect('othersource');
                }
            }
        }else{
            return load_view("edit_other_source",$arrData);
        }
    }

    /*
     * index
     * delete()
     * @author Raj Kale
     * @access public
     * @param $id
     * @return void
     */

    public function delete($id)
    {
        $id = decode_id($id);

        $response = $this->Othersource_model->delete_other_source($id);

        if($response['status'] == 'error'){
            $this->session->set_flashdata('error','Failed to delete Other Source');
            redirect('othersource');
        }else{
            $this->session->set_flashdata('success','Other Source deleted successfully.');
            redirect('othersource');
        }
    }

}
