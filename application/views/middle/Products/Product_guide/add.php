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
            <span class="caption-subject font-green-sharp bold">Add <?php echo $product[0]['title'];?> Description</span>
        </div>
        <div class="tools">
            <a href="<?php echo base_url("product_guide/index/".$product[0]['id']);?>" class="">View Description
            </a>
        </div>
    </div>
    <div class="portlet-body form">
		<?php
			$attributes = array(
				'role' => 'form',
				'id' => 'add_form',
				'autocomplete' => 'off'
				);
			echo form_open(base_url().'/product_guide/add/'.$product[0]['id'], $attributes);
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

						$options = $titleList;
                        $js = array(
                                'id'       => 'title',
                                'class'    => 'form-control'    
                                /*'onChange' => 'some_function();'*/
                        );
                        echo form_dropdown('title', $options , set_select('title'),$js);
						
						// Assuming that the 'title' field value was incorrect:
						echo form_error('title', '<span class="help-block">', '</span>');
					?>
				</div>
				<!-- <div class="form-group">
					<label>Small Input</label>
					<input type="text" class="form-control input-sm" placeholder="input-sm">
				</div> -->
				<div class="form-group <?php if(isset($has_error)){ echo $has_error;}?>">
					<?php 
                        $data = array(
                                'product_id'  => $product[0]['id']
                        );
                        echo form_hidden($data);

                        $attributes = array(
                            'class' => '',
                            'style' => ''
                        );
                        echo form_label('Description', 'description_text', $attributes);
                    ?>
                        <textarea id="description_text" name = "description_text" class="textarea" placeholder="Enter text ..." style="width: 810px; height: 200px">
                        	<?php echo set_value('description_text')?>
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

    $("#add_form").validate({

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
                required: "Please Enter Product Description"
            }
        }
    });

</script>




