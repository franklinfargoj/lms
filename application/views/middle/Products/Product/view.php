<!-- BEGIN PAGE LEVEL STYLES -->
    <link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->

<!-- BEGIN PRODUCT -->
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Products</h3>
    </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <div class="lead-top clearfix">
                <div class="float-left">
                    <span class="total-lead">Total Products</span>
                    <span class="lead-num"> : <?php echo count($productlist);?></span>
                </div>
                <div class="float-right">
                    <span class="lead-num"><a href="<?php echo site_url('product/add');?>">Add</a></span>
                </div>
            </div>
            <table id="sample_3" class="display lead-table">
                <thead>
                    <tr class="top-header">
                        <th></th>
                        <th><input type="text" name="customername" placeholder="Search Title"></th>
                        <th>
                            <?php
                                $options = $categorylist;
                                echo form_dropdown('category_id', $options ,'',array());
                            ?>
                        </th>
                        <th>
                            <?php
                                $status[''] = 'Select status';
                                $status['active'] = 'Active';
                                $status['inactive'] = 'Inactive';
                                echo form_dropdown('status', $status ,'',array());
                            ?>
                        </th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th style="text-align:center">Sr. No.</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                    <tbody>
                        <?php if($productlist){
                            $i = 0;
                            foreach ($productlist as $key => $value) {
                        ?>  
                        <tr>
                            <td style="text-align:center">
                                 <?php echo ++$i;?>
                            </td>
                            <td>
                                 <?php echo ucwords($value['title']);?>
                            </td>
                            <td>
                                 <?php echo ucwords($value['category']);?>
                            </td>
                            <td>
                                <?php echo ucwords($value['status']);?>
                            </td>
                            <td>
                                <a class="" href="<?php echo site_url('product_guide/index/'. encode_id($value['id']))?>">
                                     View
                                </a> 
                            </td>
                            <td>
                                <a class="" href="<?php echo site_url('product/edit/'. encode_id($value['id']));?>">
                                    Edit
                                </a> 
                                 
                                <span>|</span> 
                                <a class="" href="<?php echo site_url('product_guide/view_points/'. encode_id($value['id']));?>">
                                    Manage Points
                                </a>
                                <span>|</span> 
                                <a class="" href="<?php echo site_url('product_guide/points_distrubution/'. encode_id($value['id']));?>">
                                    Points Distrubution
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
        var columns = [3,4];

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
