misEngine.addToQueue(function() {
    return {
        config : {
            name : 'guides'
        },

        bindHandlers : function() {
            $('#guidesNavbar a').click(function (e) {
                e.preventDefault()
                $(this).tab('show')
            });
        },

        displayGrids : function() {
            this.displayQueueGrid();
        },

        getQueueModel : function() {
            var model = misEngine.create('component.model').setConfig({
                columns : [
                    {
                        name : 'id',
                        type : 'raw'
                    },
                    {
                        name : 'type',
                        type : 'raw'
                    },
                    {
                        name : 'type_of_writing',
                        type : 'raw'
                    },
                    {
                        name : 'num_pre',
                        type : 'raw'
                    },
                    {
                        name : 'num_queue',
                        type : 'raw'
                    },
                    {
                        name : 'comission_date',
                        type : 'raw'
                    }
                ]
            });
            return model;
        },

        displayQueueGrid : function() {
            var queueGrid = misEngine.create('component.grid');
            var queueModel = this.getQueueModel();
            var queueGridRequestData = {
                returnAsJson : true,
                id : 'queueGrid',
                model : $.toJSON(queueModel.getColumns())
            };

            queueGrid
                .setConfig({
                    renderConfig : {
                        mode : 'ajax',
                        ajaxConf : {
                            url : '/hospital/components/grid',
                            data : queueGridRequestData,
                            dataType : 'json',
                            cache : 'false',
                            success : function(data, status, jqXHR) {
                                if(data.success) {
                                    $('#queue')
                                        .css({
                                            'textAlign' : 'left'
                                        })
                                        .html(data.data);
                                }
                            },
                            error: function(jqXHR, status, errorThrown) {
                                misEngine.t(jqXHR, status, errorThrown);
                            }
                        }
                    }
                })
                .render()
                .on();
        },

        init : function() {
            this.displayGrids();
            this.bindHandlers();
            return this;
        }
    };
});