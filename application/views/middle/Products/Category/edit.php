<div class="page-title">
	<div class="container clearfix">
		<h3 class="text-center">Edit Product Category</h3>
		<div class="float-right">
            <span class="lead-num"><a href="<?php echo site_url('product_category');?>">Back</a></span>
        </div>
	</div>
</div>
<div class="page-content">
	<div class="container">
		<span></span>
			<div class="inner-content">
				<div class="product-category">
					<!-- <form> -->
					<?php
						$attributes = array(
							'role' => 'form',
							'id' => 'edit_form',
							'class' => 'form',
							'autocomplete' => 'off'
							);
						echo form_open(base_url().'/product_category/edit/'.$this->uri->segment(3,0), $attributes);
					?>
						<div class="form-control">
							<?php 
								$attributes = array(
							        'class' => '',
							        'style' => ''
								);
								echo form_label('Category Name:', 'title', $attributes);

								$data = array(
							        'type'  => 'text',
							        'name'  => 'title',
							        'id'    => 'title',
							        'class' => '',
							        'value' => $categoryDetail[0]['title'] 
								);
								echo form_input($data);
								
								// Assuming that the 'title' field value was incorrect:
								echo form_error('title', '<span class="help-block">', '</span>');
							?>	
						</div>
						<div class="form-control">
							<label>Status</label>
							<div class="radio-control">
								<input type="radio" id= "active" name="status" value="active" <?php 
								    echo set_value('status', $categoryDetail[0]['status']) == 'active' ? "checked" : ""; 
								?> />
								<label>Active</label>
							</div>
							<div class="radio-control">
								<input type="radio" id= "inactive" name="status" value="inactive" <?php 
								    echo set_value('status', $categoryDetail[0]['status']) == 'inactive' ? "checked" : ""; 
								?> />
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
		<span></span>
	</div>
</div>

<script type="text/javascript">
 	$.validator.addMethod("regx", function(value, element, regexpr) {
        return regexpr.test(value);
    });

    $("#edit_form").validate({

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
