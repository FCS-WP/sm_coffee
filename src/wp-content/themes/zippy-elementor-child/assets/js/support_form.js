$(function() {
    $("#form-field-timepicker").prop('readonly', true);
    $("#form-field-timepicker").addClass('disabled');
    flatpickr("#form-field-datepicker", {
        dateFormat: "Y-m-d",
        minDate: "today",
        disableMobile: true,
        enable: [
            function(date) {
                let day = date.getDay(); 
                return (day === 5 || day === 6 || day === 0);
            }
        ],
    });

    $("#form-field-datepicker").on('change', function(){
        let date = new Date($(this).val());
        let day = date.getDay();
        if (day === 0 || day === 6) {
            $("#form-field-timepicker").val('12:00');
        } 
        if (day === 5) {
            $("#form-field-timepicker").val('20:00');
        }
    })
});
