<?php
    $lead_status = $this->config->item('lead_status');
$all_lead_status = $this->config->item('lead_status');
$lead_type = $this->config->item('lead_type');
$source = $this->config->item('lead_source');

if(isset($leads[0]['lead_identification']) && !empty($leads[0]['lead_identification'])){
    switch ($leads[0]['lead_identification']) {
        case 'HOT':
            $color = 'green';
            break;
        case 'WARM':
            $color = 'yellow';
            break;
        case 'COLD':
            $color = 'blue';
            break;
    }
}
$input = get_session();
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
                <div class="lead-form-left top-m">

                    <div class="form-control">
                        <label>Lead ID:</label> <span class="detail-label"><?php echo ucwords($leads[0]['id']);?></span>
                    </div>

                    <div class="form-control">
                        <label>Ticket Size:</label><span class="detail-label"><?php echo convertCurrency($leads[0]['lead_ticket_range']).' Lacs';?></span>
                    </div>

                    <div class="form-control">
                        <?php
                        if(!empty($leads[0]['created_by_branch_id'])){
                            $generatedb = branchname($leads[0]['created_by_branch_id']);
                            $generatedBY =  ucwords($generatedb[0]['name']);
                        }else{
                            $generatedBY = ucwords($source[$leads[0]['lead_source']]);
                        }
                        ?>
                        <label>Generated By:</label> <span class="detail-label"><?php echo $generatedBY;?></span>
                    </div>

                    <div class="form-control">
                        <label>Product:</label> <span class="detail-label"><?php echo ucwords($leads[0]['title']);?></span>
                    </div>

                    <div class="form-control">
                        <label>Lead Status:</label> <span class="detail-label">
                            <?php $account_no = $leads[0]['opened_account_no'] ? " (".$leads[0]['opened_account_no'].")" :'';
                            if($leads[0]['status']=='FU')
                            {
                                $account_no = " (Next Followup Date :".date('d-m-Y',strtotime($leads[0]['followup_date'])).")";
                            }
                            if($leads[0]['status']=='NI')
                            {
                                $account_no = " (Reason :".$leads[0]['reason_for_drop'].")";
                                $account_no .= "<br>Description : ".$leads[0]['desc_for_drop'];
                            }
                            echo isset($leads[0]['status']) ? $all_lead_status[$leads[0]['status']].$account_no : 'NA';?></span>
                    </div>


                    <div class="form-control">
                        <label>Followup Remark:</label> <span class="detail-label"><?php echo ucwords(strtolower($leads[0]['reminder_text']));?></span>
                    </div>


                    <div class="form-control">
                        <label>Assigned To:</label> <span class="detail-label"><?php echo ucwords(strtolower($leads[0]['employee_name']));?></span>
                    </div>

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
                            <div class="form-control">
                                <label>Initial Remark:</label> <span class="detail-label"><?php echo ucwords(strtolower($leads[0]['remark']));?></span>
                            </div>
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



        <script src="<?php echo base_url().ASSETS;?>/js/jquery.base64.min.js" type="text/javascript"></script>

        <script type="text/javascript">
            $(document).ready(function(){
                <?php if($this->session->userdata('admin_type')=='EM' && in_array($leads[0]['status'],array('AO'))){?>
                $('.submit_button').hide();
                <?php }?>

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
                        <?php if($this->session->userdata('admin_type')=='EM' || ($this->session->userdata('admin_id') == $leads[0]['employee_id'])){?>
                        $('.reason').show();
                        <?php }?>
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
                            acc_no: $.base64.encode($.base64.encode(acc_no))
                        }
                    }).success(function(resp){
                        var regex = /(<([^>]+)>)/ig;
                        var body = resp;
                        var result = body.replace(regex, "");
                        var response = JSON.parse(result);

                        alert(response);
                        //var response = JSON.parse(resp);
                        if(response['status'] == 'True'){
                            $('.loader').hide();
                            alert('Success');
                            $('.submit_button').show();
                            $('#response_data').val(response['data']);
                        }else{

                            $('.loader').hide();
                            var useraction = confirm(response+"\nAre you sure you'\nwant to verify?");
                            if(useraction)
                            {
                                //on click of okay, call the required API function
                                window.location = "http://localhost/lms/leads/leads_list/assigned/ytd";
                            }
                            else
                            {
                                //on click of cancel,redirect on the same page
                                window.location = "http://localhost/lms/leads/leads_list/assigned/ytd";
                            }
                            // alert('Verification Failed');
                        }
                    });
                }

            });
        </script>