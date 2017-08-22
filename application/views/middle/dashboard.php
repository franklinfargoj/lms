<div class="lead-section float-left">
	<h3 class="title">Lead <br><strong>Generated</strong></h3>
	<div class="lead-count-wrapper">
		<div class="lead-count red">
			<a href="<?php echo site_url('leads/leads_list/generated/mtd')?>" class="">
			<!-- <a href="javascript:void(0);" class=""> -->
				<span class="big"><?php echo $leads['generated_mtd'] ; ?></span>	
				<span class="small">MTD</span>
			</a>	
		</div>
		<div class="lead-count blue">
			<a href="<?php echo site_url('leads/leads_list/generated/ytd')?>" class="">
			<!-- <a href="javascript:void(0);" class=""> -->
				<span class="big"><?php echo $leads['generated_ytd'] ; ?></span>	
				<span class="small">YTD</span>	
			</a>
		</div>
	</div>
</div>
<div class="lead-section float-right">
	<h3 class="title">Lead <br><strong>Converted</strong></h3>
	<div class="lead-count-wrapper">
		<div class="lead-count red">
			<a href="<?php echo site_url('leads/leads_list/converted/mtd')?>" class="">
			<!-- <a href="javascript:void(0);" class=""> -->
				<span class="big"><?php echo $leads['converted_mtd'] ; ?></span>	
				<span class="small">MTD</span>	
			</a>
		</div>
		<div class="lead-count blue">
			<a href="<?php echo site_url('leads/leads_list/converted/ytd')?>" class="">
			<!-- <a href="javascript:void(0);" class=""> -->
				<span class="big"><?php echo $leads['converted_ytd'] ; ?></span>	
				<span class="small">YTD</span>	
			</a>
		</div>
	</div>
</div>

<!-- <div class="col-md-6 col-sm-6 col-xs-6">
				<div class="leads font-grey-mint font-sm">
					 Leads Assigned
				</div>
				<div class="leads font-hg font-purple">
					<a href="<?php echo site_url('leads/leads_list/assigned/ytd')?>" class="">
				 		MTD  - <?php echo $leads['assigned_leads'] ; ?>
					</a>
				</div>
			</div> -->