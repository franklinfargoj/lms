<?php
$controller =  $this->router->fetch_class();
	if($controller != 'dashboard'){
		if($this->session->flashdata('success')) {?>
			<span style="color:green"><?php echo $this->session->flashdata('success');?></span>
		<?php }?>
		<?php if($this->session->flashdata('error')) {?>
			<span style="color:red"><?php echo $this->session->flashdata('error');?></span>
		<?php 
		}
	}
?>