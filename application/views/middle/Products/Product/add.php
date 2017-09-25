<div class="page-title">
	<div class="container clearfix">
		<h3 class="text-center">Add Product</h3>
		
	</div>
</div>
<div class="page-content">
	<span class="bg-top"></span>
	<div class="inner-content">
		<div class="container">
		<p id="note"><span style="color:red;">*</span> These fields are required</p>

		<div class="float-right">
            <span class="lead-num"><a href="<?php echo site_url('product');?>"><span>	&#60;</span>Back</a></span>
        </div>
			<div class="product-category add-product">
				<!-- <form> -->
				<?php
					$attributes = array(
						'role' => 'form',
						'id' => 'add_form',
						'class' => 'form',
						'autocomplete' => 'off'
						);
					echo form_open(base_url().'/product/add', $attributes);
				?>
					<div class="form-control">
						<?php
							$attributes = array(
						        'class' => '',
						        'style' => ''
							);
							echo form_label('Product Category:<span style="color:red;">*</span>', 'category_id', $attributes);

							$options = $categorylist;
							$js = array(
							        'id'       => 'category_id',
							        'class'	   => ''	
							        /*'onChange' => 'some_function();'*/
							);
							echo form_dropdown('category_id', $options , set_select('category_id'),$js);

							// Assuming that the 'category' field value was incorrect:
							echo form_error('category_id', '<span class="help-block">', '</span>');
						?>
					</div>
                    <div class="form-control">
						<?php 
							$attributes = array(
						        'class' => '',
						        'style' => ''
							);
							echo form_label('Product Name:<span style="color:red;">*</span>', 'title', $attributes);

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
						<?php
							$attributes = array(
						        'class' => '',
						        'style' => ''
							);
							echo form_label('Map With:<span style="color:red;">*</span>', 'map_with', $attributes);

                        $map[''] = 'Select';
                        foreach ($this->config->item('map') as $k => $map_value){
                            $map[$k]=$map_value;
                        }
                        $js = array(
							        'id'       => 'map_with',
							        'class'	   => ''
							        /*'onChange' => 'some_function();'*/
							);
							echo form_dropdown('map_with', $map , set_select('map_with'),$js);

							echo form_error('map_with', '<span class="help-block">', '</span>');
						?>
					</div>
					<div class="form-control">
						<label>Default Assign:<span style="color:red;">*</span></label>
						<div class="radio-control">
							<input type="radio" id= "self" name="default_assign" value="self" <?php echo  set_radio('default_assign', 'self'); ?> />
							<label>Self</label>
						</div>
						<div class="radio-control">
							<input type="radio" id= "branch" name="default_assign" value="branch" <?php echo  set_radio('default_assign', 'branch', TRUE); ?> />
							<label>Branch</label>
						</div>
					</div>
					<div class="form-control">
						<?php
							$attributes = array(
						        'class' => '',
						        'style' => ''
							);
							echo form_label('Turn Around Time:<span style="color:red;">*</span>', 'turn_around_time', $attributes);

							$options = array(
								'' =>	'select',
								'1' =>	'1',
								'2' =>	'2',
								'3' =>	'3',
								'4' =>	'4',
								'5' =>	'5',
								'6' =>	'6',
								'7' =>	'7',
								'8' =>	'8',
								'9' =>	'9',
								'10' =>'10',
								'11' =>'11',
								'12' =>'12',
								'13' =>'13',
								'14' =>'14',
								'15' =>'15'
							);
							$js = array(
							        'id'       => 'turn_around_time',
							        'class'	   => ''	
							        /*'onChange' => 'some_function();'*/
							);
							echo form_dropdown('turn_around_time', $options , set_select('turn_around_time'),$js);

							// Assuming that the 'category' field value was incorrect:
							echo form_error('turn_around_time', '<span class="help-block">', '</span>');
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
	</div>
	<span class="bg-bottom"></span>
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
            },
            turn_around_time: {
                required: true
            },
            map_with: {
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
            },
            map_with: {
                required: "Please select map with"
            }
        }
    });

</script>