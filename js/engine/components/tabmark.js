(function() { 
	return {
		config : {
			name : 'component.tabmark',
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
            console.log(this);
            $(this.config.selector).css({
                'display' : 'inline'
            });
        },

        hide : function() {
            $(this.config.selector).css({
                'display' : 'none'
            });
        },
	
		init : function(config) {
            $(config.selector).css('display', 'none');
            this.bindHandlers();
			return this;
		}
	};
})();