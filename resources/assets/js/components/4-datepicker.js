$(function(){
    //datetimepicker with start and end with time
    $('.startdtp').datetimepicker({
        format: 'M/D/YYYY h:mm A'
    }); 
     
    $('.repeatdtp').datetimepicker({
        format: 'M/D/YYYY'
    });
    $('.reminderdtp').datetimepicker({
        format: 'M/D/YYYY'
    });
    $('.enddtp').datetimepicker({
        useCurrent: false,
        format: 'M/D/YYYY h:mm A'
    });
    $(".startdtp").on("dp.change", function (e) {
        $('.enddtp').data("DateTimePicker").minDate(e.date);
        
    });
    $(".enddtp").on("dp.change", function (e) {
        $('.startdtp').data("DateTimePicker").maxDate(e.date);
    });
    $(".reminderdtp").on("dp.change", function (e) {
        var reminder = $('#reminder_date_get').val();
        $('#reminder_date').val(reminder);
    });

    //datetimepicker with start and end WITHOUT time
    $('.recdtp').datetimepicker({
        format: 'M/D/YYYY'
    });
    $('.startdp').datetimepicker({
        format: 'M/D/YYYY'
    });
    $('.enddp').datetimepicker({
        format: 'M/D/YYYY',
        useCurrent: false //Important! See issue #1075
    });
    $(".startdp").on("dp.change", function (e) {
        $('.enddp').data("DateTimePicker").minDate(e.date);
    });
    $(".enddp").on("dp.change", function (e) {
        $('.startdp').data("DateTimePicker").maxDate(e.date);
    });

    //datetimepicker WITHOUT time
    $('.datepicker').datetimepicker({
        format: 'M/D/YYYY'
    });
    //datetimepicker WITH time
    $('.datetimepicker').datetimepicker({
        format: 'M/D/YYYY h:MM A'
    });

    $('.bdatepicker').datetimepicker({
        format: 'M/D/YYYY',
        viewMode: 'years'
    });

});
