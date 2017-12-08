<link rel="stylesheet" href="<?php echo base_url().ASSETS;?>css/jquery-ui.css">
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Other Processing Center</h3>
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
                echo form_open(site_url().'rapc/search', $attributes);
                ?>
                <div class="float-right">
                    <span class="lead-num"><a href="<?php echo site_url('rapc/upload');?>">Upload</a></span>
                </div>
                <p id="note"><span style="color:red;">*</span> These fields are required</p>
                <div class="lead-form-left">
                    <div class="form-control">
                        <?php
                        $attributes = array(
                            'class' => '',
                            'style' => ''
                        );
                        echo form_label('Center Type: <span style="color:red;">*</span> ', 'type_id', $attributes);

                        if(isset($typelist)){
                            $options = $typelist;
                            $js = array(
                                'id'       => 'type_id',
                                'class'    => ''
                            );
                            echo form_dropdown('type_id', $options , '',$js);
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
                        echo form_label('Processing Center: <span style="color:red;">*</span> ', 'center_id', $attributes);
                        ?>
                        <select name="center_id">
                            <option value="">Select</option>
                        </select>
                    </div>
                </div>
                <div class="form-control form-submit clearfix">
                    <a href="javascript:void(0);" class="float-right">
                        <img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav">
                        <span><input type="submit" class="custom_button" name="Submit" value="Submit"></span>
                        <img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="right-nav">
                    </a>
                </div>
                <!-- </form> -->
                <?php echo form_close();?>
            </div>

            <img class="loader" src="<?php echo base_url().ASSETS;?>images/35.gif" alt="35" style="display:none;">
            <!-- Tab contents start here -->
            <!-- Tab contents ends here -->
        </div>
    </div>
    <span class="bg-bottom"></span>
</div>

<script type="text/javascript">

    /*Validation*/
    var validate = $("#search_form").validate({
        rules: {
            type_id: {
                required: true
            },
            center_id: {
                required: true
            }
        },
        messages: {
            type_id: {
                required: "Please select center type"
            },
            center_id: {
                required: "Please select processing center"
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
    $('#type_id').change(function () {
        var csrf = $("input[name=csrf_dena_bank]").val();
        var category_id = $(this).val();
        $.ajax({
            method: "POST",
            url: baseUrl + "rapc/centerlist",
            data: {
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                type_id: category_id
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