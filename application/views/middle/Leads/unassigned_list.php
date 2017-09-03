<div class="page-content">
<div class="container">
<div class="unassigned-content">
<span class="bg-top"></span>
<div class="inner-content">
<div class="container">
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url() . ASSETS; ?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN PRODUCT CATEGOEY-->
<table class="upload-table lead-table" id="sample_3">
    <thead>
    <tr class="top-header">
        <th></th>
        <th><input type="text" name="customername" placeholder="Search Customer Name"></th>
        <th><input type="text" name="customername" placeholder="Search Product Name"></th>
        <th><input type="text" name="customername" placeholder="Search Lead"></th>
        <th><input type="text" name="customername" placeholder="Search Days"></th>
        <th></th>
    </tr>
    <tr>
        <th>Sr. No</th>
        <th>Customer Name</th>
        <th>Product Name</th>
        <th>Lead as (H/W/C)</th>
        <th>Days</th>
        <th>Details</th>
    </tr>
    </thead>
    <tbody>
    <?php if ($unassigned_leads) {
        $i = 0;
        foreach ($unassigned_leads as $key => $value) {
            ?>
            <tr>
                <td>
                    <?php echo ++$i; ?>
                </td>
                <td>
                    <?php echo $value['lead_name']; ?>
                </td>
                <td>
                    <?php echo $value['product_title']; ?>
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
                    <a href="<?php echo site_url('leads/unassigned_leads_details/' . encode_id($value['id'])); ?>">View</a>
                </td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>
</div>
</div>
<span class="bg-bottom"></span>
</div>
</div>
</div>
<script src="<?php echo base_url() . ASSETS; ?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() . ASSETS; ?>js/config.datatable.js"></script>

<script>
    jQuery(document).ready(function () {
        var table = $('#sample_3');
        var columns = [5];


        //Initialize datatable configuration
        initTable(table, columns);

    });
</script>