jQuery(document).ready(function() {
    $("#start_date, #end_date").datepicker({
        dateFormat: 'dd-mm-yy',
        maxDate: 0
    });

    $.validator.addMethod(
        "CustomDate",
        function(value, element) {
            // put your own logic here, this is just a (crappy) example
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        },
        "Please enter a date in the format dd-mm-yyyy."
    );

    $('#search_form').validate({
        rules: {
            start_date: {
                required: true,
                CustomDate: true
            },
            end_date: {
                required: true,
                CustomDate: true
            }
        },
        messages: {
            start_date: {
                required: "Start Date required"
            },
            end_date: {
                required: "End Date required"
            }
        },
        submitHandler: function(form) {
            var startDate = $('#start_date').datepicker("getDate"),
            endDate = $('#end_date').datepicker("getDate");
            if (startDate && endDate && startDate > endDate) {
                $('#start_date').after('<label id="end_date-error" class="error" for="end_date">Start date is greater than the end date.</label>');
                $('#start_date').datepicker("setDate", endDate);
                return false;
            }else{
                $('.custom_button').attr('disabled','disabled');
                $('.result').hide();
                $('.no_result').hide();
                $('.loader').show();
                setTimeout(function(){ 
                    form.submit();
                    if(($('#export').length > 0) || ($('#national').length > 0)){
                        $('.custom_button').removeAttr('disabled');
                        $('.result').show();
                        $('.loader').hide();
                        $('#export').remove();
                        $('#national').remove();
                    }
                }, 1000);
            }
        }
    });

    setTimeout(function(){        
        $('.loader').hide();
        $('.result').show();
    }, 2000);
    $('.export_to_excel').click(function(){
        var input = "<input type='hidden' id='export' name='export' value='yes'/>";
        $(this).append(input);
        $('#search_form').submit();
    });
    $('.export_national').click(function(){
        var input = "<input type='hidden' id='export' name='export' value='yes'/><input type='hidden' id='national' name='national' value='yes'/>";
        $(this).append(input);
        $('#search_form').submit();
    });
});
