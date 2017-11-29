<?php
/**
 * Created by PhpStorm.
 * User: webwerks1
 * Date: 15/8/17
 * Time: 6:32 PM
 */
$form_attributes = array('id' => 'upload_lead', 'method' => 'POST','class' =>'form');
$data_input = array('id' => 'file', 'name'=>'filename','type' => 'file');

$data_submit = array(
    'name' => 'Submit',
    'id' => 'Submit',
    'type' => 'Submit',
    'content' => 'Submit',
    'value' => 'Submit'
);
?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->

<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Upload RAPC</h3>
    </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
    <div class="container">
        <div class="float-right">
            <span class="lead-num"><a href="<?php echo site_url('rapc');?>">Back</a></span>
        </div>
    <p id="note"><span style="color:red;">*</span> These fields are required</p>
        <div class="upload-content">

            <div class="upload-form">
                <?php
                $url = base_url('rapc/upload');
                echo form_open_multipart($url, $form_attributes);
                ?>

                    <div class="form-control">
                        <label>Select File:<span style="color:red;">*</span></label>
                        <?php echo form_input($data_input);?>
                        <div class="valid-msg"><span>*</span>Only xls | xlsx</div>
                    </div>
                    <span>
                    </span>
               <!--<div class="form-control form-submit clearfix">
                    <a href="javascript:void(0);" class="active">
<!--                        <img alt ="left nav" src="--><?php ///*echo base_url().ASSETS;*/?><!--images/left-nav.png">-->
                        <!--<span><input class="custom_button" type="submit" name="Submit" value="Submit"></span>
<!--                        <img alt = "right nav" src="--><?php ///*echo base_url().ASSETS;*/?><!--images/right-nav.png">-->
                    <!--</a>
                </div>-->

            <div class="form-control form-submit clearfix">
                <a href="javascript:void(0);" class="active float-right">
                        <img alt="left nav" src="<?php echo base_url().ASSETS;?>/images/left-nav.png">
                        <span><input class="custom_button" name="Submit" value="Submit" type="submit"></span>
                        <img alt="right nav" src="<?php echo base_url().ASSETS;?>/images/right-nav.png">
                    </a>
            </div>

<!--                <button class="btn-submit" type="submit"></button>-->
                <?php echo form_close();?>
            </div>
            <div class="upload-xl">
                <a href="<?php echo base_url('uploads/sample/Branch_RAPC_Mapping_Sample_DataSheet.xlsx')?>">
                    <img src="<?php echo base_url().ASSETS;?>images/excel-img.png" alt="excel">
                    <span>Download Sample File</span>
                </a>
            </div>
        </div>

</div>
</div>
<span class="bg-bottom"></span>
</div>
<script src="<?php echo base_url().ASSETS;?>js/jquery.dataTables.min.js"></script>
<!--<script src="--><?php //echo base_url().ASSETS;?><!--js/config.datatable.js"></script>-->
<script type="text/javascript">

    var table = $('#sample_3');
    var columns = [5];

    var inituploadTable = function (table,columns) {
        /*
         * Initialize DataTables, with no sorting on the 'details' column
         */


        var oTable = table.DataTable({
            "columnDefs": [{
                "orderable": false,
                "targets": columns
            }],
            "order": [
                [0, 'desc']
            ],
            "lengthMenu": [
                [5, 15, 20, -1],
                [5, 15, 20, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
        });

    }
    inituploadTable(table,columns);
    /*jQuery(document).ready(function() {
        var table = $('#sample_3');
        var columns = [4];

        //Initialize datatable configuration
        initTable(table,columns);

    });*/
    $('#upload_lead').validate({

        rules:{
            filename:{
                required:true
            }
        },messages:{

            filename:{
                required:'Please upload a file.'
            }
        }
    });
</script>
