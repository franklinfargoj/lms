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
	<link href="<?php echo base_url().ASSETS;?>/css/style.css" rel="stylesheet">
	<link href="<?php echo base_url().ASSETS;?>/css/jquery-ui.css" rel="stylesheet">
	<script src="<?php echo base_url().ASSETS;?>/js/jquery.min.js" type="text/javascript"></script>
	<script src = "<?php echo base_url().ASSETS;?>/js/jquery-ui.js"></script>
</head>
<body>
	<h1>FAQs</h1>
	<div id="accordion">
		<?php 
			if($faqs){
				$i = 0;
				foreach ($faqs as $key => $value) {
					
		?>
		    <h3>
		    	<span><?php echo ++$i;?>.</span>&nbsp;&nbsp;&nbsp;
		    	<a href="#"><?php echo $value['question'];?></a></h3>
			<div>
			    <?php echo $value['answer'];?>
			</div>
		<?php 
				}
			}
		?>
	</div>

	<div><a href="<?php echo site_url();?>">Back</a></div>

	<script type="text/javascript">
		$(function() {
	    $("#accordion").accordion({ autoHeight: true});
	        $("#accordion").accordion({ collapsible: true });
	});
	</script>
</body>
</html>