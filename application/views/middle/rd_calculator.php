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
$data_interest = array('name'=>'interest','id'=>'interest');
?>
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">RD Calculator</h3>
    </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <div class="lead-form">
                <div class="lead-form-left r-form">
                    <?php
                    echo form_open('', $form_attributes);
                    ?>
                    <p id="note"><span style="color:red;">*</span> These fields are required</p>
                    <div class="form-control">
                        <label>Senior Citizen: <span style="color:red;">*</span> </label>
                        <div class="radio-control">
                            <input type="radio" id="senior" name="citizen"
                                   value="1" <?php echo set_radio('citizen', '1'); ?>/>
                            <label>Yes</label>
                        </div>
                        <div class="radio-control">
                            <input type="radio" name="citizen" id="junior"
                                   value="0" <?php echo set_radio('citizen', '0', TRUE); ?>/>
                            <label>No</label>
                        </div>
                    </div>
                    <div class="form-control r-field">
                        <label>My Initial Amount (Rs):<span style="color:red;">*</span> </label>
                        <?php echo form_input($data_amount);?>
                    </div>
                    <div class="form-control ravish-field">
                        <label>Rate Of interest (%):<span style="color:red;">*</span> </label>
                        <?php echo form_input($data_interest);?>
                        <span class="is_senior">+ 0.5%</span>
                    </div>
                    <div class="form-control">
                        <label>Date Of Opening:<span style="color:red;">*</span> </label>
                        <?php
                        $data = array(
                            'type'  => 'text',
                            'name'  => 'date_opening',
                            'id'    => 'date_opening',
                            'class' => 'datepicker_recurring_start',
                            'value' => '',
                            'disabled'=>'disabled'
                        );
                        echo form_input($data);?>
                    </div>
                    <div class="form-control">
                        <label>For A Term (In Months)<span style="color:red;">*</span> </label>
                        <?php echo form_input($data_term);?>
                    </div>
                    <div class="form-control">
                        <label>Due Date of RD:<span style="color:red;">*</span> </label>
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

                        <button type="submit" name="Submit" value="Calculate" class="full-btn float-right">
<img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav" class="left-btn-img">
<span class="btn-txt">Calculate</span>
<img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="left-nav" class="right-btn-img">
</button>                        <a href="javascript:void(0);" class="reset float-right">
                            Reset
                        </a>
                    </div>
                    <?php echo form_close(); ?>
                    <div class="form-control">
                        <label>Maturity Value is:</label>
                        <?php echo form_input($data_maturity);?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="bg-bottom"></span>
</div>
<script src = "<?php echo base_url().ASSETS;?>/js/calculator.js"></script>
<script>
    $(document).ready(function () {
        var rS = <?php echo rateOfInterestSenior;?>;
        rd_calculator(rS);
    });
</script>