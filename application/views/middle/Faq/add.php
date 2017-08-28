<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Add FAQ</h3>
        <div class="float-right">
            <span class="lead-num"><a href="<?php echo site_url('faq');?>">Back</a></span>
        </div>
    </div>
</div>
<div class="page-content">
    <div class="container">
        <span></span>
            <div class="inner-content">
                <div class="product-category">
                    <!-- <form> -->
                    <?php
                        $attributes = array(
                            'role' => 'form',
                            'id' => 'add_form',
                            'class' => 'form',
                            'autocomplete' => 'off'
                            );
                        echo form_open(site_url().'/faq/add/', $attributes);
                    ?>
                        <div class="form-control">
                            <?php 
                                $attributes = array(
                                    'class' => '',
                                    'style' => ''
                                );
                                echo form_label('Question', 'question', $attributes);

                                $data = array(
                                    'type'  => 'text',
                                    'name'  => 'question',
                                    'id'    => 'question',
                                    'class' => '',
                                    'value' => set_value('question') 
                                );
                                echo form_input($data);
                                
                                // Assuming that the 'title' field value was incorrect:
                                echo form_error('question', '<span class="help-block">', '</span>');
                            ?>
                        </div>
                        <div class="form-control">
                            <?php 
                                $attributes = array(
                                    'class' => '',
                                    'style' => ''
                                );
                                echo form_label('Answer', 'answer', $attributes);
                            ?>
                        
                            <textarea name="answer" rows="7" cols="80" style="width: 810px; height: 200px">
                                <?php echo set_value('answer')?>
                            </textarea>
                            <?php echo form_error('answer', '<span class="help-block">', '</span>');?>
                        </div>
                        <div class="form-control">
                            <label>Status</label>
                            <div class="radio-control">
                                <input type="radio" id= "active" name="status" value="active" <?php echo  set_radio('status', 'active', TRUE); ?> />
                                <label>Active</label>
                            </div>
                            <div class="radio-control">
                                <input type="radio" id= "inactive" name="status" value="inactive" <?php echo  set_radio('status', 'inactive'); ?> />
                                <label>Inactive</label>
                            </div>
                        </div>
                        <div class="form-control form-submit clearfix">
                            <a href="#" class="reset">
                                Reset
                            </a>
                            <a href="javascript:void(0);" class="active">
                                <img alt ="left nav" src="<?php echo base_url().ASSETS;?>images/left-nav.png">
                                <span><input type="submit" name="Submit" value="Submit"></span>
                                <img alt = "right nav" src="<?php echo base_url().ASSETS;?>images/right-nav.png">
                            </a>
                        </div>
                    <!-- </form> -->
                    <?php echo form_close();?>
                </div>
            </div>
        <span></span>
    </div>
</div>
<script src="<?php echo base_url().PLUGINS;?>ckeditor/ckeditor.js"></script>
<script type="text/javascript">
    $(function () {
        // Replace the <textarea id="answer"> with a CKEditor
        // instance, using default configuration.
        //CKEDITOR.replace('answer');
        CKEDITOR.replace( 'answer', {
            uiColor: '#01559d'
        });  
    }); 

 	$.validator.addMethod("regx", function(value, element, regexpr) {
        return regexpr.test(value);
    });

    $("#add_form").validate({

        rules: {
            question: {
                required: true
            },
            answer: {
                required: true
            }
        },
        messages: {
            question: {
                required: "Please Enter Question"
            },
            answer: {
                required: "Please Enter Answer"
            }
        }
    });

</script>




