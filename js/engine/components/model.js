(function() {
    return {
        config : {
            name : 'component.model'
        },

        render : function() {
            return this;
        },

        getColumns : function() {
            return this.config.columns ? this.config.columns : []
        },

        init : function() {
            return this;
        }
    };
})();