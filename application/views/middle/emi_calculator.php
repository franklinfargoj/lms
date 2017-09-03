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
							<div class="form-control range-slider">
									<label>Loan Amount</label>
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
									<span class="tick" style="left:100%;">|<br><span class="marker">200L</span></span>
								</div>
								
							</div>
							<div class="form-control range-slider">
								<label>Loan Tenure</label>
                                <input id ="years"type="text" name="years">
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
									<label>Interest Rate</label>
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
							<a href="#" class="float-right">
									<img src="<?php echo base_url().ASSETS;?>images/left-nav.png">
                                <span><input type="submit" class="custom_button" name="Submit" value="Calculate"></span>
									<img src="<?php echo base_url().ASSETS;?>images/right-nav.png">
							</a><br/>
							<div>Your EMI</div>
							<span style="color: black" id="your_emi" class="input-box"></span>
							<div>per month</div>
						</div>
                    <?php echo form_close(); ?>
				</div>
			</div>
            </div>
            <span class="bg-bottom"></span>
		</div>

<script>

    $(document).ready(function(){
        var slider1 = $("#slider1");
        var amount = $("#amount");
        var div = $('#slider1div');

        slider1.slider({
           orientation:"horizontal",
            max: 20000000,
            min: 0,
            step:100000,
            animate: true,
            values: [0],
            slide: function (event, ui) {
                amount.val(ui.values[0]);
                var width = (ui.values[0]/20000000) * 100 + '%';
                div.width(width);
            }
        });
        var value = slider1.slider('values', 0);
        amount.val(value);

        amount.keyup(function () {
            if($.isNumeric(amount.val())) {
                slider1.slider('values', 0, amount.val());
                var width = '100%';
                if (amount.val() <= 20000000)
                    width = (amount.val() / 20000000) * 100 + '%';
                div.width(width);
            }
        });

        var slider2 = $("#slider2");
        var year = $("#years");
        var div2 = $('#slider2div');

        slider2.slider({
            step:.5,
            orientation:"horizontal",
            max: 30,
            min: 0,
            animate: true,
            values: [0],
            slide: function (event, ui) {
                year.val(ui.values[0]);
                var width = (ui.values[0]/30) * 100 + '%';
                div2.width(width);
            }
        });

        var value2 = slider2.slider('values', 0);
        year.val(value2);

        year.keyup(function () {
            if($.isNumeric(year.val())) {
                slider2.slider('values', 0, year.val());
                var width = '100%';
                if (year.val() <= 30)
                    width = (year.val() / 30) * 100 + '%';
                div2.width(width);
            }
        });

        var slider3 = $("#slider3");
        var interest = $("#interest");
        var div3 = $('#slider3div');

        slider3.slider({
            step:.25,
            orientation:"horizontal",
            max: 20,
            min: 5,
            animate: true,
            values: [5],
            slide: function (event, ui) {
                interest.val(ui.values[0]);
                var width = (ui.values[0]-5)/15 * 100  + '%';
                div3.width(width);
            }
        });

        var value3 = slider3.slider('values', 0);
        interest.val(value3);

        interest.keyup(function () {
            if($.isNumeric(interest.val())) {
                slider3.slider('values', 0, interest.val());
                var width = '100%';
                if (interest.val() <= 20)
                    var width = (interest.val()-5)/15 * 100  + '%';
                div3.width(width);
            }
        });


        $("#emi").validate({
            rules:{
                interest:{
                    required:true,
                    number:true,
                    max:20,
                    min:0
                },
                years:{
                    required:true,
                    number:true,
                    max:30,
                    min:0
                },
                amount:{
                    required:true,
                    number:true,
                    max:20000000,
                    min:0
                }
            },
            messages:{
                interest: {
                    required: "Please enter interest",
                    number: "Only numbers allowed",
                    max:"Please Enter a value less than or equal to 20"
                },
                years: {
                    required: "Please enter year",
                    number: "Only numbers allowed",
                    max:"Please Enter a value less than or equal to 30"
                },
                amount: {
                    required: "Please enter amount",
                    number: "Only numbers allowed",
                    max:"Please Enter a value less than or equal to 200L"
                }
            }
        });



    });
    $('#emi').on('submit', function(e){
        e.preventDefault();
        if($('#emi').valid()){
            var P = $("#amount").val();
            var IN = $("#interest").val();
            var R = IN /(12 * 100);
            var N = $("#years").val();
            var X = Math.pow((1+R),N);
            var Y = Math.pow((1+R),N-1);
            var EMI = (P * R * X) / Y;

            $('#your_emi').html(EMI.toFixed(2));
        }
    });
    $( function() {

        // setup master volume
        $(".ui-slider").slider({
            value: 70,
            orientation: "horizontal",
            range: "min",
            animate: true
        });

        // setup graphic EQ
        $( "#eq > span" ).each(function() {
            // read initial values from markup and remove that
            var value = parseInt( $( this ).text(), 20 );
            $( this ).empty().slider({
                value: value,
                range: "min",
                animate: true,
                orientation: "vertical"
            });
        });
    } );
</script>