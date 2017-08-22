<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <link rel="icon" href="images/favicon.png" type="image/x-icon">
		<title>Dena Bank</title>
		<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
		<link href="<?php echo base_url().ASSETS;?>/css/style.css" rel="stylesheet">
		<link href="<?php echo base_url().ASSETS;?>/css/responsive.css" rel="stylesheet">
	</head>
	<body class="login-page">
		<div class="login-wrapper">
			<div class="bank-logo">
				<img src="<?php echo base_url().ASSETS;?>/images/login-logo.jpg">
			</div>
			<div class="login-form">
				<!-- <form class="form"> -->
				<?php 
					$attributes = array(
						'role' => 'form',
						'method' => 'post',
						'class' => 'form',
						'id' => 'login-form',
						'autocomplete' => 'off'
					);
					echo form_open(site_url().'login/check_login', $attributes);
				?>
				<?php echo $this->load->view('common/message',array(),TRUE);?>
					<div class="form-control">
						<!-- <label>User ID</label> -->
						<?php 
							$attributes = array(
						        'class' => '',
						        'style' => ''
							);
							echo form_label('User ID', 'username', $attributes);
						?>
						<div class="input-control">
							<?php 
								$data = array(
							        'type'  => 'text',
							        'name'  => 'username',
							        'id'    => 'username',
							        'class' => '',
							        'placeholder' => '',
							        'autocomplete' => 'off'
							        
								);
								echo form_input($data);
							?>
						</div>
					</div>
					<?php 
						// Assuming that the 'title' field value was incorrect:
						echo form_error('username', '<span class="help-block">', '</span>');
					?>
					<div class="form-control">
						<!-- <label>Password</label> -->
						<?php 
							$attributes = array(
						        'class' => '',
						        'style' => ''
							);
							echo form_label('Password', 'password', $attributes);
						?>
						<div class="input-control">
							<?php 
								$data = array(
							        'type'  => 'password',
							        'name'  => 'password',
							        'id'    => 'password',
							        'class' => '',
							        'placeholder'  => '',
							        'autocomplete' => 'off'
							    );
								echo form_input($data);
							?>
						</div>
					</div>
					<?php 
						//Assuming that the 'password' field value was incorrect:
						echo form_error('password', '<span class="help-block">', '</span>');
					?>
					<div class="form-options clearfix">
						<a href="javascript:void(0)" class="float-left">FAQ'S</a>
						<div class="float-right">
							<label class="control control--checkbox">Remember Me ?
						      	<input type="checkbox"/>
						      	<div class="control__indicator">
						      		<div class="check">
						      			<img src="<?php echo base_url();?>assets1/images/tick.png">
						      		</div>
						      	</div>
						    </label>
						</div>
					</div>
					<div class="form-control form-submit clearfix">
						<input type="submit" name="submit" value="Login" class="submit-btn">
					</div>
				</form>
			</div>
		</div>
		<div class="footer-login">
			<span>Copyright &copy; Dena Sampark. 2017</span>
		</div>

		<script src="<?php echo base_url().ASSETS;?>/js/jquery.min.js" type="text/javascript"></script>
		<script src="<?php echo base_url().ASSETS;?>/js/jquery.validate.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			var inputs = $('.control--checkbox input');
			inputs.on('change', function(){
			var ref = $(this),
			    wrapper = ref.parent();
			if(ref.is(':checked')) wrapper.addClass('checked');
			else wrapper.removeClass('checked');
			});
			inputs.trigger('change');

			$(document).ready(function(){
				$('#login-form').validate({
		            errorElement: 'span', //default input error message container
		            errorClass: 'help-block', // default input error message class
		            focusInvalid: false, // do not focus the last invalid input
		            rules: {
		                username: {
		                    required: true
		                },
		                password: {
		                    required: true
		                },
		                remember: {
		                    required: false
		                }
		            },

		            messages: {
		                username: {
		                    required: "Username is required."
		                },
		                password: {
		                    required: "Password is required."
		                }
		            },
				});
			})
		</script>

	</body>
</html>