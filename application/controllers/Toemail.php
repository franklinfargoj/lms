<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Toemail extends CI_Controller {

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
        $this->load->model(['Lead', 'Toemail_model']);
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
        $this->make_bread->add('To Email', '', 0);
        $arrData['breadcrumb'] = $this->make_bread->output();

//        $arrData['userData'] = $this->Lead->get_employee_dump(array('id', 'hrms_id','name','designation','email_id','email_status', 'zone_id','zone_name'),array('designation like' => '%ZONAL MANAGER%', 'email_status' => 'active'),array(),'employee_dump');

        return load_view("list_to_email",$arrData);
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

        $this->make_bread->add('To Email', 'toemail', 0);
        $this->make_bread->add('Edit', '', 1);
        $arrData['breadcrumb'] = $this->make_bread->output();

        $arrData['to_email'] = $this->Toemail_model->view_to_email($id);

        if($this->input->post()){
            $this->form_validation->set_rules('email_status','Status', 'required');
            if ($this->form_validation->run() == FALSE){
                $arrData['has_error'] = 'has-error';
                return load_view("edit_to_email",$arrData);
            }else{
                $update = array(
                    'email_status' => $this->input->post('email_status')
                );

                $response = $this->Toemail_model->edit_to_email($id,$update);
                if($response['status'] == 'error'){
                    $this->session->set_flashdata('error','Failed to edit CC email');
                    redirect('edit_to_email'.encode_id($id));
                }else{
                    $this->session->set_flashdata('success','To email updated successfully.');
                    redirect('toemail');
                }
            }
        }else{
            return load_view("edit_to_email",$arrData);
        }
    }

    public function listToEmail()
    {
        $columns = array(
            1 =>'name',
            2=> 'email_id',
            3=> 'email_status',
        );

        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir = $this->input->post('order')[0]['dir'];

        $totalData = $this->Lead->all_employee_dump_count();

        $totalFiltered = $totalData;

//        $posts = $this->Lead->all_employee_dump($limit,$start,$order,$dir);

        if(!empty($this->input->post('columns')[1]['search']['value']) && !empty($this->input->post('columns')[2]['search']['value'])){
            $search['name'] = $this->input->post('columns')[1]['search']['value'];
            $search['email_id'] = $this->input->post('columns')[2]['search']['value'];

            $posts =  $this->Lead->employee_dump_search_for_all($limit,$start,$search,$order,$dir);

            $totalFiltered = $this->Lead->employee_dump_search_count_for_all($search);
        }
        elseif(!empty($this->input->post('columns')[1]['search']['value'])) {
            $search = $this->input->post('columns')[1]['search']['value'];

            $key = $this->input->post('columns')[1]['data'];

            $posts =  $this->Lead->employee_dump_search($limit,$start,$search,$order,$dir,$key);

            $totalFiltered = $this->Lead->employee_dump_search_count($search,$key);
        }

        elseif(!empty($this->input->post('columns')[2]['search']['value'])){
            $search = $this->input->post('columns')[2]['search']['value'];

            $key = $this->input->post('columns')[2]['data'];

            $posts =  $this->Lead->employee_dump_search($limit,$start,$search,$order,$dir,$key);

            $totalFiltered = $this->Lead->employee_dump_search_count($search,$key);
        }

        else
        {
            $posts = $this->Lead->all_employee_dump($limit,$start,$order,$dir);
        }

        $data = array();
        $i = 1;
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                $nestedData['id'] = $i;
                $nestedData['name'] = $post->name;
                $nestedData['email_id'] = $post->email_id;
                $nestedData['email_status'] = $post->email_status;
//                $nestedData['action'] = "<a class='' href=".site_url('toemail/edit/'. encode_id($post->id));">";
                $nestedData['action'] = "<a class='' href='".site_url('toemail/edit/'.encode_id($post->id))."'>Edit</a>";

                $data[] = $nestedData;

                $i++;
            }
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);
    }

}
