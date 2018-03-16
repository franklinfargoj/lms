<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Reminder extends CI_Controller
{

    /*
     * construct
     * constructor method
     * @author Franklin Fargoj
     * @access private
     * @param none
     * @return void
     *
     */
    function __construct()
    {
        // Initialization of class
        parent::__construct();
        //is_cli() OR show_404();
        $this->load->model('Reminder_model');
        $this->load->model('Lead');
    }

    /*
     * current_day_schedules
     * Current day employee schedule
     * @author Franklin Fargoj
     * @access private
     * @param none
     * @return void
     */
    public function current_day_schedules(){
        $today_schedule = $this->Reminder_model->get_current_schedule();

        //pe($today_schedule);die;
        //$today_schedule[0]['remind_to']; //pe($today_schedule);die;
        if (count($today_schedule) > 0) {
           foreach ($today_schedule as $key => $value) {
               $contact_no= $this->Lead->get_employee_dump(array('contact_no'),array('hrms_id' => $value['remind_to']),array(),'employee_dump');
               $contact=$contact_no[0]->contact_no;
               send_sms($contact,$value['reminder_text']);
           }
        }
    }
}