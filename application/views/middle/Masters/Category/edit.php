<!-- BEGIN  ADD PRODUCT CATEGORY-->
<div class="portlet light">
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-cogs font-green-sharp"></i>
			<span class="caption-subject font-green-sharp bold uppercase">Add Product Category</span>
		</div>
		<div class="tools">
			<!-- <a href="javascript:;" class="collapse">
			</a>
			<a href="#portlet-config" data-toggle="modal" class="config">
			</a>
			<a href="javascript:;" class="reload">
			</a> -->
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
			echo form_open(base_url().'/product_category/edit/'.$this->uri->segment(3,0), $attributes);
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
					        'value' => $categoryDetail[0]['title'],
					        'placeholder' => '' 
						);
						echo form_input($data);
						
						// Assuming that the 'title' field value was incorrect:
						echo form_error('title', '<span class="help-block">', '</span>');
					?>
				</div>
				<!-- <div class="form-group">
					<label>Small Input</label>
					<input type="text" class="form-control input-sm" placeholder="input-sm">
				</div>
				<div class="form-group">
					<label>Large Select</label>
					<select class="form-control input-lg">
						<option>Option 1</option>
						<option>Option 2</option>
						<option>Option 3</option>
						<option>Option 4</option>
						<option>Option 5</option>
					</select>
				</div>
				<div class="form-group">
					<label>Default Select</label>
					<select class="form-control">
						<option>Option 1</option>
						<option>Option 2</option>
						<option>Option 3</option>
						<option>Option 4</option>
						<option>Option 5</option>
					</select>
				</div>
				<div class="form-group">
					<label>Small Select</label>
					<select class="form-control input-sm">
						<option>Option 1</option>
						<option>Option 2</option>
						<option>Option 3</option>
						<option>Option 4</option>
						<option>Option 5</option>
					</select>
				</div> -->
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