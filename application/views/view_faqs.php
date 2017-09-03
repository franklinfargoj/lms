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
	<body>
		<div class="header">
			<div class="container">
				<div class="logo">
					<img src="<?php echo base_url().ASSETS;?>images/logo.png">
				</div>
				<div class="right-nav">
					<div class="log-in">
						<a href="<?php echo site_url();?>">
	                        <img src="<?php echo base_url().ASSETS;?>images/login-btn.png" alt="">
	                        <span>Login</span>
	                    </a>
					</div>
				</div>
			</div>
		</div>
		<div class="main-content">
			<div class="page-title">
				<div class="container clearfix">
					<h3 class="text-center">Frequently Asked Questions</h3>
				</div>
			</div>
			<div class="page-content">
			  	<span class="bg-top"></span>
				<div class="inner-content">
				  	<div class="container">
						<div id="accordion" class="faq-accordion">
						    	<?php 
									if($faqs){
										foreach ($faqs as $key => $value) {
								?>
						              	<h3><span>.</span><?php echo $value['question'];?></h3>  
							            <div>
							            	<p><?php echo $value['answer'];?></p>  
							            </div>
				            	<?php 
										}
									}
								?>
						</div>
	       			</div>
				</div>
				<span class="bg-bottom"></span>
			</div>
		</div>
		<div class="footer">
			<div class="container">
				<div class="copyright">
					Copyright &copy; Dena Sampark. <?php echo date("Y")?>
				</div>
			</div>
		</div>
		<script src="<?php echo base_url().ASSETS;?>/js/jquery.min.js" type="text/javascript"></script>
		<script src = "<?php echo base_url().ASSETS;?>/js/jquery-ui.js"></script>
		<script>
			$( function() {
				$( "#accordion" ).accordion();
			});
		</script>
	</body>
</html>