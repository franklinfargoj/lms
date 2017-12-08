<link href="<?php echo base_url();?>assets/admin/pages/css/profile.css" rel="stylesheet" type="text/css"/>
<!-- BEGIN PAGE CONTENT INNER -->
<div class="row">
	<div class="col-md-4">
    	<div class="profile-sidebar" style="width: 250px;">
			<!-- PORTLET MAIN -->
			<div class="portlet light profile-sidebar-portlet">
				<!-- SIDEBAR USERPIC -->
				<div class="profile-userpic">
					<img src="<?php echo base_url();?>assets/admin/pages/media/profile/avatar.png" class="img-responsive" alt="">
				</div>
				<!-- END SIDEBAR USERPIC -->
				<!-- SIDEBAR USER TITLE -->
				<div class="profile-usertitle">
					<div class="profile-usertitle-name">
						 <?php echo $this->session->userdata('admin_name');?>
					</div>
				</div>
				<!-- END SIDEBAR USER TITLE -->
			</div>
			<!-- END PORTLET MAIN -->
		</div>
	</div>
    <div class="col-md-8">
		<!-- BEGIN  ADD PRODUCT CATEGORY-->
		<div class="portlet light">
			<div class="portlet-title">
				<div class="caption">
					<!-- <i class="fa fa-cogs font-green-sharp"></i> -->
					<span class="caption-subject font-green-sharp bold">Change Password</span>
				</div>
			</div>
			<div class="portlet-body form">
				<?php
					$attributes = array(
						'role' => 'form',
						'id' => 'add_form',
						'autocomplete' => 'off'
						);
					echo form_open(base_url().'my_profile/reset_password', $attributes);
				?>
				<!-- <form role="form"> -->
					<div class="form-body">
						<div class="form-group <?php if(isset($has_error)){ echo $has_error;}?>">
							<?php 
								$attributes = array(
							        'class' => 'control-label',
							        'style' => ''
								);
								echo form_label('Current Password', 'current_pwd', $attributes);

								$data = array(
							        'type'  => 'password',
							        'name'  => 'current_pwd',
							        'id'    => 'current_pwd',
							        'class' => 'form-control',
							        'value' => set_value('current_pwd') 
								);
								echo form_input($data);
								
								// Assuming that the 'title' field value was incorrect:
								echo form_error('current_pwd', '<span class="help-block">', '</span>');
							?>
						</div>
						<div class="form-group <?php if(isset($has_error)){ echo $has_error;}?>">
							<?php 
								$attributes = array(
							        'class' => 'control-label',
							        'style' => ''
								);
								echo form_label('New Password', 'new_pwd', $attributes);

								$data = array(
							        'type'  => 'password',
							        'name'  => 'new_pwd',
							        'id'    => 'new_pwd',
							        'class' => 'form-control',
							        'value' => set_value('new_pwd') 
								);
								echo form_input($data);
								
								// Assuming that the 'title' field value was incorrect:
								echo form_error('new_pwd', '<span class="help-block">', '</span>');
							?>
						</div>
						<div class="form-group <?php if(isset($has_error)){ echo $has_error;}?>">
							<?php 
								$attributes = array(
							        'class' => 'control-label',
							        'style' => ''
								);
								echo form_label('Re-type New Password', 're_pwd', $attributes);

								$data = array(
							        'type'  => 'password',
							        'name'  => 're_pwd',
							        'id'    => 're_pwd',
							        'class' => 'form-control',
							        'value' => set_value('re_pwd') 
								);
								echo form_input($data);
								
								// Assuming that the 'title' field value was incorrect:
								echo form_error('re_pwd', '<span class="help-block">', '</span>');
							?>
						</div>
					</div>
					<div class="form-actions right">
						<button type="reset" class="btn default">Cancel</button>
						<button type="submit" class="btn green-haze">Change Password </button>
					</div>
				<!-- </form> -->
				<?php echo form_close();?>
			</div>
		</div>
		<!-- END ADD PRODUCT CATEGORY-->
	</div>
</div>
<!-- END PAGE CONTENT INNER -->

<script type="text/javascript">
 	$.validator.addMethod("regx", function(value, element, regexpr) {
        return regexpr.test(value);
    });

    $("#add_form").validate({

        rules: {
            current_pwd: {
                required: true
            },
            new_pwd: {
                required: true
            },
            re_pwd: {
                required: true
            }
        },
        messages: {
            current_pwd: {
                required: "Please type current password"
            },
            new_pwd: {
                required: "Please type new password"
            },
            re_pwd: {
                required: "Please re-type new password"
            }
        }
    });

</script>