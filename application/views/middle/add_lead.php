<style type="text/css">
    .error {
        color: red
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
    'name' => 'phone_no',
    'id' => 'phone_no',
    'value' => set_value('phone_no', '')
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
$data_state = array('class' => 'form-control ',
    'name' => 'state_id',
    'id' => 'state_id',
    'value' => set_value('state_id', '')
);
$data_branch = array('class' => 'form-control ',
    'name' => 'branch_id',
    'id' => 'branch_id',
    'value' => set_value('branch_id', '')
);
$data_district = array('class' => 'form-control ',
    'name' => 'district',
    'id' => 'district_id',
    'value' => set_value('district', '')
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
$data_cancel = array(
    'name' => 'Cancel',
    'id' => 'Cancel',
    'value' => 'Cancel',
    'type' => 'Button',
    'content' => 'Cancel',
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
                <?php if ($this->session->userdata("success_message")) { ?>
                    <div class="alert alert-success">
                        <strong>Success!</strong> Lead added successfully.
                    </div>
                <?php }
                $url = base_url('Leads/add');
                echo form_open($url, $form_attributes);
                ?>
                <div class="form-body">
                    <div class="form-group">
                        <label>Customer Type</label>
                        <?php echo form_dropdown('customer_type', $customer_options, '', $extra) ?>
                        <?php echo form_error('customer_type'); ?>
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
                        <?php echo form_error('phone_no'); ?>
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
                        <?php echo form_dropdown('product_category', $options, $category_selected, $category_extra) ?>
                        <?php echo form_error('product_category'); ?>
                    </div>
                    <div class="form-group " id="product_select">
                        <label>Product</label>
                        <?php echo form_dropdown('product', $product_options, $product_selected, $product_extra) ?>
                        <?php echo form_error('product'); ?>
                    </div>

                    <div class="form-group">
                        <div class="radio-list">
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
                    <div class="form-group">
                        <label>Lead Identification</label>
                        <?php echo form_dropdown('lead_identification', $lead_id_options, '', $extra) ?>
                        <?php echo form_error('lead_identification'); ?>
                    </div>
                    <div class="form-group">
                        <label>Account No</label>
                        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-mobile"></i>
            </span>
                            <?php echo form_input($data_account);
                            ?>
                        </div>
                        <?php echo form_error('account_no'); ?>
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <?php echo form_textarea($data_remark, '', $remark_extra);
                        ?>

                        <?php echo form_error('remark'); ?>
                        <label id="remark_id-error" class="error" for="remark"></label>
                    </div>
                    <div class="form-actions">
                        <?php echo form_button($data_submit) ?>
                        <?php echo form_button($data_cancel) ?>
                    </div>

                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
    <script type="text/javascript">
        $(document).ready(function () {
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
                var base_url = "http://10.0.11.33/lms";
                var category_id = $('#product_category').val();
                var csrf = $("input[name=csrf_dena_bank]").val();
                $.ajax({
                    method: "POST",
                    url: base_url + "/Leads/Productlist",
                    data: {
                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                        category_id: category_id
                    }
                }).success(function (resp) {
                    if (resp) {
                        $("#product_select").html(JSON.parse(resp));
                    }
                });
            });

            $.validator.addMethod("regx", function (value, element, regexpr) {
                return regexpr.test(value);
            });

            $("#addlead").validate({

                rules: {
                    customer_type: {
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
                    phone_no: {
                        required: true,
                        number: true,
                        maxlength: 10,
                        minlength: 10
                    },
                    product_category: {
                        required: true
                    },
                    product: {
                        required: true
                    },
                    lead_identification: {
                        required: true
                    },
                    state_id: {
                        required: true
                    },
                    district: {
                        required: true
                    },
                    branch_id: {
                        required: true
                    },
                    remark: {
                        required: true
                    }
                },
                messages: {
                    customer_type: {
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
                    phone_no: {
                        required: "Please enter phone number",
                        maxlength: 'Please enter no more than 10 digits',
                        minlength: 'Please enter no less than 10 digits'


                    },
                    product_category: {
                        required: "Please select product category"
                    },
                    product: {
                        required: "Please select product"
                    },
                    district: {
                        required: "Please select district"
                    },
                    state_id: {
                        required: "Please select state"
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
