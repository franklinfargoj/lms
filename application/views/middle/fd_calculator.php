<?php
/**
 * Created by PhpStorm.
 * User: webwerk
 * Date: 12/9/17
 * Time: 2:14 PM
 */
$form_attributes = array('class' => 'form', 'method' => 'post', 'accept-charset' => '', 'id' => 'fd_calculate');
$data_month = array('name'=>'month','id'=>'month','placeholder'=>'Months');
$data_days = array('name'=>'days','id'=>'days','placeholder'=>'Days');
$data_amount = array('name'=>'amount','id'=>'amount');
$data_interest = array('name'=>'interest','id'=>'interest');
$data_maturity = array('name'=>'maturity','id'=>'maturity');
?>
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">FD calculator</h3>
    </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <div class="lead-form">
                <div class="lead-form-left">
                    <?php
                    echo form_open('', $form_attributes);
                    ?>
                    <div class="form-control">
                        <label>Senior Citizen:</label>
                        <div class="radio-control">
                            <input type="radio" id="is_own_branch" name="is_own_branch"
                                   value="1" <?php echo set_radio('is_own_branch', '1', TRUE); ?> />
                            <label>Yes</label>
                        </div>
                        <div class="radio-control">
                            <input type="radio" name="is_own_branch" id="is_other_branch"
                                   value="0" <?php echo set_radio('is_own_branch', '0'); ?> />
                            <label>No</label>
                        </div>
                    </div>
                    <div class="form-control">
                        <label>Deposit Term:</label>
                            <?php echo form_input($data_month);?>
                            <?php echo form_input($data_days);?>
                    </div>
                    <div class="form-control">
                        <label>Amount of F.D:</label>
                            <?php echo form_input($data_amount);?>
                    </div>
                    <div class="form-control">
                        <label>Rate of Interest:</label>
                            <?php echo form_input($data_interest);?>
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
</div>
<script>
    $.validator.addMethod('minStrict', function (value, el, param) {
        return value > param;
    });
    $("#fd_calculate").validate({
        rules:{
            interest:{
                required:true,
                number:true,
                minStrict:0,
                max:20
            },
            month:{
                required:true,
                minStrict:0,
                number:true
            },
            days:{
                required:true,
                minStrict:0,
                number:true
            },
            amount:{
                required:true,
                minStrict:0,
                number:true
            }
        },
        messages:{
            interest: {
                required: "Please enter interest",
                number: "Only numbers allowed",
                max:"Please Enter a value less than or equal to 20",
                minStrict:"Please enter interest more than 0"
            },
            month: {
                required: "Please enter month",
                number: "Only numbers allowed",
                minStrict:"Please enter month more than 0"
            },
            days: {
                required: "Please enter days",
                number: "Only numbers allowed",
                minStrict:"Please enter days more than 0"
            },
            amount: {
                required: "Please enter amount",
                number: "Only numbers allowed",
                minStrict:"Please enter amount more than 0"
            }
        }
    });
    var month = $('#month').val();
    var days = $('#days').val();
    var interest = $('#interest').val();
    var amount = $('#amount').val();



</script>