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

    var controller = "<?php echo $this->router->fetch_class()?>";
    $('body').on('change', '.switch-input', function(){
        var id = $(this).attr('id');
        if($(this).is(':checked')) {
            $.ajax({
                url: baseUrl+controller+'/activate', 
                type: 'POST',      
                data: {
                  	'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                  	id:id
                },     
                cache: false,
                success: function(returnhtml){
                    $(this).attr("checked","checked");
                }
            });
        
        }else{
            $.ajax({
                url: baseUrl+controller+'/deactivate', 
                type: 'POST',      
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    id:id
                },     
                cache: false,
                success: function(returnhtml){                          
                    $(this).removeAttr("checked");
                }
            }); 
        }
    });
});
</script>