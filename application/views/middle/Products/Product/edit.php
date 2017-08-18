<!-- BEGIN  EDIT PRODUCT -->
<div class="portlet light">
	<div class="portlet-title">
		<div class="caption">
			<!-- <i class="fa fa-cogs font-green-sharp"></i> -->
			<span class="caption-subject font-green-sharp bold">Edit Product</span>
		</div>
		<div class="tools">
			<a href="<?php echo base_url('product');?>" class="">List
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
			echo form_open(base_url().'/product/edit/'.$this->uri->segment(3,0), $attributes);
		?>
			<div class="form-body">
				<div class="form-group">
					<?php
						$attributes = array(
					        'class' => '',
					        'style' => ''
						);
						echo form_label('Product category', 'category_id', $attributes);

						//$options = array_merge(array('' => 'Select'),$categorylist);
						$options = $categorylist;
						$js = array(
						        'id'       => 'category_id',
						        'class'	   => 'form-control'	
						        /*'onChange' => 'some_function();'*/
						);

						//$shirts_on_sale = array('small', 'large');
						echo form_dropdown('category_id', $options , $productDetail[0]['category_id'],$js);
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
					        'value' => $productDetail[0]['title'],
					        'placeholder' => '' 
						);
						echo form_input($data);
						
						// Assuming that the 'title' field value was incorrect:
						echo form_error('title', '<span class="help-block">', '</span>');
					?>
				</div>
				<div class="form-group">
					<div class="radio-list">
						<label class="radio-inline">
							<input type="radio" id= "self" name="default_assign" value="self" <?php 
							    echo set_value('default_assign', $productDetail[0]['default_assign']) == 'self' ? "checked" : ""; 
							?> />
							Self
						</label>
						<label class="radio-inline">
							<input type="radio" id="branch" name="default_assign" value="branch" <?php 
							    echo set_value('default_assign', $productDetail[0]['default_assign']) == 'branch' ? "checked" : ""; 
							?> />
							Branch
						</label>
					</div>
					<?php echo form_error('default_assign'); ?>
				</div>
			</div>
			<div class="form-actions right">
				<button type="reset" class="btn default">Cancel</button>
				<button type="submit" class="btn green">Submit</button>
			</div>
		<?php echo form_close();?>
	</div>
</div>
<!-- END  EDIT PRODUCT -->

<script type="text/javascript">
 	$.validator.addMethod("regx", function(value, element, regexpr) {
        return regexpr.test(value);
    });

    $("#edit_form").validate({

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
                required: "Please Enter Product Name"
            },
            category_id: {
                required: "Please Select Product Category"
            }
        }
    });

</script>