$(document).ready(function() {
    function init() {
        for(var i = 0; i < tableChooserConfig.length; i++) {
            (function(chooserConfig) {
                var chooser = $(chooserConfig.selector);
                var cleanClone = null;

                $(chooser).on('click', '.plus', function() {
                    $(this).parents('tbody').append($(cleanClone).clone());
                });

                $(chooser).on('templateAccept', function(e, template) {
                    var template = $.parseJSON(template);
                    // Клонируем согласно количеству полей в шаблоне
                    $(chooser).find('tbody tr').remove();
                    for(var i = 0; i < template.length; i++) {
                        var row = $(cleanClone).clone();
                        chooserConfig.templateAccept(row, template[i]);
                        $(row).appendTo($(chooser).find('tbody'));
                    }
                });

                $(chooser).on('update', function(e) {
                    $.ajax({
                        'url' : chooserConfig.url,
                        'cache' : false,
                        'dataType' : 'json',
                        'data' : chooserConfig.params,
                        'type' : 'GET',
                        'success' : function(data, textStatus, jqXHR) {
                            if(data.success == 'true' || data.success == true) {
                                var data = data.data;
                                $(chooser).find('tbody tr:not(:first)').remove();
                                chooserConfig.fill(data, chooser);
                                cleanClone = $(chooser).find('tbody tr:first').clone();
                            } else {

                            }
                        }
                    });
                });
            })(tableChooserConfig[i]);
        }
    }

    var tableChooserConfig = [
        {
            'selector': '#tableFields',
            'fill' : function(data, chooser) {
                var tableFieldsCombo = $(chooser).find('.dbField');
                $(tableFieldsCombo).find('option').remove();
                for(var i in data.dbFields) {
                    $(tableFieldsCombo).append($('<option>').prop({
                        'value' : i
                    }).text(data.dbFields[i]));
                }

                var fileFieldsCombo = $(chooser).find('.tasuField');
                $(fileFieldsCombo).find('option').remove();
                for(var i in data.fileFields) {
                    $(fileFieldsCombo).append($('<option>').prop({
                        'value' : i
                    }).text(data.fileFields[i]));
                }
            },
            'url': '/index.php/admin/tasu/gettablefields',
            'templateAccept' : function(row, data) {
                row.find('.dbField').val(data.dbField);
                row.find('.tasuField').val(data.fileField);
            },
            'params': {
                'table' : function() {
                    return $.trim($('#tableList').val());
                },
                'tasufile': function() {
                    return $('#filesList .success td:first a').prop('id').substr(2);
                }
            }
        },
        {
            'selector': '#tableKey',
            'fill' : function(data, chooser) {
                var tableFieldsCombo = $(chooser).find('.dbField');
                $(tableFieldsCombo).find('option').remove();
                for(var i in data.dbFields) {
                    $(tableFieldsCombo).append($('<option>').prop({
                        'value' : i
                    }).text(data.dbFields[i]));
                }
            },
            'url': '/index.php/admin/tasu/gettablefields',
            'templateAccept' : function(row, data) {
                row.find('.dbField').val(data.dbField);
            },
            'params': {
                'table' : function() {
                    return $.trim($('#tableList').val());
                },
                'tasufile': function() {
                    return $('#filesList .success td:first a').prop('id').substr(2);
                }
            }
        }
    ];
    init();
});