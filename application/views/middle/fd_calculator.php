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
        <h3 class="text-center">FD calculator</h3>
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
                    <div class="form-control ravish-field2">
                        <label>Deposit Term:</label>
                            <?php echo form_input($data_month);?>
                            <?php echo form_input($data_days);?>
                    </div>
                    <div class="form-control">
                        <label>Rate of Interest:</label>
                        <?php echo form_input($data_interest);?>
                    </div>
                    <div class="form-control">
                        <label>Period:</label>
                            <?php
                                $data_period[''] = 'Select';
                                $data_period['1'] = 'year(s)';
                                $data_period['12'] = 'month(s)';
                                $data_period['365'] = 'day(s)';
                            ?>
                            <?php echo form_input($data_number);?>
                            <?php echo form_dropdown('tenurePeriod',$data_period,'','id=tenurePeriod')?>
                    </div>
                    <div class="form-control">
                        <label>Frequency:</label>
                        <?php
                        $data_fd[''] = 'Select Frequency';
                        $data_fd['0'] = 'Simple Interest';
                        $data_fd['12'] = 'Monthly';
                        $data_fd['4'] = 'Quaterly';
                        $data_fd['2'] = 'Halfyearly';
                        $data_fd['1'] = 'Annually';
                        ?>
                        <?php echo form_dropdown('frequency',$data_fd,'','id=frequency')?>
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
<script src = "<?php echo base_url().ASSETS;?>/js/calculator.js"></script>
<script>
    fd_calculator();
</script>