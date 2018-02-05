<?php
$lead_type = $this->config->item('lead_type');
$lead_status = $this->config->item('lead_status');
$lead_sources = $this->config->item('lead_source');
//pe($status_flow);die;
?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">
            Master Report
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
    echo form_open(site_url().'reports/index/status_flow', $attributes);
    $data = array(
        'view'   => isset($view) ? $view : '',
        'zone_id'  => isset($zone_id) ? encode_id($zone_id) : '',
        'branch_id' => isset($branch_id) ? encode_id($branch_id) : '',
        'export' =>'yes'
    );

    echo form_hidden($data);
?>
<div class="lead-form">
    <span class="bg-top"></span>
    <div class="inner-content">
        
     <div class="container">
<!--         <p style="font-style:italic;"><strong>Purpose :</strong> This report shows number of employees logged into the application from either mobile or web in the time period specified.</p>-->
    <div class="form">
    <p id="note"><span style="color:red;">*</span> These fields are required</p>
        <div class="lead-form-left" id="l-width">
            <div class="form-control">
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
<!--            <div class="form-control interested-info">-->
<!--                --><?php
//                $attributes = array(
//                    'class' => '',
//                    'style' => ''
//                );
//                echo form_label('Product Category:<span style="color:red;">*</span>', 'product_category_id', $attributes);
//
//                if(isset($category_list)){
//                    $options = $category_list;
//                    $js = array(
//                        'id'       => 'product_category_id',
//                        'class'    => ''
//                    );
//                    if(isset($product_category_id)){
//                        $product_category_id = $product_category_id;
//                    }else{
//                        $product_category_id = '';
//                    }
//                    echo form_dropdown('product_category_id', $options , $product_category_id,$js);
//                }
//                ?>
<!--            </div>-->
<!--            <div class="form-control">-->
<!--                --><?php
//                $attributes = array(
//                    'class' => '',
//                    'style' => ''
//                );
//                echo form_label('Lead Source:<span style="color:red;">*</span>', 'lead_source', $attributes);
//                ?>
<!--                --><?php
//                if($lead_sources){
//                    $options3['']='All';
//                    foreach ($lead_sources as $key => $value) {
//                        $options3[$key] = $value;
//                    }
//                    if(isset($lead_source)){
//                        $lead_source = $lead_source;
//                    }else{
//                        $lead_source = '';
//                    }
//                    echo form_dropdown('lead_source', $options3 ,$lead_source,array());
//                }
//                ?>
<!--            </div>-->
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
<!--            <div class="form-control productlist">-->
<!--                --><?php
//                $attributes = array(
//                    'class' => '',
//                    'style' => ''
//                );
//                echo form_label('Product:<span style="color:red;">*</span>', 'product_id', $attributes);
//                ?>
<!--                --><?php
//                if(isset($product_list)){
//                    $options = $product_list;
//                    $js = array(
//                        'id'       => 'product_id',
//                        'class'    => ''
//                    );
//                    if(isset($product_id)){
//                        $product_id = $product_id;
//                    }else{
//                        $product_id = '';
//                    }
//                    echo form_dropdown('product_id', $options ,$product_id,$js);
//                }else{
//                    ?>
<!--                    <select name="product_id">-->
<!--                        <option value="">All</option>-->
<!--                    </select>-->
<!--                    --><?php
//                }
//                ?>
<!--            </div>-->

            <div class="form-control form-submit clearfix">
                <button type="submit" name="Submit" value="Submit" class="full-btn float-right">
                    <img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav" class="left-btn-img">
                    <span class="btn-txt">Submit</span>
                    <img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="left-nav" class="right-btn-img">
                </button>    			    </div>
        </div>
   </div>
    </div>
        <script src="<?php echo base_url().ASSETS;?>js/reports.js"></script>
