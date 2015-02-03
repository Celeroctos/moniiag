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
            alert(this.config.selector);
            $(this.config.selector).modal({});
        },
	
		init : function() {
			return this;
		}
	};
})();