$(document).ready(function() {
    $('#mainSideMenu a[href$="#"]').on('click', function() {
        $(this).parents('li:eq(0)').find('ul:eq(0)').slideToggle();
        return false;
    })
    $('#mainSideMenu a[href$="#"]').parents('li.active').find('ul:eq(0)').css('display', 'block');
});