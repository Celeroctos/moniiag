(function() { 
	return {
		config : {
			name : 'component.modal',
            selector : null
        },
		
		bindHandlers : function() {
            $(this).on('show', $.proxy(function(e) {
                this.show();
            }, this));
		},

        show : function() {
            $(this.config.selector).modal({});
        },
	
		init : function() {
            this.bindHandlers();
			return this;
		}
	};
})();