<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url() . ASSETS; ?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN PRODUCT CATEGOEY-->
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">
            <?php 
                echo ucwords($lead_source);
            ?>
        </h3>
    </div>
</div>
<?php 
    //Form
    $attributes = array(
        'role' => 'form',
        'id' => 'detail_form',
        'class' => 'form',
        'autocomplete' => 'off'
    );
    echo form_open(site_url().'leads/assign_multiple', $attributes);
?>
<div class="lead-top">
    <div class="container clearfix">
        <div class="float-left">
            <span class="total-lead">
                Total
            </span>
            <span class="lead-num"> : <?php echo count($unassigned_leads);?></span>
        </div>
        <div class="float-right">
            <?php 
                if(ucwords($lead_source) != 'Walk-in'){
                    if ($unassigned_leads) {
            ?>
                <div class="form-control">
                    <label>Assign To:</label>   
                    <select name="assign_to">
                        <option value="">Select Employee</option>
                        <option value="2">Employee 1</option>
                    </select>
                </div>
                <div class="form-control form-submit clearfix">
                    <a href="javascript:void(0);" class="float-right">
                            <img src="<?php echo base_url().ASSETS;?>images/left-nav.png">
                            <span><input type="submit" class="custom_button" value="Submit" /></span>
                            <img src="<?php echo base_url().ASSETS;?>images/right-nav.png">
                    </a>
                </div>
            <?php
                    }   
                }
            ?>
            <!-- <a href="">
                <img src="<?php echo base_url().ASSETS;?>images/excel-btn.png" alt="btn">
            </a> -->
        </div>
    </div>
</div>

<table class="upload-table lead-table" id="sample_3">
    <thead>
    <tr class="top-header">
        <th></th>
        <th><input type="text" name="customername" placeholder="Search Customer Name"></th>
        <th><input type="text" name="customername" placeholder="Search Product Name"></th>
        <th><input type="text" name="customername" placeholder="Search Days"></th>
        <th></th>
    </tr>
    <tr>
        <th>Sr. No</th>
        <th>Customer Name</th>
        <th>Product Name</th>
        <th>Elapsed Days</th>
        <th>Details</th>
    </tr>
    </thead>
    <tbody>
    <?php 
        if ($unassigned_leads) {
            $i = 0;
            foreach ($unassigned_leads as $key => $value) {
    ?>
            <tr>
                <td>
                    <?php 
                        echo ++$i; 
                    ?>

                </td>
                <td>
                    <?php echo ucwords($value['lead_name']); ?>
                </td>
                <td>
                    <?php echo ucwords($value['product_title']); ?>
                </td>
                <td>
                    <?php $created_date = explode(' ', $value['created_on']);

                    $now = date_create(date('Y-m-d')); // or your date as well
                    //echo $created_date[0];
                    $generated_date = date_create($created_date[0]);
                    $datediff = date_diff($now, $generated_date);

                    echo $datediff->format("%a days");
                    ?>
                </td>
                <td>
                    <a href="<?php echo site_url('leads/unassigned_leads_details/'.encode_id($lead_source).'/'. encode_id($value['id'])); ?>">View</a>
                </td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>
<?php 
    echo form_close();
?>
<script src="<?php echo base_url() . ASSETS; ?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() . ASSETS; ?>js/config.datatable.js"></script>

<script type="text/javascript">
    jQuery(document).ready(function() {
        var table = $('#sample_3');
        var columns = [4];


        //Initialize datatable configuration
        initTable(table, columns);

    });
</script>