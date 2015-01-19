(function() { 
	return {
		config : {
			name : 'component.datetimepicker',
			renderConfig : {
				mode : 'external'
			}
		},
		
		getExternalRender : function(widgetConfig) {
			if(!widgetConfig.selector) {
				misEngine.t('Error for Datetimepicker : selector not exists');
			}

			var selector = widgetConfig.selector;
			delete widgetConfig.selector;
			return $(selector).datetimepicker(widgetConfig);
		},
		
		bindHandlers : function() {
		
		},
	
		init : function() {
			return this;
		}
	};
})();