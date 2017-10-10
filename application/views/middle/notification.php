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
            <div class="notify">    
                <h3 class="high <?php echo $class;?>-clr" id="title-<?php echo $value['id']?>">
                    <?php echo ucwords(strtolower($value['title']));?>
                </h3>  
                <div id="desc-<?php echo $value['id']?>">
                    <p><?php echo $value['description_text'];?></p>
                </div>
            </div>    
            <?php 
                }
            }
            ?>
        </div>
        <?php 
        if((count($unread) == 0)){?>
            <span> No Records Found</span>
        <?php }
        ?>
    </div>
</div>
</div>
<span class="bg-bottom"></span>