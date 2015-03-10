misEngine.class('component.grid', function() {
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

        reloadGrid : function(params) {
            if(params) {
                for(var i in params) {
                    this.config.renderConfig.ajaxConf.data[i] = params[i];
                }
            }
            $.ajax(this.config.renderConfig.ajaxConf);
        },

        bindHandlers : function() {

        },

        init : function() {
            return this;
        }
    };
});