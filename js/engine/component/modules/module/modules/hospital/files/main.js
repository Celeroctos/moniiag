misEngine.class('component.module.hospital', function() {
	return {
        comissionGrid : null,
        queueGrid : null,
        historyGrid : null,
        hospitalizationGrid : null,
        comissionGridModal : null,
        medicalExamModal : null,
        currentDateTimepicker : null,
        tabmarks : [],
        activeTab : null,
		config : {
			name : 'hospital'
		},

		displayDatetimepickers : function() {
			this.currentDateTimepicker = misEngine.create('component.datetimepicker');
			this.currentDateTimepicker
				.setConfig({
					widget : { // Property "widget" uses if component includes other component, in external libs
						language: 'ru',
						format: 'yyyy-mm-dd',
						weekStart: 1,
						todayBtn:  1,
						autoclose: 1,
						todayHighlight: 1,
						startView: 2,
						minView: 2,
						forceParse: 0,
						selector : '#sideCalendar'
					}
				})
				.render()
				.hide()
                .on();
		},

        displayTabmarks : function() {
            this.tabmarks = [
                misEngine.create('component.tabmark', {
                    selector : '#queueTabmark',
                    renderConfig : {
                        mode : 'ajax',
                        ajaxConf : {
                            url : '/hospital/components/tabmark',
                            data : {
                                serverModel : 'QueueGrid'
                            },
                            type : 'GET',
                            dataType : 'json',
                            success : function(data, status, jqXHR) {
                                if(data.success) {
                                    $('#queueTabmark .roundedLabelText').text(data.num);
                                }
                            },
                            error: function(jqXHR, status, errorThrown) {
                                misEngine.t(jqXHR, status, errorThrown);
                            }
                        }
                    }
                }),
                misEngine.create('component.tabmark', {
                    selector : '#comissionTabmark',
                    renderConfig : {
                        mode : 'ajax',
                        ajaxConf : {
                            url : '/hospital/components/tabmark',
                            data : {
                                serverModel : 'ComissionGrid'
                            },
                            type : 'GET',
                            dataType : 'json',
                            success : function(data, status, jqXHR) {
                                if(data.success) {
                                    $('#comissionTabmark .roundedLabelText').text(data.num);
                                }
                            },
                            error: function(jqXHR, status, errorThrown) {
                                misEngine.t(jqXHR, status, errorThrown);
                            }
                        }
                    }
                }),
                misEngine.create('component.tabmark', {
                    selector : '#hospitalizationTabmark',
                    renderConfig : {
                        mode : 'ajax',
                        ajaxConf : {
                            url : '/hospital/components/tabmark',
                            data : {
                                serverModel : 'HospitalizationGrid'
                            },
                            type : 'GET',
                            dataType : 'json',
                            success : function(data, status, jqXHR) {
                                if(data.success) {
                                    $('#hospitalizationTabmark .roundedLabelText').text(data.num);
                                }
                            },
                            error: function(jqXHR, status, errorThrown) {
                                misEngine.t(jqXHR, status, errorThrown);
                            }
                        }
                    }
                }),
                misEngine.create('component.tabmark', {
                    selector : '#historyTabmark',
                    renderConfig : {
                        mode : 'ajax',
                        ajaxConf : {
                            url : '/hospital/components/tabmark',
                            data : {
                                serverModel : 'HistoryGrid'
                            },
                            type : 'GET',
                            dataType : 'json',
                            success : function(data, status, jqXHR) {
                                if(data.success) {
                                    $('#historyTabmark .roundedLabelText').text(data.num);
                                }
                            },
                            error: function(jqXHR, status, errorThrown) {
                                misEngine.t(jqXHR, status, errorThrown);
                            }
                        }
                    }
                })
            ];

            $(this.tabmarks).each(function(index, element) {
                $(element).trigger('show');
            });
        },
		
		displayGrids : function() {
			this.displayQueueGrid();
			this.displayComissionGrid();
			this.displayHospitalizationGrid();
			this.displayHistoryGrid();
		},
		
		displayQueueGrid : function() {
			this.queueGrid = misEngine.create('component.grid');
			var queueGridRequestData = { 
				returnAsJson : true,
				id : 'queueGrid',
                serverModel : 'QueueGrid',
                container : '#queue'
			};
			
			this.queueGrid
				.setConfig({
					renderConfig : {
						mode : 'ajax',
						ajaxConf : {
							url : '/hospital/components/grid',
							data : queueGridRequestData,
							dataType : 'json',
							success : function(data, status, jqXHR) {
								if(data.success) {
									$(queueGridRequestData.container)
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
		
		displayComissionGrid : function() {
			this.comissionGrid = misEngine.create('component.grid');
			var comissionGridRequestData = {
				returnAsJson : true,
				id : 'comissionGrid',
                serverModel : 'ComissionGrid',
                container : '#comission'
			};
			this.comissionGrid
				.setConfig({
					renderConfig : {
						mode : 'ajax',
						ajaxConf : {
							url : '/hospital/components/grid',
							data : comissionGridRequestData,
							dataType : 'json',
                            type : 'GET',
							success : function(data, status, jqXHR) {
								if(data.success) {
									$(comissionGridRequestData.container).css({
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
                .bindEvents({
                    'editComissionDate' : function(e) {
                        alert('editComissionDate event triggered');
                    }
                })
                .render()
                .on();
        },
		
		displayHospitalizationGrid : function() {
			this.hospitalizationGrid = misEngine.create('component.grid');
			var hospitalizationGridRequestData = { 
				returnAsJson : true,
                serverModel : 'HospitalizationGrid',
                container : '#hospitalization',
                id : 'hospitalizationGrid'
			};
			
			this.hospitalizationGrid
				.setConfig({
					renderConfig : {
						mode : 'ajax',
						ajaxConf : {
							url : '/hospital/components/grid',
							data : hospitalizationGridRequestData,
							dataType : 'json',
							success : function(data, status, jqXHR) {
								if(data.success) {
									$('#hospitalization')
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
		
		displayHistoryGrid : function() {
			this.historyGrid = misEngine.create('component.grid');
			var historyGridRequestData = { 
				returnAsJson : true,
				id : 'historyGrid',
                serverModel : 'HistoryGrid',
                container : '#history'
			};
			this.historyGrid
				.setConfig({
					renderConfig : {
						mode : 'ajax',
						ajaxConf : {
							url : '/hospital/components/grid',
							data : historyGridRequestData,
							dataType : 'json',
							success : function(data, status, jqXHR) {
								if(data.success) {
									$('#history')
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

        getQueueModel : function() {
            var model = misEngine.create('component.model').setConfig({
                columns : [
                    {
                        name: 'id',
                        type: 'raw'
                    }
                ]
            });
            return model;
        },

        getHospitalizationModel : function() {
            var model = misEngine.create('component.model').setConfig({
                columns : [
                    {
                        name : 'id',
                        type : 'raw'
                    }
                ]
            });
            return model;
        },

        getHistoryModel : function() {
            var model = misEngine.create('component.model').setConfig({
                columns : [
                    {
                        name : 'id',
                        type : 'raw'
                    }
                ]
            });
            return model;
        },

        renderModals : function() {
            this.getComissionModal();
            this.getMedicalExamModal();
        },

        getComissionModal : function() {
            this.comissionGridModal = misEngine.create('component.modal').setConfig({
                selector : '#changeHospitalizationDatePopup',
                renderConfig : {
                    mode : 'internal'
                }
            }).render().on();
        },

        getMedicalExamModal : function() {
            this.medicalExamModal = misEngine.create('component.modal').setConfig({
                selector : '#MedicalExamPopup',
                renderConfig : {
                    mode : 'internal'
                }
            }).render().on();
        },

        bindHandlers : function() {
            $('#hospitalizationNavbar a').click(function (e) {
                e.preventDefault();
            });

            this.changeComissionDateHandler();
            this.openMedicalExamPopupHandler();
            this.changeCurrentDateHandler();
            this.changeTabHandler();
            this.reloadGridsHandler();
            return this;
        },

        /**
         * Handlers
         */
        changeComissionDateHandler : function() {
            var selector = '.changeHospitalizationDate'
            var comissionGridModal = this.comissionGridModal;
            $(document).on('click', selector, function(e) {
                var directionId = $(this).prop('id').substr(2);
                var gridId = $(this).parents('.grid-view').prop('id');
                $.ajax({
                    'url': '/hospital/hospitalization/getdirectiondata?id=' + directionId,
                    'cache': false,
                    'dataType': 'json',
                    'type': 'GET',
                    'success': function (data, textStatus, jqXHR) {
                        if(data.success) {
                            if(data.data.hospitalization_date) {
                                $('input[name="FormHospitalizationDateChange[hospitalization_date]"]').val(data.data.hospitalization_date);
                            }
                            $('input[name="FormHospitalizationDateChange[id]"]').val(directionId);

                            $('input[name="FormHospitalizationDateChange[grid_id]"]').val(gridId);
                            $(comissionGridModal).trigger('show');
                        }
                    }
                });
            });
        },

        openMedicalExamPopupHandler : function() {
            var selector = '.showPatient'
            var medicalExamModal = this.medicalExamModal;
            $(document).on('click', selector, function(e) {
                var directionId = $(this).prop('id').substr(2);
                var gridId = $(this).parents('.grid-view').prop('id');
                $(medicalExamModal).trigger('show');
            });
        },

        reloadGridsHandler : function() {
            var comissionGrid = this.comissionGrid;
            var queueGrid = this.queueGrid;
            var historyGrid = this.historyGrid;
            var hospitalizationGird = this.hospitalizationGrid;

            var gridModal = this.comissionGridModal;
            $(document).on('reload', '#queueGrid, #comissionGrid, #hospitalizationGrid, #historyGrid', function(e) {
                switch($(this).prop('id')) {
                    case 'comissionGrid':
                        comissionGrid.reloadGrid();
                        this.tabmarks[0].updateTabmark();
                    break;
                    case 'queueGrid':
                        queueGrid.reloadGrid();
                        this.tabmarks[1].updateTabmark();
                    break;
                    case 'hospitalizationGrid':
                        hospitalizationGird.reloadGrid();
                        this.tabmarks[2].updateTabmark();
                    break;
                    case 'historyGrid':
                        historyGrid.reloadGrid();
                        this.tabmarks[3].updateTabmark();
                    break;
                }
                $(gridModal).trigger('hide');
            });
        },

        changeCurrentDateHandler : function() {
            this.currentDateTimepicker
                .getWidget()
                .on('changeDate', $.proxy(function(e) {
                    var params = {
                        filter : {
                            hospitalization_date : e.date.getFullYear() + '-' + (e.date.getMonth() + 1) + '-' + e.date.getDate()
                        }
                    };
                    this.reloadTab(params);
                }, this));
        },

        changeTabHandler : function() {
            $('#queueTab, #comissionTab, #hospitalizationTab, #historyTab').on('shown.bs.tab', $.proxy(function(e) {
                this.activeTab = $(e.currentTarget).prop('id');
                $('#sideCalendar').css({
                    'display' : this.activeTab == 'queueTab' ? 'none' : 'block'
                });
                this.reloadTab();
            }, this));
        },


        /* End of handlers */

        reloadTab : function(params) {
            switch(this.activeTab) {
                case 'comissionTab' :
                    this.comissionGrid.reloadGrid();
                    this.tabmarks[0].updateTabmark();
                break;
                case 'queueTab' :
                    this.queueGrid.reloadGrid();
                    this.tabmarks[1].updateTabmark();
                break;
                case 'hospitalizationTab' :
                    this.hospitalizationGrid.reloadGrid();
                    this.tabmarks[2].updateTabmark();
                break;
                case 'historyTab' :
                    this.historyGrid.reloadGrid();
                    this.tabmarks[3].updateTabmark();
                break;
            }
        },

        renderHelpSystem : function() {
            var helpSystem = misEngine.create('component.helper', {
                file : '/js/engine/component/modules/helper/files/text/hospital.js',
                iconContainer : '#helperIcon'
            });
        },

        run : function() {
            this.displayDatetimepickers();
            this.displayGrids();
            this.displayTabmarks();
            this.renderHelpSystem();
            this.renderModals();
            this.bindHandlers();

            this.activeTab = 'queueTab'; // First opened tab in window
        },

        init : function() {
			return this;
		}
	};
});