<style type="text/css">
    .error{
        position: inherit !important;
    }
</style>
<div class="page-title">
	<div class="container clearfix">
		<h3 class="text-center">Lead Detail</h3>
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
				<!-- <form> -->
				 <?php 
                    //Form
                    $attributes = array(
                        'role' => 'form',
                        'id' => 'detail_form',
                        'class' => 'form',
                        'autocomplete' => 'off'
                        );
                    echo form_open(site_url().'leads/update_lead_status', $attributes);
                ?>
				<div class="lead-form-left">
					<div class="form-control">
						<label>Lead ID:</label> <span class="detail-label"><?php echo $unassigned_leads[0]['id'];?></span>
					</div>
					<div class="form-control">
						<label>Product Name:</label> <span class="detail-label"><?php echo ucwords($unassigned_leads[0]['product_title']);?></span>
					</div>
					<?php if((in_array($this->session->userdata('admin_type'),array('BM')))){
						$data = array(
                            'lead_id' => encode_id($unassigned_leads[0]['id']),
                            'lead_type'    => 'unassigned'
                        );
                        echo form_hidden($data);
					?>
					<div class="form-control">
						<label>Assign To:</label>   
						<select name="assign_to">
							<option value="">Select Employee</option>
							<?php $result = get_details($this->session->userdata('admin_id'));?>
							<?php foreach ($result['list'] as $key =>$value){?>
								<option value="<?php echo $value->DESCR10.'-'.$value->DESCR30;?>"><?php echo ucwords($value->DESCR30);?></option>
							<?php }?>
						</select>
					</div>
					<?php }?>
				</div>


				<div class="lead-form-right">
                    <?php if(isset($backUrl)){?>
                        <a href="<?php echo site_url($backUrl);?>" class="reset float-right form-style"> &#60; Back</a>
                    <?php }?>
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
				<?php if((in_array($this->session->userdata('admin_type'),array('BM')))){?>
					<div class="form-control form-submit clearfix">
						<a href="javascript:void(0);" class="float-right">
	                            <img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav">
	                            <span><input type="submit" class="custom_button" value="Submit" /></span>
	                            <img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="right-nav">
	                    </a>
						<a href="javascript:void(0);" class="reset float-right">
						   Reset
						</a>
						
					</div>
				<?php }?>
			<!-- </form> -->
			<?php
				echo form_close();
				}
			?>
		</div>
	</div>
	<span class="bg-bottom" id="bg-w"></span>
</div>
<script type="text/javascript">
	$("#detail_form").validate({
            rules: {
                assign_to: {
                    required: true
                }
            },
            messages: {
                assign_to: {
                    required: "Please select employee"
                }
            }
        });
</script>