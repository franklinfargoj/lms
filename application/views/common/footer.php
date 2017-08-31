<div class="footer">
	<div class="container">
		<div class="copyright">
			Copyright &copy; Dena Sampark. <?php echo date("Y")?>
		</div>
	</div>
</div>
<script src = "<?php echo base_url().ASSETS;?>/js/jquery-ui.js"></script>

<script type="text/javascript">
$(function () {
    $('body').on('click','.reset',function(){
      $(this).closest('form')[0].reset();
    });
    $( "#tabs" ).tabs();

    setTimeout(function(){        
        $(".success_message").fadeOut('slow');
        $(".error_message").fadeOut('slow');
    }, 10000);

});
</script>