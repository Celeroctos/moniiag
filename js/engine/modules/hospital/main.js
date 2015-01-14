$(document).ready(function() {
	function HospitalModule() {
		return {
			init : function(config) {
				$('#sideCalendar').datetimepicker(config.datetimepicker ? config.datetimepicker : {});
				return this;
			}
		};
	}
	
	var hospitalModule = new HospitalModule().init({
		datetimepicker : {
			language: 'ru',
            format: 'yyyy-mm-dd',
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0
		}
	});
});