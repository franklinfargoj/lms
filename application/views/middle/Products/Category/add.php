<div class="page-title">
	<div class="container clearfix">
		<h3 class="text-center">Add Product Category</h3>
		
	</div>
</div>
<div class="page-content">
	<span class="bg-top"></span>
	<div class="inner-content">
		<div class="container">
		<p id="note"><span style="color:red;">*</span> These fields are required</p>
			<div class="float-right">
            	<span class="lead-num"><a href="<?php echo site_url('product_category');?>"><span>&#60;</span>Back</a></span>
        	</div>
			<div class="product-category">
				<!-- <form> -->
				<?php
					$attributes = array(
						'role' => 'form',
						'id' => 'add_form',
						'class' => 'form',
						'autocomplete' => 'off'
						);
					echo form_open(base_url().'/product_category/add', $attributes);
				?>
					<div class="form-control">
						<?php 
							$attributes = array(
						        'class' => '',
						        'style' => ''
							);
							echo form_label('Category Name:<span style="color:red;">*</span>', 'title', $attributes);

							$data = array(
						        'type'  => 'text',
						        'name'  => 'title',
						        'id'    => 'title',
						        'class' => '',
						        'value' => set_value('title') 
							);
							echo form_input($data);
							
							// Assuming that the 'title' field value was incorrect:
							echo form_error('title', '<span class="help-block">', '</span>');
						?>	
					</div>
					<div class="form-control">
						<label>Status:<span style="color:red;">*</span></label>
						<div class="radio-control">
							<input type="radio" id= "active" name="status" value="active" <?php echo  set_radio('status', 'active', TRUE); ?> />
							<label>Active</label>
						</div>
						<div class="radio-control">
							<input type="radio" id= "inactive" name="status" value="inactive" <?php echo  set_radio('status', 'inactive'); ?> />
							<label>Inactive</label>
						</div>
					</div>
					<div class="form-control form-submit clearfix">
						<a href="javascript:void(0);" class="reset">
							Reset
						</a>
	                    <a href="javascript:void(0);" class="active">
	                        <img alt ="left nav" src="<?php echo base_url().ASSETS;?>images/left-nav.png">
	                        <span><input class="custom_button" type="submit" name="Submit" value="Submit"></span>
	                        <img alt = "right nav" src="<?php echo base_url().ASSETS;?>images/right-nav.png">
	                    </a>
	                </div>
				<!-- </form> -->
				<?php echo form_close();?>
			</div>
		</div>
	</div>
	<span class="bg-bottom"></span>
</div>

<script type="text/javascript">
 	$.validator.addMethod("regx", function(value, element, regexpr) {
        return regexpr.test(value);
    });

    $("#add_form").validate({

        rules: {
            title: {
                required: true
            }
        },
        messages: {
            title: {
                required: "Please Enter Product Category"
            }
        }
    });

</script>