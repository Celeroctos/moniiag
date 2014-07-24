//
$(document).ready(function(){

    activeChooser = null;

    // Обработчики кнопок "Добавить строку в КЛАДР"
    $(document).on('click','.addNewRegionButton',function(){
        console.log('Была нажата клавиша добавления нового региона');
        swapCladdrFields('addRegionPopup');
        activeChooser = $(this).parents('div.chooser');
        $('#addRegionPopup').modal({});
    });

    $(document).on('click','.addNewDistrictButton',function(){
        console.log('Была нажата клавиша добавления нового района');
        swapCladdrFields('addDistrictPopup');
        activeChooser = $(this).parents('div.chooser');
        $('#addDistrictPopup').modal({});
    });

    $(document).on('click','.addNewSettlementButton',function(){
        console.log('Была нажата клавиша добавления нового населённого пункта');
        swapCladdrFields('addSettlementPopup');
        activeChooser = $(this).parents('div.chooser');
        $('#addSettlementPopup').modal({});
    });

    $(document).on('click','.addNewStreetButton',function(){
        console.log('Была нажата клавиша добавления новой улицы');
        swapCladdrFields('addStreetPopup');
        activeChooser = $(this).parents('div.chooser');
        $('#addStreetPopup').modal({});
    });

    // Обработчики успешного добавления в КЛАДР
    $("#region-add-form").on("success",function(e,data)
        {
            //console.log('Регион добавился');
            //onCladdrAdd(this,data);

            // Смотрим - надо вывести ошибки
            result = jQuery.parseJSON(data);
            if (result.success==false || result.success=="false")
            {
            //    console.log('Произошла ошибка :-(');
            //    alert ('Произошла ошибка. '+result.errors.name[0]);
            }
            else
            {
                console.log('Регион добавился');
                onCladdrAdd(this,data);
            }

        }
    );

    $("#district-add-form").on("success",function(e,data)
        {
            //console.log('Район добавился');
            //onCladdrAdd(this,data);

            // Смотрим - надо вывести ошибки
            result = jQuery.parseJSON(data);
            if (result.success==false || result.success=="false")
            {
             //   console.log('Произошла ошибка :-(');
              //  alert ('Произошла ошибка. '+result.errors.name[0]);
            }
            else
            {
                console.log('Район добавился');
                onCladdrAdd(this,data);
            }


        }
    );

    $("#settlement-add-form").on("success",function(e,data)
        {
            //console.log('Населённый пункт добавился');
            //onCladdrAdd(this,data);

            // Смотрим - надо вывести ошибки
            result = jQuery.parseJSON(data);
            if (result.success==false || result.success=="false")
            {
             //   console.log('Произошла ошибка :-(');
             //   alert ('Произошла ошибка. '+result.errors.name[0]);
            }
            else
            {
                console.log('Населённый пункт добавился');
                onCladdrAdd(this,data);
            }
        }
    );

    $("#street-add-form").on("success",function(e,data)
        {
            // Смотрим - надо вывести ошибки
            result = jQuery.parseJSON(data);
            if (result.success==false || result.success=="false")
            {
            //    console.log('Произошла ошибка :-(');
            //    alert ('Произошла ошибка. '+result.errors.name[0]);
            }
            else
            {
                console.log('Улица добавилась');
                onCladdrAdd(this,data);
            }
        }
    );

    // Перекачивает значения полей из формы редактирования адреса в форму добавления
    function swapCladdrFields(activeFormName)
    {
        // Если нет видимого поп-апа, в котором редактируется адрес
        if ( (editAddrObject = $('#editAddressPopup:visible').length)<=0)
            return;

        // Перекачиваем регион
        // Проверяем - есть ли внутри регион если да - то перекачиваем его
        if ( $('#'+activeFormName + ' div[id^=regionChooser]').length>0 )
        {
            console.log('Протаскиваем регион');
            //$('#'+activeFormName + ' div[id^=regionChooser]').attr('id').clearAll();
            //regionValue = $(editAddrObject).find('regionChooser').getChoosed();
            toChooser = $('#'+activeFormName + ' div[id^=regionChooser]').attr('id');
            $.fn[toChooser].clearAll();
            regionValue = $.fn['regionChooser'].getChoosed();
            console.log(regionValue);
            if ($(regionValue).length>0)
            {
                $.fn[toChooser].addChoosed(
                    $('<li>').prop('id', 'r' + regionValue[0].id).text(regionValue[0].name), regionValue[0]
                );
            }
        }

        // Перекачиваем район
        if ( $('#'+activeFormName + ' div[id^=districtChooser]').length>0 )
        {
            console.log('Протаскиваем район');
            toChooser = $('#'+activeFormName + ' div[id^=districtChooser]').attr('id');
            $.fn[toChooser].clearAll();
            //$('#'+activeFormName + ' div[id^=districtChooser]').clearAll();
            districtValue = $.fn['districtChooser'].getChoosed();
            console.log(districtValue);

            if ($(districtValue).length>0)
            {
                $.fn[toChooser].addChoosed(
                    $('<li>').prop('id', 'r' + districtValue[0].id).text(districtValue[0].name), districtValue[0]
                );
            }
        }

        // Перекачиваем населённый пункт
        if ( $('#'+activeFormName + ' div[id^=settlementChooser]').length>0 )
        {
            console.log('Протаскиваем населённый пункт');
            //$('#'+activeFormName + ' div[id^=settlementChooser]').clearAll();
            toChooser = $('#'+activeFormName + ' div[id^=settlementChooser]').attr('id');
            $.fn[toChooser].clearAll();
            settlementValue = $.fn['settlementChooser'].getChoosed();
            console.log(settlementValue);

            if ($(settlementValue).length>0)
            {
                $.fn[toChooser].addChoosed(
                    $('<li>').prop('id', 'r' + settlementValue[0].id).text(settlementValue[0].name), settlementValue[0]
                );
            }
        }

        // Улицу перекачивать не нужно (не бывает такого, что в окне добавления объекта КЛАДР есть улица)

    }

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



        // Очищаем активный чюзер
        // Вообще-то здесь это делать не правильно. Нужно использовать специальный метод в чюзепре
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

        $('#'+$(activeChooser).prop('id')).one('rowadd', function(){
           // Имитируем нажатие на первый элемент
           //    списка

            // Ищем внутри this список с вариантами
            variants = $(this).find('ul.variants li');
            // Берём первый элемент и триггерим событие click
            $(variants[0]).trigger('click');

        });

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