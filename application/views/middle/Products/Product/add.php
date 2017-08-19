<!-- BEGIN  ADD PRODUCT CATEGORY-->
<div class="portlet light">
	<div class="portlet-title">
		<div class="caption">
			<!-- <i class="fa fa-cogs font-green-sharp"></i> -->
			<span class="caption-subject font-green-sharp bold">Add Product</span>
		</div>
		<div class="tools">
			<a href="<?php echo base_url('product');?>" class="btn btn-sm blue"></i>Back
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
			echo form_open(base_url().'/product/add', $attributes);
		?>
		<!-- <form role="form"> -->
			<div class="form-body">
				<div class="form-group <?php if(isset($has_error)){ echo $has_error;}?>">
					<?php
						$attributes = array(
					        'class' => '',
					        'style' => ''
						);
						echo form_label('Product category', 'category_id', $attributes);

						$options = $categorylist;
						$js = array(
						        'id'       => 'category_id',
						        'class'	   => 'form-control'	
						        /*'onChange' => 'some_function();'*/
						);
						echo form_dropdown('category_id', $options , set_select('category_id'),$js);

						// Assuming that the 'category' field value was incorrect:
						echo form_error('category_id', '<span class="help-block">', '</span>');
					?>
				</div>
				<div class="form-group <?php if(isset($has_error)){ echo $has_error;}?>">
					<?php 
						$attributes = array(
					        'class' => '',
					        'style' => ''
						);
						echo form_label('Product Name', 'title', $attributes);

						$data = array(
					        'type'  => 'text',
					        'name'  => 'title',
					        'id'    => 'title',
					        'class' => 'form-control',
					        'value' => set_value('title')
						);
						echo form_input($data);
						
						// Assuming that the 'title' field value was incorrect:
						echo form_error('title', '<span class="help-block">', '</span>');
					?>
				</div>
				<div class="form-group">
					<label>Default Assign</label>
					<div class="radio-list">
						
						<label class="radio-inline">
							<input type="radio" id= "self" name="default_assign" value="self" <?php echo  set_radio('default_assign', 'self', TRUE); ?> />
							Self
						</label>
						<label class="radio-inline">
							<input type="radio" id="branch" name="default_assign" value="branch" <?php echo  set_radio('default_assign', 'branch'); ?> />
							Branch
						</label>
					</div>
					<?php echo form_error('default_assign'); ?>
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
<!-- END ADD PRODUCT CATEGORY-->

<script type="text/javascript">
 	$.validator.addMethod("regx", function(value, element, regexpr) {
        return regexpr.test(value);
    });

    $("#add_form").validate({

        rules: {
            title: {
                required: true
            },
            category_id: {
                required: true
            }
        },
        messages: {
            title: {
                required: "Please enter product name"
            },
            category_id: {
                required: "Please select product category"
            }
        }
    });

</script>