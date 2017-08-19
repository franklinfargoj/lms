<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/bootstrap-wysihtml5/wysiwyg-color.css"/>

<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN PRODUCT DESCRIPTION-->
<div class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            <!-- <i class="fa fa-cogs font-green-sharp"></i> -->
            <span class="caption-subject font-green-sharp bold">Edit Ticker</span>
        </div>
        <div class="tools">
            <a href="<?php echo site_url('ticker');?>" class="btn btn-sm blue"></i>Back
            </a>
        </div>
    </div>
    <div class="portlet-body form">
		<?php
			$attributes = array(
				'role' => 'form',
				'id' => 'edit_form',
				'autocomplete' => 'off'
				);
			echo form_open(base_url().'/ticker/edit/'.$this->uri->segment(3,0), $attributes);
		?>
		<!-- <form role="form"> -->
			<div class="form-body">
				<div class="form-group <?php if(isset($has_error)){ echo $has_error;}?>">
					<?php 
						$attributes = array(
					        'class' => '',
					        'style' => ''
						);
						echo form_label('Title', 'title', $attributes);

						$data = array(
					        'type'  => 'text',
					        'name'  => 'title',
					        'id'    => 'title',
					        'class' => 'form-control',
					        'placeholder' => '',
					        'value' => $tickerDetail[0]['title']
						);
						echo form_input($data);
						
						// Assuming that the 'title' field value was incorrect:
						echo form_error('title', '<span class="help-block">', '</span>');
					?>
				</div>
				<div class="form-group <?php if(isset($has_error)){ echo $has_error;}?>">
					<?php 
                        $attributes = array(
                            'class' => '',
                            'style' => ''
                        );
                        echo form_label('Description', 'description_text', $attributes);
                    ?>
                        <textarea name = "description_text" class="textarea" placeholder="Enter text ..." style="width: 810px; height: 200px">
                    			<?php echo $tickerDetail[0]['description_text'];?>
                        </textarea>
                    <?php echo form_error('description_text', '<span class="help-block">', '</span>');?>
				</div>
			</div>
			<div class="form-actions right">
				<button type="reset" class="btn default">Reset</button>
				<button type="submit" class="btn green">Submit</button>
			</div>
		<!-- </form> -->
		<?php echo form_close();?>
	</div>
</div>
<!-- END PRODUCT DESCRIPTION-->

<script src="<?php echo base_url();?>assets/global/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?php echo base_url();?>assets/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript">
    $('.textarea').wysihtml5();
</script>
<script type="text/javascript">
 	$.validator.addMethod("regx", function(value, element, regexpr) {
        return regexpr.test(value);
    });

    $("#edit_form").validate({

        rules: {
            title: {
                required: true
            },
            description_text: {
                required: true
            }
        },
        messages: {
            title: {
                required: "Please Enter Title"
            },
            description_text: {
                required: "Please Enter Description"
            }
        }
    });

</script>




