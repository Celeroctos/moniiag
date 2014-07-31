$(document).ready(function() {
    InitPaginationList('logsSearchResult', 'changedate', 'desc', updateLogsTable);

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
                    'field' : 'oms_number',
                    'op' : 'eq',
                    'data' :  $('#date').val()
                },
                {
                    'field' : 'first_name',
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
});