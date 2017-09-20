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
                    <p id="note"><span style="color:red;">*</span> These fields are required</p>
                    <div class="form-control">
                        <label>Senior Citizen: <span style="color:red;">*</span> </label>
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
                        <label>My Initial Amount (Rs):<span style="color:red;">*</span> </label>
                        <?php echo form_input($data_amount);?>
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
    rd_calculator();
</script>