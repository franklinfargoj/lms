<?php
$lead_type = $this->config->item('lead_type');
$lead_status = $this->config->item('lead_status');
?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->
<div class="lead-form">
    <!-- <form> -->
        <?php 
            //Form
            $attributes = array(
                'role' => 'form',
                'id' => 'search_form',
                'class' => 'form',
                'autocomplete' => 'off'
            );
            echo form_open(site_url().'reports/pendancy_leads_reports', $attributes);
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
                        'class' => 'datepicker_recurring_start'
                        
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
                        'class' => 'datepicker_recurring_start'
                        
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
    <!-- </form> -->
    <?php echo form_close();?>
</div>
<!-- BEGIN LEADS -->
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">
            Pendancy Leads
        </h3>
    </div>
</div>
<div class="lead-top">
    <div class="container clearfix">
        <div class="float-left">
            <span class="total-lead">
                Total
            </span>
            <span class="lead-num"> :</span>
        </div>
        <div class="float-right">
            <?php
            $param1 = isset($type) ? $type.'/' : '';
            $param2 = isset($till) ? $till.'/' : '';
            $param3 = isset($status) ? $status.'/' : '';
            $param4 = isset($param) ? $param.'/' : '';
            ?>
            <a href="<?php echo base_url('leads/export_excel_listing/'
                .$param1.$param2.$param3.$param4);?>">
                <img src="<?php echo base_url().ASSETS;?>images/excel-btn.png" alt="btn">
            </a>
        </div>
    </div>
</div>
<?php 
    if(isset($leads) && !empty($leads)){
?>
    <div class="page-content">
        <span class="bg-top"></span>
            <div class="inner-content">
                <div class="container">
                    <table id="sample_3" class="display lead-table">
                        <thead>
                            <tr>
                                <th>
                                    Sr. No.
                                </th>
                                <?php if(in_array($this->session->userdata('admin_type'),array('ZM','BM','EM'))){?>
                                <th>
                                    Zone
                                </th>
                                <?php }?>
                                <?php if(in_array($this->session->userdata('admin_type'),array('BM','EM'))){?>
                                <th>
                                    Branch
                                </th>
                                <?php }?>
                                <?php if(in_array($this->session->userdata('admin_type'),array('EM'))){?>
                                <th>
                                    Employee Name
                                </th>
                                <?php }?>
                                <th>
                                    Source Type
                                </th>
                                <th>
                                    Product Name
                                </th>
                                <th>
                                    Total Pending Leads
                                </th>
                                <?php 
                                    foreach ($lead_status as $key => $value) {
                                        if(!in_array($key,array('AO','Converted','Closed'))){
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
                            </tr>
                        </thead>
                        <?php 
                            $i = 0;
                            foreach ($leads as $key => $value) {
                        ?>
                            <tr>
                                <td>
                                    <?php echo ++$i;?>
                                </td>
                                <?php if(in_array($this->session->userdata('admin_type'),array('ZM','BM','EM'))){?>
                                <td>
                                    <?php 
                                        echo isset($value['zone_id']) ? $value['zone_id'] : 'All';
                                    ?>
                                </td>
                                <?php }?>
                                <?php if(in_array($this->session->userdata('admin_type'),array('BM','EM'))){?>
                                <td>
                                    <?php 
                                        echo isset($value['branch_id']) ? $value['branch_id'] : 'All';
                                    ?>
                                </td>
                                <?php }?>
                                <?php if(in_array($this->session->userdata('admin_type'),array('EM'))){?>
                                <td>
                                    <?php 
                                        echo isset($value['employee_id']) ? $value['employee_id'] : '';
                                    ?>
                                </td>
                                <?php }?>
                                <td>
                                    <?php 
                                        echo !empty($lead_source) ? $lead_source : 'All';
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        echo !empty($product_id) ? $product_id : 'All';
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
                                        if(!in_array($k,array('AO','Converted','Closed'))){
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
                            </tr>
                        <?php
                            }
                        ?>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        <span class="bg-bottom"></span>
    </div>
<?php
    }
?>

<!-- END LEADS-->
<script src="<?php echo base_url().ASSETS;?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().ASSETS;?>js/config.datatable.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() { 
        var table = $('#sample_3');
        var columns = [];
        
        //Initialize datatable configuration
        initTable(table,columns);

        $('body').on('focus',".datepicker_recurring_start", function(){
            $(this).datepicker({dateFormat: 'dd-mm-yy'});

        });
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
    });
</script>
