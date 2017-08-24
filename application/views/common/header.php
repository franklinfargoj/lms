<div class="header">
	<div class="container">
		<div class="logo">
			<img src="<?php echo base_url().ASSETS;?>/images/logo.png">
		</div>
		<div class="top-navigation">
			<ul>
				<li class="active">
					<a href="<?php echo site_url('dashboard')?>" >
						Home
					</a>
				</li>
				<?php if(in_array($this->session->userdata('admin_type'),array('Super admin'))) {?>
				<li>
					<a href="<?php echo site_url('product_category')?>">
						Category
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('Super admin'))) {?>
				<li>
					<a href="<?php echo site_url('product')?>">
						Products
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('Super admin'))) {?>
				<li>
					<a href="<?php echo site_url('faq')?>">
						FAQs
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('Super admin'))) {?>
				<li>
					<a href="<?php echo site_url('ticker')?>">
						Tickers
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('Super admin'))) {?>
				<li>
					<a href="<?php echo site_url('leads/upload')?>">
						Leads Upload
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('EM','BM'))) {?>
				<li>
					<a href="<?php echo site_url('leads/leads_list/assigned/ytd')?>">
						Assigned Leads
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('BM'))) {?>
				<li>
					<a href="<?php echo site_url('leads/unassigned_leads')?>">
						Unassigned Leads
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('EM','BM'))) {?>
				<li>
					<a href="<?php echo site_url('leads/add')?>">
						Add Lead 
					</a>
				</li>
				<?php }?>

			</ul>
		</div>
		<div class="right-nav">
				<div class="notification">
					<span class="count">4</span>
					<img src="<?php echo base_url().ASSETS;?>images/bell.png">
				</div>
				<div class="logged-in">
					<img src="<?php echo base_url().ASSETS;?>images/pic.png" alt="pic">
					<div>
					<span class="name">Hi, <?php echo $this->session->userdata('admin_name');?> !!</span>
					<a href="<?php echo site_url('login/logOut');?>">Logout</a>
					</div>
				</div>
			</div>
	</div>
</div>