$(document).ready(function() {
	function AuthManager() {
		return {
			serverUrl : '/users/islogged',
			callbackIsLogged : null,
			setOptions : function(options) {
				for(var i in options) {
					if(this.hasOwnProperty(i)) {
						this[i] = options[i];
					}
				}
				return this;
			},
			isLogged : function() {
				if(this.callbackIsLogged == null || typeof this.callbackIsLogged != 'function') {
					console.log('Not found callback function in auth manager');
					return false;
				}
				$.ajax({
					'url' : this.serverUrl,
					'cache' : false,
					'dataType' : 'json',
					'type' : 'GET',
					'async' : 'true',
					'success' : $.proxy(this.callbackIsLogged, this)
				});
			}
		}
	}

	var completeModel = (function() {
		return {
			counter : 0,
			endcounter : 2,
			numInvokers : 0,
			action : null,
			showModel : function() {
				if(++this.counter == this.endcounter && typeof this.action == 'function') {
					this.action();
					$(this).trigger('reset');
				} 
				this.maybeResetInvokers(true);
			},
			
			setOptions : function(options) {
				for(var i in options) {
					if(this.hasOwnProperty(i)) {
						this[i] = options[i];
					}
				}
				return this;
			},
			
			incrementInvokers : function() {
				this.numInvokers++;
				this.maybeResetInvokers(false);
			},
			
			resetCounter : function() {
				this.counter = 0;
			},
			
			maybeResetInvokers : function(fromSuccess) {
				if(this.numInvokers >= this.endcounter) {
					this.numInvokers = 0;
					if(typeof fromSuccess == 'undefined' && !fromSuccess) {
						alert('Данные не сохранены, попробуйте ещё раз....');
					}
				}
			},
			
			init : function(options) {
				$(this).on('makethis', $.proxy(this.showModel, this));
				$(this).on('incinvoker', $.proxy(this.incrementInvokers, this));
				$(this).on('reset', $.proxy(this.resetCounter, this));
				this.setOptions(options)
				return this;
			}
		}
	})
	().init({
		action : function() {
			alert('Настройки успешно сохранены!');
		}
	});

	function Shedule() {
		var obj = {
			container : null,
			idOfComponent : null,
			url : null,
			saveSettingsUrl : null,
			ajaxLoaderWidth : 32,
			ajaxLoaderHeight : 32,
			filters : null, // default filters 
			ajaxLoadGif : false, // AjaxGif for loading
			loadedData : null, // Loaded data for page navigation
			currentPage : 0, // Current page in doctorsList
			perPage : 10, // How much rows in shedule page 
			fade : false, // Fade-effect by page navigation
			updateTimeout : 500, // Time for shedule updating
			ajaxLoadGif : null, // This is the link for generated gif
			theadLink : null, // This is the link for thead in shedule
			sortBy : 'last_name', // Sort by field..
			saveSettingsUrl : null, 
			setOptions : function(options) {
				for(var i in options) {
					if(this.hasOwnProperty(i)) {
						this[i] = options[i];
					}
				}
				return this;
			},
			getOption : function(optionKey, callback) {
				if(this.hasOwnProperty(optionKey)) {
					if(typeof callback == 'function') {
						return callback(this[optionKey]);
					} else {
						return this[optionKey];
					}
				}
				return null;
			},
			saveOptions : function() {
				$.ajax({
					'url' : this.saveSettingsUrl,
					'cache' : false,
					'dataType' : 'json',
					'data' : {
						'module' : 2,
						'values' : {
							'perPage' : this.getOption('perPage'),
							'updateTimeout' : this.getOption('updateTimeout'),
							'sortBy' : this.getOption('sortBy')
						}
					},
					'type' : 'GET',
					'async' : 'true',
					'success' : $.proxy(function(data, textStatus, jqXHR) {
						$(completeModel).trigger('incinvoker');
						if(data.success) {
							$(completeModel).trigger('makethis');
						}
					}, this),
					'error' : function(jqXHR, status, errorThrown) {
						$(completeModel).trigger('incinvoker');
					}
				});
			},
			getCurrentDays : function() {
				var days = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
				var months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
				var dayData = [];
				var currentMonth = (new Date()).getMonth();
				var prev = null;
				if(this.loadedData.length > 0) {
					for(var i in this.loadedData[0].shedule) {
						if(prev && prev > parseInt(this.loadedData[0].shedule[i].day)) {
							// Get next month...
							currentMonth = (currentMonth + 1) % 11;
						}
						prev = parseInt(this.loadedData[0].shedule[i].day);
						dayData.push({
							'dayname' : days[this.loadedData[0].shedule[i].weekday],
							'day' : prev,
							'monthname' : months[currentMonth]
						});
					}
				}
				return dayData;
			},
			getAjaxGif : function() {
				return $('<img>').prop({
					'src': '/images/ajax-loader.gif',
					'width': this.ajaxLoaderWidth,
					'height': this.ajaxLoaderHeight,
					'alt': 'Загрузка...'
				});
			},
			getCurrentDoctorsList : function() {
				return this.createRequest(false);
			},
			/* This method requests all doctors with their shedule per filters */
			createRequest : function(async) {
				// Create ajaxgif loader 
				if(this.ajaxLoadGif) {
					$(this.container).prepend(
						this.ajaxLoadGifLink = $(this.getAjaxGif()).css({
							'marginLeft' : '50%',
							'marginBottom' : '10px'
						})
					);
				}
				
				if(typeof async == 'undefined') {
					async = true;
				}
				if(this.url == null) {
					console.log("Not found shedule URL. Script has broken, sorry for fucking hostkeeper, who doesn't setted script normally");
					return false;
				}
				if(this.filters == null) {
					console.log("Not found filters... Default too");
					return false;
				}
				
				$.ajax({
					'url' : this.url,
					'cache' : false,
					'dataType' : 'json',
					'data' : {
						'filters' : $.toJSON(this.filters),
						'sidx' : this.sortBy,
						'rows' : 15,
						'page' : 1,
						'sord' : 'desc'
					},
					'type' : 'GET',
					'async' : async,
					'success' : $.proxy(this.parseResponse, this)
				});
			},
			
			parseResponse : function(inData) {
				var preparedData = [];
				if(inData.success) {
					this.loadedData = inData.data.shedule.data;
					
					var settings = inData.data.settings;

					// Настройки для расписания
					this.setOptions({
						'perPage' : parseInt(settings.perPage),
						'updateTimeout' : parseInt(settings.updateTimeout),
						'sortBy' : settings.sortBy
					});

					// Настройки для бегущей строки
					marquee.setOptions({
						'updateTimeout' : parseInt(settings.mUpdateTimeout),
						'text' : settings.text
					});
					
					
					if(this.ajaxLoadGif && this.ajaxLoadGifLink != null) {
						$(this.ajaxLoadGifLink).remove();
					}
					
					$(this.container).prepend(
						$('<table>').addClass('table').append(
							this.theadLink = this.getHeader(), 
							this.prepareData(this.getFirstPage())
						).prop('id', this.idOfComponent != null ? this.idOfComponent : 'sheduleTable')
					);
					this.setUpdateTimer();
				} 
			},
			
			getFirstPage : function() {
				this.currentPage = 0;
				return this.getPage(this.currentPage);
			},
			
			getPage : function(page) {
				return this.loadedData.slice(this.currentPage * this.perPage, this.currentPage * this.perPage + this.perPage); 
			},
			
			getNextPage : function() {
				if((this.perPage * this.currentPage + this.perPage) >= this.loadedData.length) {
					this.currentPage = 0;
				} else {
					this.currentPage++;
				}
				return this.getPage(this.currentPage);
			},

			prepareData : function(doctors) {
				var tbody = $('<tbody>');

				for(var i = 0; i < doctors.length; i++) {
					var newTr = $('<tr>').addClass('sheduleTr');
					var fioTd, cabTd;
					var fio = doctors[i].last_name + ' ' + doctors[i].first_name + (doctors[i].middle_name == null ? '' : ' ' + doctors[i].middle_name);
					
					$(newTr).append(
						fioTd = $('<td>').addClass('col-xs-3').append(fio),
						postTd = $('<td>').addClass('col-xs-1').append($('<span>').addClass('profession').text(doctors[i].post.toUpperCase())),
						cabTd = $('<td>').addClass('col-xs-1').text(doctors[i].cabinet)
					);

					for(var j = 0; j < doctors[i].shedule.length; j++) {
						if(typeof doctors[i].shedule[j].beginTime != 'undefined' && typeof doctors[i].shedule[j].endTime != 'undefined') {
							$(newTr).append(
								$('<td>').text(doctors[i].shedule[j].beginTime + ' - ' + doctors[i].shedule[j].endTime)
							);
						} else {
							$(newTr).append(
								$('<td>').css({
									'background' : '#f2f5f6'
								})
							);
						}
					}
					$(tbody).append(newTr);
				}
				return tbody;
			},
			getHeader : function() {
				var thead = $('<thead>');
				var days = this.getCurrentDays();
				var headTr;
				$(thead).append(
					headTr = $('<tr>').append(
						$('<td>').addClass('col-xs-3').text('Врач'),
						$('<td>').addClass('col-xs-1').text('Специальность'),
						$('<td>').addClass('col-xs-1').text('Каб.')
					)
				)
				for(var i = 0; i < days.length; i++) {
					var newTd = $('<td>').html(days[i].dayname + '<br />' + days[i].day + ' ' + days[i].monthname).appendTo(headTr);
				}
				return thead;
			},
			display : function() {
				if(this.container == null) {
					return 'Container for table not found...';
				}
				this.reload();
			},
			setUpdateTimer : function() {
				setTimeout($.proxy(function() {
					if(this.fade) {
						$(this.container).find('tbody tr').fadeOut(700, function() {
							$(this.container).find('tbody tr').remove();
						});
					} else {
						$(this.container).find('tbody tr').remove();
					}
					this.goToNextPage();
				}, this), this.updateTimeout);
			},
			
			reload : function() {
				this.createRequest();
			},
			
			goToNextPage : function() {
				$(this.container).find('table').append(
					this.prepareData(this.getNextPage())
				);
				this.setUpdateTimer();
			},
		}

		return obj;
	}
	
	
	var sheduleTable = new Shedule();
	sheduleTable.setOptions({
		container : $('#sheduleRow'),
		idOfComponent : 'publicSheduleTable',
		url : '/reception/doctors/getpublicshedule',
		saveSettingsUrl : '/settings/system/settingsjsonedit',
		ajaxLoadGif : true,
		fade : false,
		updateTimeout : 3000, 
		perPage : 9,
		filters : {
			"groupOp" : "AND",
			"rules" : [
				{
					"field" : "ward_code",
					"op" : "eq",
					"data" : "-1"
				},
				{
					"field" : "post_id",
					"op" : "eq",
					"data" : "-1"
				},
				{
					"field" : "greeting_type",
					"op" : "eq",
					"data" : "0"
				},
				{
					"field" : "middle_name",
					"op" : "cn",
					"data" : ""
				},
				{
					"field" : "last_name",
					"op" : "cn",
					"data": ""
				},
				{
					"field" : "first_name",
					"op" : "cn",
					"data" : ""
				},
				{
					"field" : "diagnosis",
					"op" : "in",
					"data" : []
				}
			]
		}
	}).display();
	
	
	function Marquee() {
		return {
			updateTimeout : 70, // Timeof updating
			currentMargin : 0,
			step : 0.15,
			text : 'Заданный текст строки...',
			saveSettingsUrl : null,
			setOptions : function(options) {
				for(var i in options) {
					if(this.hasOwnProperty(i)) {
						this[i] = options[i];
					}
				}
				return this;
			},
			getOption : function(optionKey, callback) {
				if(this.hasOwnProperty(optionKey)) {
					if(typeof callback == 'function') {
						return callback(this[optionKey]);
					} else {
						return this[optionKey];
					}
				}
				return null;
			},
			saveOptions : function() {
				if(this.saveSettingsUrl == null) {
					console.log("Can't save settings");
					return false;
				}
				$.ajax({
					'url' : this.saveSettingsUrl,
					'cache' : false,
					'dataType' : 'json',
					'data' : {
						'module' : 3,
						'values' : {
							'text' : this.getOption('text'),
							'mUpdateTimeout' : this.getOption('updateTimeout')
						}
					},
					'type' : 'GET',
					'async' : 'true',
					'success' : $.proxy(function(data, textStatus, jqXHR) {
						$(completeModel).trigger('incinvoker');
						if(data.success) {
							$(completeModel).trigger('makethis');
						}
					}, this),
					'error' : function(jqXHR, status, errorThrown) {
						$(completeModel).trigger('incinvoker');
					}
				});
			},
			go : function() {
				if(this.hasOwnProperty('text') && this.hasOwnProperty('text') != null) {	
					$('.marquee span').text(this.text);
				}
				setTimeout($.proxy(function() {
					this.makeStep();
					this.go();
				}, this), this.updateTimeout);
				return this;
			},
			makeStep : function() {
				if(this.currentMargin > 100) {
					this.currentMargin = 0;
				} else {
					this.currentMargin += this.step
				}
				$('.marquee span').css({
					'marginLeft' : this.currentMargin + '%'
				});
			},
			init : function() {
			
			
			}
		};
	}
	
	var marquee = new Marquee().setOptions({
		updateTimeout : 40,
		saveSettingsUrl : '/settings/system/settingsjsonedit'
	}).go();
	
	
	function SettingsWindow(options) {
		return {
			iconContainer : null,
			settingsPopover : null,
			submitButton : null,
			form : null,
			settingsFormElements : {},
			setOptions : function(options) {
				for(var i in options) {
					if(this.hasOwnProperty(i)) {
						this[i] = options[i];
					}
				}
				return this;
			},
			init : function() {
				if(this.iconContainer == null) {
					console.log('Not found icon container');
					return false;
				}
				$(this.iconContainer)
					.on('mouseover', $.proxy(this.showSettingsIcon, this))
					.on('mouseout', $.proxy(this.hideSettingsIcon, this))
					.on('click', $.proxy(this.openWindow, this));
				return this;
			},
			showSettingsIcon : function(e) {
				$(this.iconContainer).animate({
					'opacity' : 1
				}, 200);
			},
			
			hideSettingsIcon : function(e) {
				if(this.settingsPopover != null) {
					return false;
				}
				$(this.iconContainer).animate({
					'opacity' :  0
				}, 200);
			},
			
			openWindow : function(e) {
				if(this.settingsPopover != null) {
					this.settingsPopover.popover('show');
					return true; 
				}
			
			    this.settingsPopover = $(this.iconContainer).popover({
					animation: true,
					html: true,
					placement: 'left',
					title: 'Настройки табло',
					delay: {
						show: 100,
						hide: 100
					},
					container: $(this.iconContainer),
					content: $.proxy(function() {
						return this.createSettingsForm();
					}, this)
				}).on('hidden.bs.popover', $.proxy(function(e) {
					this.settingsPopover = null;
					$(this.iconContainer).trigger('mouseout');
				}, this));
						
				this.settingsPopover.popover('show');
				
				var span = $('<span class="glyphicon glyphicon-remove" title="Закрыть окно"></span>').css({
					marginLeft: '470px',
					position: 'absolute',
					cursor: 'pointer'
				});

				$(span).on('click', $.proxy(function(e) {
					this.settingsPopover.popover('destroy');
				}, this));
				
				$(this.iconContainer).find('.popover').css({
					'top' : '3px',
					'left' : '-500px',
					'minWidth' : '500px',
				}).append(span);
				
				$(this.iconContainer).find('.popover-content').css({
					'fontSize' : '15px',
					'minWidth' : '500px',
				});

				$(this.iconContainer).find('.popover .arrow').remove();
				
				$(this.iconContainer).find('.popover-title').css({
					'fontSize' : '16px',
					'fontWeight' : 'bold'
				});
				
				$(this.iconContainer).on('click', '.popover', function(e) {
					return false;
				});
			},
			
			createSettingsForm : function() {
				this.form = $('<form>').addClass('form-horizontal col-xs-12').prop({
					'role' : 'form'
				});
				this.form.on('keydown', $.proxy(function(e) {
					if(e.keyCode == 13) {
						this.submitButton.trigger('click');
					}
				}, this));

				$(this.form).append(
					$('<div>').addClass('form-group').append(
						$('<label>').addClass('col-xs-5').text('Отображать строк'),
						$('<div>').addClass('col-xs-2').append(
							this.settingsFormElements.perPage = $('<input>').addClass('form-control').prop({
								'type' : 'textfield',
								'value' : sheduleTable.getOption('perPage')
							})
						)
					), 
					$('<div>').addClass('form-group').append(
						$('<label>').addClass('col-xs-5').text('Таймаут между страницами (секунд)'),
						$('<div>').addClass('col-xs-2').append(
						this.settingsFormElements.updateTimeout = $('<input>').addClass('form-control').prop({
								'type' : 'textfield',
								'value' : sheduleTable.getOption('updateTimeout', function(value) {
									return value / 1000; // Секунды
								})
							})
						)
					),
					$('<div>').addClass('form-group').append(
						$('<label>').addClass('col-xs-5').text('Сортировать по'),
						$('<div>').addClass('col-xs-7').append(
							this.settingsFormElements.sortBy = $('<select>').addClass('form-control').append(
								$('<option>').prop({
									'value' : 'last_name'
								}).text('ФИО')
							).val(sheduleTable.getOption('sortBy'))
						)
					),
					$('<div>').addClass('form-group').append(
						$('<label>').addClass('col-xs-5').text('Текст бегущей строки'),
						$('<div>').addClass('col-xs-7').append(
							this.settingsFormElements.text = $('<input>').addClass('form-control').prop({
								'type' : 'textfield',
								'value' : marquee.getOption('text')
							})
						)
					),
					$('<div>').addClass('form-group').append(
						$('<label>').addClass('col-xs-5').text('Скорость бегущей строки (мс)'),
						$('<div>').addClass('col-xs-2').append(
							this.settingsFormElements.mUpdateTimeout = $('<input>').addClass('form-control').prop({
								'type' : 'textfield',
								'value' : marquee.getOption('updateTimeout')
							})
						)
					),
					$('<div>').addClass('form-group').append(
						$('<div>').addClass('col-xs-2').append(
							this.submitButton = $('<input>').addClass('btn btn-success').prop({
								'value' : 'Сохранить',
								'type' : 'button'
							}).on('click', $.proxy(this.saveSettings, this))
						)
					)
				);
				return this.form;
			},
			
			saveSettings : function(e) {
				if(isNaN(parseInt(this.settingsFormElements.updateTimeout.val() * 1000))) {
					alert('Неверно задан таймайт: введено не числовое значение!');
					return false;
				}
				
				if(isNaN(parseInt(this.settingsFormElements.perPage.val()))) {
					alert('Неверно задано количество результатов на страницу: введено не числовое значение!');
					return false;
				}
				
				if(isNaN(parseInt(this.settingsFormElements.mUpdateTimeout.val()))) {
					alert('Неверно задан таймайт: введено не числовое значение!');
					return false;
				}
				
				var authManager = new AuthManager();
				authManager.setOptions({
					'callbackIsLogged' : $.proxy(function(data, textStatus, jqXHR) {
						if(data.success) {	
							sheduleTable.setOptions({
								'updateTimeout' : parseInt(this.settingsFormElements.updateTimeout.val() * 1000),
								'perPage' : parseInt(this.settingsFormElements.perPage.val()),
								'sortBy' : this.settingsFormElements.sortBy.val()
							});
							sheduleTable.saveOptions();
							
							marquee.setOptions({
								'updateTimeout' : this.settingsFormElements.mUpdateTimeout.val(),
								'text' : this.settingsFormElements.text.val()
							});
							marquee.saveOptions();
							
							this.settingsPopover.popover('destroy');
						} else {
												
							var authWindow = new AuthWindow().setOptions({
								parentContainer : $(this.settingsPopover).find('.popover'),
								authManager : authManager
							}).init();

							authWindow.open();
						}
					}, this)
				}).isLogged();
			}
		}
	}
	
	var settingWindow = new SettingsWindow().setOptions({
		iconContainer : '#sheduleNavbar .glyphicon-cog'
	}).init();
	
	function AuthWindow() {
		return {
			authPopover : null,
			authManager : null, // Auth Manager for login control
			parentContainer : null,
			loginElement : null, // Textfield for login
			passwordElement : null, // Textfield for password
			serverUrl : '/users/login',
			form : null, // Form for login, DOM element
			setOptions : function(options) {
				for(var i in options) {
					if(this.hasOwnProperty(i)) {
						this[i] = options[i];
					}
				}
				return this;
			},
			
			createWindow : function() {
				if(this.authPopover != null) {
					this.authPopover.popover('show');
					return true; 
				}

			    this.authPopover = $(this.parentContainer).popover({
					animation: true,
					html: true,
					placement: 'left',
					title: 'Авторизация',
					delay: {
						show: 100,
						hide: 100
					},
					container: $(this.parentContainer),
					content: $.proxy(function() {
						return this.createAuthForm();
					}, this)
				}).on('hidden.bs.popover', $.proxy(function(e) {
					this.authPopover = null;
				}, this));
						
				this.authPopover.popover('show');
				
				var span = $('<span class="glyphicon glyphicon-remove" title="Закрыть окно"></span>').css({
					marginLeft: '570px',
					position: 'absolute',
					cursor: 'pointer'
				});

				$(span).on('click', $.proxy(function(e) {
					this.authPopover.popover('destroy');
				}, this));
				
				console.log($(this.parentContainer).find('.popover'));
				$(this.parentContainer).find('.popover').css({
					'top' : '3px',
					'left' : '-600px',
					'minWidth' : '600px',
				}).append(span);
				
				$(this.parentContainer).find('.popover .popover-content').css({
					'fontSize' : '15px',
					'minWidth' : '600px',
				});

				$(this.parentContainer).find('.popover .popover-title').css({
					'fontSize' : '16px',
					'fontWeight' : 'bold'
				});
				
				$(this.parentContainer).on('click', '.popover', function(e) {
					return false;
				});
				
				return this;
			},
			
			createAuthForm : function() {
				this.form = $('<form>').addClass('navbar-form col-xs-12').prop({
					'role' : 'form'
				});
				this.form.on('keydown', $.proxy(function(e) {
					if(e.keyCode == 13) {
						this.submitButton.trigger('click');
					}
				}, this));

				$(this.form).append(
					$('<div>').addClass('form-group').append(
						this.loginElement = $('<input>').addClass('form-control col-xs-2').prop({
							'type' : 'textfield',
							'name' : 'login',
							'id' : 'login',
							'placeholder' : 'Логин'
						})
					), 
					$('<div>').addClass('form-group').append(
						this.passwordElement = $('<input>').addClass('form-control col-xs-2').prop({
							'type' : 'password',
							'name' : 'password',
							'id' : 'password',
							'placeholder' : 'Пароль'
						})
					),
					$('<div>').addClass('form-group').append(
						this.submitButton = $('<input>').addClass('btn btn-success').prop({
							'value' : 'Войти',
							'type' : 'button',
							'id' : 'loginSubmit'
						}).on('click', $.proxy(this.tryLogin, this))
					)
				);
				
				return this.form;
			},
			
			tryLogin : function() {
				if(this.serverUrl == null) {
					console.log('Not found server url for login..');
					return false;
				}
				
				if($.trim(this.loginElement.val()) == '') {
					alert('Вы не ввели логин!');
					return false;
				}
				
				if($.trim(this.passwordElement.val()) == '') {
					alert('Вы не ввели пароль!');
					return false;
				}
				
				$.ajax({
					'url' : this.serverUrl,
					'cache' : false,
					'dataType' : 'json',
					'type' : 'POST',
					'async' : 'true',
					'data' : {
						'FormLogin' : {
							'login' : this.loginElement.val(),
							'password' : this.passwordElement.val()
						}
					},
					'success' : $.proxy(function(data, textStatus, jqXHR) {
						if(data.success == 'true') { // Sick!
							settingWindow.saveSettings(); // Second try to save settings..
						} else {
							alert('Пользователя с такой парой логин-пароль не существует!');
						}
					}, this)
				});
			
			},
			
			open : function() {
				this.createWindow();
			},
			
			init : function() {
				return this;
			},
		}
	}
	
	function RealtimeClock() {
		return {
			timeContainer : null,
			dateContainer : null,
			dateObj : new Date(), // Obj for getTime()
			state : 0, // For ":" 
			days : ['понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота', 'воскресенье'],
			months : ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'],
			go : function() {
				if(this.timeContainer == null || $(this.timeContainer).length == 0) {
					console.log('Not found clock container....');
				}
				if(this.dateContainer == null || $(this.dateContainer).length == 0) {
					console.log('Not found date container....');
				}
				setTimeout($.proxy(this.setCurrentDate, this), 950); 
			},
			setCurrentDate : function() {
				$(this.timeContainer).html(this.dateObj.getHours() + (this.state ? ':' : ' ') + this.dateObj.getMinutes());
				this.state = this.state ? 0 : 1;
				$(this.dateContainer).html('Сегодня ' + this.days[this.dateObj.getDay()] + ', ' + this.dateObj.getDate() + ' ' + this.months[this.dateObj.getMonth()] + ' ' + this.dateObj.getFullYear() + ' г.');
				setTimeout($.proxy(this.setCurrentDate, this), 950); 
			},
			setOptions : function(options) {
				for(var i in options) {
					if(this.hasOwnProperty(i)) {
						this[i] = options[i];
					}
				}
				return this;
			}
		};
	};
	
	var clock = new RealtimeClock().setOptions({
		'timeContainer' : '#timeCont',
		'dateContainer' : '#dateCont'
	}).go();
});