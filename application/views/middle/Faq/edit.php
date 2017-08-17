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
            <span class="caption-subject font-green-sharp bold">Edit Faq</span>
        </div>
        <div class="tools">
            <a href="<?php echo site_url('faq');?>" class="">List
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
			echo form_open(base_url().'/faq/edit/'.$this->uri->segment(3,0), $attributes);
		?>
		<!-- <form role="form"> -->
			<div class="form-body">
				<div class="form-group <?php if(isset($has_error)){ echo $has_error;}?>">
					<?php 
						$attributes = array(
					        'class' => '',
					        'style' => ''
						);
						echo form_label('Question', 'question', $attributes);

						$data = array(
					        'type'  => 'text',
					        'name'  => 'question',
					        'id'    => 'question',
					        'class' => 'form-control',
					        'placeholder' => '',
					        'value' => $faqDetail[0]['question']
						);
						echo form_input($data);
						
						// Assuming that the 'title' field value was incorrect:
						echo form_error('question', '<span class="help-block">', '</span>');
					?>
				</div>
				<div class="form-group <?php if(isset($has_error)){ echo $has_error;}?>">
					<?php 
                        $attributes = array(
                            'class' => '',
                            'style' => ''
                        );
                        echo form_label('Answer', 'answer', $attributes);
                    ?>
                        <textarea name = "answer" class="textarea" placeholder="Enter text ..." style="width: 810px; height: 200px">
                    			<?php echo $faqDetail[0]['answer'];?>
                        </textarea>
                    <?php echo form_error('answer', '<span class="help-block">', '</span>');?>
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
            question: {
                required: true
            },
            answer: {
                required: true
            }
        },
        messages: {
            question: {
                required: "Please Enter Question"
            },
            answer: {
                required: "Please Enter Answer"
            }
        }
    });

</script>




