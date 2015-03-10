misEngine.class('component.tabmark', function() {
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
            $(this.config.selector).css({
                'display' : 'inline'
            });
            this.updateTabmark();
        },

        updateTabmark : function() {
            $.ajax(this.config.renderConfig.ajaxConf)
        },

        setText : function() {

        },

        hide : function() {
            $(this.config.selector).css({
                'display' : 'none'
            });
        },
	
		init : function(config) {
            if(config) {
                this.setConfig(config);
            }
            $(config.selector).css('display', 'none');
            this.bindHandlers();
			return this;
		}
	};
});