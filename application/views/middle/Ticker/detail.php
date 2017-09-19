<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Tickers</h3>
            </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
        <div class="float-right">
            <span class="lead-num"><a href="<?php echo site_url('ticker');?>"><span><</span>Back</a></span>
        </div>

            <div id="accordion" class="faq-accordion faq-a">
                <?php 
                    if($tickerDetail){
                        
                ?>
                        <h3><span>.</span><?php echo ucwords($tickerDetail[0]['title']);?></h3>  
                        <div>
                            <p><?php echo $tickerDetail[0]['description_text'];?></p>  
                        </div>
                <?php 
                        
                    }
                ?>
            </div>
        </div>
    </div>
    <span class="bg-bottom"></span>
</div>

<script src = "<?php echo base_url().ASSETS;?>/js/jquery-ui.js"></script>
<script>
    $( function() {
        $( "#accordion" ).accordion();
    });
</script>
    