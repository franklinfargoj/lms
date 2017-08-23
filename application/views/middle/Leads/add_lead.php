<style type="text/css">
    .error {
        color: red
    }

    .hide {
        display: none;
    }
</style>

<?php
$form_attributes = array('class' => '', 'method' => 'post', 'accept-charset' => '', 'id' => 'addlead');
$data_customer = array('class' => 'form-control ',
    'name' => 'customer_name',
    'id' => 'customer_name',
    'value' => set_value('customer_name', '')
);
$data_lead = array('class' => 'form-control ',
    'name' => 'lead_name',
    'id' => 'lead_name',
    'value' => set_value('lead_name', '')
);
$data_phone = array('class' => 'form-control ',
    'name' => 'contact_no',
    'id' => 'phone_no',
    'value' => set_value('contact_no', '')
);
$data_aadhar = array('class' => 'form-control ',
    'name' => 'aadhar_no',
    'id' => 'aadhar_no',
    'value' => set_value('aadhar_no', '')
);
$data_pan = array('class' => 'form-control ',
    'name' => 'pan_no',
    'id' => 'pan_no',
    'value' => set_value('pan_no', '')
);
$data_account = array('class' => 'form-control ',
    'name' => 'account_no',
    'id' => 'account_no',
    'value' => set_value('account_no', '')
);
$data_state = array('class' => 'form-control',
    'name' => 'state_id',
    'id' => 'state_id',
    'value' => set_value('state_id', '')
);
$data_branch = array('class' => 'form-control',
    'name' => 'branch_id',
    'id' => 'branch_id',
    'value' => set_value('branch_id', '')
);
$data_district = array('class' => 'form-control',
    'name' => 'district_id',
    'id' => 'district_id',
    'value' => set_value('district_id', '')
);
$data_department_name = array('class' => 'form-control ',
    'name' => 'department_name',
    'id' => 'department',
    'value' => set_value('department_name', '')
);
$data_department_id = array('class' => 'form-control ',
    'name' => 'department_id',
    'id' => 'department',
    'value' => set_value('department_id', '')
);
$data_remark = array('class' => 'form-control ',
    'name' => 'remark',
    'id' => 'remark_id',
    'value' => set_value('remark', '')
);

$data_submit = array(
    'name' => 'Submit',
    'id' => 'Submit',
    'type' => 'Submit',
    'content' => 'Submit',
    'class' => 'btn green',
    'value' => 'Submit'
);
$data_reset = array(
    'name' => 'reset',
    'id' => 'reset',
    'value' => 'Reset',
    'type' => 'Button',
    'content' => 'Reset',
    'class' => 'btn default'
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

$category_extra = 'class="form-control" id="product_category"';
$product_extra = 'class="form-control" id="product"';
$extra = 'class="form-control"';
$remark_extra = 'style="height:50%"';
?>
<div class="row">
    <div class="col-md-6 ">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light">
            <div class="portlet-body form">

                <?php
                $url = base_url('leads/add');
                echo form_open($url, $form_attributes);
                ?>
                <div class="form-body">
                    <div class="form-group">
                        <label>Customer Type</label>
                        <?php echo form_dropdown('is_existing_customer', $customer_options, set_value('is_existing_customer'), $extra) ?>
                        <?php echo form_error('is_existing_customer'); ?>
                    </div>
                    <div class="form-group">
                        <label>Customer Name</label>
                        <div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-user"></i>
						</span>
                            <?php echo form_input($data_customer);
                            ?>
                        </div>
                        <?php echo form_error('customer_name'); ?>
                        <label id="customer_name-error" class="error" for="customer_name"></label>
                    </div>
                    <!--			<div class="form-group">-->
                    <!--				<label>Lead Name</label>-->
                    <!--				<div class="input-group">-->
                    <!--					<span class="input-group-addon">-->
                    <!--						<i class="fa fa-user"></i>-->
                    <!--					</span>-->
                    <!--					--><?php //echo form_input($data_lead);
                    //					?>
                    <!--				</div>-->
                    <!--				--><?php //echo form_error('lead_name'); ?>
                    <!--                <label id="lead_name-error" class="error" for="lead_name"></label>-->
                    <!--			</div>-->
                    <div class="form-group">
                        <label>Mobile No.</label>
                        <div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-mobile"></i>
					</span>
                            <?php echo form_input($data_phone);
                            ?>
                        </div>
                        <?php echo form_error('contact_no'); ?>
                        <label id="phone_no-error" class="error" for="phone_no"></label>
                    </div>

                    <!--			<div class="form-group">-->
                    <!--					<label>Aadhar No.</label>-->
                    <!--				<div class="input-group">-->
                    <!--					<span class="input-group-addon">-->
                    <!--						<i class="fa fa-mobile"></i>-->
                    <!--					</span>-->
                    <!--					--><?php //echo form_input($data_aadhar);
                    //					?>
                    <!--				</div>-->
                    <!--			--><?php //echo form_error('aadhar_no'); ?>
                    <!--			</div>-->
                    <!--			<div class="form-group">-->
                    <!--					<label>Pan No.</label>-->
                    <!--				<div class="input-group">-->
                    <!--					<span class="input-group-addon">-->
                    <!--						<i class="fa fa-mobile"></i>-->
                    <!--					</span>-->
                    <!--					--><?php //echo form_input($data_pan);
                    //					?>
                    <!--				</div>-->
                    <!--			</div>-->
                    <?php echo form_error('pan_no'); ?>
                    <div class="form-group">
                        <label>Product Category</label>
                        <?php echo form_dropdown('product_category_id', $options, set_value('product_category_id'), $category_extra) ?>
                        <?php echo form_error('product_category_id'); ?>
                    </div>
                    <div class="form-group " id="product_select">
                        <label>Product</label>
                        <?php echo form_dropdown('product_id', $product_options, set_value('product_id'), $product_extra) ?>
                        <?php echo form_error('product_id'); ?>
                    </div>
                        <label>Ticket range</label>
                        <div id="slider"></div>
                        <?php echo form_input($data_ticket_range)?>
                    <div class="form-group">
                        <div class="radio-list">
                            <label>Own Branch / Other Branch</label>
                            <label class="radio-inline">
                                <input type="radio" id="is_own_branch" name="is_own_branch"
                                       value="1" <?php echo set_radio('is_own_branch', '1', TRUE); ?> />
                                Own Branch
                            </label>
                            <label class="radio-inline">
                                <input type="radio" id="is_other_branch" name="is_own_branch"
                                       value="0" <?php echo set_radio('is_own_branch', '0'); ?> />
                                Other Branch
                            </label>
                        </div>
                        <?php echo form_error('is_own_branch'); ?>
                    </div>
                    <div id="state" class="form-group hide">
                        <label>State</label>
                        <div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-state"></i>
					</span>
                            <?php echo form_input($data_state);
                            ?>
                        </div>
                        <label id="state_id-error" class="error" for="state_id"></label>
                        <?php echo form_error('state_id'); ?>
                    </div>
                    <div id="district" class="form-group hide">
                        <label>District</label>
                        <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-branch"></i>
                </span>
                            <?php echo form_input($data_district);
                            ?>
                        </div>
                        <label id="district_id-error" class="error" for="district_id"></label>
                        <?php echo form_error('district'); ?>
                    </div>
                    <div id="branch" class="form-group hide">
                        <label>Branch</label>
                        <div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-branch"></i>
					</span>
                            <?php echo form_input($data_branch);
                            ?>
                        </div>
                        <label id="branch_id-error" class="error" for="branch_id"></label>
                        <?php echo form_error('branch_id'); ?>
                    </div>
                    <div id="department_id" class="form-group">
                        <label>Department Id</label>
                        <div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-branch"></i>
					</span>
                            <?php echo form_input($data_department_id);
                            ?>
                        </div>
                        <label id="department_id-error" class="error" for="department_id"></label>
                        <?php echo form_error('department_id'); ?>
                    </div>
                    <div id="department_name" class="form-group">
                        <label>Department Name</label>
                        <div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-branch"></i>
					</span>
                            <?php echo form_input($data_department_name);
                            ?>
                        </div>
                        <label id="department_name-error" class="error" for="department_name"></label>
                        <?php echo form_error('department_name'); ?>
                    </div>
                    <div class="form-group">
                        <label>Lead Identification</label>
                        <?php echo form_dropdown('lead_identification', $lead_id_options, set_value('lead_identification'), $extra) ?>
                        <?php echo form_error('lead_identification'); ?>
                    </div>
                    <!--                    <div class="form-group">-->
                    <!--                        <label>Account No</label>-->
                    <!--                        <div class="input-group">-->
                    <!--            <span class="input-group-addon">-->
                    <!--                <i class="fa fa-mobile"></i>-->
                    <!--            </span>-->
                    <!--                            --><?php //echo form_input($data_account);
                    //                            ?>
                    <!--                        </div>-->
                    <!--                        --><?php //echo form_error('account_no'); ?>
                    <!--                    </div>-->
                    <div class="form-group">
                        <label>Remarks</label>
                        <?php echo form_textarea($data_remark, '', $remark_extra);
                        ?>

                        <?php echo form_error('remark'); ?>
                        <label id="remark_id-error" class="error" for="remark"></label>
                    </div>
                    <div class="form-actions">
                        <?php echo form_button($data_reset) ?>
                        <?php echo form_button($data_submit) ?>
                    </div>

                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var range = $('#ticket_range');
        var sliderElement = $( "#slider" );
        sliderElement.slider({
            range:false,
            max:50000000,
            min:5000,
            values:[5000],
            slide:function (event,ui) {
                range.val(ui.values[0]);
            }
        });
        var value = sliderElement.slider('values',0);
        range.val(value);

        range.change(function () {
            sliderElement.slider('values',0,range.val());
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
    });
</script>
