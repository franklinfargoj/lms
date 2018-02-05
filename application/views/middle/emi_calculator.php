<?php
/**
 * Created by PhpStorm.
 * User: webwerk
 * Date: 31/8/17
 * Time: 2:04 PM
 */
$form_attributes = array('class' => 'form', 'method' => 'post', 'accept-charset' => '', 'id' => 'emi');
?>
<div class="page-title">
			<div class="container clearfix">
				<h3 class="text-center">EMI Calculator</h3>
			</div>
		</div>
		<div class="page-content">
            <span class="bg-top"></span>
            <div class="inner-content">
			<div class="container">
				<div class="emi-content">
                    <?php
                    echo form_open('', $form_attributes);
                    ?>
                    <p id="note"><span style="color:red;">*</span> These fields are required</p>
							<div class="form-control range-slider">
									<label>Loan Amount:<span style="color:red;">*</span></label>
                                    <input type="text" id="amount" name="amount">
                                   <!--  <label class="value">&#x20B9;</label> -->
                                    <img class="value" src="<?php echo base_url().ASSETS;?>images/rupees.png" alt="rupees">
							    <div id="slider1" class="ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content">
                                    <div id="slider1div" class="ui-slider-range ui-corner-all ui-widget-header ui-slider-range-min"></div><span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" style="left: 66%;"></span>
								</div>
								<div class="step">
									<span class="tick" style="left: 0%;">|<br><span class="marker">0</span></span>
									<span class="tick" style="left: 12.5%;">|<br><span class="marker">25L</span></span>
									<span class="tick" style="left:25%;">|<br><span class="marker">50L</span></span>
									<span class="tick" style="left:37.5%;">|<br><span class="marker">75L</span></span>
									<span class="tick" style="left:50%;">|<br><span class="marker">100L</span></span>
									<span class="tick" style="left:62.5%;">|<br><span class="marker">125L</span></span>
									<span class="tick" style="left:75%">|<br><span class="marker">150L</span></span>
									<span class="tick" style="left:87.5%;">|<br><span class="marker">175L</span></span>
									<span class="tick" style="left:100%;">|<br><span class="marker">>200L</span></span>
								</div>
								
							</div>
							<div class="form-control range-slider">
								<label>Loan Tenure:<span style="color:red;">*</span></label>
                                <input id ="years" type="text" name="years">
                                <label class="value">Years</label>
									<div id="slider2" class="ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content">
                                        <div id="slider2div" class="ui-slider-range ui-corner-all ui-widget-header ui-slider-range-min"></div>
                                        <span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" style="left: 66%;"></span>
									</div>
								<div class="step">
									<span class="tick" style="left: 0%;">|<br><span class="marker">0</span></span>
									<span class="tick" style="left: 16.67%;">|<br><span class="marker">5</span></span>
									<span class="tick" style="left: 33.33%;">|<br><span class="marker">10</span></span>
									<span class="tick" style="left: 50%;">|<br><span class="marker">15</span></span>
									<span class="tick" style="left: 66.67%;">|<br><span class="marker">20</span></span>
									<span class="tick" style="left: 83.33%;">|<br><span class="marker">25</span></span>
									<span class="tick" style="left: 100%;">|<br><span class="marker">30</span></span>
								</div>

							</div>
							<div class="form-control range-slider">
									<label>Interest Rate:<span style="color:red;">*</span></label>
                                    <input id="interest" type="text" name="interest">
                                    <label class="value">%</label>
									<div id="slider3" class="ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content">
                                        <div id="slider3div" class="ui-slider-range ui-corner-all ui-widget-header ui-slider-range-min"></div>
                                        <span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" style="left: 66%;"></span>
                                    </div>
                                <div class="step">
                                    <span class="tick" style="left: 0%;">|<br><span class="marker">5</span></span>
                                    <span class="tick" style="left: 16.67%;">|<br><span class="marker">7.5</span></span>
                                    <span class="tick" style="left: 33.34%;">|<br><span class="marker">10</span></span>
                                    <span class="tick" style="left: 50%;">|<br><span class="marker">12.5</span></span>
                                    <span class="tick" style="left: 66.67%;">|<br><span class="marker">15</span></span>
                                    <span class="tick" style="left: 83.34%;">|<br><span class="marker">17.5</span></span>
                                    <span class="tick" style="left: 100%;">|<br><span class="marker">20</span></span>
                                </div>
							</div>

						<div class="form-control form-submit clearfix">
							
<button type="submit" name="Submit" value="Calculate" class="full-btn float-right">
<img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav" class="left-btn-img">
<span class="btn-txt">Calculate</span>
<img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="left-nav" class="right-btn-img">
</button>
<br/>
							<div class="emi-section">
                                <label>Your EMI</label>
                                <span style="color: black" id="your_emi" class="input-box"></span>
                                <div class="emi-month-text">per month</div>
                            </div>
						</div>
                    <?php echo form_close(); ?>
				</div>
			</div>
            </div>
            <span class="bg-bottom"></span>
        </div>
<script src = "<?php echo base_url().ASSETS;?>/js/calculator.js"></script>
<script>
var min = "<?php echo minEmi?>";
var max = "<?php echo maxEmi?>";
$(document).ready(function() {
    emi_calculator(min, max);
});
</script>
