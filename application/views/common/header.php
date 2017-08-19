<div class="header">
	<div class="container">
		<div class="logo">
			<img src="<?php echo base_url().ASSETS;?>/images/logo.png">
		</div>
		<div class="top-navigation">
			<ul>
				<li>
					<a href="<?php echo site_url('dashboard')?>" class="active">
						<img src="<?php echo base_url().ASSETS;?>/images/left-nav.png">
						<span>Home</span>
						<img src="<?php echo base_url().ASSETS;?>/images/right-nav.png">
					</a>
				</li>
				<li>
					<a href="<?php echo site_url('product_category')?>">
						<img src="<?php echo base_url().ASSETS;?>/images/left-nav.png">
						<span>Category</span>
						<img src="<?php echo base_url().ASSETS;?>/images/right-nav.png">
					</a>
				</li>
				<li>
					<a href="<?php echo site_url('product')?>">
						<img src="<?php echo base_url().ASSETS;?>/images/left-nav.png">
						<span>Product</span>
						<img src="<?php echo base_url().ASSETS;?>/images/right-nav.png">
					</a>
				</li>
				<li>
					<a href="<?php echo site_url('faq')?>">
						<img src="<?php echo base_url().ASSETS;?>/images/left-nav.png">
						<span>FAQs</span>
						<img src="<?php echo base_url().ASSETS;?>/images/right-nav.png">
					</a>
				</li>
				<li>
					<a href="<?php echo site_url('ticker')?>">
						<img src="<?php echo base_url().ASSETS;?>/images/left-nav.png">
						<span>Ticker</span>
						<img src="<?php echo base_url().ASSETS;?>/images/right-nav.png">
					</a>
				</li>
				
				<li>
					<a href="<?php echo site_url('leads/upload')?>">
						<img src="<?php echo base_url().ASSETS;?>/images/left-nav.png">
						<span>Lead Upload</span>
						<img src="<?php echo base_url().ASSETS;?>/images/right-nav.png">
					</a>
				</li>
				<li>
					<a href="<?php echo site_url('leads/add')?>">
						<img src="<?php echo base_url().ASSETS;?>/images/left-nav.png">
						<span>Add Lead</span>
						<img src="<?php echo base_url().ASSETS;?>/images/right-nav.png">
					</a>
				</li>
				<li>
					<a href="<?php echo site_url('leads/unassigned_leads')?>">
						<img src="<?php echo base_url().ASSETS;?>/images/left-nav.png">
						<span> Unassigned Leads</span>
						<img src="<?php echo base_url().ASSETS;?>/images/right-nav.png">
					</a>
				</li>
			</ul>
		</div>
		<div class="right-nav">
			<div class="notification">
				<span class="count">4</span>
				<img src="<?php echo base_url().ASSETS;?>/images/bell.png">
			</div>
			<div class="logged-in">
				<span class="name">Hi <?php echo $this->session->userdata('admin_name');?> !!</span>
				<a href="<?php echo site_url('login/logOut');?>">Logout</a>
			</div>
		</div>
	</div>
</div>