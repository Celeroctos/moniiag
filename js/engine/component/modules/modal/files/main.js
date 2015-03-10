misEngine.class('component.modal', function() {
	return {
		config : {
			name : 'component.modal',
            selector : null
        },
		
		bindHandlers : function() {
            $(this).on('show', $.proxy(function(e) {
                this.show();
            }, this));
            $(this).on('hide', $.proxy(function(e) {
                this.hide();
            }, this));
		},

        show : function() {
            $(this.config.selector).modal({});
        },

        hide : function() {
            $(this.config.selector).modal('hide');
        },
	
		init : function() {
            this.bindHandlers();
			return this;
		}
	};
});