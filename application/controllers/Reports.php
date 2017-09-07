<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Reports extends CI_Controller
{


    /*
     * construct
     * constructor method
     * @author Gourav Thatoi
     * @access private
     * @param none
     * @return void
     * 
     */
    function __construct()
    {
        // Initialization of class
        parent::__construct();
        is_logged_in();
        $this->load->model('Lead');
    }

    public function pendancy_leads_reports()
    {
        $arrData['breadcrumb'] = $this->make_bread->output();
        if($this->input->post()){
            $arrData['start_date']     = $this->input->post('start_date');
            $arrData['end_date']   = $this->input->post('end_date');
            $arrData['product_category_id']    = $this->input->post('product_category_id');
            $arrData['product_id']     = $this->input->post('product_id');
            $arrData['lead_source'] = $this->input->post('lead_source');
            $arrData = $this->search($arrData);
            if(!empty($arrData['product_category_id'])){
                $product_list = $this->Lead->get_all_products(array('category_id' => $arrData['product_category_id'],'is_deleted' => 0,'status' => 'active'));
                $arrData['product_list'] = dropdown($product_list,'All');
            }
        }
        //Get All dropdown
        $category_list = $this->Lead->get_all_category(array('is_deleted' => 0,'status' => 'active'));
        $arrData['category_list'] = dropdown($category_list,'All');
        $arrData['lead_sources'] = $this->Lead->get_enum(Tbl_Leads,'lead_source');
        return load_view('Reports/pendancy_leads_view',$arrData);
    }

    private function search($arrData){

        $login_user = get_session();
        $action = 'list';
        $table = Tbl_Leads.' as l';
        $select = array('COUNT(*) as count','la.status');
        $join = array();
        $group_by = array();

        $join[] = array('table' => Tbl_LeadAssign.' as la','on_condition' => 'la.lead_id = l.id','type' => '');
        $where  = array('la.is_deleted' => 0,'la.is_updated' => 1,'MONTH(la.created_on)' => date('m'));
        if(!empty($arrData['start_date'])){
            $start_date = $arrData['start_date'];
        }
        if(!empty($arrData['end_date'])){
            $end_date = $arrData['end_date'];
        }
        if(!empty($arrData['product_category_id'])){
            $where['l.product_category_id'] = $arrData['product_category_id'];
        }
        if(!empty($arrData['product_id'])){
            $where['l.product_id'] = $arrData['product_id'];
        }
        if(!empty($arrData['lead_source'])){
            $where['l.lead_source'] = $arrData['lead_source'];
        }
        
        //Login 
        if($login_user['designation_name'] == 'EM'){
            $select[] = 'la.employee_id';
            $select[] = 'la.branch_id';
            $select[] = 'la.zone_id';

            $where['la.branch_id'] = $login_user['branch_id'];
            $where['la.zone_id'] = $login_user['zone_id'];

            $group_by[]  =  'la.employee_id';
        }
        if($login_user['designation_name'] == 'BM'){
            $select[] = 'la.branch_id';
            $select[] = 'la.zone_id';
            
            $where['la.zone_id'] = $login_user['zone_id'];

            $group_by[] = 'la.branch_id';
        }
        if($login_user['designation_name'] == 'ZM'){
            $select[] = 'la.zone_id';
            $group_by[] = 'la.zone_id';
        }
        if($login_user['designation_name'] == 'RM'){
            $select[] = 'la.zone_id';
            $group_by[] = 'la.zone_id';
        }
        $group_by[] = 'la.status';
        $leads = $this->Lead->get_leads($action,$table,$select,$where,$join,$group_by,$order_by = array());
        //pe($this->db->last_query());
        /*pe($leads);
        exit;*/
        $arrData['leads'] = array();
        foreach ($leads as $key => $value) {
            /*if($login_user['designation_name'] == 'EM'){
                $arrData['leads'][$value['employee_id']][] = array(
                    'employee_id' => $value['employee_id'],
                    'branch_id' => $value['branch_id'],
                    'zone_id' => $value['zone_id'],
                    'status' => $value['status'],
                    'count' => $value['count'],
                );
                
            }*/
            if($login_user['designation_name'] == 'BM'){
                $arrData['leads'][$value['branch_id']]['branch_id'] = $value['branch_id'];
                $arrData['leads'][$value['branch_id']]['zone_id'] = $value['zone_id'];
                if(!in_array($value['status'],array('AO','Converted','Closed'))){
                    if(isset($arrData['leads'][$value['branch_id']]['total'])){
                        $arrData['leads'][$value['branch_id']]['total'] += $value['count'];
                    }else{
                        $arrData['leads'][$value['branch_id']]['total'] = $value['count'];    
                    }
                    $arrData['leads'][$value['branch_id']]['status'][$value['status']] = $value['count'];
                }
            }
            /*if($login_user['designation_name'] == 'ZM'){
                $arrData['leads'][$value['zone_id']][]  = array(
                    'zone_id' => $value['zone_id'],
                    'status' => $value['status'],
                    'count' => $value['count'],
                );
                
            }
            if($login_user['designation_name'] == 'RM'){
                $arrData['leads'][$value['zone_id']] = array();
                $data = array(
                    'zone_id' => $value['zone_id'],
                );
                array_push($arrData['leads'][$value['zone_id']],$data);
            }*/
        }
        //pe($arrData);
        return $arrData;
    }

    

}