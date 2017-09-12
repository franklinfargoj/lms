
<?php
$form_attributes = array('class' => 'form', 'method' => 'post', 'accept-charset' => '', 'id' => 'addlead');
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
if ($states != '') {
    foreach ($states as $key => $value) {
        $data_state[$value['code']] = $value['name'];
    }
}

$data_branch[''] = 'Select Branch';
if ($branches != '') {
    foreach ($branches as $key => $value) {
        $data_branch[$value['code']] = $value['name'];
    }
}

$data_district[''] = 'Select District';
if ($districts != '') {
    foreach ($districts as $key => $value) {
        $data_district[$value['code']] = $value['name'];
    }
}

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


$options = $category;
/*foreach ($category as $key => $value) {
    $options[$value['id']] = $value['title'];
}*/

$product_options[''] = 'Select';
if ($products != '') {
    foreach ($products as $key => $value) {
        $product_options[$value['id']] = $value['title'];
    }
}
$input = get_session();
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
$state_extra = 'id="state_id"';
$district_extra = 'id="district_id"';
$branch_extra = 'id="branch_id"';
$remark_extra = 'style="rows:4 ; cols:80"';
?>
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Add Lead</h3>
    </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <div class="lead-form">
                <?php
                $url = base_url('leads/add');
                echo form_open($url, $form_attributes);
                ?>
                <div class="lead-form-left">
                    <!--<div class="form-control">
                        <label>Customer Type</label>
                        <div class="radio-control">
                            <input type="radio" name="is_existing_customer"
                                   value="1" <?php /*echo set_radio('is_existing_customer', '1', TRUE); */?> />
                            <label>New</label>
                        </div>
                        <div class="radio-control">
                            <input type="radio" name="is_existing_customer"
                                   value="0" <?php /*echo set_radio('is_existing_customer', '0'); */?> />
                            <label>Existing</label>
                        </div>
                    </div>-->
    <!--                --><?php //echo form_error('is_existing_customer'); ?>
                    <div class="form-control">
                        <label>Customer Name:</label>
                        <?php echo form_input($data_customer);?>
                        <?php echo form_error('customer_name'); ?>
                    </div>
                    <div class="form-control">
                        <label>Customer Number:</label>
                        <?php echo form_input($data_phone); ?>
                        <?php echo form_error('contact_no'); ?>
                    </div>
                    <div class="form-control">
                        <label>Product Category:</label>
                        <?php echo form_dropdown('product_category_id', $options, set_value('product_category_id'), $category_extra) ?>
                        <?php echo form_error('product_category_id'); ?>
                    </div>
                    <div class="form-control" id="product_select">
                        <label>Product</label>
                        <?php echo form_dropdown('product_id', $product_options, set_value('product_id'), $product_extra) ?>
                        <?php echo form_error('product_id'); ?>
                    </div>
                    <div class="form-control range-slider">
                        <label>Ticket Size</label>
                         <?php echo form_input($data_ticket_range)?><img src="../assets2/images/rupees.png" alt="rupees" id="rs">
                        <div id="master">
                            <div class="ui-slider-range ui-corner-all ui-widget-header ui-slider-range-min"></div>
                        </div>
                        
                        <div class="step">
                            <span class="float-left">5000</span>
                            <span class="float-right">1 Crore & above</span>
                        </div>
                       
                    </div>

                </div>


                <div class="lead-form-right">
                    <div class="form-control">
                        
                        <?php 
                            if(in_array($this->session->userdata('admin_type'),array('RM','ZM'))){
                                $checked = TRUE;
                                $style = "style='display:none'";
                            }else{
                                $checked = FALSE;
                                $style = "";
                        ?>
                            <label>Branch Type:</label>
                            <div class="radio-control">
                                <input type="radio" id="is_own_branch" name="is_own_branch"
                                       value="1" <?php echo set_radio('is_own_branch', '1', TRUE); ?> />
                                <label>Own Branch</label>
                            </div>
                        <?php
                            }
                        ?>
                        <div class="radio-control" <?php echo $style;?>>
                            <input type="radio" name="is_own_branch" id="is_other_branch"
                                   value="0" <?php echo set_radio('is_own_branch', '0',$checked); ?> />
                            <label>Other Branch</label>
                        </div>
                        <?php echo form_error('is_own_branch'); ?>
                    </div>
                    <div id="state" class="form-control">
                        <label>State:</label>
                        <?php echo form_dropdown('state_id', $data_state,$input['state_id'],'disabled '.$state_extra) ?>
                        <?php echo form_error('state_id'); ?>
                    </div>
                    <div id="district" class="form-control">
                        <label>District:</label>
                        <?php echo form_dropdown('district_id', $data_district,$input['district_id'],'disabled '.$district_extra) ?>
                        <?php echo form_error('district_id'); ?>
                    </div>
                    <div id="branch" class="form-control">
                        <label>Branch:</label>
                        <?php echo form_dropdown('branch_id', $data_branch,$input['branch_id'],'disabled '.$branch_extra) ?>
                        <?php echo form_error('branch_id'); ?>
                    </div>

                    <!--<div id="identification" class="form-control">
                        <label>Lead Identification:</label>
                        <?php /*echo form_dropdown('lead_identification', $lead_id_options, set_value('lead_identification'), $extra) */?>
                    </div>
                    --><?php /*echo form_error('lead_identification'); */?>

                    <div class="form-control">
                        <label>Remark/Notes</label>
                        <?php echo form_textarea($data_remark, '', $remark_extra);?>
                        <?php echo form_error('remark'); ?>
                    </div>
                </div>
                <div class="form-control form-submit clearfix">

                    <a href="javascript:void(0);" class="active float-right">
                        <img alt ="left nav" src="<?php echo base_url().ASSETS;?>images/left-nav.png">
                        <span><input type="submit" class="custom_button" name="Submit" value="Submit"></span>
                        <img alt = "right nav" src="<?php echo base_url().ASSETS;?>images/right-nav.png">
                    </a>
                    <a href="javascript:void(0);" class="reset float-right">
                        Reset
                    </a>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <span class="bg-bottom"></span>
</div>
<script>
    $(document).ready(function(){
            var sliderElement = $("#master");
            var range = $('#ticket_range');
            var div = $('.ui-slider-range');
            // setup master volume
            sliderElement.slider({
                step:5000,
                orientation: "horizontal",
                max: 10000000,
                min: 5000,
                animate: true,
                values: [5000],
                slide: function (event, ui) {
                    range.val(ui.values[0]);
                    console.log(ui.values[0]);
                    var width = (ui.values[0]-5000)/(10000000) * 100 + '%';
                    div.width(width);
                }
            });
            var value = sliderElement.slider('values', 0);
            range.val(value);

            range.keyup(function () {
                if($.isNumeric(range.val())) {
                    sliderElement.slider('values', 0, range.val());
                    var width = '100%';
                    if (range.val() <= 10000000)
                        width = (range.val() / 10000000) * 100 + '%';
                    div.width(width);
                }
            });

        if ($('#is_other_branch').is(':checked')) {
            $('select[name="state_id"]').prop('disabled',false);
            $('select[name="branch_id"]').prop('disabled',false);
            $('select[name="district_id"]').prop('disabled',false);
            $('select[name="state_id"]').val('');
            $('select[name="branch_id"]').val('');
            $('select[name="district_id"]').val('');
        }

        $('#is_other_branch').click(function () {
            var dist = '<select name="district_id" id = "district_id"><option value="">Select District</option></select>';
            var branch = '<select name="branch_id" id = "branch_id"><option value="">Select Branch</option></select>';

            $('select[name="state_id"]').prop('disabled',false);
            $('select[name="branch_id"]').prop('disabled',false);
            $('select[name="district_id"]').prop('disabled',false);
            $('select[name="state_id"]').val('');
            $('select[name="branch_id"]').html(branch);
            $('select[name="district_id"]').html(dist);
        });
        $('#is_own_branch').click(function () {
            if ($('#is_own_branch').is(':checked')) {
                var state = "<?php echo $input['state_id'];?>";
                var branch = "<?php echo $input['branch_id']?>";
                var dist = "<?php echo $input['district_id'];?>"

                $.ajax({
                    method:'POST',
                    url: base_url + 'leads/is_own_branch',
                    data:{
                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                        district_code:dist,
                        branch_code:branch,
                    }
                }).success(function (resp) {
                    if(resp){
                        var resp = JSON.parse(resp);
                        $("#branch_id").html(resp['html']);
                        $("#district_id").html(resp['html2']);

                        $('select[name="state_id"]').val(state);
                        $('select[name="state_id"]').prop('disabled',true);
                        $('select[name="branch_id"]').prop('disabled',true);
                        $('select[name="district_id"]').prop('disabled',true);
                    }
                });


            }
        });

        var base_url = "<?php echo base_url();?>";
        $('#product_category').change(function () {
            var category_id = $('#product_category').val();
            var csrf = $("input[name=csrf_dena_bank]").val();
            $.ajax({
                method: "POST",
                url: base_url + "leads/productlist",
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    category_id: category_id,
                    select_label:'Select'
                }
            }).success(function (resp) {
                if (resp) {
                    $("#product_select").html(resp);
                }
            });
        });


        $('#state_id').change(function () {
           var state_code = $('#state_id').val();
           $.ajax({
              method:'POST',
              url: base_url + 'leads/district_list',
              data:{
                  '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                  state_code:state_code,
                  select_label:'Select District'
              }
           }).success(function (resp) {
              if(resp){
                  $("#district_id").html(resp);
              }
           });
        });

        $('#district_id').change(function () {
           var district_code = $('#district_id').val();
           $.ajax({
              method:'POST',
              url: base_url + 'leads/branch_list',
              data:{
                  '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                  district_code:district_code,
                  select_label:'Select Branch'
              }
           }).success(function (resp) {
              if(resp){
                  console.log(resp);
                  $("#branch_id").html(resp);
              }
           });
        });

        $.validator.addMethod("regx", function (value, element, regexpr) {
            return regexpr.test(value);
        });

        $("#addlead").validate({

            rules: {
                /*is_existing_customer: {
                    required: true
                },*/
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
                /*lead_identification: {
                    required: true
                },*/
                lead_ticket_range: {
                    required: true,
                    number:true,
                    min:5000,
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
                remark: {
                    required: true
                }
            },
            messages: {
                /*is_existing_customer: {
                    required: "Please select customer"
                },*/
                customer_name: {
                    required: "Please enter customer name",
                    regx: "Special characters are not allowed"
                },
                lead_name: {
                    required: "Please enter lead name",
                    regx: "Special characters are not allowed"
                },
                lead_ticket_range: {
                    required: "Please select ticket range",
                    number: "Only numbers allowed",
                    min:"Please Enter a value greater than or equal to 5000"
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
                branch_id: {
                    required: "Please select branch"
                },
                /*lead_identification: {
                    required: "Please select lead identification"
                },*/
                remark: {
                    required: "Please enter remark"
                }
            }
        });
    })
</script>