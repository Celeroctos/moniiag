$(document).ready(function(e) {
    var table = $('.medcardIndex');
    // Заполняем таблицу значениями
    $('.printBtn').on('click', function(e) {
        window.print();
    });
});