<?php
	$controller =  $this->router->fetch_class();
	$method =  $this->router->fetch_method();
    $param1 = $this->uri->segment(3,0);
    $param2 = $this->uri->segment(4,0);
    $param3 = $this->uri->segment(5,0);
    $param4 = $this->uri->segment(6,0);
?>

<div class="header">
	<div class="container">
		<div class="logo">
			<img src="<?php echo base_url().ASSETS;?>images/logo.png" alt="logo">
		</div>
		<div class="top-navigation">
			<ul>
				<li class="<?php echo ((($controller == 'dashboard') && (in_array($method,array('index','leads_status','leads_performance')))) || (($controller == 'leads') && (in_array($method,array('leads_list','details'))) && ($param1 == 'generated'))) ? 'active' : ''?>">
					<a href="<?php echo site_url('dashboard')?>">
						Home
					</a>
				</li>
				<?php if(in_array($this->session->userdata('admin_type'),array('EM','BM','ZM','GM'))) {?>
				<li class="<?php echo ($controller == 'leads' && $method == 'add') ? 'active' : ''?>">
					<a href="<?php echo site_url('leads/add')?>">
						Add Lead 
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('Super admin'))) {?>
				<li class="<?php echo ($controller == 'product_category') ? 'active' : ''?>">
					<a href="<?php echo site_url('product_category')?>">
						Category
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('Super admin'))) {?>
				<li class="<?php echo (($controller == 'product') || ($controller == 'product_guide' && ($method == 'index' || $method == 'add'))) ? 'active' : ''?>">
					<a href="<?php echo site_url('product')?>">
						Products
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('Super admin'))) {?>
				<li class="<?php echo ($controller == 'faq') ? 'active' : ''?>">
					<a href="<?php echo site_url('faq')?>">
						FAQs
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('Super admin'))) {?>
				<li class="<?php echo ($controller == 'ticker') ? 'active' : ''?>">
					<a href="<?php echo site_url('ticker')?>">
						Tickers
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('Super admin'))) {?>
				<li class="<?php echo ($controller == 'leads' && $method == 'upload') ? 'active' : ''?>">
					<a href="<?php echo site_url('leads/upload')?>">
						Leads Upload
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('BM'))) {?>
				<li class="<?php echo (($controller == 'leads') && (in_array($method,array('unassigned_leads','unassigned_leads_list','unassigned_leads_details')))) ? 'active' : ''?>">
					<a href="<?php echo site_url('leads/unassigned_leads')?>">
						Unassigned Leads
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('EM','BM'))) {?>
				<li class="<?php echo (($controller == 'leads') && (in_array($method,array('leads_list','details'))) && $param1 == 'assigned') ? 'active' : ''?>">
					<a href="<?php echo site_url('leads/leads_list/assigned/ytd')?>">
						Assigned Leads
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('EM','BM','ZM','GM'))) {?>
				<li class="<?php echo (($controller == 'dashboard') && ($method == 'emi_calculator')) ? 'active' : ''?>">
					<a href="<?php echo site_url('dashboard/emi_calculator')?>">
						Calculator
					</a>
				</li>
                <li class="<?php echo (($controller == 'dashboard') && ($method == 'fd_calculator')) ? 'active' : ''?>">
					<a href="<?php echo site_url('dashboard/fd_calculator')?>">
						Fd Calculator
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('EM','BM','ZM','GM'))) {?>
				<li class="<?php echo ($controller == 'product_guide') ? 'active' : ''?>">
					<a href="<?php echo site_url('product_guide/view')?>">
						Product Guide
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('BM','ZM','GM'))) {?>
				<li class="<?php echo ($controller == 'reports') ? 'active' : ''?>">
					<a href="<?php echo site_url('reports/view')?>">
						Reports
					</a>
				</li>
				<?php }?>
			</ul>
		</div>
		<div class="right-nav">
				<div class="notification">
					<a href="<?php echo site_url('notification');?>">
						<?php if(get_notification_count() > 0){?>
						<span class="count"><?php echo get_notification_count();?></span>
						<?php }?>
						<img src="<?php echo base_url().ASSETS;?>images/bell.png" alt="bell">
					</a>
				</div>
				<div class="logged-in">
					<img src="<?php echo base_url().ASSETS;?>images/pic.png" alt="pic">
					<div>
					<span class="name">Hi, <?php echo $this->session->userdata('admin_name');?> !!</span>
						<span class="name">(<?php echo $this->session->userdata('admin_type');?>)</span>
					<a href="<?php echo site_url('login/logOut');?>">Logout</a>
					</div>
				</div>
			</div>
	</div>
</div>