<style>
    .ui-widget-header {
        background-color: #ed4c4c;
    }
</style>
<?php
$form_attributes = array('class' => '', 'method' => 'post', 'accept-charset' => '', 'id' => 'addlead');
$data_customer = array('name' => 'customer_name',
    'id' => 'customer_name',
    'value' => set_value('customer_name', '')
);
$data_lead = array('name' => 'lead_name',
    'id' => 'lead_name',
    'value' => set_value('lead_name', '')
);
$data_phone = array('name' => 'contact_no',
    'id' => 'phone_no',
    'value' => set_value('contact_no', '')
);
$data_aadhar = array('name' => 'aadhar_no',
    'id' => 'aadhar_no',
    'value' => set_value('aadhar_no', '')
);
$data_pan = array('name' => 'pan_no',
    'id' => 'pan_no',
    'value' => set_value('pan_no', '')
);
$data_account = array('name' => 'account_no',
    'id' => 'account_no',
    'value' => set_value('account_no', '')
);
$data_state[''] = 'Select State';
$data_state['1'] = 'Odisha';

$data_branch[''] = 'Select Branch';
$data_branch['1'] = 'Odisha';

$data_district[''] = 'Select District';
$data_district['1'] = 'Odisha';

$data_department_name = array('name' => 'department_name',
    'id' => 'department_name',
    'value' => set_value('department_name', '')
);
$data_department_id = array('name' => 'department_id',
    'id' => 'department_id',
    'value' => set_value('department_id', '')
);
$data_remark = array('name' => 'remark',
    'id' => 'remark_id',
    'value' => set_value('remark', '')
);

$data_submit = array(
    'name' => 'Submit',
    'id' => 'Submit',
    'type' => 'button',
    'value' => 'Submit'
);
$data_reset = array(
    'name' => 'reset',
    'id' => 'reset',
    'value' => 'Reset',
    'type' => 'button',
);

$customer_options[''] = 'Select Customer';
$customer_options['0'] = 'New';
$customer_options['1'] = 'Existing';


$options[''] = 'Select Product Category';
foreach ($category as $key => $value) {
    $options[$value['id']] = $value['title'];
}

$product_options[''] = 'Select Product';
if ($products != '') {
    foreach ($products as $key => $value) {
        $product_options[$value['id']] = $value['title'];
    }
}

$data_ticket_range = array('name'=>'lead_ticket_range','id'=>'ticket_range','type'=>'text','value'=>'');
$lead_id_options[''] = 'Select Lead Identification';
$lead_id_options['HOT'] = 'HOT';
$lead_id_options['WARM'] = 'WARM';
$lead_id_options['COLD'] = 'COLD';

$branch_options['1'] = 'Own Branch';
$branch_options['0'] = 'Other Branch';


$category_extra = 'id="product_category"';
$product_extra = 'id="product"';
$extra = '';
$remark_extra = 'style="rows:4 ; cols:80"';
?>
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Add Lead</h3>
    </div>
</div>
<div class="page-content">
    <div class="container">
        <div class="lead-form">
            <?php
            $url = base_url('leads/add');
            echo form_open($url, $form_attributes);
            ?>
            <div class="lead-form-left">
                <div class="form-control">
                    <label>Customer Type</label>
                    <div class="radio-control">
                        <input type="radio" name="is_existing_customer"
                               value="1" <?php echo set_radio('is_existing_customer', '1', TRUE); ?> />
                        <label>New</label>
                    </div>
                    <div class="radio-control">
                        <input type="radio" name="is_existing_customer"
                               value="0" <?php echo set_radio('is_existing_customer', '0'); ?> />
                        <label>Existing</label>
                    </div>
                </div>
                <?php echo form_error('is_existing_customer'); ?>
                <div class="form-control">
                    <label>Customer Name:</label>
                    <?php echo form_input($data_customer);?>
                </div>
                <?php echo form_error('customer_name'); ?>
                <div class="form-control">
                    <label>Customer Number:</label>
                    <?php echo form_input($data_phone); ?>
                </div>
                <?php echo form_error('contact_no'); ?>
                <div class="form-control">
                    <label>Product Category:</label>
                    <?php echo form_dropdown('product_category_id', $options, set_value('product_category_id'), $category_extra) ?>
                </div>
                <?php echo form_error('product_category_id'); ?>
                <div class="form-control" id="product_select">
                    <label>Product</label>
                    <?php echo form_dropdown('product_id', $product_options, set_value('product_id'), $product_extra) ?>
                </div>
                <?php echo form_error('product_id'); ?>
                <div class="form-control range-slider">
                    <label>Ticket Size</label>
                    <div id="slider" class="ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content">
                        <div class="ui-slider-range ui-corner-all ui-widget-header ui-slider-range-min"></div>
                        <span id="span_range" tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" style="left: 66%;"></span>
                        <div id="div_range" class="ui-slider-range ui-widget-header ui-corner-all ui-slider-range-min"></div>
                    </div>
                    <div class="step">
                        <span>5000</span>
                        <span style="float: right">5 crore</span>
                    </div>

                    <?php echo form_input($data_ticket_range)?>
                </div>

            </div>


            <div class="lead-form-right">
                <div class="form-control">
                    <label>Branch Type:</label>
                    <div class="radio-control">
                        <input type="radio" id="is_own_branch" name="is_own_branch"
                               value="1" <?php echo set_radio('is_own_branch', '1', TRUE); ?> />
                        <label>Own Branch</label>
                    </div>
                    <div class="radio-control">
                        <input type="radio" name="is_own_branch" id="is_other_branch"
                               value="0" <?php echo set_radio('is_own_branch', '0'); ?> />
                        <label>Other Branch</label>
                    </div>
                </div>
                <?php echo form_error('is_own_branch'); ?>
                <div id="state" class="form-control hide">
                    <label>State:</label>
                    <?php echo form_dropdown('state_id', $data_state, set_value('state_id')) ?>
                    </select>
                </div>
                <?php echo form_error('state_id'); ?>
                <div id="district" class="form-control hide">
                    <label>District:</label>
                    <?php echo form_dropdown('district_id', $data_district, set_value('district_id')) ?>
                    </select>
                </div>
                <?php echo form_error('district_id'); ?>
                <label id="district_id-error" class="error" for="district_id"></label>
                <div id="branch" class="form-control hide">
                    <label>Branch:</label>
                    <?php echo form_dropdown('branch_id', $data_branch, set_value('branch_id')) ?>
                    </select>
                </div>
                <?php echo form_error('branch_id'); ?>
                <div class="form-control">
                    <label>Department Name:</label>
                    <?php echo form_input($data_department_name);?>
                </div>
                <?php echo form_error('department_name'); ?>
                <div class="form-control">
                    <label>Department Id:</label>
                    <?php echo form_input($data_department_id);?>
                </div>
                <?php echo form_error('department_id'); ?>

                <div id="identification" class="form-control">
                    <label>Lead Identification:</label>
                    <?php echo form_dropdown('lead_identification', $lead_id_options, set_value('lead_identification'), $extra) ?>
                </div>
                <?php echo form_error('lead_identification'); ?>

                <div class="form-control">
                    <label>Remark/Notes</label>
                    <?php echo form_textarea($data_remark, '', $remark_extra);?>
                </div>
                <?php echo form_error('remark'); ?>
            </div>
            <div class="form-control form-submit clearfix">
                <a href="javascript:void(0);" class="float-right">
                    <img src="<?php echo base_url().ASSETS;?>images/left-nav.png">
                    <span><input type="submit" style="border: none" name="Submit" value="Submit"></span>
                    <img src="<?php echo base_url().ASSETS;?>images/right-nav.png">
                </a>
                <a href="javascript:void(0);" class="float-right">
                    <img src="<?php echo base_url().ASSETS;?>images/left-nav.png">
                    <span><input type="reset" style="border: none;color: white" name="Submit" value="Reset"></span>
                    <img src="<?php echo base_url().ASSETS;?>images/right-nav.png">
                </a>
<!--                <a href="javascript:void(0);" class="reset float-right">-->
<!--                    <img src="--><?php //echo base_url().ASSETS;?><!--images/reset-btn.png">-->
<!--                    <span><input type="submit" style="border: none" name="Submit" value="Submit"></span>-->
<!--                </a>-->

            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        var range = $('#ticket_range');
        var sliderElement = $( "#slider" );
        var span = $( "#span_range" );
        var div = $('#div_range');
        sliderElement.slider({
            range:"min",
            orientation: "horizontal",
            max:50000000,
            min:5000,
            animate: true,
            values:[5000],
            slide:function (event,ui) {
                range.val(ui.values[0]);
                var match_width = span.css('left');
                div.css('width',match_width);
            }
        });
        var value = sliderElement.slider('values',0);
        range.val(value);

        range.change(function () {
            sliderElement.slider('values',0,range.val());
            var match_width = span.css('left');
            console.log(match_width);
//            var width1 = match_width.split('px');
//
//            var final_width = (width1[0] * 2.96) + 'px';
//            console.log(width1[0]);
//            div.css('width',final_width);
        });

        if ($('#is_other_branch').is(':checked')) {
            $('#state').removeClass('hide');
            $('#branch').removeClass('hide');
            $('#district').removeClass('hide');
        }

        $('#is_other_branch').click(function () {
            if ($('#is_other_branch').is(':checked')) {
                $('#state').removeClass('hide');
                $('#branch').removeClass('hide');
                $('#district').removeClass('hide');
            }
        });
        $('#is_own_branch').click(function () {
            if ($('#is_own_branch').is(':checked')) {
                $('#state').addClass('hide');
                $('#branch').addClass('hide');
                $('#district').addClass('hide');
            }
        });

        $('#product_category').change(function () {
            var base_url = "<?php echo base_url();?>";
            var category_id = $('#product_category').val();
            var csrf = $("input[name=csrf_dena_bank]").val();
            $.ajax({
                method: "POST",
                url: base_url + "leads/productlist",
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    category_id: category_id
                }
            }).success(function (resp) {
                if (resp) {
                    $("#product_select").html(resp);
                }
            });
        });

        $.validator.addMethod("regx", function (value, element, regexpr) {
            return regexpr.test(value);
        });

        $("#addlead").validate({

            rules: {
                is_existing_customer: {
                    required: true
                },
                customer_name: {
                    required: true,
                    regx: /^[a-zA-Z0-9\-\s]+$/
                },
                lead_name: {
                    required: true,
                    regx: /^[a-zA-Z0-9\-\s]+$/
                },
                contact_no: {
                    required: true,
                    number: true,
                    maxlength: 10,
                    minlength: 10
                },
                product_category_id: {
                    required: true
                },
                product_id: {
                    required: true
                },
                lead_identification: {
                    required: true
                },
                lead_ticket_range: {
                    required: true,
                    number:true
                },
                state_id: {
                    required: true
                },
                district_id: {
                    required: true
                },
                branch_id: {
                    required: true
                },
                department_id: {
                    required: true
                },
                department_name: {
                    required: true
                },
                remark: {
                    required: true
                }
            },
            messages: {
                is_existing_customer: {
                    required: "Please select customer"
                },
                customer_name: {
                    required: "Please enter customer name",
                    regx: "Special characters are not allowed"
                },
                lead_name: {
                    required: "Please enter lead name",
                    regx: "Special characters are not allowed"
                },
                lead_ticket_range: {
                    required: "Please enter range",
                    number: "Only numbers allowed"
                },
                contact_no: {
                    required: "Please enter phone number",
                    maxlength: 'Please enter no more than 10 digits',
                    minlength: 'Please enter no less than 10 digits'


                },
                product_category_id: {
                    required: "Please select product category"
                },
                product_id: {
                    required: "Please select product"
                },
                district_id: {
                    required: "Please select district"
                },
                state_id: {
                    required: "Please select state"
                },
                department_name: {
                    required: "Please enter department name"
                },
                department_id: {
                    required: "Please enter department id"
                },
                branch_id: {
                    required: "Please select branch"
                },
                lead_identification: {
                    required: "Please select lead identification"
                },
                remark: {
                    required: "Please enter remark"
                }
            }
        });
    })
</script>