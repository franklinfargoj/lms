<?php
$lead_type = $this->config->item('lead_type');
$lead_status = $this->config->item('lead_status');
?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">
            Login
        </h3>

    </div>
</div>
<?php 
    //Form
    $attributes = array(
        'role' => 'form',
        'id' => 'search_form',
        'class' => 'form',
        'autocomplete' => 'off'
    );
    echo form_open(site_url().'reports/index/usage', $attributes);
    $data = array(
        'view'   => isset($view) ? $view : '',
        'zone_id'  => isset($zone_id) ? encode_id($zone_id) : '',
        'branch_id' => isset($branch_id) ? encode_id($branch_id) : ''
    );

    echo form_hidden($data);
?>
<div class="lead-form">
    <span class="bg-top"></span>
    <div class="inner-content">
        
     <div class="container">
         <p style="font-style:italic;"><strong>Purpose :</strong> This report shows number of employees logged into the application from either mobile or web in the time period specified.</p>
    <div class="form">
    <p id="note"><span style="color:red;">*</span> These fields are required</p>
    <div class="lead-form-left" id="l-width">
        <div class="form-control date-c">
        <!-- <p>sdadasd</p> -->
            <label>Start Date:<span style="color:red;">*</span></label>   
            <?php 
                if(isset($start_date)){
                    $start_date = date('d-m-Y',strtotime($start_date));
                }else{
                    $start_date = '';
                }
                $data = array(
                    'type'  => 'text',
                    'name'  => 'start_date',
                    'id'    => 'start_date',
                    'class' => 'datepicker_recurring_start',
                    'value' => $start_date
                    
                );
                echo form_input($data);
            ?>
        </div>
        
    </div>
    <div class="lead-form-right" id="r-width">
        <div class="form-control endDate">
            <label>End Date:<span style="color:red;">*</span></label>   
            <?php 
                if(isset($end_date)){
                    $end_date = date('d-m-Y',strtotime($end_date));
                }else{
                    $end_date = '';
                }
                $data = array(
                    'type'  => 'text',
                    'name'  => 'end_date',
                    'id'    => 'end_date',
                    'class' => 'datepicker_recurring_start',
                    'value' => $end_date
                    
                );
                echo form_input($data);
            ?>
        </div>
    
    <div class="form-control form-submit clearfix">
        <button type="submit" name="Submit" value="Submit" id="su" class="full-btn float-right">
<img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav" class="left-btn-img">
<span class="btn-txt">Submit</span>
<img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="left-nav" class="right-btn-img">
</button>    			    </div>
    </div>
   </div>
    </div>
<?php if($this->session->userdata('admin_type') != 'Super admin2'){?>
<img class="loader" src="<?php echo base_url().ASSETS;?>images/35.gif" style="display:none;">
<?php 
    if(isset($leads) && !empty($leads)){
?>
<script type="text/javascript">
    $('.loader').show();
</script>
<!-- BEGIN LEADS -->
    <div class="lead-top result" style="display:none;">
        <div class="container clearfix">
            <div class="float-left">
                <span class="total-lead">
                    <?php if(in_array($this->session->userdata('admin_type'),array('ZM','GM')) && $view == 'branch'){ ?>
                        Total Users Count Of Your Zone
                    <?php }else{?>
                    Total Users Count
                    <?php }?>
                </span>
                <span class="lead-num"> : <?php echo $Total;?></span>
            </div>
            <div class="float-right">
                <?php if(in_array($this->session->userdata('admin_type'),array('ZM','GM'))){ ?>

                        <?php
                        $chart_param = $start_date.'/'.$end_date;
                        $chart_param=encode_id($chart_param);
                        ?>
                        <a href="<?php echo site_url('charts/index/usage/'.$chart_param)?>" class="btn-Download">
                            Chart View
                        </a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                <?php }?>
                <a href="javascript:void(0);" class="export_to_excel btn-Download">
                    Export to Excel 
                </a>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <a href="javascript:void(0);" class="export_national btn-Download">
                    Download Bank Data
                </a>
            </div>
        </div>
    </div>
<?php echo form_close();?>
    <div class="result" style="display:none;">
        <div class="page-content">
            <div class="container">
                <table id="sample_3" class="display lead-table">
                    <thead>
                        <tr>
                            <th align="center">
                                Sr. No.
                            </th>
                            <?php if(in_array($viewName,array('ZM','BM','EM'))){?>
                            <th>
                                Zone
                            </th>
                            <?php }?>
                            <?php if(in_array($viewName,array('BM','EM'))){?>
                            <th>
                                Branch
                            </th>
                            <?php }?>
                            <?php if(in_array($viewName,array('EM'))){?>
<!--                                <th>-->
<!--                                    HRMS ID-->
<!--                                </th>-->
                                <th>
                                    Employee Name
                                </th>
<!--                                <th>-->
<!--                                    Designation-->
<!--                                </th>-->
                            <?php }?>
                            <?php if(in_array($viewName,array('EM'))){?>
                            <th align="center">
                                Logged in count
                            </th>
                            <?php }else{?>
                            <th align="center">
                                Total User
                            </th>
                            <th align="center">
                                Logged in User
                            </th>
                            <th align="center">
                                Not Logged in User
                            </th>
                            <?php }?>
                            <?php if(in_array($viewName,array('ZM','BM'))){?>
                            <th>
                                Action
                            </th>
                            <?php }?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        $i = 0;
                        foreach ($leads as $key => $value) {
                    ?>
                        <tr>
                            <td align="center">
                                <?php echo ++$i;?>
                            </td>
                            <?php if(in_array($viewName,array('ZM','BM','EM'))){?>
                            <td>
                                <?php 
                                    echo isset($value['zone_name']) ? ucwords(strtolower($value['zone_name'])) : '';
                                ?>
                            </td>
                            <?php }?>
                            <?php if(in_array($viewName,array('BM','EM'))){?>
                            <td>
                                <?php 
                                    echo isset($value['zone_name']) ? ucwords(strtolower($value['branch_name'])) : '';
                                ?>
                            </td>
                            <?php }?>
                            <?php if(in_array($viewName,array('EM'))){?>
<!--                                <td>-->
<!--                                    --><?php
//                                    echo isset($value['employee_id']) ? $value['employee_id'] : '';
//                                    ?>
<!--                                </td>-->
                                <td>
                                    <?php
                                    echo isset($value['employee_name']) ? ucwords(strtolower($value['employee_name'])) : '';
                                    ?>
                                </td>
<!--                                <td>-->
<!--                                    --><?php
//                                    echo isset($value['designation']) ? $value['designation'] : '';
//                                    ?>
<!--                                </td>-->
                            <?php }?>
                            <?php if(in_array($viewName,array('EM'))){?>
                                <td align="center">
                                    <?php echo  $value['total'];?>
                                </td>
                                <?php }else{?>
                                <td align="center">
                                    <?php echo  isset($value['total_user']) ? $value['total_user'] : 0;?>
                                </td>
                                <td align="center">
                                    <?php echo  $value['total'];?>
                                </td>
                                <td align="center">
                                    <?php echo isset($value['not_logged_in']) ? $value['not_logged_in'] : 0;?>
                                </td>
                            <?php }?>
                            <?php if(in_array($viewName,array('ZM','BM'))){
                                $param = '';
                                if(isset($value['zone_id'])){
                                    $param .= '/'.encode_id($value['zone_id']);
                                }
                                if(isset($value['branch_id'])){
                                    $param .= '/'.encode_id($value['branch_id']);   
                                }
                            ?>
                                <td>
                                    <?php if($this->session->userdata('admin_type') == 'ZM' || $this->session->userdata('admin_type') == 'BM'){?>
                                        <?php if ($i == 1 || $view == 'branch') {?>
                                            <?php
                                            if(in_array($viewName,array('ZM'))){
                                                if($view == 'branch' || $view == 'employee'){
                                                }else{
                                                    ?>
                                                    <a class="" href="<?php echo site_url('reports/index/usage/branch'.$param)?>">
                                                        Branch View
                                                    </a>
                                                    <span>|</span>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <?php
                                            if(in_array($viewName,array('ZM','BM'))){
                                                if($view == 'employee'){
                                                }else {

                                                    ?>
                                                    <a class=""
                                                       href="<?php echo site_url('reports/index/usage/employee' . $param) ?>">
                                                        Employee View
                                                    </a>
                                                    <?php
                                                }

                                            }
                                            ?>
                                        <?php }?>
                                    <?php }else{?>

                                        <?php
                                        if(in_array($viewName,array('ZM'))){
                                            if($view == 'branch' || $view == 'employee'){
                                            }else{
                                                ?>
                                                <a class="" href="<?php echo site_url('reports/index/usage/branch'.$param)?>">
                                                    Branch View
                                                </a>
                                                <span>|</span>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <?php
                                        if(in_array($viewName,array('ZM','BM'))){
                                            if($view == 'employee'){
                                            }else {

                                                ?>
                                                <a class=""
                                                   href="<?php echo site_url('reports/index/usage/employee' . $param) ?>">
                                                    Employee View
                                                </a>
                                                <?php
                                            }

                                        }
                                        ?>

                                    <?php }?>
                                </td>
                            <?php }?>
                        </tr>
                    <?php
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    </div>
    
</div>
<?php
    }else{?>
    <span class="no_result">No records found</span>
<?php }?>
<?php }else{?>
<div class="container clearfix">
    <div class="float-right">&nbsp;
        <a href="javascript:void(0);" class="export_national btn-Download">
            Download Bank Data
        </a>
    </div>
</div>
<?php }?>
<span class="bg-bottom"></span>
<!-- END LEADS-->
<script src="<?php echo base_url().ASSETS;?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().ASSETS;?>js/config.datatable.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() { 
        var table = $('#sample_3');
        if(table.length > 0){
            var columns = [3];
        
            //Initialize datatable configuration
            initTable(table,columns);
        }

        $('#su').click(function(){
            $('#su').hide();
        })
    });
</script>
<script src="<?php echo base_url().ASSETS;?>js/reports.js"></script>

