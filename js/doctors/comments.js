$(document).ready(function () {

    // На кнопку "Показать ещё" вешаем обработчик,
    //    который запросит комментарии для данного пациента и откроет поп-ап с ними (комментариями)

    $(document).on('click','#moreCommentsButton',function(){
        var url = '/index.php/doctors/shedule/getallpatientcomments?cardId='+$('#currentPatientId').val();

        $.ajax({
            'url': url,
            'cache': false,
            'dataType': 'json',
            'type': 'POST',
            'error': function (data, textStatus, jqXHR) {
                console.log(data);
            },
            'success': function (data, textStatus, jqXHR) {
                if (data.success==true) {
                   // Убиваем старое содержимое поп-апа
                    $('#allCommentsPopup .modal-body .row').empty();
                    // Добавляем в тело данные
                    $('#allCommentsPopup .modal-body .row').append( data.data );
                    $('#allCommentsPopup').modal({});

                }
            }
        });
    });



    // По кнопке "Добавить комментарии" открываем соответвующий поп-ап
    $(document).on('click','#addCommentButton',function()
        {
            // Заносим номер карточки пацента
            $('#comment-add-form #forPatientId').val(  $('#currentPatientId').val()  );
            // Открываем поп-ап с формой добавление
            $('#addCommentPopup').modal({});

        }
    );

    // Обрабатываем кнопку редактирования
    $(document).on('click','.editComment',function()
        {
            // Заносим номер карточки пацента
            $('#comment-edit-form #forPatientId').val(  $('#currentPatientId').val()  );

            // Читаем id комментария
            currentCommentId = $(this).prop('id').substr(9, $(this).prop('id').length-9  );
            // Записываем в соответствующее поле ИД комментария
            $('#comment-edit-form #commentId').val(currentCommentId);

            // Подкачиваем текст из сообщения. Строго говоря это не правильно.
            //   Но поскольку у нас врач редактирует только свой комментарий - то врядли получится
            //     , что редактируемый комментарий одновременно изменился в базе

            var commentTextValue = $(this).parents('.commentsContainer').find('.commentTextContainer p').text();

            $('#comment-edit-form #commentText').val(commentTextValue);

                // Открываем поп-ап с формой добавление
            $('#editCommentPopup').modal({});

        }
    );

    $("#comment-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления
            $('#editCommentPopup').modal('hide');
            // Закрываем поп-ап с комментариями
            $('#allCommentsPopup').modal('hide');
            // Перезагружаю последний комментарий на странице
            reloadCommentSection(ajaxData.newCommentSection);
            $("#comment-edit-form")[0].reset(); // Сбрасываем форму
            $('#successPopup p').text(ajaxData.text);
            $('#successPopup').modal({
            });

        } else {

            // Удаляем предыдущие ошибки
            $('#errorEditCommentPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorEditCommentPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorEditCommentPopup').modal({

            });
        }
    });
    $("#comment-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addCommentPopup').modal('hide');
            // Закрываем поп-ап с комментариями
            $('#allCommentsPopup').modal('hide');
            // Перезагружаю последний комментарий на странице
            reloadCommentSection(ajaxData.newCommentSection);
            $("#comment-add-form")[0].reset(); // Сбрасываем форму
            $('#successPopup p').text(ajaxData.text);
            $('#successPopup').modal({
            });
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddCommentPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddCommentPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddCommentPopup').modal({

            });
        }
    });

    function reloadCommentSection(newCommentSection)
    {
        $('.greetingCommentBlock').empty();
        $('.greetingCommentBlock').append(newCommentSection);
    }
});