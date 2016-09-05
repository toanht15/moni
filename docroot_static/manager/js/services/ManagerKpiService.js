jQuery(function(){

    $.datepicker.setDefaults({ dateFormat: 'yy-mm-dd' });
    $(".jsDate").datepicker( {
        maxDate: -1,
        minDate: new Date(2014, 9, 4)
    })
});

