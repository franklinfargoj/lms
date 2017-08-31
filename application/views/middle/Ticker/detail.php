<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Tickers</h3>
    </div>
</div>
<div class="page-content">
    <div class="container">
        <div id="accordion" class="faq-accordion">
            <?php 
                if($tickerDetail){
                    
            ?>
                    <h3><span>.</span><?php echo $tickerDetail[0]['title'];?></h3>  
                    <div>
                        <p><?php echo $tickerDetail[0]['description_text'];?></p>  
                    </div>
            <?php 
                    
                }
            ?>
        </div>
        <div class="form-control form-submit clearfix">
            <a href="<?php echo site_url('ticker');?>" class="reset float-right">
               Back
            </a>
        </div>
    </div>
</div>

<script src = "<?php echo base_url().ASSETS;?>/js/jquery-ui.js"></script>
<script>
    $( function() {
        $( "#accordion" ).accordion();
    });
</script>
    