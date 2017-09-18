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
            Usage Report
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
    <div class="form">
    <p id="note"><span style="color:red;">*</span> These fields are required</p>
    <div class="lead-form-left" id="l-width">
        <div class="form-control">
            <label>Start Date:<span style="color:red;">*</span></label>   
            <?php 
                if(isset($start_date)){
                    $start_date = date('d/m/Y',strtotime($start_date));
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
                    $end_date = date('d/m/Y',strtotime($end_date));
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
        <a href="javascript:void(0);" class="float-right">
            <img src="<?php echo base_url().ASSETS;?>images/left-nav.png">
            <span><input type="submit" class="custom_button" name="Submit" value="Submit"></span>
            <img src="<?php echo base_url().ASSETS;?>images/right-nav.png">
        </a>
    </div>
    </div>
   </div>
    </div>

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
                Total User Count
            </span>
            <span class="lead-num"> : <?php echo $Total;?></span>
        </div>
        <div class="float-right">
            <a href="javascript:void(0);" class="export_to_excel btn-Download">
                Export to Excel 
            </a>
            &nbsp;|

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
                            <th>
                                Employee Name
                            </th>
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
                    <tbody></tbody>
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
                                    echo isset($value['zone_name']) ? $value['zone_name'] : '';
                                ?>
                            </td>
                            <?php }?>
                            <?php if(in_array($viewName,array('BM','EM'))){?>
                            <td>
                                <?php 
                                    echo isset($value['zone_name']) ? $value['branch_name'] : '';
                                ?>
                            </td>
                            <?php }?>
                            <?php if(in_array($viewName,array('EM'))){?>
                            <td>
                                <?php 
                                    echo isset($value['employee_name']) ? $value['employee_name'] : '';
                                ?>
                            </td>
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
                                <?php 
                                    if(in_array($viewName,array('ZM'))){
                                        if($view == 'branch' || $view == 'employee'){
                                        }else{
                                ?>
                                    <a class="" href="<?php echo site_url('reports/index/usage/branch'.$param)?>">
                                        Branch View
                                    </a>
                                    <span>/</span> 
                                <?php
                                        }
                                     }
                                ?>
                                <?php 
                                    if(in_array($viewName,array('ZM','BM'))){
                                        if($view == 'employee'){
                                        }else{
                                ?>
                                    <a class="" href="<?php echo site_url('reports/index/usage/employee'.$param)?>">
                                        Employee View
                                    </a> 
                                <?php
                                        }
                                     }
                                ?>
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
        <span class="bg-bottom" id="bg-w"></span>
    </div>
</div>
<?php
    }else{?>
    <span class="no_result">No records found</span>
<?php }?>

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
    });
</script>
<script src="<?php echo base_url().ASSETS;?>js/reports.js"></script>

