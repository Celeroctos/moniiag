misEngine.class('component.helper', function() {
	return {
		config : {
			name : 'component.helper',
            selector : null,
            iconContainer : null,
            file : null
        },
        tooltips : [],
        displayed : 0,
		
		bindHandlers : function() {

		},

        makeIcon : function() {
            if(this.config.iconContainer) {
                $(this.config.iconContainer).append(
                    $('<a>').prop({
                        'href' : '#'
                    }).append(
                        $('<img>').prop({
                            'width' : 64,
                            'height' : 64,
                            'alt' : 'Помощь',
                            'title' : 'Помощь',
                            'src' : '/images/icons/help_7103.png'
                        })
                    ).on('click', $.proxy(function(e) {
                        if(!this.displayed) {
                            this.displayTooltips();
                        } else {
                            this.hideTooltips();
                        }
                        return false;
                    }, this))
                )
            }
        },

        makeTooltips : function() {
            if(this.config.file) {
                $.getScript(this.config.file, $.proxy(function(data, textStatus, jqXHR) {
                    data = eval(data);
                    for(var i = 0; i < data.length; i++) {
                        this.tooltips.push(data[i]);
                    }
                }, this));
            }
        },

        displayTooltips : function() {
            for(var i = 0; i < this.tooltips.length; i++) {
                $(this.tooltips[i].selector).popover({
                    animation: true,
                    html: true,
                    placement: 'auto',
                    title: this.tooltips[i].header,
                    delay: {
                        show: 300,
                        hide: 300
                    },
                    container: $(this.tooltips[i].selector),
                    content: $.proxy(function () {
                        return $(this.tooltips[i].body);
                    }, this)
                });
                $(this.tooltips[i].selector).popover('show');
            }
            this.displayed = 1;
        },

        hideTooltips : function() {
            for(var i = 0; i < this.tooltips.length; i++) {
                $(this.tooltips[i].selector).popover('destroy');
            }
            this.displayed = 0;
        },

		init : function(config) {
            if(config) {
                this.setConfig(config);
            }

            this.makeIcon();
            this.makeTooltips();
            this.bindHandlers();
			return this;
		}
	};
});