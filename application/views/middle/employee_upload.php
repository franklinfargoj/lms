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

<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Upload Employee</h3>
    </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <p id="note"><span style="color:red;">*</span> These fields are required</p>
            <div class="upload-content">

                <div class="upload-form">
                    <?php
                    $emp = 'employee';
                    $url = base_url('leads/upload_employee');
                    echo form_open_multipart($url, $form_attributes);
                    ?>
                    <div class="form-control">
                        <label>Select File:<span style="color:red;">*</span></label>
                        <?php echo form_input($data_input);?>
                        <div class="valid-msg"><span>*</span>Only xlx | xlxs</div>
                    </div>
                    <span>
                    </span>
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
                    <a href="<?php echo base_url('uploads/sample/sample_lead.xlsx')?>">
                        <img src="<?php echo base_url().ASSETS;?>images/excel-img.png" alt="excel">
                        <span>Download Sample File</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <span class="bg-bottom"></span>
</div>
<script type="text/javascript">
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
