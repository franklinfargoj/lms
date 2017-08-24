<?php
/**
 * Created by PhpStorm.
 * User: webwerks1
 * Date: 15/8/17
 * Time: 6:32 PM
 */
$form_attributes = array('id' => 'upload_lead', 'method' => 'POST');
$data_input = array('id' => 'file', 'name'=>'filename', 'method' => 'POST' ,'type' => 'file');

$source_options[''] = 'Select Lead Source';
$source_options['Tie Ups'] = 'Tie Ups';
$source_options['Enquiry'] = 'Enquiry';
$source_options['Analytics'] = 'Analytics';
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
        <h3 class="text-center">Upload Leads</h3>
    </div>
</div>
<div class="page-content">
    <div class="container">
        <div class="upload-content">
            <div class="upload-form">
                <?php
                $url = base_url('leads/upload');
                echo form_open_multipart($url, $form_attributes);
                ?>
                    <div class="form-control">
                        <label>Lead Source:</label>
                        <?php echo form_dropdown('lead_source', $source_options) ?>
                    </div>
                    <div class="form-control">
                        <label>Select File:</label>
                        <?php echo form_input($data_input);?>
                        <span>Only xlx | xlxs</span>
                    </div>
                    <span>
                    </span>
                    <a href="javascript:void(0);" class="active">
                        <img src="<?php echo base_url().ASSETS;?>images/left-nav.png">
                        <!-- <span>LOGIN</span> -->
                        <span><input type="submit" name="Submit" value="Submit"></span>
                        <img alt = "Submit button" src="<?php echo base_url().ASSETS;?>images/right-nav.png">
                    </a>
                <?php echo form_close();?>
            </div>
            <div class="upload-xl">
                <a href="">
                    <img alt="Download Sample File" src="<?php echo base_url().ASSETS;?>images/excel-img.png" alt="excel">
                    <span>Download Sample File</span>
                </a>
            </div>
        </div>

        <table class="upload-table" id="sample_3">
            <thead>
            <tr>
                <th>Sr. No</th>
                <th>File</th>
                <th>Date and Time</th>
                <th>Status</th>
                <th>Download</th>
            </tr>
            </thead>
        <tbody>
        <?php if(!empty($uploaded_logs)){
            $i = 0;
            foreach ($uploaded_logs as $key => $value) {
                ?>
                <tr>
                    <td>
                        <?php echo ++$i;?>
                    </td>
                    <td>
                        <?php echo $value['file_name'];?>
                    </td>
                    <td>
                        <?php echo date('d-m-Y',strtotime($value['created_time']));?>
                    </td>
                    <td>
                        <?php echo $value['status'];?>
                    </td>
                    <td>
                        <?php if($value['status'] == 'failed'){ ?>
                        <a href="<?php echo base_url('uploads/errorlog/'.$value['file_name']); ?>">Error log</a>
                        <?php }else{?>
                        <a href="<?php echo base_url('uploads/'.$value['file_name']); ?>">Uploaded File</a>
                    <?php }?>
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
    $('#upload_lead').validate({

        rules:{
            lead_source:{
                required:true
            },
            filename:{
                required:true
            }
        },messages:{
            lead_source:{
                required:'Please select lead source.'
            },
            filename:{
                required:'Please upload a file.'
            }
        }
    });
</script>
