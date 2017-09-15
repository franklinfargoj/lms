<?php
/**
 * Created by PhpStorm.
 * User: webwerk
 * Date: 12/9/17
 * Time: 2:14 PM
 */
$form_attributes = array('class' => 'form', 'method' => 'post', 'accept-charset' => '', 'id' => 'rd_calculate');
$data_month = array('name'=>'month','id'=>'month','placeholder'=>'Months');
$data_days = array('name'=>'days','id'=>'days','placeholder'=>'Days');
$data_amount = array('name'=>'amount','id'=>'amount');
$data_term = array('name'=>'term','id'=>'term','value'=>'');
$data_maturity = array('name'=>'maturity','id'=>'maturity');
?>
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">RD calculator</h3>
    </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <div class="lead-form">
                <div class="lead-form-left ravish-form">
                    <?php
                    echo form_open('', $form_attributes);
                    ?>
                    <div class="form-control">
                        <label>Senior Citizen:</label>
                        <div class="radio-control">
                            <input type="radio" id="senior" name="citizen"
                                   value="1" <?php echo set_radio('citizen', '1', TRUE); ?>/>
                            <label>Yes</label>
                        </div>
                        <div class="radio-control">
                            <input type="radio" name="citizen" id="junior"
                                   value="0" <?php echo set_radio('citizen', '0'); ?>/>
                            <label>No</label>
                        </div>
                    </div>
                    <div class="form-control ravish-field">
                        <label>My Initial Amount (Rs):</label>
                        <?php echo form_input($data_amount);?>
                    </div>
                    <div class="form-control">
                        <label>Date Of Opening:</label>
                        <?php
                        $data = array(
                            'type'  => 'text',
                            'name'  => 'date_opening',
                            'id'    => 'date_opening',
                            'class' => 'datepicker_recurring_start',
                            'value' => ''
                        );
                        echo form_input($data);?>
                    </div>
                    <div class="form-control">
                        <label>For A Term (In Months)</label>
                        <?php echo form_input($data_term);?>
                    </div>
                    <div class="form-control">
                        <label>Due Date of RD:</label>
                        <?php
                        $data = array(
                            'type'  => 'text',
                            'name'  => 'due_date_rd',
                            'id'    => 'due_date_rd',
                            'value' => '',
                            'disabled'=>'disabled'
                        );
                        echo form_input($data);?>
                    </div>
                    <div class="form-control form-submit clearfix">

                        <a href="javascript:void(0);" class="active float-right">
                            <img alt ="left nav" src="<?php echo base_url().ASSETS;?>images/left-nav.png">
                            <span><input type="submit" class="custom_button" name="Submit" value="Calculate"></span>
                            <img alt = "right nav" src="<?php echo base_url().ASSETS;?>images/right-nav.png">
                        </a>
                        <a href="javascript:void(0);" class="reset float-right">
                            Reset
                        </a>
                    </div>
                    <?php echo form_close(); ?>
                    <div class="form-control">
                        <label>Maturity Value is</label>
                        <?php echo form_input($data_maturity);?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="bg-bottom"></span>
</div>
<script>
    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);

    var today = (day)+"-"+(month)+"-"+now.getFullYear() ;

    $('#date_opening').val(today);
    $('body').on('focus',".datepicker_recurring_start", function(){
        $(this).datepicker({dateFormat: 'dd-mm-yy'});

    });
    $('#term').on('change',function () {

        var date = new Date();
        var curr_date = date.getDate();
        var curr_month = parseInt(date.getMonth());
        var curr_year = date.getFullYear();
        var date = new Date(curr_year,curr_month,curr_date);
        // Get the current date
        var currentDate = date.getDate();;
        // Set to day 1 to avoid forward
        date.setDate(1);
        // Increase month by 1
        var term = $('#term').val();
        if($.isNumeric(term)){
            var later_month = parseInt(date.getMonth())+parseInt(term);
            date.setMonth(later_month);
            // Get max # of days in this new month
            var daysInMonth = new Date(date.getYear(), date.getMonth()+1, 0).getDate();
            // Set the date to the minimum of current date of days in month
            date.setDate(Math.min(currentDate, daysInMonth));

            var future_date = date.getDate();
            var future_month = parseInt(date.getMonth())+1;
            var future_year = date.getFullYear();
            var due_date = future_date + "-" + future_month
                + "-" + future_year;
            $('#due_date_rd').val(due_date);
        }

    });

    $("#rd_calculate").on('submit',function (e) {
        e.preventDefault();
        var p = $('#amount').val();
        var t = $('#term').val();
        var r = 0.05;
        if($("#senior").is(':checked')) {
            var r = 0.55;
        }
        var n = 12;
        var cal = (1 + (0.05 /12));
        var power = (12 * t);
        var maturity = p*Math.pow(cal,power);
        $('#maturity').val(maturity.toFixed(2));
    });

    $.validator.addMethod('minStrict', function (value, el, param) {
        return value > param;
    });
    $("#rd_calculate").validate({
        rules:{
            month:{
                required:true,
                minStrict:0,
                number:true
            },
            amount:{
                required:true,
                minStrict:0,
                number:true
            },
            term:{
                required:true,
                minStrict:0,
                number:true
            }
        },
        messages:{
            month: {
                required: "Please enter month",
                number: "Only numbers allowed",
                minStrict:"Please enter month more than 0"
            },
            amount: {
                required: "Please enter amount",
                number: "Only numbers allowed",
                minStrict:"Please enter amount more than 0"
            },
            term: {
                required: "Please enter term",
                number: "Only numbers allowed",
                minStrict:"Please enter term more than 0"
            }
        }
    });
    var month = $('#month').val();
    var amount = $('#amount').val();



</script>