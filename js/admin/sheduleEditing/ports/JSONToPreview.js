$(document).ready(function () {

    $.fn['sheduleEditor.port.JSONToPreview'] = {
        getHTML:function(timetableJSON)
        {
           return $.toJSON(timetableJSON);
        }
    }


});