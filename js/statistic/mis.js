$(document).ready(function() {
	// Фильтр по отделению
	/*$('#wardId, #medpersonalId').on('change', function() {
		$('#wardId, #medpersonalId').attr('disabled', true);
		$.ajax({
			'url' : '/index.php/guides/employees/getbywardandmedworker?wardid=' + $('#wardId').val() + '&medworkerid='  + $('#medpersonalId').val(),
			'cache' : false,
			'dataType' : 'json',
			'success' : function(data, textStatus, jqXHR) {
				if(data.success) {
					$('#wardId, #medpersonalId').attr('disabled', false);
					$('#doctorId option[value!="-1"]').remove();
					for(var i = 0; i < data.data.length; i++) {
						$('#doctorId').append($('<option>').attr('value', data.data[i].id).text(data.data[i].last_name + ' ' + data.data[i].first_name + ' ' + (data.data[i].middle_name == null ? '' : data.data[i].middle_name + ', ' + data.data[i].ward + ', ' + data.data[i].post + ', табельный номер ' + data.data[i].tabel_number)));
					}
				} else {
				
				}
			}
		});
	});*/
	
	function getFilters() {
        var Result =
        {
            'groupOp' : 'AND',
            'rules' : [
                {
                    'field' : 'ward_id',
                    'op' : 'in',
                    'data' :  $('#wardId').val()
                },
                {
                    'field' : 'medworker_id',
                    'op' : 'in',
                    'data' : $('#medpersonalId').val()
                },
				{
                    'field' : 'patient_day_from',
                    'op' : 'ge',
                    'data' : $('#filterGreetingDateFrom').val()
                },
				{
                    'field' : 'patient_day_to',
                    'op' : 'le',
                    'data' : $('#filterGreetingDateTo').val()
                }
            ]
        };

        return Result;
	}
	
	$('#greeting-getstat-submit').on('click', function() {
		var filters = getFilters();
		$(this).trigger('begin');
		$.ajax({
            'url' : '/index.php/statistic/mis/getstat/?filters=' + $.toJSON(filters),
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
					var data = data.data;
					var table = $('#greetingsStat');
					$('#greetingsStat').find('tr').remove();
					var tableOfHeaders = $('#greetingsStatHeaders');
					var numAllGreetings = 0;
					var closedGreetings = 0;
					var handworkGreetings = 0;
					
					for(var i in data) {
						var wardHeaderClone = $(tableOfHeaders).find('.wardHeaderRow').clone();
						$(wardHeaderClone).find('td').text(data[i].name);
						$(table).append(wardHeaderClone);
						
						for(var j in data[i].elements) {
							var medworkerHeaderClone = $(tableOfHeaders).find('.medworkerHeaderRow').clone();
							$(medworkerHeaderClone).find('td').text(data[i].elements[j].name);
							$(table).append(medworkerHeaderClone);
							
							var dataHeaderClone = $(tableOfHeaders).find('.dataHeader').clone();
							$(table).append(dataHeaderClone);
							var dataHeaderClone2 = $(tableOfHeaders).find('.dataHeader2').clone();
							$(table).append(dataHeaderClone2);
							
							for(var k in data[i].elements[j].elements) {
								$(table).append(
								'<tr>' 
									+ '<td class="text-danger bold">' + data[i].elements[j].elements[k].name + '</td>'
									+ '<td>' + data[i].elements[j].elements[k].data.numAllGreetings + '</td>'
									+ '<td>' + data[i].elements[j].elements[k].data.closedGreetings + '</td>'
									+ '<td>' + data[i].elements[j].elements[k].data.handworkGreetings + '</td>'
								+ '</tr>'
								);
								
								numAllGreetings += parseInt(data[i].elements[j].elements[k].data.numAllGreetings);
								closedGreetings += parseInt(data[i].elements[j].elements[k].data.closedGreetings);
								handworkGreetings += parseInt(data[i].elements[j].elements[k].data.handworkGreetings) ;
							}

							var medworkerFooterClone = $(tableOfHeaders).find('.medworkerFooterRow').clone();
							$(medworkerFooterClone).find('td:eq(1)').text(data[i].elements[j].numAllGreetings);
							$(medworkerFooterClone).find('td:eq(2)').text(data[i].elements[j].closedGreetings);
							$(medworkerFooterClone).find('td:eq(3)').text(data[i].elements[j].handworkGreetings);
							$(table).append(medworkerFooterClone);
						}
						
						var wardFooterClone = $(tableOfHeaders).find('.wardFooterRow').clone();
						$(wardFooterClone).find('td:eq(1)').text(data[i].numAllGreetings);
						$(wardFooterClone).find('td:eq(2)').text(data[i].closedGreetings);
						$(wardFooterClone).find('td:eq(3)').text(data[i].handworkGreetings);
						$(table).append(wardFooterClone);
					}
					
					if(numAllGreetings > 0) {
						var allFooterClone = $(tableOfHeaders).find('.allFooterRow').clone();
						$(allFooterClone).find('td:eq(1)').text(numAllGreetings);
						$(allFooterClone).find('td:eq(2)').text(closedGreetings);
						$(allFooterClone).find('td:eq(3)').text(handworkGreetings);
						
						$(table).append(allFooterClone);
					} else {
						$('#notFoundPopup').modal({
						});
					}					
				} else {
                    $('#errorSearchPopup .modal-body .row p').remove();
                    $('#errorSearchPopup .modal-body .row').append('<p>' + data.data + '</p>')
                    $('#errorSearchPopup').modal({

                    });
                }
				$('#greeting-getstat-submit').trigger('end');
                return;
			}
		});
	});
});