$(document).ready(function () {

    // Сама таблица записанных пациентов
    $("#writtenPatients").jqGrid({
        datatype: "json",
        colNames: ['Дата', 'ФИО', 'Телефон', 'ФИО врача', 'Отписать', '', ''],
        colModel: [
            {
                name: 'patient_date',
                index: 'patient_date',
                width: 100
            },
            {
                name: 'fio',
                index: 'fio',
                width: 200
            },
            {
                name: 'contact',
                index: 'contact',
                width: 100
            },
            {
                name: 'doctor_fio',
                index: 'doctor_fio',
                width: 150
            },
            {
                name: 'unwrite',
                index: 'unwrite',
                width: 80,
                //  formatter: unwriteLinkFormatter,
                align: "center"
            },
            {
                name: 'id',
                width: 150,
                hidden: true
            },
            {
                name: 'medcard_id',
                index: 'medcard_id',
                hidden: true
            },


        ],
        rowNum: 10,
        rowList: [10, 20, 30],
        pager: '#writtenPatientsPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Записанные пациенты",
        height: 100
    });


    // Нажатие на ссылку "Отписать"
    $(document).on('click', '#writtenPatientsTimetable .unwrite-link', function (e) {
        var idGreetingToUnwrite = $(this).attr('href').substr(1);
        var params = {
            id: idGreetingToUnwrite
        };
        $.ajax({
            'url': '/doctors/shedule/unwritepatient',
            'data': params,
            'cache': false,
            'dataType': 'json',
            'type': 'GET',
            //'scope': this,
            'success': function (data, textStatus, jqXHR) {

                if (data.success == 'true') {
                    var currentRow = $('#writtenPatients').jqGrid('getGridParam', 'selrow');
                    var rowData = $('#writtenPatients').getRowData(currentRow);


                    var medcardId = rowData['medcard_id'];
                    var newLink = '';
                    // Проверим - опосредованный поциэнт или нет
                    if (medcardId=='')
                    {
                        newLink = '<a href=\"/reception/patient/writepatientwithoutdata?unwritedGreetingId='+ idGreetingToUnwrite +'\" target=\"_blank\">Перезаписать</a>';
                    }
                    else
                    {
                        newLink = '<a href=\"/reception/patient/writepatientsteptwo/?unwritedGreetingId='+ idGreetingToUnwrite +'&cardid=' + medcardId + '\" target=\"_blank\">Перезаписать</a>';
                    }

                    //var newLink = '<a href=\"/reception/patient/writepatientsteptwo/?cardid=' + medcardId + '\" target=\"_blank\">Перезаписать</a>';
                    rowData.unwrite = newLink;
                    $('#writtenPatients').jqGrid('setRowData', currentRow, rowData);


                }

                return;
            }
        });
        return false;
    });

});