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
					echo form_open(site_url().'login', $attributes);
				?>
				<h3>LOGIN</h3>
				<?php echo $this->load->view('common/message',array(),TRUE);?>
				<div class="form-control user-details">
					<?php 
							$attributes = array(
						        'class' => '',
						        'style' => ''
							);
						//	echo form_label('Username', 'username', $attributes);
						?>
					<div class="input-control">
						<?php 
								$data = array(
							        'type'  => 'text',
							        'name'  => 'username',
							        'id'    => 'username',
							        'class' => '',
							        'value'  => isset($_COOKIE["member_login"]) ? $_COOKIE["member_login"] : 'HRMS ID',
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
							//echo form_label('Password', 'password', $attributes);
						?>
					<div class="input-control">
						<?php 
								$data = array(
							        'type'  => 'password',
							        'name'  => 'password',
							        'id'    => 'password',
							        'class' => '',
							        'value'  => isset($_COOKIE["member_password"]) ? $_COOKIE["member_password"] : 'Password',
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
<p class="pswd-note">* Please Use HRMS Password</p>
				<?php echo $this->load->view('common/captcha',array(),TRUE);?>
				<div class="form-control form-submit clearfix">
					<!-- <input type="submit" name="submit" value="LOGIN" class="submit-btn"> -->

					<button type="button" class="full-btn" id="form-sub">
<img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav" class="left-btn-img">
<span class="btn-txt">LOGIN</span>
<img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="left-nav" class="right-btn-img">
</button>
				</div>
				<div class="form-options clearfix">
					<a href="<?php echo site_url('login/view_faqs')?>" class="float-right">FAQ</a>
					<div class="float-left">
						<label class="control control--checkbox">Remember me ?
					      	<input type="checkbox" id="remember_me" name="remember_me" <?php if(isset($_COOKIE["member_login"])) { ?> checked <?php } ?>/>
					      	<div class="control__indicator">
					      		<div class="check">
					      			<img src="<?php echo base_url().ASSETS;?>images/tick.png" alt="tick">
					      		</div>
					      	</div>
					    </label>
					</div>
				</div>
				<?php echo form_close();?>
			<!-- </form> -->
		</div>
		<div class="bank-logo">
			<img src="<?php echo base_url().ASSETS;?>images/login-logo.png" alt="login-logo">
		</div>
	</div>
	
			<?php 
				if($tickers){
			?>
			<div class="footer-login">
				<marquee onmouseover="this.stop();" onmouseout="this.start();">
				<?php 
					foreach ($tickers as $key => $value) {
				?>
					<img src="<?php echo base_url().ASSETS;?>images/small-circle.png" alt="small circle"><a href="<?php echo site_url('login/view_tickers/'.encode_id($value['id']));?>"><?php echo $value['title'];?></a>
				<?php 		
					}
				?>
				</marquee>
		</div>
			<?php
				}
			?>
			
		
	<script src="<?php echo base_url().ASSETS;?>/js/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo base_url().ASSETS;?>/js/jquery.base64.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url().ASSETS;?>/js/jquery.validate.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		var base_url = "<?php echo base_url()?>";
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
	            focusInvalid: true, // do not focus the last invalid input
	            rules: {
	                username: {
	                    required: true,
	                    number: true
	                },
	                password: {
	                    required: true
	                },
	                /*captcha: {
	                    required: true
	                },*/
	                captext: {
	                    required: true
	                }
	            },

	            messages: {
	                username: {
	                    required: "Please enter HRMS ID",
	                    number : "HRMS ID should contain only number"
	                },
	                password: {
	                    required: "Please enter password"
	                },
	                /*captcha: {
	                    required: "Please enter security code"
	                },*/
	                captext: {
	                    required: "Please enter security code"
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

			setTimeout(function(){        
			        $(".success_message").fadeOut('slow');
			        $(".error_message").fadeOut('slow');
		    }, 10000);

			$('#username')
				  .on('focus', function(){
				      var $this = $(this);
				      if($this.val() == 'HRMS ID'){
				          $this.val('');
				      }
				  })
				  .on('blur', function(){
				      var $this = $(this);
				      if($this.val() == ''){
				          $this.val('HRMS ID');
				      }
				  });

		  	$('#password')
				  .on('focus', function(){
				      var $this = $(this);
				      if($this.val() == 'Password'){
				          $this.val('');
				      }
				  })
				  .on('blur', function(){
				      var $this = $(this);

				      if($this.val() == ''){
				          $this.val('Password');
				      }


				  });
			$('#form-sub').click(function () {
				var newpwd = $('#password').val();
                            if(window.btoa){
                             var enc_salt = window.btoa(window.btoa(newpwd))+'/'+"<?php echo get_salt();?>";
                               }else{
                            var enc_salt = $.base64.encode($.base64.encode(newpwd))+'/'+"<?php echo get_salt();?>";
                         }
                $.ajax({
                method: "POST",
                url: base_url + "login/aes",
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    'auth' : enc_salt
                }
            }).success(function (resp) {
              var rre = JSON.parse(resp);
              var aes = rre['aes'];
              $('#password').val(aes);
              $('#login-form').submit();
            });

				
			});



		})
	
	$('#refresh_captcha').click(function(){
        $.ajax({
                method: "GET",
                url: base_url + "login/load_captcha/refresh",
                data: {
                }
            }).success(function (resp) {
                if (resp) {
                    $('#captcha_img').html(resp);
                }
            });
    });
	</script>

</body>
</html>
