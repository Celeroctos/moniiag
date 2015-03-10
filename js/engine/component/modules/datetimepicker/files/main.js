misEngine.class('component.datetimepicker', function() {
    return {
		config : {
			name : 'component.datetimepicker',
			renderConfig : {
				mode : 'external'
			}
		},
        datetimepickerWidget : null,

		getExternalRender : function(widgetConfig) {
			if(!widgetConfig.selector) {
				misEngine.t('Error for Datetimepicker : selector not exists');
			}

			var selector = widgetConfig.selector;
			delete widgetConfig.selector;
            this.datetimepickerWidget = $(selector).datetimepicker(widgetConfig);
			return this.datetimepickerWidget;
		},

        getWidget : function() {
            return this.datetimepickerWidget;
        },

        hide : function() {
            $(widgetConfig.selector).hide();
        },

		bindHandlers : function() {

		},

		init : function() {
			return this;
		}
	};
});