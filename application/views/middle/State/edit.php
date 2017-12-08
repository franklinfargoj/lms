<!-- BEGIN  ADD PRODUCT CATEGORY-->
<div class="portlet light">
	<div class="portlet-title">
		<div class="caption">
			<!-- <i class="fa fa-cogs font-green-sharp"></i> -->
			<span class="caption-subject font-green-sharp bold">Add State</span>
		</div>
		<div class="tools">
			<a href="<?php echo base_url('state');?>" class="">List
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
			echo form_open(base_url().'/state/edit/'.$this->uri->segment(3,0), $attributes);
		?>
		<!-- <form role="form"> -->
			<div class="form-body">
				<!-- <div class="form-group">
					<label>Large Input</label>
					<input type="text" class="form-control input-lg" placeholder="input-lg">
				</div> -->
				<div class="form-group <?php if(isset($has_error)){ echo $has_error;}?>">
					<?php 
						$attributes = array(
					        'class' => '',
					        'style' => ''
						);
						echo form_label('Product Category Name', 'title', $attributes);

						$data = array(
					        'type'  => 'text',
					        'name'  => 'title',
					        'id'    => 'title',
					        'class' => 'form-control',
					        'value' => $stateDetail[0]['title'],
					        'placeholder' => '' 
						);
						echo form_input($data);
						
						// Assuming that the 'title' field value was incorrect:
						echo form_error('title', '<span class="help-block">', '</span>');
					?>
				</div>
			</div>
			<div class="form-actions right">
				<button type="button" class="btn default">Cancel</button>
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

    $("#edit_form").validate({

        rules: {
            title: {
                required: true
            }
        },
        messages: {
            title: {
                required: "Please Enter State"
            }
        }
    });

</script>