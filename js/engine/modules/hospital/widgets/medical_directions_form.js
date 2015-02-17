$(document).ready(function(e) {
    $('#toHospitalizationBtn').on('click', function(e) {
        $('.directionsList').addClass('no-display');
        $('.directionAdd').removeClass('no-display');
    });

    $('#directionAddClose').on('click', function(e, withRefresh) {
        $('.directionsList').removeClass('no-display');
        $('.directionAdd').addClass('no-display');
        if(withRefresh) {
            $('.directionsList').trigger('refresh');
        }
    });

    $('.directionsList').on('refresh', function(e) {
        var overlay = $('<div>').addClass('overlay');
        $(this).css({
            'position' : 'static'
        }).prepend(overlay);

        $.ajax({
            'url' : '/hospital/mdirections/get',
            'data' : {
                'omsId' : $('#directionOmsId').val(),
                'cardNumber' : $('#cardNumber').val()
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : $.proxy(function(data, textStatus, jqXHR) {
                if(data.success) {
                    overlay.remove();
                    $('.directionsList .cont a').remove();
                    for(var i = 0; i < data.directions.length; i++) {
                        $('.directionsList .cont').prepend(
                            $('<li>').append(
                                $('<a>').prop({
                                    'href' : 'dir' + data.directions[i].id
                                }).text('На госпитализацию в ' + data.directions[i].shortname + ' от ' + data.directions[i].create_date)
                            )
                        );
                    }
                }
            }, this)
        });
    });

    $('#directionAddSubmit').on('click', function(e) {
        var overlay = $('<div>').addClass('overlay');
        $(this).parents('.overlayCont').css({
            'position' : 'static'
        }).prepend(overlay);

        $.ajax({
            'url' : '/hospital/mdirections/add',
            'data' : $(this).parents('form').serialize(),
            'cache' : false,
            'dataType' : 'json',
            'type' : 'POST',
            'success' : $.proxy(function(data, textStatus, jqXHR) {
                if(data.success) {
                    overlay.remove();
                    $('#directionAddClose').trigger('click', [1]);
                }
            }, this)
        });
    });

    // First refresh after page loading
    $('.directionsList').trigger('refresh');
});