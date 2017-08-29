
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->

<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Branch Manager</h3>
    </div>
</div>
<div class="page-content">
    <div class="container">
        <table class="upload-table" id="sample_3">
            <thead>
            <tr class="top-header">
                <th></th>
                <th><input type="text" name="customername" placeholder="Search Employee Name"></th>
                <th><input type="text" name="customername" placeholder="Search Generated Leads"></th>
                <th><input type="text" name="customername" placeholder="Search Converted Leads"></th>
                <th></th>
            </tr>
            <tr>
                <th>Sr. No</th>
                <th>Employee Name</th>
                <th colspan="2">MTD</th>
                <th>Action</th>
            </tr>

            </thead>
            <tbody>
            <tr>
                <td></td>
                <td></td>
                <td>Genrated Leads</td>
                <td>Converted Leads</td>
                <td></td>
            </tr>
            <?php if(!empty($leads['generated_leads'])){
                $i = 0;
                foreach ($leads['generated_leads'] as $key => $value) {
                    ?>
                    <tr>
                        <td>
                            <?php echo ++$i;?>
                        </td>
                        <td>
                            <?php echo $value['created_by_name'];?>
                        </td>
                        <td>
                            <?php echo $value['total'];?>
                        </td>
                        <td>
                            <?php $converted = 0;
                            if(!empty($leads['converted_leads'])) {
                                if (in_array($value['created_by'], $leads['all_converted_created_by'])) {
                                    foreach ($leads['converted_leads']as $k => $converted_value) {
                                        if ($value['created_by'] == $converted_value['created_by'])
                                            $converted = $converted_value['total'];
                                    }
                                }
                            }
                            echo $converted;
                            ?>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <?php
                }
            }?>
            </tbody>
        </table>
    </div>
</div>
<script src="<?php echo base_url().ASSETS;?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().ASSETS;?>js/config.datatable.js"></script>
<script type="text/javascript">

    jQuery(document).ready(function() {
        var table = $('#sample_3');
        var columns = [4];

        //Initialize datatable configuration
        initTable(table,columns);

    });

</script>
