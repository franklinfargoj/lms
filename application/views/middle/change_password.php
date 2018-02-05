<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Change Password</h3>

    </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <p id="note"><span style="color:red;">*</span> All fields are required</p>
            <div class="product-category add-product">
                <!-- <form> -->
                <?php
                $attributes = array(
                    'role' => 'form',
                    'id' => 'add_form',
                    'class' => 'form',
                    'autocomplete' => 'off'
                );
                echo form_open(base_url().'change_password/reset_password', $attributes);
                ?>
                <div class="form-control">
                    <?php
                    $attributes = array(
                        'class' => '',
                        'style' => ''
                    );
                    echo form_label('Current Password', 'current_pwd', $attributes);

                    $data = array(
                        'type'  => 'password',
                        'name'  => 'current_pwd',
                        'id'    => 'current_pwd',
                        'class' => '',
                        'value' => set_value('current_pwd')
                    );
                    echo form_input($data);

                    // Assuming that the 'title' field value was incorrect:
                    echo form_error('current_pwd', '<span class="help-block">', '</span>');
                    ?>
                </div>
                <div class="form-control">
                    <?php
                    $attributes = array(
                        'class' => '',
                        'style' => ''
                    );
                    echo form_label('New Password', 'new_pwd', $attributes);

                    $data = array(
                        'type'  => 'password',
                        'name'  => 'new_pwd',
                        'id'    => 'new_pwd',
                        'class' => '',
                        'value' => set_value('new_pwd')
                    );
                    echo form_input($data);

                    // Assuming that the 'title' field value was incorrect:
                    echo form_error('new_pwd', '<span class="help-block">', '</span>');
                    ?>
                </div>
                <div class="form-control">
                    <?php
                    $attributes = array(
                        'class' => '',
                        'style' => ''
                    );
                    echo form_label('Re-type New Password', 're_pwd', $attributes);

                    $data = array(
                        'type'  => 'password',
                        'name'  => 're_pwd',
                        'id'    => 're_pwd',
                        'class' => '',
                        'value' => set_value('re_pwd')
                    );
                    echo form_input($data);

                    // Assuming that the 'title' field value was incorrect:
                    echo form_error('re_pwd', '<span class="help-block">', '</span>');
                    ?>
                </div>


                <div class="form-control form-submit clearfix">
                    <a href="javascript:void(0);" class="reset">
                        Reset
                    </a>
                    <a href="#">
                        <img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav">
                        <span><input class="custom_button" type="submit" name="Submit" value="Submit"></span>
                        <img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="right-nav">
                    </a>
                </div>
                <!-- </form> -->
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
            current_pwd: {
                required: true
            },
            new_pwd: {
                required: true
            },
            re_pwd: {
                required: true,
                equalTo: "#new_pwd"
            }
        },
        messages: {
            current_pwd: {
                required: "Please type current password"
            },
            new_pwd: {
                required: "Please type new password"
            },
            re_pwd: {
                required: "Please re-type new password",
                equalTo: "Please type new password again"
            }
        }
    });

</script>