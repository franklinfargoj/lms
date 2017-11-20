<?php
//    $lead_status = $this->config->item('lead_status');
    $all_lead_status = $this->config->item('lead_status');
    $lead_type = $this->config->item('lead_type');
    $drop_reason = $this->config->item('drop_reason');
    $color = 'gray';
    if(isset($leads[0]['lead_identification']) && !empty($leads[0]['lead_identification'])){
        switch ($leads[0]['lead_identification']) {
            case 'HOT':
                $color = 'red';
                break;
            case 'WARM':
                $color = 'green';
                break;
            case 'COLD':
                $color = 'blue';
                break;
        }
    }
    $input = get_session();
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

    $data_district[''] = 'Select City';
    if ($districts != '') {
        foreach ($districts as $key => $value) {
            $data_district[$value['code']] = $value['name'];
        }
    }
    $state_extra = 'id="state_id"';
    $district_extra = 'id="district_id"';
    $branch_extra = 'id="branch_id"';
?>
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Lead Detail</h3>
        
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
                        <div class="lead-form-left  top-m">

                            <div class="form-control">
                                <label>Lead ID:</label> <span class="detail-label"><?php echo ucwords($leads[0]['id']);?></span>
                            </div>
                            <div class="form-control">
                                <label>Product:</label> <span class="detail-label"><?php echo ucwords($leads[0]['product_title']);?></span>
                            </div>


                            <?php if (isset($leads[0]['lead_identification']) && !empty($leads[0]['lead_identification'])){ ?>
                            <div class="form-control">
                                <label>Lead Identified as :</label>
                                <span class="detail-label" style="color:<?php echo $color;?>">
                                    <?php echo !empty($leads[0]['lead_identification']) ? ucwords($lead_type[$leads[0]['lead_identification']]) : '';?>
                                </span>
                            </div>
                            <?php }?>
                            <div class="form-control">
                                <label>Lead Status:</label> <span class="detail-label">
                                    <?php $account_no = $leads[0]['opened_account_no'] ? " (".$leads[0]['opened_account_no'].")" :'';
                                    if($leads[0]['status']=='FU')
                                    {
                                        $account_no = " (Next Followup Date :".date('d-m-Y',strtotime($leads[0]['remind_on'])).")";
                                    }
                                    if($leads[0]['status']=='NI')
                                    {
                                        $account_no = " (Reason :".$leads[0]['reason_for_drop'].")";
                                    }
                                    echo isset($leads[0]['status']) ? $all_lead_status[$leads[0]['status']].$account_no : 'NA';?></span>
                            </div>
                           
                            <div class="form-control">
                                <label>Assigned To:</label> <span class="detail-label"><?php echo ucwords(strtolower($leads[0]['employee_name']));?></span>
                            </div>
                            <?php if(($type == 'assigned') && (in_array($this->session->userdata('admin_type'),array('EM','BM'))) && ($leads[0]['status'] != 'Converted')){?>
                                <!-- <div class="form-control">
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
                                </div> -->
                                <div class="form-control">
                                    <?php

                                    if(($this->session->userdata('admin_type')=='EM' && in_array($leads[0]['status'],array('AO','NI','Closed','Converted')))
                                    || ($this->session->userdata('admin_type')=='BM' && (!in_array($leads[0]['status'],array('NI','AO'))
                                    && (($leads[0]['category_title'] != 'Fee Income' && $leads[0]['status'] != 'DC') ||
                                        ($leads[0]['category_title'] != 'Fee Income' && $leads[0]['status'] == 'DC'))
                                    ))){}
                                        else {
                                            ?>
                                            <label>Change Status:</label>
                                    <?php
                                        }
                                        $data = array(
                                            'lead_id' => encode_id($leads[0]['id']),
                                            'lead_type'    => 'assigned',
                                            'remind_to'  => $leads[0]['employee_id']
                                        );
                                        echo form_hidden($data);
                                        $options1['']='Select';
                                        if(!empty($lead_status)){
                                            foreach ($lead_status as $key => $value) {
                                                if($leads[0]['category_title'] != 'Fee Income') {
                                                    if ($key != $leads[0]['status']) {
                                                        if (((in_array($this->session->userdata('admin_type'), array('EM'))) && (in_array($key, array('Converted', 'Closed')))) || (in_array($key, $previous_status))) {
                                                            continue;
                                                        }
                                                    }
                                                    $options1[$key] = $value;
                                                }else{
                                                    if($key !='AO'){
                                                        if ($key != $leads[0]['status']) {
                                                            if (((in_array($this->session->userdata('admin_type'), array('EM'))) && (in_array($key, array('Converted', 'Closed')))) || (in_array($key, $previous_status))) {
                                                                continue;
                                                            }
                                                        }
                                                        $options1[$key] = $value;
                                                    }

                                                }
                                            }
                                        }
                                        $js = array(
                                                'id'       => 'lead_status',
                                                'class'    => 'form-control'
                                        );
                                    $data_status = array(
                                        'name'        => 'lead_status',
                                        'type'       => 'hidden',
                                        'value'       => $leads[0]['status']
                                    );
                                    if(($this->session->userdata('admin_type')=='EM' && in_array($leads[0]['status'],array('AO','NI','Closed','Converted')))
                                        || ($this->session->userdata('admin_type')=='BM' && (!in_array($leads[0]['status'],array('NI','AO'))
                                        && (($leads[0]['category_title'] != 'Fee Income' && $leads[0]['status'] != 'DC')
                                        || ($leads[0]['category_title'] != 'Fee Income' && $leads[0]['status'] == 'DC'))
                                            ))){
                                        echo form_input($data_status);
                                    }
                                    else{
                                            echo form_dropdown('lead_status', $options1 , $leads[0]['status'],$js);
                                    }
                                        $category_name = array('name'=>'cat_name','type'=>'hidden','value'=>$leads[0]['category_title']);
                                        echo form_input($category_name);
                                        $customer_name = array('name'=>'customer_name','type'=>'hidden','value'=>$leads[0]['customer_name']);
                                        echo form_input($customer_name);
                                    ?>
                                </div>
                                <div class="form-control reason" style="visibility: hidden">
                                    <label>Reason For Drop :<span style="color:red;">*</span></label>
                                    <?php
                                    echo "<span class='detail-label'>";
                                    $options21[''] = 'Select';
                                    foreach ($drop_reason as $key => $value) {
                                        $options2[$key] = ucwords($value);
                                    }
                                    $js = array(
                                        'id' => 'reason',
                                        'class' => 'form-control'
                                    );
                                    echo form_dropdown('reason', $options21, '', $js);
                                    echo "</span>";
                                    echo form_error('reason');
                                    ?>
                                </div>
                                <?php if($this->session->userdata('admin_type')=='EM'){?>
                                <div class="form-control followUp" style="display:none">
                                    <label>Next Followup Date:<span style="color:red;">*</span></label>
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
                                        <label>Followup Remark:<span style="color:red;">*</span></label>
                                        <textarea rows="4" cols="80" name="reminder_text"><?php if(!empty($leads[0]['reminder_text'])) echo $leads[0]['reminder_text'];?></textarea>
                                    </div>
                                    <?php }?>
                                    <?php if($this->session->userdata('admin_type')=='EM' && in_array($leads[0]['status'],array('AO'))){?>
                                        <div class="form-control accountOpen" >
                                    <?php }else{?>
                                        <div class="form-control accountOpen" style="display:none">
                                    <?php }?>


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
                                <?php if($this->session->userdata('admin_type')=='EM' && in_array($leads[0]['status'],array('AO'))){?>
                                            <div class="form-control form-submit clearfix accountOpen">
                                    <?php }else{?>
                                    <div class="form-control form-submit clearfix accountOpen" style="display:none">
                                        <?php }?>

                                    <a href="javascript:void(0);" class="float-right verify_account">
                                        <img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav">
                                        <span>Verify</span>
                                        <img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="right-nav">
                                    </a>
                                </div>
                                <div class="form-control lead_identified">
                                    <?php
                                    if($this->session->userdata('admin_type')=='EM'){
                                        if($leads[0]['lead_identification'] == '') {
                                            if (isset($lead_identification)) {
                                                $status_array = array('AO', 'Closed', 'Converted', 'NI','FU','DC');
                                                $admin = array('EM');
                                                if (isset($leads[0]['status']) && in_array($leads[0]['status'], $status_array)
                                                    && in_array($this->session->userdata('admin_type'), $admin)
                                                ) {}
                                                else {
                                                    echo "<label>Lead Identified as :</label>";
                                                    echo "<span class='detail-label'>";
                                                    $options2[''] = 'Select';
                                                    foreach ($lead_type as $key => $value) {
                                                        $options2[$key] = ucwords($value);
                                                    }
                                                    $js = array(
                                                        'id' => 'lead_identification',
                                                        'class' => 'form-control'
                                                    );
                                                    echo form_dropdown('lead_identification', $options2, $leads[0]['lead_identification'], $js);
                                                    echo "</span>";
                                                }
                                            }
                                        }else{
                                            $data_lead_identified_as = array('name'=>'lead_identification','id'=>'lead_identification','type'=>'hidden','value'=>$leads[0]['lead_identification']);
                                              echo form_input($data_lead_identified_as);
                                        }
                                    }
                                    ?>
                                </div>
                                <img class="loader" src="<?php echo base_url().ASSETS;?>images/35.gif" alt="35" style="display:none;">
                            <?php }?>
                        </div>


                        <div class="lead-form-right">
                        <?php if(isset($backUrl)){?>
                            <a href="<?php echo site_url($backUrl);?>" class="reset float-right form-style"> &#60; Back</a>
                        <?php }?>
                            <div class="form-control ">
                                <label>Customer Name:</label> <span class="detail-label"><?php echo ucwords($leads[0]['customer_name']);?></span>
                            </div>
                            <div class="form-control">
                                <label>Contact:</label> <span class="detail-label"><?php echo $leads[0]['contact_no'];?></span>
                            </div>

                            <!--                            <div class="form-control">-->
                            <!--                                <label>Category Name:</label> <span class="detail-label">--><?php //echo ucwords($leads[0]['category_title']);?><!--</span>-->
                            <!--                            </div>-->

                            <?php
                            $exclude_status_bm = array('Converted','Closed','AO','DC');
                            if(($type == 'assigned') && (in_array($this->session->userdata('admin_type'),array('BM')) &&
                                !in_array($leads[0]['status'],$exclude_status_bm))
                            ){?>

                            <div class="form-control">
                                <label>Do you want to reassign this lead ?</label>
                            </div>
                                <div class="form-control">
                                <div class="radio-control">
                                    <input type="radio" id="is_own_branch" name="is_own_branch"
                                           value="1" <?php echo set_radio('is_own_branch', '1'); ?> />
                                    <label>Other Employee</label>
                                </div>
                                <div class="radio-control">
                                    <input type="radio" name="is_own_branch" id="is_other_branch"
                                           value="0" <?php echo set_radio('is_own_branch', '0'); ?> />
                                    <label>Other Branch</label>
                                </div>
                                <?php echo form_error('is_own_branch'); ?>
                            </div>
                                <div class="form-control" id="reroute">
                                    <!--                                    <label>Reroute To:</label>-->
                                    <select name="reroute_to" id="reroute_to" style="visibility: hidden">
                                        <option value="">Select Employee</option>
                                        <?php $result = get_details($this->session->userdata('admin_id'));?>
                                        <?php foreach ($result['list'] as $key =>$value){?>
                                            <option value="<?php echo $value->DESCR10.'-'.$value->DESCR30;?>"><?php echo ucwords($value->DESCR30);?></option>
                                        <?php }?>
                                    </select>
                                </div>
                                <div id="state" class="form-control" style="visibility: hidden">
<!--                                    <label>State:</label>-->
                                    <?php echo form_dropdown('state_id', $data_state,$input['state_id'],''.$state_extra) ?>
                                    <?php echo form_error('state_id'); ?>
                                </div>
                                <div id="district" class="form-control" style="visibility: hidden">
<!--                                    <label>District:</label>-->
                                    <?php echo form_dropdown('district_id', $data_district,$input['district_id'],''.$district_extra) ?>
                                    <?php echo form_error('district_id'); ?>
                                </div>
                                <div id="branch" class="form-control" style="visibility: hidden">
<!--                                    <label>Branch:</label>-->
                                    <?php echo form_dropdown('branch_id', $data_branch,$input['branch_id'],''.$branch_extra) ?>
                                    <?php echo form_error('branch_id'); ?>
                                </div>

                            <?php }?>
                        </div>
                        <div class="form-control form-submit clearfix">
                            <?php
                            $exclude_status_bm = array('Converted','Closed');
                            $exclude_status_em = array('Converted','Closed','NI');

                            if(($type == 'assigned') &&
                                    ($this->session->userdata('admin_type') == 'EM' && !in_array($leads[0]['status'],$exclude_status_em)) ||
                                    ($this->session->userdata('admin_type') == 'BM' && !in_array($leads[0]['status'],$exclude_status_bm))){
                                ?>
                                <a href="javascript:void(0);" class="float-right submit_button">
                                    <img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav">
                                    <span><input type="submit" class="custom_button" value="Submit" /></span>
                                    <img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="right-nav">
                                </a>
                            <?php }?>
                        </div>
                    <!-- </form> -->
                    <?php
                    $attr = array(
                        'type'  => 'text',
                        'name'  => 'response_data',
                        'id'    => 'response_data',
                        'value' => '',
                        'style'=>'display:none'
                    );
                    echo form_input($attr);
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
        <?php if($this->session->userdata('admin_type')=='EM' && in_array($leads[0]['status'],array('AO'))){?>
        $('.submit_button').hide();
        <?php }?>
//        $("#state").hide();
//        $("#branch").hide();
//        $("#district").hide();
//        $("#reroute").hide();

        var lead_status = "<?php echo $leads[0]['status']?>";  //Current Lead status
        var category_title = "<?php echo $leads[0]['category_title']?>";  //Current Category
        $('.reason').hide();
        if(lead_status == 'FU'){
            $('.followUp').show();              //Display follow up fields 
        }
        if(lead_status == 'NC'){
            $('#lead_identification').attr('disabled','disabled');              //Display follow up fields
        }

        $('#lead_status').change(function(){
            var option = $(this).val();         
            action(option);
        });

        $('body').on('focus',".datepicker_recurring_start", function(){
            $(this).datepicker({dateFormat: 'dd-mm-yy',minDate: 0});

        });
        
        $('#product_category_id').change(function () {
            var csrf = $("input[name=csrf_dena_bank]").val();
            var category_id = $(this).val();
            $.ajax({
                method: "POST",
                url: baseUrl + "leads/productlist",
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    category_id: category_id,
                    select_label:'Select'
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
            if(option == 'FU' || option == 'AO' || option == 'Converted'|| option == 'DC'){
                $('.lead_identified').show();
            }else{
                $('.lead_identified').hide();
            }

            $('.submit_button').show();
            if(option == 'FU'){
               $('.followUp').show();
               $('.accountOpen').hide();
//               $('.datepicker_recurring_start').focus();
                $('#lead_identification').removeAttr('disabled');
                $('.reason').hide();

            }else if(option == 'AO'){
                //alert(category_title);
                if(category_title != 'Fee Income'){
                    $('.accountOpen').show();
                    $('.verify_account').show();
                    $('.submit_button').hide();
                    $('.reason').hide();
                }
                $('.reason').hide();
                $('.followUp').hide();
            }else if(option == 'NI'){
                $('.accountOpen').hide();
                $('.followUp').hide();
                $('.reason').show();
            }
            else{
                $('.accountOpen').hide();
                $('.followUp').hide();
                $('.reason').hide();
                $('#lead_identification').attr('disabled','disabled');
            }
        }

        //Validation
        $.validator.addMethod("regx", function(value, element, regexpr) {
            return regexpr.test(value);
        });
        $.validator.addMethod(
        "CustomDate",
        function(value, element) {
            // put your own logic here, this is just a (crappy) example
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        },
        "Please enter a date in the format dd-mm-yyyy."
    );

        $("#detail_form").validate({
            rules: {
                product_category_id: {
                    required: true
                },
                product_id: {
                    required: true
                },
                remind_on: {
                    required: true,
                    CustomDate:true
                },
                lead_identification : {
                    required : function(el) {
                        if(($('#lead_status').val() != 'NC') && ($('#lead_identification').val() == '')){
                            return true
                        }else{
                            return false
                        }
                    }
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
                reason:{
                    required:true
                },
                reminder_text:{
                    required:true
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
                },
                lead_identification : {
                    required: "Please select lead identification"
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
                reason:{
                    required:"Please enter reason for drop"
                },
                reminder_text:{
                    required:"Please enter Followup remarks"
                }
            }
        });
    });

    var base_url = "<?php echo base_url();?>";
    if ($('#is_own_branch').is(':checked')) {
        $("#state").css('visibility', 'hidden');
        $("#branch").css('visibility', 'hidden');
        $("#district").css('visibility', 'hidden');
        $("#reroute").css('visibility', 'visible');
        $("#reroute_to").css('visibility', 'visible');
    }

    $('#is_other_branch').click(function () {
        var dist = '<select name="district_id" id = "district_id"><option value="">Select City</option></select>';
        var branch = '<select name="branch_id" id = "branch_id"><option value="">Select Branch</option></select>';
        $("#state").css('visibility', 'visible');
        $("#branch").css('visibility', 'visible');
        $("#district").css('visibility', 'visible');
        $("#reroute").css('visibility', 'hidden');
        $("#reroute_to").css('visibility', 'hidden');
        $('select[name="state_id"]').val('');
        $('select[name="branch_id"]').html(branch);
        $('select[name="district_id"]').html(dist);
    });
    $('#is_own_branch').click(function () {
        $("#state").css('visibility', 'hidden');
        $("#branch").css('visibility', 'hidden');
        $("#district").css('visibility', 'hidden');
        $("#reroute").css('visibility', 'visible');
        $("#reroute_to").css('visibility', 'visible');
    });
    $('#state_id').change(function () {
        var state_code = $('#state_id').val();
        $.ajax({
            method:'POST',
            url: base_url + 'leads/district_list',
            data:{
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                state_code:state_code,
                select_label:'Select City'
            }
        }).success(function (resp) {
            if(resp){
                var res = JSON.parse(resp);
                $("#district_id").html(res['district']);
                $("#branch_id").html(res['branch']);
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
                var res = JSON.parse(resp);
                $("#branch_id").html(res);
            }
        });
    });

    $('.verify_account').click(function () {
        var acc_no = $.trim($('#accountNo').val());
        if(acc_no.length === 0 || acc_no.length != 12){
            alert('Please Enter 12 digit Account number.');
        }else{
            $('.loader').show();
            $.ajax({
                type: "POST",
                url: base_url + "leads/verify_account",
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    acc_no: acc_no
                }
            }).success(function(resp){
                    var regex = /(<([^>]+)>)/ig;
                    var body = resp;
                    var result = body.replace(regex, "");
                    var response = JSON.parse(result);
                        if(response['status'] == 'True'){
                            $('.loader').hide();
                            alert('Success');
                            $('.submit_button').show();
                            $('#response_data').val(response['data']);
                        }else{
                            $('.loader').hide();
                            alert('Verification Failed');
                        }
            });
        }

    });
</script>
