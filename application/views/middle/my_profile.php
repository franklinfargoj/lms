<link href="<?php echo base_url();?>assets/admin/pages/css/profile.css" rel="stylesheet" type="text/css"/>
<!-- BEGIN PAGE CONTENT INNER -->
<div class="row">
	<div class="col-md-4">
    	<div class="profile-sidebar" style="width: 250px;">
			<!-- PORTLET MAIN -->
			<div class="portlet light profile-sidebar-portlet">
				<!-- SIDEBAR USERPIC -->
				<div class="profile-userpic">
					<img src="<?php echo base_url();?>assets/admin/pages/media/profile/profile_user.jpg" class="img-responsive" alt="">
				</div>
				<!-- END SIDEBAR USERPIC -->
				<!-- SIDEBAR USER TITLE -->
				<div class="profile-usertitle">
					<div class="profile-usertitle-name">
						 <?php echo $this->session->userdata('admin_name');?>
					</div>
					<!-- <div class="profile-usertitle-job">
						 Developer
					</div> -->
				</div>
				<!-- END SIDEBAR USER TITLE -->
				<!-- SIDEBAR BUTTONS -->
				<!-- <div class="profile-userbuttons">
					<button type="button" class="btn btn-circle green-haze btn-sm">Follow</button>
					<button type="button" class="btn btn-circle btn-danger btn-sm">Message</button>
				</div> -->
				<!-- END SIDEBAR BUTTONS -->
				<!-- SIDEBAR MENU -->
				<!-- <div class="profile-usermenu">
					<ul class="nav">
						<li>
							<a href="extra_profile.html">
							<i class="icon-home"></i>
							Overview </a>
						</li>
						<li class="active">
							<a href="extra_profile_account.html">
							<i class="icon-settings"></i>
							Account Settings </a>
						</li>
						<li>
							<a href="page_todo.html" target="_blank">
							<i class="icon-check"></i>
							Tasks </a>
						</li>
						<li>
							<a href="extra_profile_help.html">
							<i class="icon-info"></i>
							Help </a>
						</li>
					</ul>
				</div> -->
				<!-- END MENU -->
			</div>
			<!-- END PORTLET MAIN -->
			<!-- PORTLET MAIN -->
			<!-- <div class="portlet light">
				STAT
				<div class="row list-separated profile-stat">
					<div class="col-md-4 col-sm-4 col-xs-6">
						<div class="uppercase profile-stat-title">
							 37
						</div>
						<div class="uppercase profile-stat-text">
							 Projects
						</div>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-6">
						<div class="uppercase profile-stat-title">
							 51
						</div>
						<div class="uppercase profile-stat-text">
							 Tasks
						</div>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-6">
						<div class="uppercase profile-stat-title">
							 61
						</div>
						<div class="uppercase profile-stat-text">
							 Uploads
						</div>
					</div>
				</div>
				END STAT
				<div>
					<h4 class="profile-desc-title">About Marcus Doe</h4>
					<span class="profile-desc-text"> Lorem ipsum dolor sit amet diam nonummy nibh dolore. </span>
					<div class="margin-top-20 profile-desc-link">
						<i class="fa fa-globe"></i>
						<a href="http://www.keenthemes.com">www.keenthemes.com</a>
					</div>
					<div class="margin-top-20 profile-desc-link">
						<i class="fa fa-twitter"></i>
						<a href="http://www.twitter.com/keenthemes/">@keenthemes</a>
					</div>
					<div class="margin-top-20 profile-desc-link">
						<i class="fa fa-facebook"></i>
						<a href="http://www.facebook.com/keenthemes/">keenthemes</a>
					</div>
				</div>
			</div> -->
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
					echo form_open(base_url().'/my_profile/change_password', $attributes);
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