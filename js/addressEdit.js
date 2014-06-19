$(document).ready(function(){

    activeChooser = null;

    // Обработчики кнопок "Добавить строку в КЛАДР"
    $('.addNewRegionButton').on('click',function(){
        console.log('Была нажата клавиша добавления нового региона');
        $('#addRegionPopup').modal({});
    });

    $('.addNewDistrictButton').on('click',function(){
        console.log('Была нажата клавиша добавления нового района');
        $('#addDistrictPopup').modal({});
    });

    $('.addNewSettlementButton').on('click',function(){
        console.log('Была нажата клавиша добавления нового населённого пункта');
        $('#addSettlementPopup').modal({});
    });

    $('.addNewStreetButton').on('click',function(){
        console.log('Была нажата клавиша добавления новой улицы');
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
        console.log(sender);
        console.log(data);
        // Закрываем окно
        $($(sender).parents('.modal')[0]).modal('hide');
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
        if($.fn["districtChooserForSettlement"].getChoosed().length == 0) {
            alert('Не выбран район!');
            return false;
        }
        var region = $.fn["regionChooserForSettlement"].getChoosed()[0].code_cladr;
        var district = $.fn["districtChooserForSettlement"].getChoosed()[0].code_cladr;
        var strData =  'FormCladrSettlementAdd[name]=' + $("#addSettlementPopup #name").val() + '&FormCladrSettlementAdd[codeCladr]=' + $("#addSettlementPopup #codeCladr").val() + '&FormCladrSettlementAdd[codeRegion]=' + region + '&FormCladrSettlementAdd[codeDistrict]=' + district + '&FormCladrSettlementAdd[id]=' + $("#addSettlementPopup #id").val();

        settings.data = strData;
    });


    $("#street-add-form").on('beforesend', function(eventObj, settings, jqXHR) {
        if($.fn["regionChooserForStreet"].getChoosed().length == 0) {
            alert('Не выбран регион!');
            return false;
        }
        if($.fn["districtChooserForStreet"].getChoosed().length == 0) {
            alert('Не выбран район!');
            return false;
        }
        if($.fn["settlementChooserForStreet"].getChoosed().length == 0) {
            alert('Не выбран населённый пункт!');
            return false;
        }
        var region = $.fn["regionChooserForStreet"].getChoosed()[0].code_cladr;
        var district = $.fn["districtChooserForStreet"].getChoosed()[0].code_cladr;
        var settlement = $.fn["settlementChooserForStreet"].getChoosed()[0].code_cladr;
        var strData =  'FormCladrStreetAdd[name]=' + $("#addStreetPopup #name").val() + '&FormCladrStreetAdd[codeCladr]=' + $("#addStreetPopup #codeCladr").val() + '&FormCladrStreetAdd[codeRegion]=' + region + '&FormCladrStreetAdd[codeDistrict]=' + district + '&FormCladrStreetAdd[id]=' + $("#addStreetPopup #id").val() + '&FormCladrStreetAdd[codeSettlement]=' + settlement;
        settings.data = strData;
    });

});