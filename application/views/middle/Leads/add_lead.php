<?php
$other_sources = $this->config->item('other_sources');
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
if(isset($states) && !empty($states)){
    foreach ($states as $state_key => $state){
        $data_state[$state['code']] = $state['name'];
    }
}

$data_branch[''] = 'Select Branch';
$data_district[''] = 'Select City';

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
                <p id="note"><span style="color:red;">*</span> These fields are required</p>
                <div class="lead-form-left">

                    <div class="form-control">
                        <label>Source:<span style="color:red;">*</span></label>
                        <select name="other_source" id="other_source">
                            <option value="">Select</option>
                            <?php foreach ($other_sources as $key => $val){?>
                             <option value="<?php echo $key;?>"><?php echo $val;?></option>
                            <?php }?>
                        </select>
                        <?php echo form_error('other_source'); ?>
                    </div>
                    <div class="form-control">
                        <label>Customer Name:<span style="color:red;">*</span> </label>
                        <?php echo form_input($data_customer);?>
                        <?php echo form_error('customer_name'); ?>
                    </div>
                    <div class="form-control">
                        <label>Contact Number:<span style="color:red;">*</span> </label>
                        <?php echo form_input($data_phone); ?>
                        <?php echo form_error('contact_no'); ?>
                    </div>
                    <div class="form-control">
                        <label>Product Category:<span style="color:red;">*</span> </label>
                        <?php echo form_dropdown('product_category_id', $options, set_value('product_category_id'), $category_extra) ?>
                        <?php echo form_error('product_category_id'); ?>
                    </div>
                    <div class="form-control" id="product_select">
                        <label>Product:<span style="color:red;">*</span> </label>
                        <?php echo form_dropdown('product_id', $product_options, set_value('product_id'), $product_extra) ?>
                        <?php echo form_error('product_id'); ?>
                    </div>
                    <div class="form-control range-slider">
                        <label style="vertical-align: top;">Ticket Size:<span style="color:red;">*</span> </label>
                        <?php echo form_input($data_ticket_range)?><img src="../assets2/images/rupees.png" alt="rupees" id="rs">
                        <div id="master">
                            <div class="ui-slider-range ui-corner-all ui-widget-header ui-slider-range-min"></div>
                        </div>

                        <div class="step" style="position: relative">
                            <span class="float-left" style="left: 0%;">0</span>
                            <span class="float-left" style="left: 25%; position: absolute">25000</span></span>
                            <span class="float-left" style="left: 50%; position: absolute">1L</span></span>
                            <span class="float-left" style="left: 75%; position: absolute">10L</span></span>
                            <span class="float-left" style="left: 100%; position: absolute">50L+</span>
                        </div>

                    </div>

                </div>


                <div class="lead-form-right">
                    <div class="form-control">

                        <?php
                        if(in_array($this->session->userdata('admin_type'),array('GM','ZM')) ||
                            ($this->session->userdata('dept_type_id') != 'BR' &&
                                in_array($this->session->userdata('admin_type'),array('EM','BM')))){
                            $checked = TRUE;
                            $style = "style='display:none'";
                        }else{
                            $checked = FALSE;
                            $style = "";
                            ?>
                            <label>Lead belongs to:<span style="color:red;">*</span> </label>
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
                        <?php echo form_error('state_id'); ?>
                    </div>
                    <div id="district" class="form-control">
                        <?php echo form_error('district_id'); ?>
                    </div>
                    <div id="branch" class="form-control">
                        <?php echo form_error('branch_id'); ?>
                    </div>

                    <div class="form-control">
                        <label>Initial Remarks: </label>
                        <?php echo form_textarea($data_remark, '', $remark_extra);?>
                        <?php echo form_error('remark'); ?>
                    </div>
                </div>
                <div class="form-control form-submit clearfix">

                    <button type="submit" name="Submit" value="Submit" class="full-btn float-right">
<img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav" class="left-btn-img">
<span class="btn-txt">Submit</span>
<img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="left-nav" class="right-btn-img">
</button>

                    <a href="javascript:void(0);" class="reset float-right" id="reset">
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
        var base_url = "<?php echo base_url();?>";
        var sliderElement = $("#master");
        var range = $('#ticket_range');
        var div = $('.ui-slider-range');
        var maxlead = "<?php echo add_lead_max;?>";
        var max = parseFloat(maxlead);

        var minlead = "<?php echo add_lead_min;?>";
        var min = parseFloat(minlead);
        // setup master volume
        sliderElement.slider({
            orientation: "horizontal",
            step:50000,
            max: max,
            min: min,
            animate: true,
            values: [min],
            slide: function (event, ui) {
                var step = sliderElement.slider('option', 'step');
                get_width_value(ui.values[0],1);
            }
        });
        var value = sliderElement.slider('values', 0);
        range.val(value);

        range.keyup(function () {
            if($.isNumeric(range.val())) {
                sliderElement.slider('values', 0, range.val());
                if(range.val() <= 25000 ){
                    var newVal = range.val() * 50;
                }
                if(range.val() > 25000 && range.val() <= 100000 ){
                    var existingVal = 1250000;
                    var newVal = (existingVal + range.val() * 12.5);
                }
               if(range.val() > 100000 && range.val() <= 1000000 ){
                   var existingVal = 2500000; 
                   var newVal = (existingVal + range.val() * 1.25);
               }
               if(range.val() > 1000000){
                   var existingVal = 3750000;
                   if(range.val()<=5000000){
                       var newVal = (existingVal + range.val() * .25);
                   }else{
                       var newVal = 5000000;
                   }
               }
                get_width_value(newVal,0);
            }else{
                sliderElement.slider('values', 0, 0);
                var newVal = 0;
                get_width_value(newVal,0);
            }
        });

        if ($('#is_other_branch').is(':checked')) {
            other_branch();
        }

        $('#is_other_branch').click(function () {
            var state = '';
            var district = '';
            other_branch(state,district);
        });
        if($('#is_own_branch').is(':checked')) {
            is_own_branch();
        };
        $('#is_own_branch').click(function () {
            is_own_branch();
        });

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

        $.validator.addMethod("regx", function (value, element, regexpr) {
            return regexpr.test(value);
        });
        $.validator.addMethod("lettersonly", function(value, element) {
            return this.optional(element) || /^[a-z\s]+$/i.test(value);
        }, "Only alphabetical characters");
        $.validator.addMethod("numbersonly", function(value, element) {
            return this.optional(element) || /^[0-9]+$/i.test(value);
        }, "Only Numbers Allowed");

        $.validator.addMethod("name_validation", function(value, element, regexpr) {
            return regexpr.test(value);
        }, "Enter a valid name.");

        $.validator.addMethod("contact_validation", function(value, element, regexpr) {
            return regexpr.test(value);
        }, "Enter a valid namekjkj.");

        $("#addlead").validate({

            rules: {
                customer_name: {
                    required: true,
                    lettersonly: true
                },
                lead_name: {
                    required: true,
                    regx: /^[a-zA-Z0-9\-\s]+$/
                },
                contact_no: {
                    required: true,
                    number: true,
                    maxlength: 10,
                    minlength: 10,
                    numbersonly: true,
                    regx: /^[1-9][0-9]*$/
                },
                product_category_id: {
                    required: true
                },
                product_id: {
                    required: true
                },
                other_source:{
                    required: true
                },
                lead_ticket_range: {
                    required: true,
                    number:true,
                    min:1,
                    regx: /^[1-9][0-9]*$/
                },
                state_id: {
                    required: true
                },
                district_id: {
                    required: true
                },
                branch_id: {
                    required: true
                }
            },
            messages: {
                customer_name: {
                    required: "Please enter customer name",
                    lettersonly: "Only alphabets are allowed."
                },
                lead_name: {
                    required: "Please enter lead name",
                    regx: "Special characters are not allowed"
                },
                lead_ticket_range: {
                    required: "Please select ticket range",
                    number: "Only numbers allowed",
                    min:"Ticket size should be greater than 0",
                    regx:"Ticket size should begin with number greater then zero"
                },
                contact_no: {
                    required: "Please enter phone number",
                    maxlength: 'Phone number is not 10 digits',
                    minlength: 'Phone number is not 10 digits',
                    regx: "Contact number should not begin with zero"
                },
                product_category_id: {
                    required: "Please select product category"
                },
                product_id: {
                    required: "Please select product"
                },
                other_source:{
                    required : "Please select lead source"
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
            }
        });

        function is_own_branch() {
            var state = "<?php echo $input['state_id'];?>";
            var branch = "<?php echo $input['branch_id'];?>";
            var district = "<?php echo $input['district_id'];?>";
            $.ajax({
                method: "POST",
                url: base_url + "leads/is_own_branch",
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    state_id: state,
                    district_id:district,
                    branch_id:branch,
                    select_label:'Select'
                
}            }).success(function (resp) {
                if (resp) {

                    var res = JSON.parse(resp);
                    $("#state").html(res['state']);
                    $("#district").html(res['district']);
                    $("#branch").html(res['branch']);
                    $('select[name="state_id"]').prop('disabled',true);
                    $('select[name="branch_id"]').prop('disabled',true);
                    $('select[name="district_id"]').prop('disabled',true);
                }
            });
        }
        $('body').on('change','#state_id',function () {
            var state = $('#state_id').val();
            $.ajax({
                method: "POST",
                url: base_url + "leads/district_list",
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    state_code:state,
                    select_label:'Select City'
                }
            }).success(function (resp) {
                if (resp) {
                    var res = JSON.parse(resp);
                    $("#district").html(res['district']);
                    $("#branch").html(res['branch']);
                }
            });
        });
        $('body').on('change','#district_id',function () {
            var district = $('#district_id').val();
            $.ajax({
                method: "POST",
                url: base_url + "leads/branch_list",
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    district_code:district,
                    select_label:'Select Branch'
                }
            }).success(function (resp) {
                if (resp) {
                    var res = JSON.parse(resp);
                    $("#branch").html(res);
                }
            });
        });
        function other_branch() {
            $.ajax({
                method: "POST",
                url: base_url + "leads/is_other_branch",
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                }
            }).success(function (resp) {
                if (resp) {
                    var res = JSON.parse(resp);
                    $("#state").html(res['state']);
                    $("#district").html(res['district']);
                    $("#branch").html(res['branch']);
                    $('select[name="state_id"]').prop('disabled',false);
                    $('select[name="branch_id"]').prop('disabled',false);
                    $('select[name="district_id"]').prop('disabled',false);
                }
            });
        }

        $('#reset').click(function () {
            var div = $('.ui-slider-range');
            var sliderElement = $("#master");
            sliderElement.slider('values', 0, 0);
            var width = '0%';
            div.width(width);
        });

        function get_width_value(value,is_from_input){
            if(value == 1166666.669){
                value = 1200000;
            }
            if(value<=1250000)
            {
                var newVal = (value / 50);
                var width = (newVal / 25000) * 25 + '%';
                if(is_from_input){
                    range.val(Math.round(newVal));
                    if(value == 1250000){
                        sliderElement.slider('option', 'step', 83333.3335);
                    }else{
                        sliderElement.slider('option', 'step', 50000);
                    }
                }else{
                    sliderElement.slider('values', 0, value);
                }
                div.width(width);
            }
            if(value>1250000 && value<=2500000)
            {
                if(value == 2361111.11584){
                    value =  2416666;
                }
                var newVal = (value / 25);
                var width = (newVal / 100000) * 50 + '%';
                if(is_from_input){
                    var numWidth = (newVal / 100000) * 50;
                    var newWidth = numWidth - 25;
                    var total = 75000;
                    var val = 25000 + (total * newWidth) / 25;
                    range.val(Math.round(val));
                    sliderElement.slider('option', 'step', 83333.3335);
                }
                else{
                    sliderElement.slider('values', 0, value);
                }
                div.width(width);

            }
            if(value>2500000 && value<=3750000)
            {
                if(value == 3593750){
                    value = 3611111;
                }
                var newVal = (value / 3.75);
                var width = (newVal / 1000000) * 75 + '%';
                if(is_from_input){
                    var numWidth = (newVal / 1000000) * 75;
                    var newWidth = numWidth - 50;
                    var total = 900000;
                    var val = 100000 + (total * newWidth) / 25;
                    range.val(Math.round(val));
                    if(value == 3750000){
                        sliderElement.slider('option','step',156250);
                    }else{
                        sliderElement.slider('option','step',138888.889167);
                    }
                }else{
                    sliderElement.slider('values', 0, value);
                }
                div.width(width);
            }
            if(value>3750000)
            {
                var width = (value / 5000000) * 100 + '%';
                if(is_from_input){
                    var numWidth = (value / 5000000) * 100;
                    var newWidth = numWidth - 75;
                    var total = 4000000;
                    var val = 1000000 + (total * newWidth) / 25;
                    range.val(Math.round(val));
                   sliderElement.slider('option','step',62500);
                }else{
                    sliderElement.slider('values', 0, value);
                }
                div.width(width);
            }

            if(range.val() > 0){
                $("#ticket_range-error").hide();
            }else{
                $("#ticket_range-error").show();
            }
        }
    });
</script>