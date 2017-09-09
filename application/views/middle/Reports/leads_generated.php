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
            Leads Generated Report
        </h3>
    </div>
</div>

<div class="lead-form">
    <span class="bg-top"></span>
    <?php 
        //Form
        $attributes = array(
            'role' => 'form',
            'id' => 'search_form',
            'class' => 'form',
            'autocomplete' => 'off'
        );
        echo form_open(site_url().'reports/index/leads_generated', $attributes);
        $data = array(
            'view'   => isset($view) ? $view : '',
            'zone_id'  => isset($zone_id) ? encode_id($zone_id) : '',
            'branch_id' => isset($branch_id) ? encode_id($branch_id) : ''
        );

        echo form_hidden($data);
    ?>
    <div class="lead-form-left">
        <div class="form-control">
            <label>Start Date:</label>   
            <?php 
                if(isset($start_date)){
                    $start_date = $start_date;
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
        <div class="form-control interested-info">
            <?php 
                $attributes = array(
                    'class' => '',
                    'style' => ''
                );
                echo form_label('Product Category:', 'product_category_id', $attributes);

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
                echo form_label('Lead Source:', 'lead_source', $attributes);
            ?>
            <?php 
                if($lead_sources){
                    $options3['']='All';
                    foreach ($lead_sources as $key => $value) {
                        $options3[$value] = $value;
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
        <div class="form-control endDate">
            <label>End Date:</label>   
            <?php 
                if(isset($end_date)){
                    $end_date = $end_date;
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
        <div class="form-control productlist">
            <?php 
                $attributes = array(
                    'class' => '',
                    'style' => ''
                );
                echo form_label('Product:', 'product_id', $attributes);
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
        <a href="javascript:void(0);" class="float-right">
            <img src="<?php echo base_url().ASSETS;?>images/left-nav.png">
            <span><input type="submit" class="custom_button" name="Submit" value="Submit"></span>
            <img src="<?php echo base_url().ASSETS;?>images/right-nav.png">
        </a>
    </div>
    <?php echo form_close();?>
    <span class="bg-bottom"></span>
</div>
<img class="loader" src="<?php echo base_url().ASSETS;?>images/35.gif" style="display:none;">
<?php 
    if(isset($leads) && !empty($leads)){
?>
<script type="text/javascript">
    $('.loader').show();
</script>
<!-- BEGIN LEADS -->
<div id="result" style="display:none;">
    <div class="lead-top">
        <div class="container clearfix">
            <div class="float-left">
                <span class="total-lead">
                    Total Generated Leads
                </span>
                <span class="lead-num"> : <?php echo $Total;?></span>
            </div>
            <div class="float-right">
                <a href="<?php echo base_url('leads/export_excel_listing/');?>">
                    <img src="<?php echo base_url().ASSETS;?>images/excel-btn.png" alt="btn">
                </a>
            </div>
        </div>
    </div>
    <div class="page-content">
        <span class="bg-top"></span>
        <div class="inner-content">
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
                            <th>
                                Source Type
                            </th>
                            <th>
                                Category Name
                            </th>
                            <th>
                                Product Name
                            </th>
                            <th align="center">
                                Total Generated Leads
                            </th>
                            <?php 
                                foreach ($lead_status as $key => $value) {
                                    //if(!in_array($key,array('AO','Converted','Closed'))){
                            ?>
                            <th align="center">
                                <?php
                                    echo $value; 
                                ?>
                            </th>
                            <?php
                                    //}
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
                            <td align="center">
                                <?php echo ++$i;?>
                            </td>
                            <?php if(in_array($viewName,array('ZM','BM','EM'))){?>
                            <td>
                                <?php 
                                    echo isset($value['zone_id']) ? $value['zone_id'] : 'All';
                                ?>
                            </td>
                            <?php }?>
                            <?php if(in_array($viewName,array('BM','EM'))){?>
                            <td>
                                <?php 
                                    echo isset($value['branch_id']) ? $value['branch_id'] : 'All';
                                ?>
                            </td>
                            <?php }?>
                            <?php if(in_array($viewName,array('EM'))){?>
                            <td>
                                <?php 
                                    echo isset($value['created_by']) ? $value['created_by'] : '';
                                ?>
                            </td>
                            <?php }?>
                            <td>
                                <?php 
                                    echo !empty($lead_source) ? ucwords($lead_source) : 'All';
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
                            <td align="center">
                                <?php 
                                    echo $value['total'];
                                ?>
                            </td>
                            <?php 
                            //pe($value['status']);
                                foreach ($lead_status as $k => $v) {
                                    //if(!in_array($k,array('AO','Converted','Closed'))){
                            ?>
                            <td align="center">
                                <?php
                                if(in_array($k,array_keys($value['status']))){
                                        echo $value['status'][$k];
                                    }else{
                                        echo 0;
                                    }
                                ?>
                            </td>
                            <?php
                                    //}
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
                                <?php 
                                    if(in_array($viewName,array('ZM'))){
                                        if($view == 'branch' || $view == 'employee'){
                                        }else{
                                ?>
                                    <a class="" href="<?php echo site_url('reports/index/leads_generated/branch'.$param)?>">
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
                                    <a class="" href="<?php echo site_url('reports/index/leads_generated/employee'.$param)?>">
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
        <span class="bg-bottom"></span>
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
                    $('.productlist').remove();
                    var html = '<div class="form-control productlist">'+resp+'</div>';
                    $( html ).insertAfter( ".endDate" );
                }
            });
        });

        $("#start_date, #end_date").datepicker({
            dateFormat: 'yy-mm-dd'
        });

        $('#search_form').validate({
            rules: {
                start_date: {
                    required: true,
                    dateISO: true
                },
                end_date: {
                    required: true,
                    dateISO: true
                }
            },
            messages: {
                start_date: {
                    required: "Start Date required",
                    dateISO: "Invalid date. Must be formatted yyyy-mm-dd"
                },
                end_date: {
                    required: "End Date required",
                    dateISO: "Invalid date. Must be formatted yyyy-mm-dd"
                }
            },
            submitHandler: function(form) {
                var startDate = $('#start_date').datepicker("getDate"),
                endDate = $('#end_date').datepicker("getDate");
                if (startDate && endDate && startDate > endDate) {
                    alert("Start date is greater than the end date.");
                    $('#start_date').datepicker("setDate", endDate);
                    return false;
                }else{
                    $('.custom_button').attr('disabled','disabled');
                    $('#result').hide();
                    $('.no_result').hide();
                    $('.loader').show();
                    setTimeout(function(){        
                        form.submit();
                    }, 2000);
                }
            }
        });

        setTimeout(function(){        
            $('.loader').hide();
            $('#result').show();
        }, 2000);
    });
</script>
