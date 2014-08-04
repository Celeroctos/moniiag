$(document).ready(function() {
    InitPaginationList('logsSearchResult', 'id', 'desc', updateLogsTable);

    $('#logs-search-submit').click(function(e) {
        $(this).trigger('begin');
        updateLogsTable();
        return false;
    });

    function getFilters() {
        var usersIds = [];
        var choosedUsers = $.fn['userChooser'].getChoosed();
        for(var i = 0; i < choosedUsers.length; i++) {
            usersIds.push(choosedUsers[i].id);
        }

        var Result =
        {
            'groupOp' : 'AND',
            'rules' : [
                {
                    'field' : 'changedate',
                    'op' : 'eq',
                    'data' :  $.trim($('#date').val())
                },
                {
                    'field' : 'user_id',
                    'op' : 'in',
                    'data' : usersIds
                }
            ]
        };

        return Result;
    }

    function updateLogsTable() {
        var filters = getFilters();
        var PaginationData = getPaginationParameters('logsSearchResult');
        if (PaginationData != '') {
            PaginationData = '&'+PaginationData;
        }
        $.ajax({
            'url' : '/index.php/admin/logs/search/?filters=' + $.toJSON(filters)+PaginationData,
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
					displayLogs(data.rows);
					printPagination('logsSearchResult',data.total);
                    $('#logs-search-submit').trigger('end');
                } else {
                    $('#errorSearchPopup .modal-body .row p').remove();
                    $('#errorSearchPopup .modal-body .row').append('<p class="errorText">' + data.data + '</p>')
                    $('#errorSearchPopup').modal({
                    });
                    $('#logs-search-submit').trigger('end');
                }
                return;
            }
        });
    }
	
	// Отобазить таблицу тех, кто без карт
    function displayLogs(data) {
        // Заполняем пришедшими данными таблицу тех, кто без карт
        var table = $('#logsSearchResult tbody');
        table.find('tr').remove();
        for(var i = 0; i < data.length; i++) {
            table.append(
                '<tr>' +
                    '<td>' + data[i].id + '</td>' +
                    '<td>' + data[i].login + '</td>' +
					'<td>' + data[i].url + '</td>' +
					'<td>' + data[i].changedate + ' '+ data[i].changetime + '</td>' +
                '</tr>'
            );
        }
        table.parents('div.no-display').removeClass('no-display');
    }
});