

$('.is_senior').css("display","none");
$('#senior').click(function () {
    $('.is_senior').css("display","inline");

});
$('#junior').click(function () {
    $('.is_senior').css("display","none");

});

$(".reset").click(function () {
    $('#maturity').val('');
});

$.validator.addMethod('minStrict', function (value, el, param) {
    return value > param;
});
$.validator.addMethod("noDecimal", function(value, element) {
    return !(value % 1);
}, "No decimal numbers");

function fd_calculator(rS) {
    $("#fd_calculate").validate({
        rules:{
            interest:{
                required:true,
                number:true,
                minStrict:0
            },
            tenure:{
                required:true,
                number:true,
                minStrict:0,
                noDecimal:0
            },
            frequency:{
                required:true
            },
            tenurePeriod:{
                required:true
            },
            principal:{
                required:true,
                minStrict:0,
                number:true,
                noDecimal:0
            }
        },
        messages:{
            interest: {
                required: "Please enter interest",
                number: "Only numbers allowed",
                minStrict:"Please enter interest more than 0"
            },
            tenure: {
                required: "Please enter number",
                number: "Only numbers allowed",
                minStrict:"Please enter month more than 0",
                noDecimal:"Decimal numbers not allowed."
            },
            tenurePeriod: {
                required: "Please select period"
            },
            frequency: {
                required: "Please select Frequency"
            },
            principal: {
                required: "Please enter amount",
                number: "Only numbers allowed",
                minStrict:"Please enter amount more than 0",
                noDecimal:"Decimal numbers not allowed."
            }
        },
        submitHandler: function(form) {
            getfdMatVal();

            function calMatVal(principalVal, interestVal, tenureVal, tenurePeriodVal, frequencyVal)
            {
                var fdMatVal=0;
                var retStr="";
                if(tenureVal <= 90 && tenurePeriodVal == 365)
                {
                    frequencyVal = 0;
                }

                if(frequencyVal == 0)	//	Simple interest
                {
                    fdMatVal = principalVal * (1 + ((interestVal * tenureVal) / (tenurePeriodVal*100)));
                    //retStr = retStr + "Simple Interest = " +fdMatVal;
                }
                else	//	Compound interest
                {
                    var val1 = 1 + interestVal/(100 * frequencyVal);
                    var val2 = (tenureVal * frequencyVal / tenurePeriodVal);
                    var val3 = 0;

                    val3 = Math.pow(val1, val2);
                    fdMatVal = (principalVal * val3);
                    //retStr = retStr + "Compund Interest = " +fdMatVal;
                }

                retStr = retStr + fdMatVal.toFixed(2);
                return(retStr);
            }
            function getfdMatVal()
            {
                var i_Chars = ".";

                var principalVal = $('#principal').val();

                var interestVal = $('#interest').val();
                if($("#senior").is(':checked')) {
                    var interestVal = parseInt(interestVal) + parseFloat(rS);
                }
                var tenureVal = $('#tenure').val();

                var tenurePeriodVal = $('#tenurePeriod').val();

                if($("#sdr").is(':checked')) {
                    var frequencyVal = $('#sdr').val();
                }
                if($("#fdr").is(':checked')) {
                    var frequencyVal = $('#fdr').val();
                }
                //Get computable values
                principalVal = parseFloat(principalVal);
                interestVal = parseFloat(interestVal);
                tenureVal = parseFloat(tenureVal);
                tenurePeriodVal = parseFloat(tenurePeriodVal);
                frequencyVal = parseFloat(frequencyVal);

                var retStr = calMatVal(principalVal, interestVal, tenureVal, tenurePeriodVal, frequencyVal);
                if(retStr != 'NaN'){
                    $('#maturity').val(retStr);
                }
//        document.getElementById("resp_matval").innerHTML = "<strong>"+retStr+"</strong></span>";
//        document.getElementById("resp_intval").innerHTML = "<em> Interest earned Rs."+(retStr - principalVal).toFixed(2)+"</em>";

            }
        }
    });
}

function rd_calculator(rS) {
    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);

    var today = (day)+"-"+(month)+"-"+now.getFullYear() ;

    $('#date_opening').val(today);
    /*$('body').on('focus',".datepicker_recurring_start", function(){
        $(this).datepicker({dateFormat: 'dd-mm-yy'});

    });*/
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
            interest:{
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
            interest: {
                required: "Please enter interest",
                number: "Only numbers allowed",
                minStrict:"Please enter amount more than 0"
            },
            term: {
                required: "Please enter term",
                number: "Only numbers allowed",
                minStrict:"Please enter term more than 0"
            }
        },
        submitHandler: function(form) {

            var p = $('#amount').val();
            var term = $('#term').val();
            var r = $('#interest').val();
            if($("#senior").is(':checked')) {
                $('#is_senior').show();
                 r = parseFloat(r) + parseFloat(rS);
            }
                var MonthlyAmount= p;
                var AnnualInterestRate = r;
                var Quarters=Math.floor(term/3);
                var MonthPayment=MonthlyAmount*((Math.pow(AnnualInterestRate/400+1,Quarters)-1)*(1200/AnnualInterestRate+2));
            var FracMonths=term-Quarters*3;
            if (FracMonths!=0)
                {
                    alert("Months should be in multiple of one or more complete quarter(s)!!")
                    MonthPayment=0
                }
            $('#maturity').val(Math.round(MonthPayment));
        }
    });
}

function emi_calculator(min, max) {
        var min = parseInt(min, 10);
        var max = parseInt(max, 10);
        var slider1 = $("#slider1");
        var amount = $("#amount");
        var div = $('#slider1div');
        slider1.slider({
            orientation: "horizontal",
            max: max,
            min: min,
            step: 100000,
            animate: true,
            values: [0],
            slide: function (event, ui) {
                amount.val(ui.values[0]);
                var width = (ui.values[0] / parseInt(max)) * 100 + '%';
                div.width(width);
            }
        });
        var value = slider1.slider('values', 0);
        amount.val(value);

        amount.keyup(function () {
            if ($.isNumeric(amount.val())) {
                slider1.slider('values', 0, amount.val());
                var width = '100%';
                if (amount.val() <= parseInt(max))
                    width = (amount.val() / parseInt(max)) * 100 + '%';
                div.width(width);
            }
        });

        var slider2 = $("#slider2");
        var year = $("#years");
        var div2 = $('#slider2div');

        slider2.slider({
            step: .5,
            orientation: "horizontal",
            max: 30,
            min: 0,
            animate: true,
            values: [0],
            slide: function (event, ui) {
                year.val(ui.values[0]);
                var width = (ui.values[0] / 30) * 100 + '%';
                div2.width(width);
            }
        });

        var value2 = slider2.slider('values', 0);
        year.val(value2);

        year.keyup(function () {
            if ($.isNumeric(year.val())) {
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
            step: .25,
            orientation: "horizontal",
            max: 20,
            min: 5,
            animate: true,
            values: [5],
            slide: function (event, ui) {
                interest.val(ui.values[0]);
                var width = (ui.values[0] - 5) / 15 * 100 + '%';
                div3.width(width);
            }
        });

        var value3 = slider3.slider('values', 0);
        interest.val(value3);

        interest.keyup(function () {
            if ($.isNumeric(interest.val())) {
                slider3.slider('values', 0, interest.val());
                var width = '100%';
                if (interest.val() <= 20)
                    var width = (interest.val() - 5) / 15 * 100 + '%';
                div3.width(width);
            }
        });
        $("#emi").validate({
            rules: {
                interest: {
                    required: true,
                    number: true,
                    minStrict: 0,
                    max: 20
                },
                years: {
                    required: true,
                    number: true,
                    max: 30,
                    minStrict: 0,
                },
                amount: {
                    required: true,
                    number: true,
                    max: 20000000,
                    minStrict: 0,
                }
            },
            messages: {
                interest: {
                    required: "Please enter interest",
                    number: "Only numbers allowed",
                    max: "Please Enter a value less than or equal to 20",
                    minStrict: "Please enter interest more than 0"
                },
                years: {
                    required: "Please enter year",
                    number: "Only numbers allowed",
                    max: "Please Enter a value less than or equal to 30",
                    minStrict: "Please enter year more than 0"
                },
                amount: {
                    required: "Please enter amount",
                    number: "Only numbers allowed",
                    max: "Please Enter a value less than or equal to 200L",
                    minStrict: "Please enter amount more than 0"
                }
            },
            submitHandler: function (form) {
                if ($('#emi').valid()) {
                    var P = $("#amount").val();
                    var IN = $("#interest").val();
                    var R = IN / (12 * 100);
                    var N = $("#years").val()*12;
                    var X = Math.pow((1 + R), N);
                    var Y = Math.pow((1 + R), N) - 1;
                    //Math.pow((1+r),n)-1
                    var EMI = (P * R * X) / Y;
                    $('#your_emi').html(Math.ceil(EMI));
                }
            }
        });
        $(function () {

            // setup master volume
            $(".ui-slider").slider({
                value: 70,
                orientation: "horizontal",
                range: "min",
                animate: true
            });

            // setup graphic EQ
            $("#eq > span").each(function () {
                // read initial values from markup and remove that
                var value = parseInt($(this).text(), 20);
                $(this).empty().slider({
                    value: value,
                    range: "min",
                    animate: true,
                    orientation: "vertical"
                });
            });
        });
    }
