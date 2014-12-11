/**
 * Created by dmitry on 2014-12-10.
 */

var KeysPager = {
    grid: function() {
        this._grid = $("#keys").jqGrid({
            url: globalVariables.baseUrl + '/admin/external/get',
            datatype: "json",
            colNames: ['Ключ', 'Описание'],
            colModel: [
                { name: 'key',         index: 'key',         width: 250 },
                { name: 'description', index: 'description', width: 300 }
            ],
            rowNum: 25,
            rowList: [25, 50, 100],
            pager: '#keysPager',
            sortname: 'key',
            viewrecords: true,
            sortorder: "key",
            caption: "Ключи",
            height: 300,
            ondblClickRow: function() {
                var rowIndex = KeysPager._grid.jqGrid('getGridParam', 'selrow');
                if (rowIndex != null) {
                    KeysPager.edit(KeysPager._grid.jqGrid('getRowData', rowIndex).key);
                }
            }
        }).jqGrid('navGrid','#keysPager', {
                edit: false,
                add: false,
                del: false
            }
        );
    },
    add: function() {
        $("#addExternalPopup").modal().draggable("disable");
    },
    edit: function(key) {
        $.get(globalVariables.baseUrl + "/admin/external/one", {
            key: key
        }, function(data) {
            var json = $.parseJSON(data);
            if (!json.success) {
                console.log(json);
                return true;
            }
            var editModal = $("#editExternalPopup");
            editModal.modal().draggable("disable");
            editModal.find("form #key").val(json.key);
            editModal.find("form #description").val(json.description);
        });
    },
    drop: function(key) {
        $.get(globalVariables.baseUrl + "/admin/external/delete", {
            key: key
        }, function(data) {
            var json = $.parseJSON(data);
            if (!json.success) {
                console.log(json);
                return true;
            }
            KeysPager.update(null, data);
        });
    },
    update: function(event, data) {
        var json = $.parseJSON(data);
        if (json.success) {
            $("#addExternalPopup").modal("hide");
            $("#editExternalPopup").modal("hide");
            $("#keys").trigger("reloadGrid");
            $("#external-add-form")[0].reset();
            $("#external-edit-form")[0].reset();
        } else {
            $('#errorAddCategoriePopup .modal-body .row p').remove();
            for(var i in json.errors) {
                for(var j = 0; j < json.errors[i].length; j++) {
                    $('#errorAddCategoriePopup .modal-body .row').append("<p>" + json.errors[i][j] + "</p>")
                }
            }
            $('#errorAddCategoriePopup').modal({
            }).css("z-index", 1052).disableSelection();
        }
    },
    construct: function() {
        KeysPager.grid();
        $("#addExternal").click(function() {
            KeysPager.add();
        });
        $("#editExternal").click(function() {
            var rowIndex = KeysPager._grid.jqGrid('getGridParam', 'selrow');
            if (rowIndex != null) {
                KeysPager.edit(KeysPager._grid.jqGrid('getRowData', rowIndex).key);
            }
        });
        $("#deleteExternal").click(function() {
            var rowIndex = KeysPager._grid.jqGrid('getGridParam', 'selrow');
            if (rowIndex != null) {
                KeysPager.drop(KeysPager._grid.jqGrid('getRowData', rowIndex).key);
            }
        });
        $("#external-add-form").on("success", KeysPager.update);
        $("#external-edit-form").on("success", KeysPager.update);
    },
    _grid: null
};

$(document).ready(function() {
    KeysPager.construct();
});