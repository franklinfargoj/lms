<?php
$lead_type = $this->config->item('lead_type');
$lead_status = $this->config->item('lead_status');
$lead_sources = $this->config->item('lead_source');
//pe($lead_sources);
?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">
            Pendency Leads
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
    echo form_open(site_url().'reports/index/pendancy_leads_reports', $attributes);
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
        <div class="container ">
            <p style="font-style:italic;"><strong>Purpose :</strong> This report shows count of those leads which have not been acted upon in due time.</p>
        <div class="form">
        <p id="note"><span style="color:red;">*</span> These fields are required</p>
            <div class="lead-form-left">
<!--                    <div class="form-control">-->
<!--                        <label id="cal">Start Date:<span style="color:red;">*</span> </label>-->
<!--                        --><?php
//                            if(isset($start_date)){
//                                $start_date = date('d-m-Y',strtotime($start_date));
//                            }else{
//                                $start_date = '';
//                            }
//                            $data = array(
//                                'type'  => 'text',
//                                'name'  => 'start_date',
//                                'id'    => 'start_date',
//                                'class' => 'datepicker_recurring_start',
//                                'value' => $start_date
//
//                            );
//                            echo form_input($data);
//                        ?>
<!--                    </div>-->
                    <div class="form-control interested-info">
                        <?php 
                            $attributes = array(
                                'class' => '',
                                'style' => ''
                            );
                            echo form_label('Product Category:<span style="color:red;">*</span> ', 'product_category_id', $attributes);

                            if(isset($category_list)){
                                $options = $category_list;
                                $js = array(
                                        'id'       => 'product_category_id',
                                        'class'    => ''
                                );
                                if(isset($product_category_id)){
                                    $product_category_id = $product_category_id;
                                }else{
                                    $product_category_id = '';
                                }
                                echo form_dropdown('product_category_id', $options , $product_category_id,$js);    
                            }
                        ?>
                    </div>
                    <div class="form-control">
                        <?php 
                            $attributes = array(
                                'class' => '',
                                'style' => ''
                            );
                            echo form_label('Lead Source:<span style="color:red;">*</span> ', 'lead_source', $attributes);
                        ?>
                        <?php 
                            if($lead_sources){
                                //echo "<pre>";print_r($lead_sources);die;
                                $options3['']='All';
                                foreach ($lead_sources as $key => $value) {
                                    $options3[$key] = $value;
                                }
                                if(isset($lead_source)){
                                    $lead_source = $lead_source;
                                }else{
                                    $lead_source = '';
                                }
                                echo form_dropdown('lead_source', $options3 ,$lead_source,array());
                            }
                        ?>
                    </div>
            </div>
            <div class="lead-form-right">
<!--                <div class="form-control endDate">-->
<!--                    <label>End Date:<span style="color:red;">*</span> </label>-->
<!--                    --><?php
//                        if(isset($end_date)){
//                            $end_date = date('d-m-Y',strtotime($end_date));
//                        }else{
//                            $end_date = '';
//                        }
//                        $data = array(
//                            'type'  => 'text',
//                            'name'  => 'end_date',
//                            'id'    => 'end_date',
//                            'class' => 'datepicker_recurring_start',
//                            'value' => $end_date
//
//                        );
//                        echo form_input($data);
//                    ?>
<!--                </div>-->
                <div class="form-control productlist">
                    <?php 
                        $attributes = array(
                            'class' => '',
                            'style' => ''
                        );
                        echo form_label('Product:<span style="color:red;">*</span>', 'product_id', $attributes);
                    ?>
                    <?php 
                        if(isset($product_list)){
                            $options = $product_list;
                            $js = array(
                                    'id'       => 'product_id',
                                    'class'    => ''
                            );
                            if(isset($product_id)){
                                $product_id = $product_id;
                            }else{
                                $product_id = '';
                            }
                            echo form_dropdown('product_id', $options ,$product_id,$js);    
                        }else{
                    ?>
                        <select name="product_id">
                            <option value="">All</option>
                        </select>
                    <?php 
                        }
                    ?>
                </div>
            </div>
            <div class="form-control form-submit clearfix">
                <button type="submit" name="Submit" value="Submit" class="full-btn float-right">
<img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav" class="left-btn-img">
<span class="btn-txt">Submit</span>
<img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="left-nav" class="right-btn-img">
</button>    			            </div>
        </div>
            <?php if($this->session->userdata('admin_type') != 'Super admin2'){?>
        <img class="loader" src="<?php echo base_url().ASSETS;?>images/35.gif" alt="35" style="display:none;">
        <?php 
            if(isset($leads) && !empty($leads)){
        ?>
        <script type="text/javascript">
            $('.loader').show();
        </script>
        <div class="lead-top result"  style="display:none;">
            <div class="container clearfix">
                <div class="float-left top-lead">
                    <span class="total-lead ">
                        <?php if(in_array($this->session->userdata('admin_type'),array('ZM','GM')) && $view == 'branch'){ ?>
                            Total Pending Leads Of Your Zone
                        <?php }else{?>
                            Total Pending Leads
                        <?php }?>

                    </span>
                    <span class="lead-num"> : <?php echo $Total;?></span>
                </div>
                <div class="float-right">
                    <?php if(in_array($this->session->userdata('admin_type'),array('ZM','GM'))){ ?>

                            <?php
                            if(!isset($product_category_id) || $product_category_id == ''){
                                $product_category_id1= 'all';
                            }else{
                                $product_category_id1 =$product_category_id;
                            }
                            if(!isset($product_id) || $product_id == ''){
                                $product_id1= 'all';
                            }else{
                                $product_id1=$product_id;
                            }
                            if(!isset($lead_source) || $lead_source == ''){
                                $lead_source1= 'all';
                            }else{
                                $lead_source1=$lead_source;
                            }
                            $chart_param = $start_date.'/'.$end_date.'/'.$product_category_id1.'/'.$product_id1.'/'.$lead_source1;
                            $chart_param=encode_id($chart_param);
                            ?>
                            <a href="<?php echo site_url('charts/index/pendancy_leads_reports/'.$chart_param)?>" class="btn-Download">
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
        
    <?php echo form_close();?>
    <div class="result" style="display:none;">
        <div class="page-content">
            
            
                <div class="container">
                    <table id="sample_3" class="display lead-table">
                        <thead>
                            <tr>
                                <th>
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
<!--                                    <th>-->
<!--                                        HRMS ID-->
<!--                                    </th>-->
                                    <th>
                                        Employee Name
                                    </th>
<!--                                    <th>-->
<!--                                        Designation-->
<!--                                    </th>-->
                                <?php }?>
                                <th>
                                    Source Type
                                </th>
                                <th>
                                    Category Name
                                </th>
                                <th>
                                    Product Name
                                </th>
                                <th>
                                    Total Pending Leads
                                </th>
                                <?php
                                    foreach ($lead_status as $key => $value) {
                                        if(!in_array($key,array('AO','Converted','Closed','NI'))){
                                ?>
                                <th>
                                    <?php
                                        echo $value;
                                    ?>
                                </th>
                                <?php
                                        }
                                    }
                                ?>
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
                                <td>
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
                                        echo isset($value['branch_name']) ? ucwords(strtolower($value['branch_name'])) : '';
                                    ?>
                                </td>
                                <?php }?>
                                <?php if(in_array($viewName,array('EM'))){?>
<!--                                    <td>-->
<!--                                        --><?php
//                                            echo isset($value['employee_id']) ? $value['employee_id'] : '';
//                                        ?>
<!--                                    </td>-->
                                    <td>
                                        <?php
                                            echo isset($value['employee_name']) ? ucwords(strtolower($value['employee_name'])) : '';
                                        ?>
                                    </td>
<!--                                    <td>-->
<!--                                        --><?php
//                                            echo isset($value['designation']) ? $value['designation'] : '';
//                                        ?>
<!--                                    </td>-->
                                <?php }?>
                                <td>
                                    <?php
                                        echo !empty($lead_source) ? ucwords($lead_sources[$lead_source]) : 'All';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        echo !empty($category) ? ucwords($category) : 'All';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        echo !empty($product) ? ucwords($product) : 'All';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        echo $value['total'];
                                    ?>
                                </td>
                                <?php
                                //pe($value['status']);
                                    foreach ($lead_status as $k => $v) {
                                        if(!in_array($k,array('AO','Converted','Closed','NI'))){
                                ?>
                                <td>
                                    <?php
                                    if(in_array($k,array_keys($value['status']))){
                                            echo $value['status'][$k];
                                        }else{
                                            echo 0;
                                        }
                                    ?>
                                </td>
                                <?php
                                        }
                                    }
                                ?>
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
                                        <a class="" href="<?php echo site_url('reports/index/pendancy_leads_reports/branch'.$param)?>">
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
                                                       href="<?php echo site_url('reports/index/pendancy_leads_reports/employee' . $param) ?>">
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
                                                    <a class="" href="<?php echo site_url('reports/index/pendancy_leads_reports/branch'.$param)?>">
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
                                                       href="<?php echo site_url('reports/index/pendancy_leads_reports/employee' . $param) ?>">
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

            
     
    
<?php
    }else{?>
    <span class="no_result">No records found</span>
<?php }
}else{?>
    <div class="container clearfix">
        <div class="float-right">&nbsp;
            <a href="javascript:void(0);" class="export_national btn-Download">
                Download Bank Data
            </a>
        </div>
    </div>
<?php }?>
</div>
</div>
<span class="bg-bottom" id="bg-w"></span>
<!-- END LEADS-->
<script src="<?php echo base_url().ASSETS;?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().ASSETS;?>js/config.datatable.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() { 
        var table = $('#sample_3');
        console.log(table.length);
        if(table.length > 0){
            var columns = [5];
        
            //Initialize datatable configuration
            initTable(table,columns);
        }
        
        $('#product_category_id').change(function () {
            var csrf = $("input[name=csrf_dena_bank]").val();
            var category_id = $(this).val();
            $.ajax({
                method: "POST",
                url: baseUrl + "leads/productlist",
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    category_id: category_id,
                    select_label:'All'
                }
            }).success(function (resp) {
                if (resp) {
                    $('.productlist').html(resp);
//                    var html = '<div class="form-control productlist">'+resp+'</div>';
//                    $( html ).insertAfter( ".endDate" );
                }
            });
        });
    });
</script>
<script src="<?php echo base_url().ASSETS;?>js/reports.js"></script>
