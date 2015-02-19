(function() {
    return {
        config : {
            name : 'component.grid',
                renderConfig : {
                mode : 'ajax',
                    ajaxConf : {
                    url : null,
                        error : function(jqXHR, status) { },
                    success : function(jqXHR, status, errorThrown) { },
                    data : { }
                }
            }
        },


        setConfig : function(config) {
            for(var prop in config) {
                this.config[prop] = config[prop];
            }
            return this;
        },

        reloadGrid : function() {
            console.log(this.config.renderConfig.ajaxConf);
            $.ajax(this.config.renderConfig.ajaxConf);
        },

        bindHandlers : function() {

        },

        init : function() {
            return this;
        }
    };
})();