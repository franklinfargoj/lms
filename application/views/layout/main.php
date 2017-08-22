<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?php echo base_url().ASSETS;?>images/favicon.png" type="image/x-icon">
	<title>Dena Bank</title>
	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
	<link href="<?php echo base_url().ASSETS;?>/css/style.css" rel="stylesheet">
	<link href="<?php echo base_url().ASSETS;?>/css/responsive.css" rel="stylesheet">
	<script src="<?php echo base_url().ASSETS;?>/js/jquery.min.js" type="text/javascript"></script>
</head>
<body>
	<?php echo $this->load->view('common/header',array(),TRUE);?>

	<div class="main-content">
		<div class="container clearfix">
			<?php echo $this->load->view('common/breadcumb',array(),TRUE);?>

			<?php echo $this->load->view('common/message',array(),TRUE);?>

			<?php echo $this->load->view($middle,array(),TRUE);?>
		</div>
	</div>
	<?php echo $this->load->view('common/footer',array(),TRUE);?>
</body>
</html>