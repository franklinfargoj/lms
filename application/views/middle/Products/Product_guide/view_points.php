<!-- BEGIN PAGE LEVEL STYLES -->
    <link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->

<!-- BEGIN PRODUCT -->
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Points</h3>
    </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <div class="lead-top clearfix">
                <div class="float-left">
                    <span class="total-lead">Total</span>
                    <span class="lead-num"> : <?php echo count($pointsData);?></span>
                </div>
                <div class="float-right">
                    <span class="lead-num"><a href="<?php echo site_url('product_guide/manage_points/'.encode_id($product[0]['id']));?>">Add</a></span>
                    <span class="lead-num"><a href="<?php echo site_url('product');?>">Back</a></span>
                </div>
            </div>
            <table id="sample_3" class="display lead-table">
                <thead>
                    <!-- <tr class="top-header">
                        <th></th>
                        <th><input type="text" name="customername" placeholder="Search Title"></th>
                        <th>
                            <?php 
                                $options = $categorylist;
                                echo form_dropdown('category_id', $options ,'',array());
                            ?>
                        </th>
                        <th>
                            <select name="status">
                                <option value="">Select status</option>
                                <option value="active">active</option>
                                <option value="inactive">inactive</option>
                            </select>
                        </th>
                        <th></th>
                        <th></th>
                    </tr> -->
                    <tr>
                        <th style="text-align:center">Sr. No.</th>
                        <th>Min Range</th>
                        <th>Max Range</th>
                        <th>Points</th>
                        <th>Action</th>
                    </tr>
                </thead>
                    <tbody>
                        <?php if($pointsData){
                            $i = 0;
                            foreach ($pointsData as $key => $value) {
                        ?>
                        <tr>
                            <td style="text-align:center">
                                 <?php echo ++$i;?>
                            </td>
                            <td>
                                 <?php echo $value['from_range'];?>
                            </td>
                            <td>
                                 <?php echo $value['to_range'];?>
                            </td>
                            <td>
                                <?php echo $value['points'];?>
                            </td>
                            <td>
                                <a class="delete" href="javascript:void(0);" data-url="<?php echo site_url('product_guide/delete_points/'. encode_id($value['id']).'/'.encode_id($value['product_id']))?>">
                                    Delete
                                </a>
                            </td>
                        </tr>   
                        <?php   
                            }
                        }?>
                </tbody>
            </table>
        </div>
    </div>
    <span class="bg-bottom"></span>
</div>
<!-- END PRODUCT-->
<script src="<?php echo base_url().ASSETS;?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().ASSETS;?>js/config.datatable.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() { 
        var table = $('#sample_3');
        var columns = [];

        //Initialize datatable configuration
        initTable(table,columns);
        $('.delete').click(function(){
            var url = $(this).data('url');
            var result = confirm("Are you sure want to delete?");
            if(result == true){
                window.location.href = url;
            }
        });
    });
</script>
