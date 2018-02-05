<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->

<!-- BEGIN PRODUCT -->
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center"><?php echo $type_id;?></h3>
    </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <div class="lead-top clearfix">
                <div class="float-left">
                    <span class="total-lead">Total Branch</span>
                    <span class="lead-num"> : <?php echo count($list);?></span>
                </div>
                <div class="float-right">
                    <span class="lead-num"><a href="<?php echo site_url('rapc');?>">Back</a></span>
                </div>
<!--                <div class="float-right">-->
<!--                    <span class="lead-num"><a href="--><?php //echo site_url('rapc/upload');?><!--">Upload</a></span>-->
<!--                </div>-->
            </div>
            <table id="sample_3" class="display lead-table">
                <thead>
                <tr>
                    <th style="text-align:center">Sr. No.</th>
                    <th>Type</th>
                    <th>Branch Name</th>
                    <th>RAPC</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php if($list){
                    $i = 0;
                    foreach ($list as $key => $value) {
                        ?>
                        <tr>
                            <td style="text-align:center">
                                <?php echo ++$i;?>
                            </td>
                            <td>
                                <?php echo ucwords($value['processing_center']);?>
                            </td>
                            <td>
                                <?php
                                $br_name = branchname($value['branch_id']);
                                echo ucwords(strtolower($br_name[0]['name']));?>
                            </td>
                            <td>
                                <?php
                                $procc_name = branchname($value['other_processing_center_id']);
                                echo ucwords(strtolower($procc_name[0]['name']));?>
                            </td>
                            <td>
<!--                                <a class="" href="--><?php //echo site_url('rapc/edit/'. encode_id($value['id']));?><!--">-->
<!--                                    Edit-->
<!--                                </a>-->
<!--                                <span>|</span>-->
                                <a class="delete" href="javascript:void(0);" data-url="<?php echo site_url('rapc/delete/'. encode_id($value['id']))?>">
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
        var columns = [4];

        //Initialize datatable configuration
        initTable(table,columns);
});
        $('.delete').click(function(){
            var url = $(this).data('url');
            var result = confirm("Are you sure want to delete?");
            if(result == true){
                window.location.href = url;
            }
        });
</script>
