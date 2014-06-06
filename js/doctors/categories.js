$(document).ready(function() {
    $('button[id^=ba]').filter('[id*=history]').prop('disabled', true);
    $('button[id^=ba]').filter(':not([id*=history])').on('click', function(e) {
        var elementId = $(this).attr('id').substr($(this).attr('id').lastIndexOf('_') + 1);
        $('#controlId').val(elementId);
        globalVariables.elementId = elementId;
        globalVariables.domElement = $(this).attr('id');
        $('#addValuePopup').modal({
        });
    });

    $('input[type="number"]').on('focus', function(e) {
        $(this).css({
            backgroundColor: "rgb(255, 255, 255)"
        });
    });

    $('input[type="number"]').on('keydown', function(e) {
        if((!(e.keyCode >= 48 && e.keyCode < 58) && !(e.keyCode > 96 && e.keyCode < 106)) && e.keyCode != 8 && e.keyCode != 9 && e.keyCode != 46) {
            return false;
        }
    });

    $('input[type="number"]').on('blur', function(e) {
        $(this).css({
            backgroundColor: "rgb(255, 255, 255)"
        });
        var max = parseFloat($(this).attr('max'));
        var min = parseFloat($(this).attr('min'));
        var value = parseFloat('' + $(this).val() + String.fromCharCode(e.keyCode));
        if(typeof max != 'undefined') {
            if(value > max) {
                $(this).animate({
                    backgroundColor: "rgb(255, 196, 196)"
                });
                $(this).focus();
                return false;
            }
        }
        if(typeof min != 'undefined') {
            if(value < min) {
                $(this).animate({
                    backgroundColor: "rgb(255, 196, 196)"
                });
                $(this).focus();
                return false;
            }
        }
    });

    $("#add-value-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addValuePopup').modal('hide');
            $("#add-value-form")[0].reset(); // Сбрасываем форму
            $(globalVariables.domElement).find('option:first').before('<option value="' + ajaxData.id + '">' + ajaxData.display + '</option>');
        } else {
           showErrors(ajaxData);
        }
    });

    $("#add-greeting-value-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addGreetingComboValuePopup').modal('hide');
            $("#add-greeting-value-form")[0].reset(); // Сбрасываем форму
            $(globalVariables.domElement).find('option:first').before('<option value="' + ajaxData.id + '">' + ajaxData.display + '</option>');
            $(globalVariables.domElement).val(ajaxData.id);
        } else {
            showErrors(ajaxData);
        }
    });

    function showErrors(ajaxData) {
        // Удаляем предыдущие ошибки
        $('#errorPopup .modal-body .row p').remove();
        // Вставляем новые
        // Только одна ошибка...
        if(ajaxData.hasOwnProperty('error')) {
            $('#errorPopup .modal-body .row').append("<p>" + ajaxData.error + "</p>")
        } else {
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }
        }

        $('#errorPopup').modal({
        });
    }

    $('.accordion-inner select').each(function(index, element) {
        var currentValue = $(element).val();
        $(element).on('change', function(e) {
            if($(this).val() == '-3') {
                globalVariables.domElement = element;
                var elementId =  $(element).attr('id').substr($(element).attr('id').lastIndexOf('_') + 1);
                $('#addGreetingComboValuePopup #controlId').val(elementId);
                $('#addGreetingComboValuePopup').modal({});
                $(element).val(currentValue);
                return false;
            } else {
                currentValue = $(this).val();
            }
        });
    });


    $('.templatesListNav a').click(function (e) {
        e.preventDefault();
        var tabId = $(this).prop('id').substr(1);
        $('#template-edit-form').find('[id^=tab]').addClass('no-display');
        $('#template-edit-form').find('#tab' + tabId).removeClass('no-display').show(500);
        $(this).tab('show')
    });
});