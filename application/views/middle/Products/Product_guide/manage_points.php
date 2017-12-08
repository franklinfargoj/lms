<div class="page-title">
	<div class="container clearfix">
		<h3 class="text-center">Add</h3>
		<div class="float-right">
            <span class="lead-num"><a href="<?php echo site_url('product_guide/view_points/'.encode_id($product[0]['id']));?>"><span><</span>Back</a></span>
        </div>
	</div>
</div>
<div class="page-content">
	<span class="bg-top"></span>
	<div class="inner-content">
		<div class="container">
			<div class="product-category add-product">
				<!-- <form> -->
				<?php
					$attributes = array(
						'role' => 'form',
						'id' => 'add_form',
						'class' => 'form',
						'autocomplete' => 'off'
						);
					echo form_open(base_url().'/product_guide/manage_points/'.encode_id($product[0]['id']), $attributes);
				?>
					<div class="form-control">
						<?php
							$attributes = array(
						        'class' => '',
						        'style' => ''
							);
							echo form_label('Min Range:', 'from_range', $attributes);

							$data = array(
						        'type'  => 'text',
						        'name'  => 'from_range',
						        'id'    => 'from_range',
						        'class' => '',
						        'value' => set_value('from_range')
							);
							echo form_input($data);

							// Assuming that the 'category' field value was incorrect:
							echo form_error('from_range', '<span class="help-block">', '</span>');
						?>
					</div>
					<div class="form-control">
						<?php
							$attributes = array(
						        'class' => '',
						        'style' => ''
							);
							echo form_label('Max Range:', 'to_range', $attributes);

							$data = array(
						        'type'  => 'text',
						        'name'  => 'to_range',
						        'id'    => 'to_range',
						        'class' => '',
						        'value' => set_value('to_range')
							);
							echo form_input($data);

							// Assuming that the 'category' field value was incorrect:
							echo form_error('to_range', '<span class="help-block">', '</span>');
						?>
					</div>
					<div class="form-control">
						<?php
							$attributes = array(
						        'class' => '',
						        'style' => ''
							);
							echo form_label('Points:', 'points', $attributes);

							$data = array(
						        'type'  => 'text',
						        'name'  => 'points',
						        'id'    => 'points',
						        'class' => '',
						        'value' => set_value('points')
							);
							echo form_input($data);

							// Assuming that the 'category' field value was incorrect:
							echo form_error('points', '<span class="help-block">', '</span>');

							$data = array(
                                'product_id'  => encode_id($product[0]['id'])
                            );
                            echo form_hidden($data);
						?>
					</div>
					<div class="form-control form-submit clearfix">
						<!-- <a href="javascript:void(0);" class="reset">
							Reset
						</a> -->
						<a href="#">
							<img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav">
							<span><input class="custom_button" type="submit" name="Submit" value="OK"></span>
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
            from_range: {
                required: true,
                number:true
            },
            to_range: {
                required: true,
                number:true
            },
            points: {
                required: true,
                number:true
            }
        },
        messages: {
            from_range: {
                required: "Please Enter Min Range",
                number:"Min Range should be number"
            },
            to_range: {
                required: "Please Enter Max Range",
                number:"Max Range should be number"
            },
            points: {
                required: "Please Enter Points",
                number:"Points should be number"
            }
        }
    });

</script>