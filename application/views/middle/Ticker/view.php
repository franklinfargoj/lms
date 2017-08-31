<!-- BEGIN PAGE LEVEL STYLES -->
    <link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->

<!-- BEGIN TICKER -->
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Tickers</h3>
    </div>
</div>
<div class="lead-top">
    <div class="container clearfix">
        <div class="float-left">
            <span class="total-lead">Total Tickers</span>
            <span class="lead-num"> : <?php echo count($tickerlist);?></span>
        </div>
        <div class="float-right">
            <a href="<?php echo base_url('ticker/add')?>">
                Add
            </a>
        </div>
    </div>
</div>
<div class="page-content">
    <div class="container">
        <table id="sample_3" class="display lead-table">
            <thead>
                <tr class="top-header">
                    <th></th>
                    <th><input type="text" name="customername" placeholder="Search Title"></th>
                    <th>
                        <select name="status">
                            <option value="">Select status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <th>Sr. No.</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
                <tbody>
                    <?php if($tickerlist){
                        $i = 0;
                        foreach ($tickerlist as $key => $value) {
                    ?>  
                    <tr>
                        <td>
                             <?php echo ++$i;?>
                        </td>
                        <td>
                             <?php echo $value['title'];?>
                        </td>
                        <td>
                            <?php echo $value['status'];?>
                            <!-- <label class="switch switch-flat">
                                <input class="switch-input" id="<?php echo $value['id'];?>" type="checkbox" />
                                <span class="switch-label" data-on="Active" data-off="Inactive"></span> <span class="switch-handle"></span>
                            </label> -->
                        </td>
                        <td>
                            <a class="" href="<?php echo site_url('ticker/view/'.encode_id($value['id']))?>">
                                 View
                            </a> 
                        </td>
                        <td>
                            <a class="" href="<?php echo site_url('ticker/edit/'. encode_id($value['id']));?>">
                                Edit
                            </a> 
                            <span>/</span> 
                            <a class="delete" href="javascript:void(0);" data-url="<?php echo site_url('ticker/delete/'. encode_id($value['id']))?>">
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
<!-- END TICKER-->
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
            confirm("Are you sure want to delete?", function(result) {
               if(result == true){
                window.location.href = url;
               }
            }); 
        });
    });
</script>
