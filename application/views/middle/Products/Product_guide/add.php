<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Add <?php echo ucwords($product[0]['title']);?> Description</h3>
       
    </div>
</div>
<div class="page-content">
    <div class="">
        <span class="bg-top"></span>
        <div class="inner-content">
            <div class="container">
            <p id="note"><span style="color:red;">*</span> These fields are required</p>

             <div class="float-right m">
            <span class="lead-num"><a href="<?php echo base_url("product_guide/index/".encode_id($product[0]['id']));?>"><span> &#60;</span>Back</a></span>
        </div>
                <div class="product-category">
                    <!-- <form> -->
                    <?php
                        $attributes = array(
                            'role' => 'form',
                            'id' => 'add_form',
                            'class' => 'form',
                            'autocomplete' => 'off'
                            );
                        echo form_open(base_url().'/product_guide/add/'.encode_id($product[0]['id']), $attributes);
                    ?>
                        <div class="form-control">
                            <?php 
                                $attributes = array(
                                    'class' => '',
                                    'style' => ''
                                );
                                echo form_label('Title:<span style="color:red;">*</span>', 'title', $attributes);

                                $options = $titleList;
                                $js = array(
                                        'id'       => 'title',
                                        'class'    => 'form-control'    
                                        /*'onChange' => 'some_function();'*/
                                );
                                echo form_dropdown('title', $options , set_select('title'),$js);
                                
                                // Assuming that the 'title' field value was incorrect:
                                echo form_error('title', '<span class="help-block">', '</span>');
                            ?>
                        </div>
                        <div class="form-control">
                            <?php 
                                $data = array(
                                        'product_id'  => $product[0]['id']
                                );
                                echo form_hidden($data);

                                $attributes = array(
                                    'class' => '',
                                    'style' => ''
                                );
                                echo form_label('Description:<span style="color:red;">*</span>', 'description_text', $attributes);
                            ?>
                            <textarea id="description_text" name = "description_text" class="textarea" placeholder="Enter text ..." style="width: 810px; height: 200px">
                                <?php echo set_value('description_text')?>
                            </textarea>
                            <?php echo form_error('description_text', '<span class="help-block">', '</span>');?>
                        </div>
                        <div class="form-control form-submit clearfix">
                            <a href="javascript:void(0);" class="reset">
                                Reset
                            </a>
                            <a href="#" class="active">
                                <img alt ="left nav" src="<?php echo base_url().ASSETS;?>images/left-nav.png">
                                <span><input type="submit" class="custom_button" name="Submit" value="Submit"></span>
                                <img alt = "right nav" src="<?php echo base_url().ASSETS;?>images/right-nav.png">
                            </a>
                        </div>
                    <!-- </form> -->
                    <?php echo form_close();?>
                </div>
            </div>
        </div>
        <span class="bg-bottom" id="bg-w
        "></span>
    </div>
</div>
<script src="<?php echo base_url().PLUGINS;?>ckeditor/ckeditor.js"></script>
<script type="text/javascript">
    $(function () {
        // Replace the <textarea id="answer"> with a CKEditor
        // instance, using default configuration.
        //CKEDITOR.replace('answer');
        CKEDITOR.replace( 'description_text', {
            uiColor: '#01559d'
        });  
    });
 	$.validator.addMethod("regx", function(value, element, regexpr) {
        return regexpr.test(value);
    });

    $("#add_form").validate({

        rules: {
            title: {
                required: true
            },
            description_text: {
                required: true
            }
        },
        messages: {
            title: {
                required: "Please Enter Title"
            },
            description_text: {
                required: "Please Enter Product Description"
            }
        }
    });
    $('body').on('click','.reset',function(){
        CKEDITOR.instances['description_text'].setData('');
    });
</script>




