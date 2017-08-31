<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/bootstrap-wysihtml5/wysiwyg-color.css"/>

<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN PRODUCT DESCRIPTION-->
<div class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            <!-- <i class="fa fa-cogs font-green-sharp"></i> -->
            <span class="caption-subject font-green-sharp bold">View <?php echo $product[0]['title'];?> Description</span>
        </div>
        <div class="tools">
            <a href="<?php echo base_url('product_guide/add/'. encode_id($product[0]['id']));?>" class="btn btn-sm green"><i class="fa fa-plus"></i>Add
            </a>
        </div>
    </div>
    <div class="portlet-body">
        <?php if($productguidelist){?>
        <div class="tabbable-custom ">
            <ul class="nav nav-tabs ">
                <?php 
                    $i = 0;
                    foreach ($productguidelist as $key => $value) { 
                    $i++;
                ?>
                 <li class="<?php if($i == 1) echo 'active';?>">
                    <a href="#tab_<?php echo $value['id'];?>" data-toggle="tab">
                        <?php echo $value['title'];?>
                    </a>
                </li>
                <?php 
                    }
                ?>
            </ul>
            <div class="tab-content">
                <?php 
                    $i = 0;
                    foreach ($productguidelist as $key => $value) { 
                    $i++;
                ?>
                    <div class="tab-pane <?php if($i == 1) echo 'active';?>" id="tab_<?php echo $value['id'];?>">
                        <!-- BEGIN EXTRAS PORTLET-->
                        <div class="portlet light">
                            <div class="portlet-body form">
                                <?php
                                    $attributes = array(
                                            'role' => 'form',
                                            'id' => 'add_form',
                                            'autocomplete' => 'off'
                                    );
                                    echo form_open(base_url().'/product_guide/edit', $attributes);
                                ?>
                                <div class="form-body">
                                    <div class="form-group  <?php if(isset($has_error)){ echo $has_error;}?>">
                                        <!-- <div class="col-md-12"> -->
                                            <?php
                                                $data = array(
                                                    'product_id'  => encode_id($value['product_id']),
                                                    'id' => encode_id($value['id'])
                                                );
                                                echo form_hidden($data);

                                                $attributes = array(
                                                'class' => '',
                                                'style' => ''
                                            );
                                            //echo form_label('Description', 'description_text', $attributes);
                                            ?>

                                            <textarea name = "description_text" class="textarea" placeholder="Enter text ..." style="width: 810px; height: 200px">
                                                <?php echo $value['description_text'];?> 
                                            </textarea>
                                            <?php echo form_error('description_text', '<span class="help-block">', '</span>');?>
                                        <!-- </div> -->
                                    </div>
                                    <div class="form-actions right">
                                        <button type="submit" class="btn green">Submit</button>
                                    </div>
                                </div>
                                <?php echo form_close();?>
                            </div>
                        </div>
                    </div>
                <?php 
                    }
                ?>
            </div>
        </div>
        <?php }else{?>
        <span class="help-block">No data found. Please add description for product</span>
        <?php }?>
    </div>
</div>
<!-- END PRODUCT DESCRIPTION-->

<script src="<?php echo base_url();?>assets/global/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?php echo base_url();?>assets/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript">
    $('.textarea').wysihtml5();
</script>





