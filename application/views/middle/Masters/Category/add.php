<!-- BEGIN  ADD PRODUCT CATEGORY-->
<div class="portlet light">
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-cogs font-green-sharp"></i>
			<span class="caption-subject font-green-sharp bold uppercase">Add Product Category</span>
		</div>
		<div class="tools">
			<a href="<?php echo base_url('product_category');?>" class="">List
			</a>
		</div>
	</div>
	<div class="portlet-body form">
		<?php
			$attributes = array(
				'role' => 'form',
				'id' => 'add_form',
				'autocomplete' => 'off'
				);
			echo form_open(base_url().'/product_category/add', $attributes);
		?>
		<!-- <form role="form"> -->
			<div class="form-body">
				<!-- <div class="form-group">
					<label>Large Input</label>
					<input type="text" class="form-control input-lg" placeholder="input-lg">
				</div> -->
				<div class="form-group <?php if(isset($has_error)){ echo $has_error;}?>">
					<?php 
						$attributes = array(
					        'class' => '',
					        'style' => ''
						);
						echo form_label('Product Category Name', 'title', $attributes);

						$data = array(
					        'type'  => 'text',
					        'name'  => 'title',
					        'id'    => 'title',
					        'class' => 'form-control',
					        'placeholder' => '' 
						);
						echo form_input($data);
						
						// Assuming that the 'title' field value was incorrect:
						echo form_error('title', '<span class="help-block">', '</span>');
					?>
				</div>
			</div>
			<div class="form-actions right">
				<button type="button" class="btn default">Cancel</button>
				<button type="submit" class="btn green">Submit</button>
			</div>
		<!-- </form> -->
		<?php echo form_close();?>
	</div>
</div>
<!-- END ADD PRODUCT CATEGORY-->