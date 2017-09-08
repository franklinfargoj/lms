<div class="page-title">
	<div class="container clearfix">
		<h3 class="text-center">Notification</h3>
	</div>
</div>
<span class="bg-top"></span>
<div class="inner-content">
<div class="page-content">
    <div class="container">
        <div id="accordion" class="notifi-accordion">
            <?php 
            if(!empty($unread)){
                foreach ($unread as $key => $value) {
                    $class = lcfirst(substr($value['priority'],0,1));
            ?>
                
                <h3 class="high <?php echo $class;?>-clr" id="title-<?php echo $value['id']?>">
                    <?php 
                        switch ($value['priority']) {
                            case 'High':
                                $imgUrl = base_url().ASSETS.'images/error.png';
                                break;
                            case 'Low':
                                $imgUrl = base_url().ASSETS.'images/smile.png';
                                break;
                            case 'Normal':
                                $imgUrl = base_url().ASSETS.'images/sad.png';
                                break;
                        }
                    ?>
                    <!-- <img src="<?php echo $imgUrl;?>" alt=""> -->
                    <span class="<?php echo $class;?>-bg "><?php echo $value['priority'];?></span>
                    <?php echo $value['title'];?>
                </h3>  
                <div id="desc-<?php echo $value['id']?>">
                    <p><?php echo $value['description_text'];?></p>
                    <a href="javascript:void(0);" class="read" data-cid="<?php echo $value['id'];?>">Mark as read</a> 
                </div>
                
            <?php 
                }
            }
            
            if(!empty($read)){
                foreach ($read as $key => $value) {
                    $class = lcfirst(substr($value['priority'],0,1));
            ?>
                
                <h3 class="high <?php echo $class;?>-clr" id="title-<?php echo $value['id']?>">
                    <?php 
                        switch ($value['priority']) {
                            case 'High':
                                $imgUrl = base_url().ASSETS.'images/error.png';
                                break;
                            case 'Low':
                                $imgUrl = base_url().ASSETS.'images/smile.png';
                                break;
                            case 'Normal':
                                $imgUrl = base_url().ASSETS.'images/sad.png';
                                break;
                        }
                    ?>
                    <!-- <img src="<?php echo $imgUrl;?>" alt=""> -->
                    <span class="<?php echo $class;?>-bg "><?php echo $value['priority'];?></span>
                    <?php echo $value['title'];?>
                </h3>  
                <div id="desc-<?php echo $value['id']?>">
                    <p><?php echo $value['description_text'];?></p>
                </div>
                
            <?php 
                }
            }
            ?>
        </div>
        <?php 
        if((count($unread) == 0) && (count($read) == 0)){?>
            <span> No Records Found</span>
        <?php }
        ?>
    </div>
</div>
</div>
<span class="bg-bottom"></span>
<script>
    $( function() {
        $("#accordion").accordion();
    });

    $('body').on('click','.read',function(){
        var id = $(this).data('cid');
        $.ajax({
            url: baseUrl+'notification/mark_as_read', 
            type: 'POST',      
            data: {
                id:id,
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            },     
            cache: false,
            success: function(response){
            if(response == 'true'){
                location.reload();
             }
            }
        });
    });
</script>