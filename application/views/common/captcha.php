<div class="passwordinner">
    <div class="pull-left" id="captcha_img"><?php echo $capimage; ?></div>
    <span id="refresh_captcha" style="cursor:pointer;float:right">
        <img src="<?php echo base_url().ASSETS.'images/refresh.gif'?>">
    </span>
</div>
<div class="form-control">   
    <div class="input-control">
        <?php
            $data_cap = array(
                'name' => 'captext',
                'id' => 'captext',
                'class' => 'form-control form-control-solid placeholder-no-fix',
                // 'style' => 'float:right;',
            );
            echo form_input($data_cap);
        ?>
    </div>
</div>
<?php
echo form_error('captext', '<span class="help-block">', '</span>');
?>


    