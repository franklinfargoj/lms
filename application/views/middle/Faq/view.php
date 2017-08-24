<!-- BEGIN PAGE LEVEL STYLES -->
    <link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->

<!-- BEGIN TICKER -->
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">FAQs</h3>
    </div>
</div>
<div class="lead-top">
    <div class="container clearfix">
        <div class="float-left">
            <span class="total-lead">Total FAQs</span>
            <span class="lead-num"> : <?php echo count($faqlist);?></span>
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
        <table id="sample_3" class="display lead-table" cellspacing="0">
            <thead>
                <tr class="top-header">
                    <th></th>
                    <th><input type="text" name="customername" placeholder="Search Question"></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <th>Sr. No.</th>
                    <th>Question</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
                <tbody>
                    <?php if($faqlist){
                        $i = 0;
                        foreach ($faqlist as $key => $value) {
                    ?>  
                    <tr>
                        <td>
                             <?php echo ++$i;?>
                        </td>
                        <td>
                             <?php echo $value['question'];?>
                        </td>
                        <td>
                            <a class="" href="<?php echo site_url('faq/view/'.encode_id($value['id']))?>">
                                 View
                            </a> 
                        </td>
                        <td>
                            <a class="" href="<?php echo site_url('faq/edit/'. encode_id($value['id']));?>">
                                <img src="<?php echo base_url().ASSETS;?>images/pencil.png" alt="btn">
                            </a> 
                            
                            <a class="delete" href="javascript:void(0);" data-url="<?php echo site_url('faq/delete/'. encode_id($value['id']))?>">
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
<!-- END TICKER-->
<script src="<?php echo base_url().ASSETS;?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().ASSETS;?>js/config.datatable.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() { 
        var table = $('#sample_3');
        var columns = [2,3];

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
