<?php
$lead_source_list = $this->config->item('lead_source');
?>
<div class="page-title">
	<div class="container clearfix">
		<h3 class="text-center">Update Lead Routing Mapping</h3>

	</div>
</div>
<div class="page-content">
	<span class="bg-top"></span>
	<div class="inner-content">
		<div class="container">
            <div class="float-right">
                <span class="lead-num"><a href="<?php echo site_url('rapc/mapping_list');?>">Back</a></span>
            </div>
		<p id="note"><span style="color:red;">*</span> These fields are required</p>
			<div class="product-category add-product">
				<?php
				$attributes = array(
					'role' => 'form',
					'id' => 'add_form',
					'class' => 'form',
					'autocomplete' => 'off'
				);
				echo form_open(base_url().'rapc/edit_mapping/'.encode_id($lead_source), $attributes);
				?>
                <div class="form-control">
                    <label>Lead Source:<span style="color:red;">*</span></label>
                    <?php echo ucwords($lead_source_list[$lead_source]); ?>
                </div>
					<div class="form-control">
						<label>Default Assign:<span style="color:red;">*</span></label>
						<div class="radio-control">
							<input type="radio" id= "self" name="default_assign" value="0" <?php
							echo set_value('status', $routeDetail[0]['route_to']) == '0' ? "checked" : "";
							?> />
							<label>Branch</label>
						</div>
						<div class="radio-control">
							<input type="radio" id= "branch" name="default_assign" value="1" <?php
							echo set_value('status', $routeDetail[0]['route_to']) == '1' ? "checked" : "";
							?> />
							<label>Processing Center</label>
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