<?php
$controller =  $this->router->fetch_class();
	if($controller != 'dashboard'){
		if($this->session->flashdata('success')) {?>
			<div id="prefix_1021245302593" class="Metronic-alerts alert alert-success fade in">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">
					
				</button>
				<?php echo $this->session->flashdata('success');?>
			</div>
		<?php }?>
		<?php if($this->session->flashdata('error')) {?>
			<div id="prefix_1327623515599" class="Metronic-alerts alert alert-danger fade in">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">
					
				</button>
				<?php echo $this->session->flashdata('error');?>
			</div>
		<?php 
		}
	}
?>