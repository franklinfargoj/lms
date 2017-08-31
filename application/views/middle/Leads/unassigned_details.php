<div class="page-title">
	<div class="container clearfix">
		<h3 class="text-center">Unassigned Lead Detail</h3>
	</div>
</div>
<div class="page-content">
	<span class="bg-top"></span>
    <div class="inner-content">
	<div class="container">
		<div class="lead-form">
			<?php 
				if(!empty($unassigned_leads)){
			?>
				<form>
				<div class="lead-form-left">
					<div class="form-control">
						<label>Conversion Status:</label> <span class="detail-label red"><?php echo $unassigned_leads[0]['lead_identification'];?></span>
					</div>
					<div class="form-control">
						<label>Lead ID:</label> <span class="detail-label"><?php echo $unassigned_leads[0]['id'];?></span>
					</div>
					<div class="form-control">
						<label>Product Name:</label> <span class="detail-label"><?php echo ucwords($unassigned_leads[0]['product_title']);?></span>
					</div>
					<div class="form-control">
						<label>Assign To:</label>   
						<select name="product">
					    <option value="product1"></option>
					    <option value="product1">Vishal (Branch - State)</option>
					  </select>
					</div>
				</div>


				<div class="lead-form-right">
					<div class="form-control">
						<label>Customer Name:</label> <span class="detail-label"><?php echo ucwords($unassigned_leads[0]['customer_name']);?></span>
					</div>
					<div class="form-control">
						<label>Phone Number:</label> <span class="detail-label"><?php echo $unassigned_leads[0]['contact_no'];?></span>
					</div>
					<div class="form-control">
						<label>Remark/Notes</label>
						<p class="remark-notes"><?php echo $unassigned_leads[0]['remark'];?></p>
					</div>
				</div>
				<div class="form-control form-submit clearfix">
					<a href="#" class="float-right">
							<img src="<?php echo base_url() . ASSETS; ?>images/left-nav.png">
							<span>Submit</span>
							<img src="<?php echo base_url() . ASSETS; ?>images/right-nav.png">
					</a>
					<a href="#" class="reset float-right">
					   Reset
					</a>
					
				</div>
			</form>
			<?php
				}
			?>
		</div>
	</div>
	<span class="bg-bottom"></span>
</div>