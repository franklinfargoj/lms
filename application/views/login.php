<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?php echo base_url().ASSETS;?>images/favicon.png" type="image/x-icon">
	<title>Dena Bank</title>
	<link href="<?php echo base_url().ASSETS;?>css/Lato.css" rel="stylesheet">
	<link href="<?php echo base_url().ASSETS;?>css/Montserrat.css" rel="stylesheet"> 
	<link href="<?php echo base_url().ASSETS;?>css/style.css" rel="stylesheet">
	<link href="<?php echo base_url().ASSETS;?>css/responsive.css" rel="stylesheet">
	<link href="<?php echo base_url().ASSETS;?>css/override.css" rel="stylesheet">
</head>
<body class="login-page">
	<div class="login-wrapper">
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
				<h3>LOGIN</h3>
				<div class="form-control user-details">
					<?php 
							$attributes = array(
						        'class' => '',
						        'style' => ''
							);
							echo form_label('Username', 'username', $attributes);
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
				<div class="form-control password">
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
				<div class="form-control form-submit clearfix">
					<!-- <input type="submit" name="submit" value="LOGIN" class="submit-btn"> -->
					<a href="javascript:void(0);" class="active">
						<img src="<?php echo base_url().ASSETS;?>images/left-nav.png">
						<!-- <span>LOGIN</span> -->
						<span><input type="submit" name="submit" value="LOGIN" class=""></span>
						<img src="<?php echo base_url().ASSETS;?>images/right-nav.png">
					</a>
				</div>
				<div class="form-options clearfix">
					<a href="<?php echo site_url('login/view_faqs')?>" class="float-right">FAQ's</a>
					<div class="float-left">
						<label class="control control--checkbox">Remember me ?
					      	<input type="checkbox" />
					      	<div class="control__indicator">
					      		<div class="check">
					      			<img src="<?php echo base_url().ASSETS;?>images/tick.png">
					      		</div>
					      	</div>
					    </label>
					</div>
				</div>
				<?php echo form_close();?>
			<!-- </form> -->
		</div>
		<div class="bank-logo">
			<img src="<?php echo base_url().ASSETS;?>images/login-logo.png">
		</div>
	</div>
	
			<?php 
				if($tickers){
			?>
			<div class="footer-login">
				<marquee>
				<?php 
					foreach ($tickers as $key => $value) {
				?>
					&bull;<a href="<?php echo site_url('login/view_tickers/'.encode_id($value['id']));?>"><?php echo $value['title'];?></a>
				<?php 		
					}
				?>
				</marquee>
		</div>
			<?php
				}
			?>
			
		
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
	                    required: "Please enter username"
	                },
	                password: {
	                    required: "Please enter password"
	                }
	            },
	            highlight: function (element) { // hightlight error inputs
	                $(element)
	                    .closest('.form-control').addClass('has-error'); // set error class to the control group
	            },
	            errorPlacement: function(error, element) {
				    error.insertAfter(element.closest('.form-control'));
				}
			});
		})
	</script>

</body>
</html>