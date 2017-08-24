<div class="container clearfix">
	<div class="lead-section float-left">
		<h3 class="title">Lead <br><strong>Generated</strong></h3>
		<div class="lead-count-wrapper">
			<div class="lead-count red">
				<span class="big"><?php echo $leads['generated_mtd'] ; ?></span>	
				<span class="small">MTD</span>
			</div>
			<div class="lead-count blue">
				<span class="big"><?php echo $leads['generated_ytd'] ; ?></span>	
				<span class="small">YTD</span>
			</div>
			<div class="view-content">
				<a href="<?php echo site_url('leads/leads_list/generated/ytd')?>">VIEW</a>
			</div>
		</div>
	</div>
	<div class="lead-section float-right">
		<h3 class="title">Lead <br><strong>Converted</strong></h3>
		<div class="lead-count-wrapper">
			<div class="lead-count red">
				<span class="big"><?php echo $leads['converted_mtd'] ; ?></span>	
				<span class="small">MTD</span>	
			</div>
			<div class="lead-count blue">
				<span class="big"><?php echo $leads['converted_ytd'] ; ?></span>	
				<span class="small">YTD</span>	
			</div>
			<div class="view-content">
				<a href="<?php echo site_url('leads/leads_list/converted/ytd')?>">VIEW</a>
			</div>
		</div>
	</div>
</div>