<link rel="stylesheet" href="<?php echo base_url().ASSETS;?>css/jquery-ui.css">
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center"><?php echo ucwords($product[0]['title']);?> Description</h3>
        <div class="float-right">
            <span class="lead-num"><a href="<?php echo base_url('product_guide/add/'. encode_id($product[0]['id']));?>">Add</a></span>
        </div>
    </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <?php if($productguidelist){?>
                <div id="tabs" class="product-guide-tab">
                    <ul>
                        <?php 
                            $i = 0;
                            foreach ($productguidelist as $key => $value) { 
                            $i++;
                        ?>
                            <li>
                                <a class="tab" href="#tabs-<?php echo $value['id'];?>"><?php echo $value['title'];?></a>
                            </li>
                        <?php 
                            }
                        ?>
                    </ul>
                    <?php 
                        $i = 0;
                        foreach ($productguidelist as $key => $value) { 
                        $i++;
                    ?>
                        <div id="tabs-<?php echo $value['id'];?>" class="tab-content">
                            <?php
                                $attributes = array(
                                        'role' => 'form',
                                        'id' => 'add_form',
                                        'class' => 'form',
                                        'autocomplete' => 'off'
                                );
                                echo form_open(base_url().'/product_guide/edit', $attributes);
                            ?>
                                <div class="form-control">
                                    <?php
                                        $data = array(
                                            'product_id'  => encode_id($value['product_id']),
                                            'id' => encode_id($value['id'])
                                        );
                                        echo form_hidden($data);
                                    ?>
                                    <textarea class="description_text" name="description_text_<?php echo $value['id'];?>" rows="7" cols="80" style="width: 810px; height: 200px">
                                        <?php echo $value['description_text'];?>
                                    </textarea>

                                    <?php echo form_error('description_text', '<span class="help-block">', '</span>');?>
                                </div>
                                <div class="form-control form-submit clearfix">
                                   <button type="submit" name="Submit" value="Submit" class="full-btn float-right">
<img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav" class="left-btn-img">
<span class="btn-txt">Submit</span>
<img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="left-nav" class="right-btn-img">
</button>                                </div>
                            <?php echo form_close();?>
                        </div>
                        <script type="text/javascript">
                            $(function () {
                                CKEDITOR.replace( "description_text_<?php echo $value['id'];?>", {
                                    uiColor: '#01559d'
                                });  
                            });
                        </script>
                    <?php 
                        }
                    ?>
                </div>
            <?php }else{?>
                <span class="help-block">No data found. Please add description for product</span>
            <?php }?>
        </div>
    </div>
    <span class="bg-bottom"></span>
</div>
<script src="<?php echo base_url().PLUGINS;?>ckeditor/ckeditor.js"></script>
<script type="text/javascript">
    /*$(function () {
        CKEDITOR.replace( 'description_text', {
            uiColor: '#01559d'
        });  
    });*/
    $.validator.addMethod("regx", function(value, element, regexpr) {
        return regexpr.test(value);
    });
    $("#add_form").validate({
        rules: {
            description_text: {
                required: true
            }
        },
        messages: {
            description_text: {
                required: "Please Enter Description"
            }
        }
    });

</script>




