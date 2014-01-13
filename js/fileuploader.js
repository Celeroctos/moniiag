$(document).ready(function() {
    var fileUploadersGroups = $('.fileinput-group');
    var cleanExample = null;
    for(var i = 0; i < fileUploadersGroups.length; i++) {
        (function(fileUploadersGroup) {
            var uploaders = $(fileUploadersGroup).find('.fileinput');
            if(cleanExample == null && uploaders.length > 0) {
                // Сохраняем проклонированный узел, чтобы генерировать новые узлы для массовой загрузки
                cleanExample = $(uploaders[0]).clone();
            }
            // Подвязка генерации новых элементов управления
            $(document).on('click', '.fileinput button.plus', function() {
                // Если среди группы аплоадеров есть элемент, у которого закрыта кнопка добавления контрола, то это означает, что не надо создавать новый контрол: есть, куда пихать файлы
                if($('.fileinput').find('button.plus').filter('.no-display').length == 0) {
                    var nextElement = $(cleanExample).clone();
                    $(fileUploadersGroup).find('.fileinput-wrap:last').append($('<div>').append(nextElement));
                    uploaders.push(nextElement);
                }
            });
            $(document).on('click', '.fileinput .close', function() {
                $(this).parents('.fileinput').find('button.plus').addClass('no-display');
                // Смотрим, нет ли инпута, который был нагенерирован, но ещё не заполнен. Если да - его надо удалить
                if($(fileUploadersGroup).find('.fileinput').length > 1) {
                    $(fileUploadersGroup).find('.fileinput button.plus').filter('.no-display').parents('.fileinput').remove();
                }
                uploaders = $(fileUploadersGroup).find('.fileinput');
            });
            var contId = $(fileUploadersGroup).prop('id');
            $(fileUploadersGroup).find('.submit').on('click', function() {
                // Теперь грузим файлы
                var currentIndex = 0;
                var uploadProcess = false; // Флаг, который говорит нам о том, что процесс загрузки не идёт
                var currentProcent = 0; // Процент загрузки текущего файла

                var hiddenIframe = $('<iframe>').appendTo($('body'));
                $(hiddenIframe).css({
                    'class' : 'no-display'
                });
                var iframeForm = $('<form>').prop({
                    'enctype' : 'multipart/form-data',
                    'action' : fileUploadersConfig[contId].url
                });
                var iframeDocumentElement = $(hiddenIframe).contents();
                $(iframeDocumentElement).ready(function() {
                    uploadProcessFunc();
                });

                function uploadProcessFunc() {
                    if(!uploadProcess) {
                        var fileField = $(uploaders[currentIndex]).find('input[type="file"]');
                        $(fileField).clone().appendTo(iframeForm);
                        $(iframeForm).appendTo($(iframeDocumentElement).find('body'));
                        $(iframeForm).submit();
                        // Показывам прогресс-бар, скрываем файл
                        $(uploaders[currentIndex]).find('span:not([class="description"]), a, input').hide();
                        $(uploaders[currentIndex]).find('.progress').removeClass('no-display');
                        $(uploaders[currentIndex]).find('button.plus').hide().removeClass('no-display');
                        uploadProcess = true; // Начинаем процесс загрузки
                        setTimeout(function() {
                            uploadProcessFunc();
                        }, 1000);
                    } else {
                        // Делаем запрос на сервер. В том случае, если загружено не полностью, запускать ещё раз на проверку
                        $.ajax({
                            'url' : fileUploadersConfig[contId].url + '?onlysayuploadedpart=1',
                            'cache' : false,
                            'dataType' : 'json',
                            'type' : 'GET',
                            'success' : function(data, textStatus, jqXHR) {
                                if(data.success == true) {
                                    // Перестраиваем прогресс-бар
                                    var progress = $(uploaders[currentIndex]).find('.progress');
                                    var dataForCalc = data.data;
                                    currentProcent = (dataForCalc.uploaded / dataForCalc.filesize) * 100;
                                    $(progress).find('.progress-bar').css({'width' : currentProcent + '%'});
                                    // Устанавливаем описание, сколько процентов загружено
                                    $(progress).find('.progress-bar .sr-only').text(currentProcent + '% завершено');
                                    $(progress).find('.description').text(currentProcent + '% завершено');
                                    if(currentProcent == 100) {
                                        // Нужно завершить загрузку файла, скрыть прогресс-бар и начать загрузку следующего, если ещё не все загружены
                                        currentProcent = 0;
                                        uploadProcess = false;
                                        currentIndex = (uploaders.length - 1 > currentIndex) ?  currentIndex + 1 : null;
                                        // Выводить сообщение, что все файл успешно загружен
                                        $(progress).removeClass('active');
                                        $(progress).find('.description').addClass('text-success').html('Файл загружен');
                                        $(progress).find('.ok, .ok span').removeClass('no-display').show();
                                        // Ещё есть файлы
                                        if(currentIndex != null) {
                                            uploadProcessFunc();
                                        }
                                    } else {
                                        setTimeout(function() {
                                            uploadProcessFunc();
                                        }, 1000);
                                    }
                                } else {
                                    $('#errorPopup').modal({});
                                }
                            }
                        });
                    }
                }
            });
        })(fileUploadersGroups[i]);
    }
});

var fileUploadersConfig = {
    'tasuIn' : {
        'url' : '/index.php/admin/tasu/uploadoms'
    }
}