// 111111111
$(document).ready(function(){

    activeChooser = null;

    // Обработчики кнопок "Добавить строку в КЛАДР"
    $(document).on('click','.addNewRegionButton',function(){
        console.log('Была нажата клавиша добавления нового региона');
        activeChooser = $(this).parents('div.chooser');
        $('#addRegionPopup').modal({});
    });

    $(document).on('click','.addNewDistrictButton',function(){
        console.log('Была нажата клавиша добавления нового района');
        activeChooser = $(this).parents('div.chooser');
        $('#addDistrictPopup').modal({});
    });

    $(document).on('click','.addNewSettlementButton',function(){
        console.log('Была нажата клавиша добавления нового населённого пункта');
        activeChooser = $(this).parents('div.chooser');
        $('#addSettlementPopup').modal({});
    });

    $(document).on('click','.addNewStreetButton',function(){
        console.log('Была нажата клавиша добавления новой улицы');
        activeChooser = $(this).parents('div.chooser');
        $('#addStreetPopup').modal({});
    });

    // Обработчики успешного добавления в КЛАДР
    $("#region-add-form").on("success",function(e,data)
        {
            console.log('Регион добавился');
            onCladdrAdd(this,data);
        }
    );

    $("#district-add-form").on("success",function(e,data)
        {
            console.log('Район добавился');
            onCladdrAdd(this,data);
        }
    );

    $("#settlement-add-form").on("success",function(e,data)
        {
            console.log('Населённый пункт добавился');
            onCladdrAdd(this,data);
        }
    );

    $("#street-add-form").on("success",function(e,data)
        {
            console.log('Улица добавилась');
            onCladdrAdd(this,data);
        }
    );

    // Функция-обработчик успешного добавления в кладр строки
    //   должна вызываться для всех форм добавления в КЛАДР
    function onCladdrAdd(sender,data)
    {
        chooserId = $(activeChooser).attr('id');
        // Разрешаем ввод данных для чюзера
        $.fn[chooserId].enable();

        console.log(sender);
        console.log(data);
        // Закрываем окно
        $($(sender).parents('.modal')[0]).modal('hide');

        // Берём поле id=name
        valForChooser = $(sender).find('#name').val();

        // ставим в фокус текстовое поле чюзера
        $(activeChooser).find('input[type=text]').focus();

        // Ставим только что добавленное значение
        $(activeChooser).find('input[type=text]').val(valForChooser);

        // Триггерим событие onkeyup
        /*e = $.Event('keyup');
        $(activeChooser).find('input[type=text]').trigger(e);
        */

        /*
        var e = $.Event('keyup');
        e.which = 0; // Character 'A'
        $(activeChooser).find('input[type=text]').trigger(e);
        */

        // Очищаем активный чюзер
        // Вообще-то здесь это делать не правильно. Нужно сделать специальный метод в чюзепре
        //     Так что этот кусок надо будет переделать
        $(activeChooser).find('div.choosed span').remove();

        // Запускаем событие обновления чюзера
        $(activeChooser).find('input[type=text]').one('keydown', function(e){
            var e = $.Event('keyup');
            e.which = 0; // null character
            $(this).trigger(e);
        });

        var e = $.Event('keydown');
        e.which = 0; // null character
        $(activeChooser).find('input[type=text]').trigger(e);

        // Сбрасываем форму
        $(sender)[0].reset();

    }

    $("#district-add-form").on('beforesend', function(eventObj, settings, jqXHR) {

        if($.fn["regionChooserForDistrict"].getChoosed().length == 0) {
            alert('Не выбран регион!');
            return false;
        }
        var region = $.fn["regionChooserForDistrict"].getChoosed()[0].code_cladr;
        var strData =  'FormCladrDistrictAdd[name]=' + $("#addDistrictPopup #name").val() + '&FormCladrDistrictAdd[codeCladr]=' + $("#addDistrictPopup #codeCladr").val() + '&FormCladrDistrictAdd[codeRegion]=' + region;

        settings.data = strData;
    });


    $("#settlement-add-form").on('beforesend', function(eventObj, settings, jqXHR) {
        if($.fn["regionChooserForSettlement"].getChoosed().length == 0) {
            alert('Не выбран регион!');
            return false;
        }
        /*if($.fn["districtChooserForSettlement"].getChoosed().length == 0) {
            alert('Не выбран район!');
            return false;
        }*/
        var district = '';
        var region = $.fn["regionChooserForSettlement"].getChoosed()[0].code_cladr;

        if ($.fn["districtChooserForSettlement"].getChoosed().length>0)
        {
            district = $.fn["districtChooserForSettlement"].getChoosed()[0].code_cladr;
        }

        var strData =  'FormCladrSettlementAdd[name]=' + $("#addSettlementPopup #name").val() + '&FormCladrSettlementAdd[codeCladr]=' + $("#addSettlementPopup #codeCladr").val() + '&FormCladrSettlementAdd[codeRegion]=' + region + '&FormCladrSettlementAdd[codeDistrict]=' + district + '&FormCladrSettlementAdd[id]=' + $("#addSettlementPopup #id").val();

        settings.data = strData;
    });


    $("#street-add-form").on('beforesend', function(eventObj, settings, jqXHR) {
        if($.fn["regionChooserForStreet"].getChoosed().length == 0) {
            alert('Не выбран регион!');
            return false;
        }
     /*   if($.fn["districtChooserForStreet"].getChoosed().length == 0) {
            alert('Не выбран район!');
            return false;
        }
        if($.fn["settlementChooserForStreet"].getChoosed().length == 0) {
            alert('Не выбран населённый пункт!');
            return false;
        }*/

        var district = '';
        var settlement='';


        var region = $.fn["regionChooserForStreet"].getChoosed()[0].code_cladr;

        if ($.fn["districtChooserForStreet"].getChoosed().length>0)
        {
            district = $.fn["districtChooserForStreet"].getChoosed()[0].code_cladr;
        }

        if ($.fn["settlementChooserForStreet"].getChoosed().length>0)
        {
            settlement = $.fn["settlementChooserForStreet"].getChoosed()[0].code_cladr;
        }

        var strData =  'FormCladrStreetAdd[name]=' + $("#addStreetPopup #name").val() + '&FormCladrStreetAdd[codeCladr]=' + $("#addStreetPopup #codeCladr").val() + '&FormCladrStreetAdd[codeRegion]=' + region + '&FormCladrStreetAdd[codeDistrict]=' + district + '&FormCladrStreetAdd[id]=' + $("#addStreetPopup #id").val() + '&FormCladrStreetAdd[codeSettlement]=' + settlement;
        settings.data = strData;
    });

});