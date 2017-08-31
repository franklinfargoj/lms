<?php 
$lead_status = $this->config->item('lead_status');
?>
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Lead Detail</h3>
        <?php if(isset($status)){?>
            <a href="<?php echo site_url('leads/leads_list/'.$type.'/'.$till.'/'.$status);?>" class="reset float-right">Back</a>
        <?php }else{
        ?>
            <a href="<?php echo site_url('leads/leads_list/'.$type.'/'.$till);?>" class="reset float-right">Back</a>
        <?php }?>
    </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <?php if($leads){?>
                <div class="lead-form">
                    <!-- <form> -->
                    <?php 
                        //Form
                        $attributes = array(
                            'role' => 'form',
                            'id' => 'detail_form',
                            'class' => 'form',
                            'autocomplete' => 'off'
                            );
                        echo form_open(site_url().'leads/update_lead_status', $attributes);
                    ?>
                        <div class="lead-form-left">
                            <div class="form-control">
                                <label>Conversion Status:</label> <span class="detail-label red"><?php echo ucwords($leads[0]['lead_identification']);?></span>
                            </div>
                            <div class="form-control">
                                <label>Lead ID:</label> <span class="detail-label"><?php echo ucwords($leads[0]['id']);?></span>
                            </div>
                            <div class="form-control">
                                <label>Product Name:</label> <span class="detail-label">Home Loan</span>
                            </div>
                            <div class="form-control">
                                <label>Lead Status:</label> <span class="detail-label"><?php echo isset($leads[0]['status']) ? $lead_status[$leads[0]['status']] : 'NA';?></span>
                            </div>
                            <div class="form-control">
                                <label>Reroute To:</label> <span class="detail-label">Vishal (Branch - State)</span>
                            </div>
                            <?php if($type == 'assigned'){?>
                                <div class="form-control">
                                    <label>Interest in other product</label>
                                    <div class="radio-control">
                                         <?php 
                                            $data = array(
                                                'name'          => 'interested',
                                                'id'            => 'interested',
                                                'value'         => '1',
                                                'checked'       => FALSE,
                                                'style'         => ''
                                            );
                                            echo form_checkbox($data);
                                            // Would produce: <input type="checkbox" name="newsletter" id="newsletter" value="1" style="margin:10px" />
                                        ?>
                                    </div>
                                </div>
                                <div class="form-control interested-info" style="display:none;">
                                    <label>Category:</label>   
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
                                
                                <div class="form-control">
                                    <label>Lead Status:</label>   
                                    <?php
                                        $data = array(
                                            'lead_id' => encode_id($leads[0]['id']),
                                            'remind_to'  => $leads[0]['employee_id']
                                        );
                                        echo form_hidden($data);
                                        $options1['']='Select';
                                        foreach ($lead_status as $key => $value) {
                                            $options1[$key] = $value;
                                        }
                                        $js = array(
                                                'id'       => 'lead_status',
                                                'class'    => 'form-control'
                                        );
                                        echo form_dropdown('lead_status', $options1 , $leads[0]['status'],$js);
                                    ?>
                                </div>
                                <div class="form-control followUp" style="display:none">
                                    <label>Remind On:</label>   
                                    <?php 
                                        if(!empty($leads[0]['remind_on'])){
                                            $value = date('d-m-Y',strtotime($leads[0]['remind_on']));
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
                                <div class="form-control followUp" style="display:none">
                                    <label>Remind Text:</label>   
                                    <textarea rows="4" cols="80" name="reminder_text"><?php if(!empty($leads[0]['reminder_text'])) echo $leads[0]['reminder_text'];?></textarea>
                                </div>
                                <div class="form-control accountOpen" style="display:none">
                                    <label>Verify Account</label>   
                                    <?php 
                                        $data = array(
                                            'type'  => 'text',
                                            'name'  => 'accountNo',
                                            'id'    => 'accountNo',
                                            'class' => '',
                                            'value' => ''
                                        );
                                        echo form_input($data);
                                        ?>
                                </div>
                            <?php }?>
                        </div>


                        <div class="lead-form-right">
                            <div class="form-control">
                                <label>Customer Name:</label> <span class="detail-label"><?php echo ucwords($leads[0]['customer_name']);?></span>
                            </div>
                            <div class="form-control">
                                <label>Phone Number:</label> <span class="detail-label"><?php echo $leads[0]['contact_no'];?></span>
                            </div>
                            <div class="form-control">
                                <label>Remark/Notes</label>
                                <p class="remark-notes"><?php echo isset($leads[0]['remark']) ? $leads[0]['remark'] : 'NA';?></p>
                            </div>
                            <?php if($type == 'assigned'){?>
                            <div class="form-control">
                                <label>Reroute To:</label>   
                                <select name="product">
                                <option value="product1"></option>
                                <option value="product1">Vishal (Branch - State)</option>
                              </select>
                            </div>
                            <?php }?>
                        </div>
                        <div class="form-control form-submit clearfix">
                        <?php if($type == 'assigned'){?>
                            <a href="#" class="float-right">
                                    <img src="<?php echo base_url().ASSETS;?>images/left-nav.png">
                                    <span><input type="submit" class="custom_button" value="Submit" /></span>
                                    <img src="<?php echo base_url().ASSETS;?>images/right-nav.png">
                            </a>
                        <?php }?>
                        </div>
                    <!-- </form> -->
                    <?php 
                        echo form_close();
                    ?>
                </div>
            <?php }?>
        </div>
    </div>
    <span class="bg-bottom"></span>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        var lead_status = "<?php echo $leads[0]['status']?>";  //Current Lead status

        if(lead_status == 'FU'){
            $('.followUp').show();              //Display follow up fields 
        }

        $('#lead_status').change(function(){
            var option = $(this).val();         
            action(option);
        });

        $('body').on('focus',".datepicker_recurring_start", function(){
            $(this).datepicker({dateFormat: 'dd-mm-yy'});

        });
        /*$("button[type='reset']").on("click", function(event){
            //On Reset of form should display original values.
            action(lead_status);
            event.preventDefault();
            $(this).closest('form').get(0).reset();
        });*/

        $('#product_category_id').change(function () {
            var csrf = $("input[name=csrf_dena_bank]").val();
            var category_id = $(this).val();
            $.ajax({
                method: "POST",
                url: baseUrl + "leads/productlist",
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    category_id: category_id
                }
            }).success(function (resp) {
                if (resp) {
                    $('.productlist').remove();
                    var html = '<div class="form-control interested-info productlist">'+resp+'</div>';
                    $( html ).insertAfter( ".interested-info" );
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
            if(option == 'FU'){
               $('.followUp').show();
               $('.accountOpen').hide();
            }else if(option == 'AO'){
               $('.accountOpen').show();
               $('.followUp').hide();
            }else{
                $('.accountOpen').hide();
                $('.followUp').hide();
            }
        }

        $.validator.addMethod("regx", function(value, element, regexpr) {
            return regexpr.test(value);
        });

        $("#detail_form").validate({
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




    
    