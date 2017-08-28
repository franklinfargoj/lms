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
    var controller = "<?php echo $this->router->fetch_class()?>";
    $('body').on('change', '.switch-input', function(){
      var id = $(this).attr('id');
      if($(this).is(':checked')) {
        //$('body').append('<p class="loading"><i class="fa fa-refresh fa-spin"></i></p>');
        //setTimeout(function(){
          $.ajax({
            url: baseUrl+controller+'/activate', 
            type: 'POST',      
            data: {
          	'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
          	id:id
          },     
            cache: false,
            success: function(returnhtml){
              //$('.loading').remove();                          
              $(this).attr("checked","checked");
            }
          });
        //}, 2000);
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