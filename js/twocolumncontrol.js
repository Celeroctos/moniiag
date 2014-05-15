$(document).ready(function() {
    var controls = $('.twoColumnList');
    $(controls).each(function() {
        (function(control) {
            // Функция читает выбранное в двухколоночном списке значение
            //   и записывает его в специальное спрятанное поле
            function refreshValue(control)
            {
                // Прочитаем значения из правого списка
                var rightList = $(control).find('.twoColumnListTo option');
                var optionIds = [];
                for (i=0;i<rightList.length;i++)
                {
                    optionIds.push($(rightList[i]).attr('value'));
                }
                // Запишем в специальное скрытое поле значение
                $(control).find('.twoColumnHidden').val($.toJSON(optionIds));
            }

            refreshValue(control);

            // Ставим обработчик на кнопку ->
            $(control).find('.twoColumnAddBtn').on('click', function(e) {
                // Принцип такой - берём из левой колонки выделенные опшены.
                //   Копируем в начало списка правой колонки выделенные опшионы
                //   Удаляем их из левой колонки выделенные опшионы
                var options = $(control).find('.twoColumnListFrom option:selected');
                var rightList = $(control).find('.twoColumnListTo');
                for (i=options.length-1;i>=0;i--)
                {
                    $(rightList).prepend($(options[i]));
                }
                $(rightList).find(':selected').prop('selected', false);
                // Вызываем обновление значения
                refreshValue(control);

            });

            // Ставим обработчик на кнопку <-
            $(control).find('.twoColumnRemoveBtn').on('click', function(e) {
                // Принцип такой - берём из правой колонки выделенные опшены.
                //   Копируем в начало списка левой колонки выделенные опшионы
                //   Удаляем их из правой колонки выделенные опшионы
                var options = $(control).find('.twoColumnListTo option:selected');
                var leftList = $(control).find('.twoColumnListFrom');
                for (i=options.length-1;i>=0;i--)
                {
                    $(leftList).prepend($(options[i]).prop('selected', false));
                }
                // Вызываем обновление значения
                refreshValue(control);
            });

            // Ставим обработчик двойного нажатия клавиши
            $(control).find('.twoColumnListFrom option').on('dblclick', function(e) {
                //alert('Нажали');
                //console.log(this);
                onDoubleClickHandler(control,this);
            });


            // Ставим обработчик двойного нажатия клавиши
            $(control).find('.twoColumnListTo option').on('dblclick', function(e) {
                //alert('Нажали');
                //console.log(this);
                onDoubleClickHandler(control,this);
            });

            // Обработчик двойного нажатия неа опшн
            function onDoubleClickHandler(control,option)
            {
                // Сбрасываем выделение у опшена
                $(option).prop('selected', false);
                // Смотрим - если непосредственный родитель элемента From - переписываем в To,
                //    а если To - то во From
                // Вычислим список назначения
                var destinationList = null;
                if ($($(option).parents()[0]).hasClass('twoColumnListFrom'))
                {
                    var destinationList = $($(option).parents('.twoColumnList')[0]).find('.twoColumnListTo');
                }
                if ($($(option).parents()[0]).hasClass('twoColumnListTo'))
                {

                    var destinationList = $($(option).parents('.twoColumnList')[0]).find('.twoColumnListFrom');
                }
                // Запишем в список
                $(destinationList).prepend($(option));
                // Обновляем значение
                refreshValue(control);
            }

        })(this);
    });
});