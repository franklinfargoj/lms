<link rel="stylesheet" href="<?php echo base_url().ASSETS;?>css/jquery-ui.css">
<div class="page-title">
	<div class="container clearfix">
		<h3 class="text-center">Product Guide</h3>
	</div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
    	<div class="container">
    		<div class="lead-form">
    			<!-- <form> -->
    				<?php 
                        //Form
                        $attributes = array(
                            'role' => 'form',
                            'id' => 'search_form',
                            'class' => 'form',
                            'autocomplete' => 'off'
                        );
                        echo form_open(site_url().'product_guide/search', $attributes);
                    ?>
                    <p id="note"><span style="color:red;">*</span> These fields are required</p>
    				<div class="lead-form-left">
    					<div class="form-control">
    						<?php 
    							$attributes = array(
                                    'class' => '',
                                    'style' => ''
                                );
                                echo form_label('Product Category: <span style="color:red;">*</span> ', 'product_category_id', $attributes);

    						    if(isset($category_list)){
                                    $options = $category_list;
                                    $js = array(
                                            'id'       => 'product_category_id',
                                            'class'    => ''
                                    );
                                    if(isset($product_category_id)){
                                    	$product_category_id = $product_category_id;
                                    }else{
                                    	$product_category_id = '';
                                    }
                                    echo form_dropdown('product_category_id', $options , $product_category_id,$js);    
                                }
                            ?>
    					</div>
    				</div>
    				<div class="lead-form-right">
    					<div class="form-control productlist">
    						<?php 
    							$attributes = array(
                                    'class' => '',
                                    'style' => ''
                                );
                                echo form_label('Product: <span style="color:red;">*</span> ', 'product_id', $attributes);
                            ?>
                            <?php 
                                if(isset($product_list)){
                                    $options = $product_list;
                                    $js = array(
                                            'id'       => 'product_id',
                                            'class'    => ''
                                    );
                                    if(isset($product_id)){
                                    	$product_id = $product_id;
                                    }else{
                                    	$product_id = '';
                                    }
                                    echo form_dropdown('product_id', $options , $product_id,$js);    
                                }else{
                            ?>
    							<select name="product_id">
    							    <option value="">Select</option>
    							</select>
    						<?php 
    							}
    						?>
    					</div>
    				</div>
    				<div class="form-control form-submit clearfix">
    					<button type="submit" name="Submit" value="Submit" class="full-btn float-right">
<img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav" class="left-btn-img">
<span class="btn-txt">Submit</span>
<img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="left-nav" class="right-btn-img">
</button>    				</div>
    			<!-- </form> -->
    			<?php echo form_close();?>
    		</div>

            <img class="loader" src="<?php echo base_url().ASSETS;?>images/35.gif" alt="35" style="display:none;">
            <!-- Tab contents start here -->
            <?php if(isset($searchResult)){?>
                <?php if(!empty($searchResult)){?>
                <div id="tabs" class="product-guide-tab" style="display:none;">
                    <ul>
                        <?php 
                            $i = 0;
                            foreach ($searchResult as $key => $value) { 
                            $i++;
                        ?>
                            <li>
                                <a class="tab" href="#tabs-<?php echo $value['id'];?>"><?php echo $value['title'];?></a>
                            </li>
                        <?php 
                            }
                        ?>
                    </ul>
                    <?php 
                        $i = 0;
                        foreach ($searchResult as $key => $value) { 
                        $i++;
                    ?>
                        <div id="tabs-<?php echo $value['id'];?>" class="tab-content">
                        	<?php echo $value['description_text'];?>
                        </div>
                    <?php 
                        }
                    ?>
                </div>
                <script type="text/javascript">
                $('.loader').show();
                </script>
    	        <?php }else{?>
    	            <span class="no_result">No records found</span>
    	        <?php }?>
            <?php }?>
    		<!-- Tab contents ends here -->
        </div>
    </div>
    <span class="bg-bottom"></span>
</div>

<script type="text/javascript">

	/*Validation*/
	var validate = $("#search_form").validate({
        rules: {
            product_category_id: {
                required: true
            },
            product_id: {
                required: true
            }
        },
        messages: {
            product_category_id: {
                required: "Please select product category"
            },
            product_id: {
                required: "Please select product"
            }
        },
        submitHandler: function(form) {
            $('.custom_button').attr('disabled','disabled');
            $( ".float-right" ).addClass( "disabled" );
            $('#tabs').hide();
            $('.no_result').hide();
            $('.loader').show();
            setTimeout(function(){        
                form.submit();
            }, 2000);
        }
    });

	/*Fetch products under category*/
	$('#product_category_id').change(function () {
        var csrf = $("input[name=csrf_dena_bank]").val();
        var category_id = $(this).val();
        $.ajax({
            method: "POST",
            url: baseUrl + "leads/productlist",
            data: {
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                category_id: category_id,
                select_label:'Select'
            }
        }).success(function (resp) {
            if (resp){
               $('.productlist').html(resp);
            }
        });
    });

    setTimeout(function(){        
        $('.loader').hide();
        $('#tabs').show();
    }, 2000);

</script>