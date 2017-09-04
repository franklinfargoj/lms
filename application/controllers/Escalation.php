<?php

class Leads extends CI_Controller
{
    /**
     * Created by PhpStorm.
     * User: webwerk
     * Date: 3/9/17
     * Time: 5:55 PM
     */
    function __construct()
    {
        // Initialization of class
        parent::__construct();
        is_logged_in();
        $this->load->model('Lead');
    }

    public function pending_action()
    {
        $input = get_session();
        $result = array();
        $action = 'list';
        $table = Tbl_Leads . ' as l';
        $join = array();
        $days_diff = $this->config->item('escalation_days_diff');

        $select = array('l.branch_id', 'l.created_on', 'l.created_on', 'l.lead_ticket_range', 'l.product_category_id', 'l.product_id', 'l.created_by_name');
        $join[] = array('table' => Tbl_LeadAssign . ' as la', 'on_condition' => 'la.lead_id = l.id', 'type' => 'left');
        foreach ($days_diff as $keys => $days) {
            $whereArray = array('DATE_FORMAT(l.created_on,"%Y-%m-%d")' => date('Y-m-d', strtotime($days)));
            $result['generated_lead'][$keys] = $this->Lead->get_leads($action, $table, $select, $whereArray, $join, '', '');
        }
        $join = array();
        $select = array('l.branch_id', 'l.created_on', 'la.created_on', 'l.lead_ticket_range', 'l.product_category_id', 'l.product_id', 'l.created_by_name');
        $table = Tbl_LeadAssign . ' as la';
        $join[] = array('table' => Tbl_Leads . ' as l', 'on_condition' => 'l.id= la.lead_id', 'type' => '');
        foreach ($days_diff as $keys => $days) {
            $whereArray = array('DATE_FORMAT(la.created_on,"%Y-%m-%d")' => date('Y-m-d', strtotime($days)));
            $result['assigned_lead'][$keys] = $this->Lead->get_leads($action, $table, $select, $whereArray, $join, '', '');
        }
        pe($result);

    }

    public function unassigned_count_by_branch()
    {
        $table = Tbl_Leads . ' as l';
        $select = array('l.branch_id,COUNT(l.branch_id) as total,l.zone_id');
        $join = array(Tbl_LeadAssign . ' as la', 'la.lead_id = l.id', 'left');
        $whereArray = array('la.lead_id' => NULL);
        $group_by = array('l.branch_id');
        $result['unassigned_count'] = $this->Lead->unassigned_status_count($select, $table, $join, $whereArray, $group_by);
        pe($result);
    }

    /*public function unassigned_count_by_zone()
    {
        $table = Tbl_Leads . ' as l';
        $select = array('l.branch_id,COUNT(l.branch_id) as total,l.zone_id');
        $join = array(Tbl_LeadAssign . ' as la', 'la.lead_id = l.id', 'left');
        $whereArray = array('la.lead_id' => NULL);
        $group_by = array('l.branch_id');
        $result = $this->Lead->unassigned_status_count($select, $table, $join, $whereArray, $group_by);
        //          pe($this->db->last_query());
        pe($result);
    }*/

}