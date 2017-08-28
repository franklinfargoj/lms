<!-- BEGIN PAGE LEVEL STYLES -->
    <link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->

<!-- BEGIN PRODUCT CATEGORY -->
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Product Category</h3>
    </div>
</div>
<div class="lead-top">
    <div class="container clearfix">
        <div class="float-left">
            <span class="total-lead">Total Categories</span>
            <span class="lead-num"> : <?php echo count($categorylist);?></span>
        </div>
        <div class="float-right">
            <a href="<?php echo site_url('product_category/add');?>">
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
                    <th></th>
                </tr>
                <tr>
                    <th>Sr. No.</th>
                    <th>Title</th>
                    <th>Action</th>
                </tr>
            </thead>
                <tbody>
                    <?php if($categorylist){
                        $i = 0;
                        foreach ($categorylist as $key => $value) {
                    ?>  
                    <tr>
                        <td>
                             <?php echo ++$i;?>
                        </td>
                        <td>
                             <?php echo $value['title'];?>
                        </td>
                        <td>
                            <a class="" href="<?php echo site_url('product_category/edit/'. encode_id($value['id']));?>">
                                <img src="<?php echo base_url().ASSETS;?>images/pencil.png" alt="btn">
                            </a> 
                            
                            <a class="" href="javascript:void(0);" data-url="<?php echo site_url('product_category/delete/'. encode_id($value['id']))?>">
                                <img src="<?php echo base_url().ASSETS;?>images/delete.png" alt="btn">
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
<!-- END PRODUCT CATEGORY-->
<script src="<?php echo base_url().ASSETS;?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().ASSETS;?>js/config.datatable.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() { 
        var table = $('#sample_3');
        var columns = [2];

        //Initialize datatable configuration
        initTable(table,columns);

       $('.delete').click(function(){
            var url = $(this).data('url');
            bootbox.confirm("Are you sure want to delete?", function(result) {
               if(result == true){
                window.location.href = url;
               }
            }); 
        });
    });
</script>
