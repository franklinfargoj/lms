<?php
/**
 * Created by PhpStorm.
 * User: webwerks
 * Date: 8/9/17
 * Time: 12:38 AM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Authenticate extends CI_Controller
{

    /*
     * construct
     * constructor method
     * @author Mukesh Kurmi
	 * @access private
     * @param none
     * @return void
     *
     */
    function __construct()
    {
        // Initialization of class
        parent::__construct();
    }

    public function check_authentication($username,$password)
    {
        //True Response
        $true_res = '{"DBK_LMS_AUTH":
                            {
                                "username":"'. $username.'",
                                "password": "True"
                            }

                    }';

//False Response
        $false_res = '{"DBK_LMS_AUTH":
                            {
                                "username":"'. $username.'",
                                "password": "False"
                            }
                    }';
        if($username == '0006094' && $password == '0006094'){
            echo $true_res;
        }elseif($username == '0003391' && $password =='0003391' ){
            echo $true_res;
        }elseif($username == '0007963' && $password == '0007963'){
            echo $true_res;
        }elseif($username == '0006561' && $password =='0006561' ){
            echo $true_res;
        }elseif($username == '0007965' && $password =='0007965' ){
            echo $true_res;
        }else{
            echo $false_res;
        }
    }


    public function get_records($emp_id)
    {
        //Response for GM
        $gm_response = '{"dbk_lms_emp_record1":
                                    {
                                        "EMPLID": "0006094","deptid": "026223","dbk_dept_type": "GD","dept_discription": "GMO Office","district": "MUMBAI","state": "MH","dbk_state_id": "009846","name": "PURSHOTAM   .","supervisor": "0000805","designation_id": "560601","designation_descr": "DY. GENERAL MANAGER","url": "NA","phone": "9619480263","email": "dummy@dummy.com","detail": "","DBK_LMS_COLL": [
                                        {"DESCR10": "026656","DESCR30": "ZONE1"},{
                                        "DESCR10": "000016","DESCR30": "ZONE2"},{
                                        "DESCR10": "000021","DESCR30": "ZONE3"},{
                                        "DESCR10": "000043","DESCR30": "ZONE4"},{
                                        "DESCR10": "000050","DESCR30": "ZONE5"},{
                                        "DESCR10": "000060","DESCR30": "ZONE6"},{
                                        "DESCR10": "000061","DESCR30": "ZONE7"},{
                                        "DESCR10": "000062","DESCR30": "ZONE8"},{
                                        "DESCR10": "000066","DESCR30": "ZONE9"},{
                                        "DESCR10": "000067","DESCR30": "ZONE10"}]
                                    }
                        }';

//Response for Zone
        $zm_response = '{"dbk_lms_emp_record1":
                                    {
                                        "EMPLID": "0003391","deptid": "026656","dbk_dept_type": "ZD","dept_discription": "Zonal Office","district": "","state": "GJ","dbk_state_id": "026656","name": "JATIN HARIBHAI SARAVYA","supervisor": "0002423","designation_id": "550502","designation_descr": "ZONAL MANAGER-SC-V","url": "NA","phone": "7567123556","email": "dummy@dummy.com","detail": "","DBK_LMS_COLL": [
                                        {"DESCR10": "000564","DESCR30": "BRANCH1"},{
                                        "DESCR10": "000036","DESCR30": "BRANCH2"},{
                                        "DESCR10": "000039","DESCR30": "BRANCH3"},{
                                        "DESCR10": "000064","DESCR30": "BRANCH4"},{
                                        "DESCR10": "000083","DESCR30": "BRANCH5"},{
                                        "DESCR10": "000090","DESCR30": "BRANCH6"},{
                                        "DESCR10": "000091","DESCR30": "BRANCH7"},{
                                        "DESCR10": "000103","DESCR30": "BRANCH8"},{
                                        "DESCR10": "000116","DESCR30": "BRANCH9"},{
                                        "DESCR10": "000118","DESCR30": "BRANCH10"}]
                                    }
                        }';

// Response Branch Manager
        $bm_response = '{"dbk_lms_emp_record1":
                                    {
                                        "EMPLID": "0007963","deptid": "000564","dbk_dept_type": "BR","dept_discription": "Branch","district": "DELHI","state": "DL","dbk_state_id": "026656","name": ". ASHISH SINGH","supervisor": "0002958","designation_id": "520299","designation_descr": "BRANCH MANAGER-SC-II","url": "NA","phone": "9971866702","email": "dummy@dummy.com","detail": "","DBK_LMS_COLL": [
                                        {"DESCR10": "0006561","DESCR30": "SANDEEP KUMAR BHATOA"},{
                                        "DESCR10": "0006288","DESCR30": "NAME2"},{
                                        "DESCR10": "0011350","DESCR30": "NAME3"},{
                                        "DESCR10": "0013243","DESCR30": "NAME4"}]}
                        }';


// Response Employee
        $em_response = '{"dbk_lms_emp_record1":
                                    {
                                        "EMPLID": "0006561","deptid": "000564","dbk_dept_type": "HD","dept_discription": "HO Department","district": "MUMBAI","state": "MH","dbk_state_id": "026656","name": "SANDEEP KUMAR BHATOA","supervisor": "0001598","designation_id": "540401","designation_descr": "CHIEF MANAGER","url": "NA","phone": "9833124070","email": "dummy@dummy.com","detail": "","DBK_LMS_COLL": {"DESCR10": "","DESCR30": ""}
                                    }
                         }';

        // Response Branch Manager
        $bm1_response = '{"dbk_lms_emp_record1":
                                    {
                                        "EMPLID": "0007965","deptid": "000561","dbk_dept_type": "BR","dept_discription": "Branch","district": "DELHI","state": "DL","dbk_state_id": "026656","name": ". ASHISH SINGH","supervisor": "0002958","designation_id": "520299","designation_descr": "BRANCH MANAGER-SC-II","url": "NA","phone": "9971866702","email": "dummy@dummy.com","detail": "","DBK_LMS_COLL": [
                                        {"DESCR10": "0006565","DESCR30": "SANDY"},{
                                        "DESCR10": "0006288","DESCR30": "NAME2"},{
                                        "DESCR10": "0011350","DESCR30": "NAME3"},{
                                        "DESCR10": "0013243","DESCR30": "NAME4"}]}
                        }';

        switch ($emp_id) {
            case '0006094':
                echo $gm_response;
        break;
            case '0003391':
                echo $zm_response;
        break;
            case '0007963':
                echo $bm_response;
        break;
            case '0006561':
                echo $em_response;
        break;
            case '0007965':
                echo $bm1_response;
                break;
            default:
                echo false;
        }

    }
}