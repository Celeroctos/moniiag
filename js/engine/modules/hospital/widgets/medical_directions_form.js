$(document).ready(function(e) {
    $('#toHospitalizationBtn').on('click', function(e) {
        $('.directionsList').addClass('no-display');
        $('.directionAdd').removeClass('no-display');
    });

    $('#directionAddClose').on('click', function(e) {
        $('.directionsList').removeClass('no-display');
        $('.directionAdd').addClass('no-display');
    });

    $('#directionAddSubmit').on('click', function(e) {
        var overlay = $('<div>').addClass('overlay');
        $(this).parents('.overlayCont').css({
            'position' : 'static'
        }).prepend(overlay);

        $.ajax({
            'url': '/hospital/mdirections/add',
            'data': $(this).parents('form').serialize(),
            'cache': false,
            'dataType': 'json',
            'type': 'POST',
            'success': $.proxy(function(data, textStatus, jqXHR) {
                if(data.success) {
                    overlay.remove();
                }
            }, this)
        });
    });
});