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
$source_options['Third Party'] = 'Third Party';
$source_options['Bank Website'] = 'Bank Website';
$source_options['Analytics'] = 'Analytics';
$source_options['Walking'] = 'Walking';
$class = 'class="form-control"';
$data_submit = array(
    'name' => 'Submit',
    'id' => 'Submit',
    'type' => 'Submit',
    'content' => 'Submit',
    'class' => 'btn green',
    'value' => 'Submit'
);
?>

<div class="row">
    <div class="col-md-6 ">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light">
            <div class="portlet-body form">
                <?php
                $url = base_url('Leads/upload');
                echo form_open_multipart($url, $form_attributes);
                ?>
                <?php echo $this->session->flashdata('message'); ?>
                <div class="form-body">
                    <div class="form-group">
                        <label>Lead Source</label>
                        <?php echo form_dropdown('lead_source', $source_options,'', $class) ?>
                    </div>
                    <div class="form-group">
                        <label>Upload</label>
                        <div class="input-group">
						<span class="input-group-addon">
                            <?php echo form_input($data_input);?>
						</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <span>*Please Upload a file with extension xls or xlsx</span>
                    </div>
                </div>
                <div class="form-actions">
                    <?php echo form_button($data_submit) ?>
                </div>
                <?php echo form_close();?>
            </div>
        </div>
    </div>
</div>