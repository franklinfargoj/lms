<?php

/**
 *
 * This is App Model
 *
 * @author    Gourav Thatoi
 * @subpackage    Model
 */
class App extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

    }

    

    public function get_generated_lead($where_month_Array = array(), $where_year_Array = array())
    {
        $generated_lead_month = '';
        $generated_lead_year = '';
        if (!empty($where_month_Array) && !empty($where_year_Array)) {
            if (array_key_exists('created_by', $where_year_Array)) {
                $generated_lead_month = $this->db->where($where_month_Array)->count_all_results(Tbl_Leads);
                $generated_lead_year = $this->db->where($where_year_Array)->count_all_results(Tbl_Leads);
            }
            if (array_key_exists('branch_id', $where_year_Array)) {
                $generated_lead_month = $this->db->where($where_month_Array)->count_all_results(Tbl_Leads);
                $generated_lead_year = $this->db->where($where_year_Array)->count_all_results(Tbl_Leads);
            }
            if (array_key_exists('zone_id', $where_year_Array)) {
                $generated_lead_month = $this->db->where($where_month_Array)->count_all_results(Tbl_Leads);
                $generated_lead_year = $this->db->where($where_year_Array)->count_all_results(Tbl_Leads);
            }
            if ($generated_lead_month >= 0 && $generated_lead_year >=0 ) {
                $result['result'] = TRUE;
                $result['data']['generated_mtd'] = $generated_lead_month;
                $result['data']['generated_ytd'] = $generated_lead_year;
                return $result;
            }
            $result['result'] = FALSE;
            $result['data'] = "No generated data found.";
            return $result;
        }
    }
    public function get_converted_lead($where_month_Array = array(), $where_year_Array = array())
    {
        $converted_lead_month = $converted_lead_year = '';
        if (!empty($where_month_Array) && !empty($where_year_Array)) {
            if (array_key_exists('employee_id', $where_year_Array)) {
                $converted_lead_month = $this->db->where($where_month_Array)->count_all_results(Tbl_LeadAssign);
                $converted_lead_year = $this->db->where($where_year_Array)->count_all_results(Tbl_LeadAssign);
            }
            if (array_key_exists('branch_id', $where_year_Array)) {
                $converted_lead_month = $this->db->where($where_month_Array)->count_all_results(Tbl_LeadAssign);
                $converted_lead_year = $this->db->where($where_year_Array)->count_all_results(Tbl_LeadAssign);
            }
            if (array_key_exists('zone_id', $where_year_Array)) {
                $converted_lead_month = $this->db->where($where_month_Array)->count_all_results(Tbl_LeadAssign);
                $converted_lead_year = $this->db->where($where_year_Array)->count_all_results(Tbl_LeadAssign);
            }
            if ($converted_lead_month >= 0 && $converted_lead_year >= 0) {
                $result['result'] = TRUE;
                $result['data']['converted_mtd'] = $converted_lead_year;
                $result['data']['converted_ytd'] = $converted_lead_year;
                return $result;
            }
            $result['result'] = FALSE;
            $result['data'] = "No converted data found.";
            return $result;
        }
    }
}