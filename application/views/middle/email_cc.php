<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">CC email</h3>
    </div>
</div>

<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <p id="note"><span style="color:red;">*</span> These fields are required</p>
            <div class="float-right">
                <span class="lead-num"><a href="<?php echo site_url('ccemail');?>"><span>&#60;</span>Back</a></span>
            </div>
            <div class="product-category">

                <?php
                $attributes = array(
                    'role' => 'form',
                    'id' => 'add_form',
                    'class' => 'form',
                    'autocomplete' => 'off'
                );
                echo form_open(base_url().'ccemail/add', $attributes);
                ?>

                <div class="form-control">
                    <?php
                    $attributes = array(
                        'class' => '',
                        'style' => ''
                    );
                    echo form_label('Name:<span style="color:red;">*</span>', 'ccname', $attributes);

                    $data = array(
                        'type'  => 'text',
                        'name'  => 'ccname',
                        'id'    => 'ccname',
                        'class' => '',
                        'value' => set_value('ccname')
                    );
                    echo form_input($data);
                    // Assuming that the 'ccname' field value was incorrect:
                    echo form_error('ccname', '<span class="help-block">', '</span>');
                    ?>
                </div>

                <div class="form-control">
                    <?php
                    $attributes = array(
                        'class' => '',
                        'style' => ''
                    );
                    echo form_label('Email:<span style="color:red;">*</span>', 'ccemail', $attributes);

                    $data = array(
                        'type'  => 'email',
                        'name'  => 'ccemail',
                        'id'    => 'ccemail',
                        'class' => '',
                        'value' => set_value('ccemail')
                    );
                    echo form_input($data);

                    // Assuming that the 'title' field value was incorrect:
                    echo form_error('ccemail', '<span class="help-block">', '</span>');
                    ?>
                </div>

                <div class="form-control form-submit clearfix">
                    <a href="javascript:void(0);" class="active">
                        <img alt ="left nav" src="<?php echo base_url().ASSETS;?>images/left-nav.png">
                        <span><input class="custom_button" type="submit" name="Submit" value="Submit"></span>
                        <img alt = "right nav" src="<?php echo base_url().ASSETS;?>images/right-nav.png">
                    </a>
                </div>

                <?php echo form_close();?>

            </div>
        </div>
    </div>
    <span class="bg-bottom"></span>
</div>



<script type="text/javascript">
    $.validator.addMethod("regx", function(value, element, regexpr) {
        return regexpr.test(value);
    });

    $("#add_form").validate({

        rules: {
            ccname: {
                required: true
            },
            ccemail: {
                required: true,
                email: true*/
            }
        },
        messages: {
            ccname: {
                required: "Please enter the name"
            },
            ccemail: {
                required: "Please enter the email",
                email: "Enter valid email"
            }
        }
    });

</script>