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
            <a href="<?php echo site_url('dashboard')?>">
			    <img src="<?php echo base_url().ASSETS;?>images/logo.png" alt="logo">
            </a>
		</div>
		<div class="top-navigation">
			<ul>
				<li class="<?php echo ((($controller == 'dashboard') && (in_array($method,array('index','leads_performance'))))) ? 'active' : ''?>">
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
				<li class="<?php echo (($controller == 'product') || ($controller == 'product_guide' && (in_array($method,array('index','add','manage_points','view_points','points_distrubution'))))) ? 'active' : ''?>">
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
				<?php if(in_array($this->session->userdata('admin_type'),array('Super admin'))) {?>
					<li class="<?php echo ($controller == 'rapc' || ($controller == 'rapc' && $method == 'upload')) ? 'active' : ''?>">
						<a href="<?php echo site_url('rapc')?>">
							RAPC
						</a>
					</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('Super admin'))) {?>
					<li class="<?php echo ($controller == 'rapc' && $method == 'route') ? 'active' : ''?>">
						<a href="<?php echo site_url('rapc/mapping_list')?>">
							Lead Routing
						</a>
					</li>
				<?php }?>

<!--                --><?php //if(in_array($this->session->userdata('admin_type'),array('Super admin'))) {?>
<!--				<li class="--><?php //echo ($controller == 'leads' && $method == 'upload_employee') ? 'active' : ''?><!--">-->
<!--					<a href="--><?php //echo site_url('leads/upload_employee')?><!--">-->
<!--						Employee Upload-->
<!--					</a>-->
<!--				</li>-->
<!--				--><?php //}?>
				<?php if(in_array($this->session->userdata('admin_type'),array('BM'))) {?>
				<li class="<?php echo (($controller == 'leads') && (in_array($method,array('unassigned_leads','unassigned_leads_list','unassigned_leads_details')))) ? 'active' : ''?>">
					<a href="<?php echo site_url('leads/unassigned_leads')?>">
						Unassigned Leads
                        <?php if(unassignedLeadCount() > 0){?>
                            <span class="count"><?php echo unassignedLeadCount();?></span>
                        <?php }?>
                    </a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('EM'))) {?>
					<li class="<?php echo (($controller == 'leads' || $controller == 'dashboard') && ( $method == 'leads_status' || $method == 'generated_conversion')) ? 'active' : ''?>" id="per-droped">
						<a href="#" >
							My Performance 	&#9662;
						</a>
						<ul class="per-drop">
							<li>
<!--								<a href="--><?php //echo site_url('dashboard/leads_status/assigned')?><!--">-->
								<a href="<?php echo site_url('dashboard/leads_performance')?>">
									Lead Assigned
								</a>
							</li>
							<li>
								<a href="<?php echo site_url('dashboard/generated_conversion')?>">
									Lead Generated
								</a>
							</li>
						</ul>
					</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('BM','EM'))) {?>
				<li class="<?php echo (($controller == 'leads') && (in_array($method,array('leads_list','details'))) && $param1 == 'assigned') ? 'active' : ''?>">
					<a href="<?php echo site_url('leads/leads_list/assigned/ytd')?>">
						Assigned Leads
                        <?php $admin = $this->session->userdata('admin_type'); $total_count = assignedLeadCount($admin);
                        if($total_count > 0){?>
                            <span class="count"><?php echo $total_count;?></span>
                        <?php }?>
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('EM','BM','ZM','GM'))) {?>
				<li class="<?php echo (($controller == 'dashboard') && ($method == 'emi_calculator' || $method == 'fd_calculator' || $method == 'rd_calculator')) ? 'active' : ''?>" id="cal-droped">
					<a href="#" >
						Calculator 	&#9662;
					</a>
					<ul class="cal-drop">
						<li>
						<a href="<?php echo site_url('dashboard/emi_calculator')?>">
							EMI Calculator
						</a>
						</li>
						<li>
						<a href="<?php echo site_url('dashboard/fd_calculator')?>">
							FD Calculator
						</a>
						</li>
	                    <li>
							<a href="<?php echo site_url('dashboard/rd_calculator')?>">
							RD Calculator
							</a>
						</li>
					</ul>
				</li>
                <!-- <li class="<?php echo (($controller == 'dashboard') && ($method == 'fd_calculator')) ? 'active' : ''?>">
					<a href="<?php echo site_url('dashboard/fd_calculator')?>">
						FD Calculator
					</a>
				</li>
                <li class="<?php echo (($controller == 'dashboard') && ($method == 'rd_calculator')) ? 'active' : ''?>">
					<a href="<?php echo site_url('dashboard/rd_calculator')?>">
						Rd Calculator
					</a>
				</li> -->
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('EM','BM','ZM','GM'))) {?>
				<li class="<?php echo ($controller == 'product_guide') ? 'active' : ''?>">
					<a href="<?php echo site_url('product_guide/view')?>">
						Product Guide
					</a>
				</li>
				<?php }?>
                <?php if(in_array($this->session->userdata('admin_type'),array('EM','BM','ZM','GM'))) {?>
				<li class="<?php echo (($controller == 'leads') && ($method == 'generated')) ? 'active' : ''?>">
					<a href="<?php echo site_url('leads/generated')?>">
						Lead Generated
					</a>
				</li>
				<?php }?>
				<?php if(in_array($this->session->userdata('admin_type'),array('BM','ZM','GM','Super admin'))) {?>
				<li class="<?php echo ($controller == 'reports') ? 'active' : ''?>" id="cal-droped1">
					<a href="#">
					Reports &#9662;
					</a>
					<ul class="cal-drop1">
						<li class="<?php echo ($controller == 'usage') ? 'active' : ''?>" id="cal-droped3">
							<a href="#">

					<span class="right-toggle">&#9666;</span>Login <span class="left-toggle"> &#9656;</span>

							</a>
							<ul class="cal-drop3">
								<li>
									<a href="<?php echo site_url('reports/index/usage')?>">
									Login Report
									</a>
								</li>

							</ul>
						</li>
					
						<li class="<?php echo ($controller == 'reports') ? 'active' : ''?>" id="cal-droped2">
						<a href="#">
						 
						<span class="right-toggle">&#9666;</span>Performance<span class="left-toggle"> &#9656;</span>
						</a>
							<ul class="cal-drop2">
								<li>
									<a href="<?php echo site_url('reports/index/pendancy_leads_reports')?>">
										Pendency Leads
									</a>
								</li>
								<li>
									<a href="<?php echo site_url('reports/index/leads_type_reports')?>">
										Interested Leads Report
									</a>
								</li>

								<li>
									<a href="<?php echo site_url('reports/index/leads_generated_vs_converted')?>">
										Business Generated Report
									</a>
								</li>
							</ul>
						</li>
	                   <!--  <li>
							<a href="<?php echo site_url('reports/index/leads_generated')?>">
							Leads generated
							</a>
						</li>
						<li>
							<a href="<?php echo site_url('reports/index/leads_assigned')?>">
							Leads assigned
							</a>
						</li>
						<li>
							<a href="<?php echo site_url('reports/index/leads_generated_vs_converted')?>">
							Leads generated vs converted
							</a>
						</li> -->
						<li class="<?php echo ($controller == 'leads_classification') ? 'active' : ''?>" id="cal-droped4">
							<a href="#">
							
							<span class="right-toggle">&#9666;</span>Classification<span class="left-toggle"> &#9656;</span>
							</a>
							<ul class="cal-drop4">
<!--								<li>-->
<!--									<a href="--><?php //echo site_url('reports/index/leads_classification')?><!--">-->
<!--									Leads Classification-->
<!--									</a>-->
<!--								</li>-->
								<li>
									<a href="<?php echo site_url('reports/index/leads_generated')?>">
										Leads Generated
									</a>
								</li>
								<li>
									<a href="<?php echo site_url('reports/index/leads_assigned')?>">
                                        Current Location
									</a>
								</li>
                                <li>
                                    <a href="<?php echo site_url('reports/index/leads_unassigned')?>">
                                        Unassigned Leads
                                    </a>
                                </li>
                                <li>
									<a href="<?php echo site_url('reports/index/status_flow')?>">
										Master Report
									</a>
								</li>
                                <?php if($this->session->userdata('admin_type')=='Super admin'){?>
                                <li>
                                    <a href="<?php echo site_url('reports/index/dashboard')?>">
                                        Dashboard
                                    </a>
                                </li>
                                <?php }?>
							</ul>
						</li>
						
					</ul>
				</li>

				<?php }?>
                         <?php if(in_array($this->session->userdata('admin_type'),array('Super admin'))) {?>
				<li class="<?php echo ($controller == 'change_password') ? 'active' : ''?>">
					<a href="<?php echo site_url('change_password')?>">
						Change Password
					</a>
				</li>
				<?php }?>

                <?php if(in_array($this->session->userdata('admin_type'),array('Super admin'))) {?>
                <li>
                    <a href="<?php echo site_url('ccemail')?>">
                        CC Email
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
					<img src="<?php echo base_url().ASSETS;?>images/username.png" alt="pic" class="man-login">
					<div>
					<span class="name  responsive-login">Hi, <?php echo ucwords(strtolower($this->session->userdata('admin_name')));?> !!</span>
<!--						<span class="name responsive-login1">(--><?php //echo $this->session->userdata('admin_type');?><!--)</span>-->
					<a href="<?php echo site_url('login/logOut');?>">Logout</a>
					</div>
				</div>
        </div>
	</div>
</div>
<script type="text/javascript">
	
	    $("#cal-droped ").hover(function(){
	    	
	        $(".cal-drop").toggle();
	    });

	   	$("#cal-droped1").hover(function(){
	    	
	        $(".cal-drop1").toggle();
	    }); 
	    $("#cal-droped2").hover(function(){
	    	// event.preventDefault();
	        $(".cal-drop2").toggle();
	    }); 
	    $("#cal-droped3").hover(function(){
	    	// event.preventDefault();
	        $(".cal-drop3").toggle();
	    });
	    $("#cal-droped4").hover(function(){
	    	// event.preventDefault();
	        $(".cal-drop4").toggle();
	    });
		$("#per-droped ").hover(function(){

			$(".per-drop").toggle();
		});
	    // $(".man-login").click(function(){
	    // 	$(".responsive-login").toggle();
	    // });
</script>
