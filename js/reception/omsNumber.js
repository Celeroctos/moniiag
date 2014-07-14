if (false)
$(document).ready(function() {

    // Функция, возвращающая позицию курсора в контроле
    /*function getCursorPosition( ctrl ) {
        var CaretPos = 0;
        if ( document.selection ) {
            ctrl.focus ();
            var Sel = document.selection.createRange();
            Sel.moveStart ('character', -ctrl.value.length);
            CaretPos = Sel.text.length;
        } else if ( ctrl.selectionStart || ctrl.selectionStart == '0' ) {
            CaretPos = ctrl.selectionStart;
        }
        return CaretPos;
    }*/

    // На keydown поля поставим обработчик, в котором сохраним старое значение этого поля и позицию
    $('#lastName').on('keydown',function(e){
        globalVariables.lastOmsState =
        {
            lastValue: $(this).val(),
            lastStart: $(this)[0].selectionStart,
            lastEnd: $(this)[0].selectionEnd
        };
        globalVariables.omsStateTreated = false;
        $("#lastName").one('controlvaluechanged',onControlValueChanged);
    });

    // По событиям изменения значения проверяем это значение и если оно не соответвует - откатываем назад контрол
    $("#lastName").on("propertychange change keyup paste input", function(e){
        if (globalVariables.omsStateTreated!=undefined)
        {
            if (globalVariables.omsStateTreated == false)
            {
                globalVariables.omsStateTreated = true;
                $(this).trigger ('controlvaluechanged', [e]);
            }
        }
    });

    function onControlValueChanged(e)
    {
        $(this).off('controlvaluechanged');
        // Вот тут надо проверить значение контрола
        //   Если оно не соответствует - надо вернуть обратно то значение, которое было до изменения

        newValue = $(this).val();
        needRevese = false;
        /*for (i=0;i<newValue.length;i++)
        {
            if (newValue[i]>'л' &&newValue[i]<'я')
            {
                needRevese = true;
                break;
            }
        }*/

        // Проверяем выражение по регулярке
        if (newValue.match(/^[А-л]*$/)==null) {
            needRevese = true;
        }

        if (needRevese)
        {
            // Возвращаем назад значение
            if (globalVariables.lastOmsState!=undefined)
            {
                $(this).val( globalVariables.lastOmsState.lastValue   );
               // $(this)[0].selectionStart = globalVariables.lastOmsState.lastStart;
              //  $(this)[0].selectionEnd = globalVariables.lastOmsState.lastEnd;
                console.log($(this));
            }
        }
    }
});