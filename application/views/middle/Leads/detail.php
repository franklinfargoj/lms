<!-- BEGIN PAGE LEVEL STYLES -->
    <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN PAGE CONTENT INNER -->
    <div class="row">
        <div class="col-md-12">
             <!-- BEGIN PRODUCT CATEGOEY-->
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <!-- <i class="fa fa-cogs font-green-sharp"></i> -->
                            <span class="caption-subject font-green-sharp bold"><?php echo $title;?></span>
                        </div>
                        <div class="tools">
                            <!-- <a href="<?php echo site_url('leads/leads_list/'.$type.'/'.$till);?>" class="btn btn-sm green"><i class="fa fa-plus"></i>Back
                            </a> -->
                        </div>
                    </div>
                    <div class="portlet-body">
                       <div class="row">
                            <div class="col-md-12">
                                <?php 
                                    //Form
                                    $attributes = array(
                                        'role' => 'form',
                                        'id' => 'add_form',
                                        'autocomplete' => 'off'
                                        );
                                    echo form_open(site_url().'leads/update_lead_status', $attributes);
                                ?>
                                <div class="portlet yellow-crusta box">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-cogs"></i> Details
                                        </div>
                                    </div>
                                    <?php if($leads){?>
                                        <div class="portlet-body">
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                     Customer Name
                                                </div>
                                                <div class="col-md-7 value">
                                                    <?php echo $leads[0]['customer_name'];?>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                     Customer Phone no.
                                                </div>
                                                <div class="col-md-7 value">
                                                     <?php echo $leads[0]['contact_no'];?>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                     Lead As
                                                </div>
                                                <div class="col-md-7 value">
                                                    <?php echo $leads[0]['lead_identification'];?>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                     Lead Source
                                                </div>
                                                <div class="col-md-7 value">
                                                     <?php echo $leads[0]['lead_source'];?>
                                                </div>
                                            </div>
                                            <?php if($type == 'assigned'){?>
                                                <div class="row static-info">
                                                    <?php 
                                                        $data = array(
                                                            'name'          => 'interested',
                                                            'id'            => 'interested',
                                                            'value'         => '1',
                                                            'checked'       => FALSE,
                                                            'style'         => 'margin:10px'
                                                        );
                                                        echo form_checkbox($data);
                                                        // Would produce: <input type="checkbox" name="newsletter" id="newsletter" value="1" style="margin:10px" />
                                                    ?>
                                                    If Interested in other product
                                                </div>
                                                <div class="row static-info interested-info" style="display:none;">
                                                    <div class="col-md-5 name">
                                                         Select Category
                                                    </div>
                                                    <div class="col-md-7 value categorylist">
                                                        <?php 
                                                            if(isset($category_list)){
                                                                $options = $category_list;
                                                                $js = array(
                                                                        'id'       => 'product_category_id',
                                                                        'class'    => 'form-control'
                                                                );
                                                                echo form_dropdown('product_category_id', $options , '',$js);    
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Lead Status
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <?php
                                                            $data = array(
                                                                'lead_id' => encode_id($leads[0]['id']),
                                                                'remind_to'  => $leads[0]['employee_id']
                                                            );
                                                            echo form_hidden($data);

                                                            $options = $lead_status;
                                                            $js = array(
                                                                    'id'       => 'lead_status',
                                                                    'class'    => 'form-control'
                                                            );
                                                            echo form_dropdown('lead_status', $options , $leads[0]['status'],$js);
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="row static-info followUp" style="display:none">
                                                    <div class="col-md-5 name">Remind On</div>
                                                    <div class="col-md-7 value">
                                                        <?php 
                                                        if(!empty($leads[0]['remind_on'])){
                                                            $value = date('m/d/y',strtotime($leads[0]['remind_on']));
                                                        }else{
                                                            $value = set_value('remind_on');
                                                        }
                                                        $data = array(
                                                            'type'  => 'text',
                                                            'name'  => 'remind_on',
                                                            'id'    => 'remind_on',
                                                            'class' => 'datepicker_recurring_start',
                                                            'value' => $value
                                                        );
                                                        echo form_input($data);
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="row static-info followUp" style="display:none">
                                                    <div class="col-md-5 name">Remind Text</div>
                                                    <div class="col-md-7 value">

                                                        <textarea name="reminder_text" rows="4" cols="50"><?php if(!empty($leads[0]['reminder_text'])) echo $leads[0]['reminder_text'];?></textarea>
                                                    </div>
                                                </div>
                                            <?php }else{?>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Category
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <?php echo $leads[0]['category_title'];?>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Lead Status
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <?php echo isset($leads[0]['status']) ? $leads[0]['status'] : 'NA';?>
                                                    </div>
                                                </div>

                                            <?php }?>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">Remark</div>
                                                <div class="col-md-7 value">
                                                <?php echo isset($leads[0]['remark']) ? $leads[0]['remark'] : 'NA';?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if($type == 'assigned'){?>
                                            <div class="form-actions right">
                                                <a href="<?php echo site_url('leads/leads_list/'.$type.'/'.$till);?>" class="btn btn-sm green"><i class="fa fa-plus"></i>Cancel
                                                </a>
                                                <button type="submit" class="btn green">Submit</button>
                                            </div>
                                        <?php }?>
                                    <?php }?>
                                </div>
                                <?php 
                                    echo form_close();
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <!-- END PRODUCT CATEGOEY-->
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
<script type="text/javascript">
    $(document).ready(function(){
        var lead_status = "<?php echo $leads[0]['status']?>";  //Current Lead status

        if(lead_status == 'Interested/Follow up'){
            $('.followUp').show();              //Display follow up fields 
        }

        $('#lead_status').change(function(){
            var option = $(this).val();         
            action(option);
        });

        $('body').on('focus',".datepicker_recurring_start", function(){
            $(this).datepicker();
        });
        /*$("button[type='reset']").on("click", function(event){
            //On Reset of form should display original values.
            action(lead_status);
            event.preventDefault();
            $(this).closest('form').get(0).reset();
        });*/

        $('#product_category_id').change(function () {
            var base_url = "<?php echo base_url()?>";
            var csrf = $("input[name=csrf_dena_bank]").val();
            var category_id = $(this).val();
            $.ajax({
                method: "POST",
                url: base_url + "/leads/productlist",
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    category_id: category_id
                }
            }).success(function (resp) {
                if (resp) {
                    $('.productlist').remove();
                    var html = '<div class="row static-info productlist">'+resp+'</div>';
                    $( html ).insertAfter( ".categorylist" );
                }
            });
        });

        $('#interested').change(function () {
            var selected = $(this).val();
            if($(this).prop('checked') == true){
                $('.interested-info').show();
            }else{
                $('.interested-info').hide();
            }
        });

        var action = function(option){
            if(option == 'Interested/Follow up'){
               $('.followUp').show();
            }else{
                $('.followUp').hide();
            }
        }

        $.validator.addMethod("regx", function(value, element, regexpr) {
            return regexpr.test(value);
        });

        $("#add_form").validate({

                rules: {
                    product_category_id: {
                        required: true
                    },
                    product_id: {
                        required: true
                    },
                    remind_on: {
                        required: true
                    }
                },
                messages: {
                    product_category_id: {
                        required: "Please select product category"
                    },
                    product_id: {
                        required: "Please select product"
                    },
                    remind_on: {
                        required: "Please select follow up date"
                    }
                }
            });
    });
</script>




    
    