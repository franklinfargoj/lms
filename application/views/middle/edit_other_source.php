<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Edit Other Source</h3>

    </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <div class="float-right">
                <span class="lead-num"><a href="<?php echo site_url('othersource');?>"><span><</span>Back</a></span>
            </div>
            <div class="product-category">
                <!-- <form> -->
                <?php
                $attributes = array(
                    'role' => 'form',
                    'id' => 'edit_form',
                    'class' => 'form',
                    'autocomplete' => 'off'
                );
                echo form_open(base_url().'/othersource/edit/'.$this->uri->segment(3,0), $attributes);
                ?>
                <div class="form-control">
                    <?php
                    $attributes = array(
                        'class' => '',
                        'style' => ''
                    );
                    echo form_label('Name:<span style="color:red;">*</span>', 'title', $attributes);

                    $data = array(
                        'type'  => 'text',
                        'name'  => 'title',
                        'id'    => 'title',
                        'class' => '',
                        'value' => $get_other_source[0]['title']
                    );
                    echo form_input($data);

                    // Assuming that the 'title' field value was incorrect:
                    echo form_error('title', '<span class="help-block">', '</span>');
                    ?>
                </div>


                <div class="form-control">
                    <label>Status:<span style="color:red;">*</span></label>
                    <div class="radio-control">
                        <input type="radio" id= "active" name="status" value="active" <?php
                        echo set_value('status', $get_other_source[0]['status']) == 'active' ? "checked" : "";
                        ?> />
                        <label>Active</label>
                    </div>
                    <div class="radio-control">
                        <input type="radio" id= "inactive" name="status" value="inactive" <?php
                        echo set_value('status', $get_other_source[0]['status']) == 'inactive' ? "checked" : "";
                        ?> />
                        <label>Inactive</label>
                    </div>
                </div>

                <div class="form-control form-submit clearfix">
                    <a href="javascript:void(0);" class="active">
                        <img alt ="left nav" src="<?php echo base_url().ASSETS;?>images/left-nav.png">
                        <span><input class="custom_button" type="submit" name="Submit" value="Submit"></span>
                        <img alt = "right nav" src="<?php echo base_url().ASSETS;?>images/right-nav.png">
                    </a>
                </div>
                <!-- </form> -->
                <?php echo form_close();?>
            </div>
        </div>
    </div>
    <span class="bg-bottom"></span>
</div>
<!-- END ADD PRODUCT CATEGORY-->

