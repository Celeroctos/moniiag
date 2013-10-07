$(document).ready(function() {
    var global = {
        dateFields: [
            '#birthday-cont',
            '#document-givedate-cont',
            '#search-date-cont'
        ],
        colorPickerFields: [
            '.custom-color' // Маркировка анкет
        ]
    }

    this.initFields = function() {
        $(function () {
            // Поля дат
            for(var i = 0; i < global.dateFields.length; i++) {
                $(global.dateFields[i]).datetimepicker();
            }
            // Маркировка анкет
            for(var i = 0; i < global.colorPickerFields.length; i++) {
                $(global.colorPickerFields[i]).colorpicker({
                    format: 'hex'
                });
            }
        });
    };


    this.initFields();
});