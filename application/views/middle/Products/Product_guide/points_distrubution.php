<div class="page-title">
	<div class="container clearfix">
		<h3 class="text-center">Points Distrubution</h3>
		<div class="float-right">
            <span class="lead-num"><a href="<?php echo site_url('product');?>"><span><</span>Back</a></span>
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
					echo form_open(base_url().'/product_guide/points_distrubution/'.encode_id($product[0]['id']), $attributes);
				?>
					<div class="form-control">
						<?php
							$attributes = array(
						        'class' => '',
						        'style' => ''
							);
							echo form_label('Lead Generator Contribution (%):', 'generator_contrubution', $attributes);


							$data = array(
						        'type'  => 'text',
						        'name'  => 'generator_contrubution',
						        'id'    => 'generator_contrubution',
						        'class' => 'inputs',
						        'value' => isset($points_distrubution[0]['generator_contrubution']) ? $points_distrubution[0]['generator_contrubution'] : set_value('generator_contrubution'),
						        'min' => 0,
						        'max' => 100,
                                'maxlength' => 5
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
							echo form_label('Lead Convertor Contribution (%):', 'convertor_contrubution', $attributes);

							$data = array(
						        'type'  => 'text',
						        'name'  => 'convertor_contrubution',
						        'id'    => 'convertor_contrubution',
						        'class' => 'inputs',
						        'value' => isset($points_distrubution[0]['convertor_contrubution']) ? $points_distrubution[0]['convertor_contrubution'] : set_value('convertor_contrubution'),
						        'readonly'=>'true'
						        
							);
							echo form_input($data);

							// Assuming that the 'category' field value was incorrect:
							echo form_error('convertor_contrubution', '<span class="help-block">', '</span>');

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
            generator_contrubution: {
                required: true,
                number:true
            },
            convertor_contrubution: {
                required: true,
                number:true
            }
        },
        messages: {
            generator_contrubution: {
                required: "Please Enter Min Range",
                number:"Min Range should be number"
            },
            convertor_contrubution: {
                required: "Please Enter Max Range",
                number:"Max Range should be number"
            }
        }
    });

    $('body').on('blur','#generator_contrubution',function(){
    	var generator = $('#generator_contrubution').val();
    	if(generator != '' && generator <= 100){
    		var convertor = (100 - parseFloat(generator));
    		$('#convertor_contrubution').val(convertor);
    	}
    });

</script>