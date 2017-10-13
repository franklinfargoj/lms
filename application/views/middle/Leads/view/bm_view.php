
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->

<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Branch Manager</h3>
    </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <div class="lead-top clearfix ">
                <div class="float-left">
        <!--            <span class="total-lead">-->
        <!--                Total-->
        <!--            </span>-->
        <!--            <span class="lead-num"> : --><?php //echo count($leads);?><!--</span>-->
                </div>
                <div class="float-right">
                    <a href="<?php echo base_url('dashboard/home_excel');?>">
                        <img src="<?php echo base_url().ASSETS;?>images/excel-btn.png" alt="btn">
                    </a>
                </div>
            </div>
            <table class="display lead-table" id="sample_3">
                <thead>
                <tr class="top-header">
                    <th></th>
                    <th style="text-align:left;"><input type="text" name="customername" placeholder="Search Employee Name"></th>
                    <th><!-- <input type="text" name="customername" placeholder="Search Generated Leads"> --></th>
                    <th><!-- <input type="text" name="customername" placeholder="Search Converted Leads"> --></th>
                    <th><!-- <input type="text" name="customername" placeholder="Search Generated Leads"> --></th>
                    <th><!-- <input type="text" name="customername" placeholder="Search Converted Leads"> --></th>
                    <th></th>
                </tr>
                <tr>
                    <th style="text-align:center">Sr. No</th>
                    <th  style="text-align:left;">Employee Name</th>
                    <th style="text-align:center">Generated Leads (This Month)</th>
                    <th style="text-align:center">Generated Leads (This Year)</th>
                    <th style="text-align:center">Converted Leads (This Month)</th>
                    <th style="text-align:center">Converted Leads (This Year)</th>
                    <th  style="text-align:left;">Action</th>
                </tr>

            </thead>
            <tbody>
            <?php if(!empty($leads)){
                $i = 0;
                foreach ($leads as $key => $value) {
                    ?>
                    <tr>
                        <td style="text-align:center">
                            <?php echo ++$i;?>
                        </td>
                        <td >
                            <?php echo ucwords(strtolower($value['created_by_name']));?>
                        </td>
                        <td style="text-align:center">
                            <?php echo $value['total_generated_mtd'];?>
                        </td>
                        <td style="text-align:center">
                            <?php echo $value['total_generated_ytd'];?>
                        </td>
                        <td style="text-align:center">
                            <?php echo $value['total_converted_mtd'];?>
                        </td>
                        <td style="text-align:center">
                            <?php echo $value['total_converted_ytd'];?>
                        </td>
                        <td>
                            <a href="<?php echo site_url('dashboard/leads_status/generated/'.encode_id($value['created_by']));?>">View</a>
                            <span>|</span> 
                            <a href="<?php echo base_url('dashboard/leads_performance/'.encode_id($value['created_by']));?>">Performance</a>
                        </td>
                    </tr>
                    <?php
                }
            }?>
            </tbody>
        </table>
    </div>
    <span class="bg-bottom" id="bg-w"></span>
</div>
<script src="<?php echo base_url().ASSETS;?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().ASSETS;?>js/config.datatable.js"></script>
<script type="text/javascript">

    jQuery(document).ready(function() {
        var table = $('#sample_3');
        var columns = [6];

        //Initialize datatable configuration
        initTable(table,columns);

    });

</script>
