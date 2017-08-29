<div class="page-title">
	<div class="container clearfix">
		<h3 class="text-center">Edit Product</h3>
		<div class="float-right">
            <span class="lead-num"><a href="<?php echo site_url('product');?>">Back</a></span>
        </div>
	</div>
</div>
<div class="page-content">
	<div class="container">
		<span></span>
			<div class="inner-content">
				<div class="product-category add-product">
					<!-- <form> -->
					<?php
						$attributes = array(
							'role' => 'form',
							'id' => 'edit_form',
							'autocomplete' => 'off'
							);
						echo form_open(base_url().'/product/edit/'.$this->uri->segment(3,0), $attributes);
					?>
						<div class="form-control">
							<?php
								$attributes = array(
							        'class' => '',
							        'style' => ''
								);
								echo form_label('Product category:', 'category_id', $attributes);

								//$options = array_merge(array('' => 'Select'),$categorylist);
								$options = $categorylist;
								$js = array(
								        'id'       => 'category_id',
								        'class'	   => ''	
								        /*'onChange' => 'some_function();'*/
								);

								//$shirts_on_sale = array('small', 'large');
								echo form_dropdown('category_id', $options , $productDetail[0]['category_id'],$js);
							?>
						</div>
						<div class="form-control">
							<?php 
								$attributes = array(
							        'class' => '',
							        'style' => ''
								);
								echo form_label('Product Name:', 'title', $attributes);

								$data = array(
							        'type'  => 'text',
							        'name'  => 'title',
							        'id'    => 'title',
							        'class' => '',
							        'value' => $productDetail[0]['title']
								);
								echo form_input($data);
								
								// Assuming that the 'title' field value was incorrect:
								echo form_error('title', '<span class="help-block">', '</span>');
							?>
						</div>
						<div class="form-control">
							<label>Default Assign</label>
							<div class="radio-control">
								<input type="radio" id= "self" name="default_assign" value="self" <?php 
								    echo set_value('default_assign', $productDetail[0]['default_assign']) == 'self' ? "checked" : ""; 
								?> />
								<label>Self</label>
							</div>
							<div class="radio-control">
								<input type="radio" id= "branch" name="default_assign" value="branch" <?php 
								    echo set_value('default_assign', $productDetail[0]['default_assign']) == 'branch' ? "checked" : ""; 
								?> />
								<label>Branch</label>
							</div>
						</div>
						<div class="form-control">
							<label>Status</label>
							<div class="radio-control">
								<input type="radio" id= "active" name="status" value="active" <?php 
								    echo set_value('status', $productDetail[0]['status']) == 'active' ? "checked" : ""; 
								?> />
								<label>Active</label>
							</div>
							<div class="radio-control">
								<input type="radio" id= "inactive" name="status" value="inactive" <?php 
								    echo set_value('status', $productDetail[0]['status']) == 'inactive' ? "checked" : ""; 
								?> />
								<label>Inactive</label>
							</div>
						</div>
						<div class="form-control">
							<?php
								$attributes = array(
							        'class' => '',
							        'style' => ''
								);
								echo form_label('Turn Around Time:', 'turn_around_time', $attributes);

								$options = array(
									'' =>	'select',
									'10' =>	'10',
									'20' =>	'20',
									'30' =>	'30',
									'40' =>	'40',
									'50' =>	'50',
									'60' =>	'60',
									'70' =>	'70',
									'80' =>	'80',
									'90' =>	'90',
									'100' =>'100'
								);
								$js = array(
								        'id'       => 'turn_around_time',
								        'class'	   => ''	
								        /*'onChange' => 'some_function();'*/
								);
								echo form_dropdown('turn_around_time', $options , $productDetail[0]['turn_around_time'],$js);

								// Assuming that the 'category' field value was incorrect:
								echo form_error('turn_around_time', '<span class="help-block">', '</span>');
							?>
						</div>
						<div class="form-control form-submit clearfix">
							<a href="javascript:void(0);" class="reset">
								Reset
							</a>
							<a href="#">
								<img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav">
								<span><input class="custom_button" type="submit" name="Submit" value="Submit"></span>
								<img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="right-nav">
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
            },
            category_id: {
                required: true
            },
            turn_around_time: {
                required: true
            }
        },
        messages: {
            title: {
                required: "Please enter product name"
            },
            category_id: {
                required: "Please select product category"
            },
            turn_around_time: {
                required: "Please select turn around time"
            }
        }
    });

</script>