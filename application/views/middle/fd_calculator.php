<?php
/**
 * Created by PhpStorm.
 * User: webwerk
 * Date: 12/9/17
 * Time: 2:14 PM
 */
$form_attributes = array('class' => 'form', 'method' => 'post', 'accept-charset' => '', 'id' => 'fd_calculate');
$data_number = array('name'=>'tenure','id'=>'tenure','placeholder'=>'Months');
$data_amount = array('name'=>'principal','id'=>'principal');
$data_interest = array('name'=>'interest','id'=>'interest');
$data_maturity = array('name'=>'maturity','id'=>'maturity');
?>
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">FD Calculator</h3>
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
                        <label>Senior Citizen:<span style="color:red;">*</span> </label>
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
                    <div class="form-control">
                        <label>Principal (Rs.):<span style="color:red;">*</span> </label>
                        <?php echo form_input($data_amount);?>
                    </div>
                    <div class="form-control">
                        <label class="l-width">Rate of Interest (%):<span style="color:red;">*</span> </label>
                        <?php echo form_input($data_interest);?>
                        <span class="is_senior">+ 0.5%</span>
                    </div>
                    <div class="form-control">
                        <label>Period:<span style="color:red;">*</span> </label>
                            <?php
                                $data_period[''] = 'Select';
                                $data_period['1'] = 'Year(s)';
                                $data_period['12'] = 'Month(s)';
                                $data_period['365'] = 'Day(s)';
                            ?>
                            <?php echo form_input($data_number);?>
                            <?php echo form_dropdown('tenurePeriod',$data_period,'','id=tenurePeriod')?>
                    </div>
                    <div class="form-control">
                        <label>Frequency:<span style="color:red;">*</span> </label>
                        <div class="radio-control">
                            <input type="radio" id="sdr" name="frequency" class="frequency"
                                   value="4" <?php echo set_radio('frequency', '0'); ?>/>
                            <label>SDR</label>
                            <label id="frequency-error" class="error" for="frequency"></label>
                        </div>
                        <div class="radio-control">
                            <input type="radio" name="frequency" id="fdr" class="frequency"
                                   value="0" <?php echo set_radio('frequency', '4'); ?>/>
                            <label>FDR</label>
                        </div>
                    </div>
                    <div class="form-control form-submit clearfix">

                       <button type="submit" name="Submit" value="Calculate" class="full-btn float-right">
<img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav" class="left-btn-img">
<span class="btn-txt">Calculate</span>
<img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="left-nav" class="right-btn-img">
</button>
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
    var rS = <?php echo rateOfInterestSenior;?>;
    fd_calculator(rS);
</script>