<?php
$controller =  $this->router->fetch_class();
	if($controller != 'dashboard'){
		if($this->session->flashdata('success')) {?>
			<!-- <span style="color:green"></span> -->
			<div class="success_message"><?php echo $this->session->flashdata('success');?></div>
		<?php }?>
		<?php if($this->session->flashdata('error')) {?>
			<!-- <span style="color:red"></span> -->
			<div class="error_message"><?php echo $this->session->flashdata('error');?></div>
		<?php 
		}
	}
?>