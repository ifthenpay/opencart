
// getting the variables from twig

// const url_get_sub_entities = '{{ url_get_sub_entities }}';
// const url_clear_configuration = '{{ url_clear_configuration }}';
// const url_request_account = '{{ url_request_account }}';
// const url_refresh_accounts = '{{ url_refresh_accounts }}';
// const url_test_callback = '{{ url_test_callback }}';

// const entry_plh_sub_entity = '{{ entry_plh_sub_entity }}';
// const text_are_you_sure_clear = '{{ text_are_you_sure_clear }}';
// const text_are_you_sure_request_account = '{{ text_are_you_sure_request_account }}';
// const text_are_you_sure_refresh_accounts = '{{ text_are_you_sure_refresh_accounts }}';
// const text_are_you_sure_test_callback = '{{ text_are_you_sure_test_callback }}';

// const sub_entity_dynamic = '{{ sub_entity_dynamic }}';
// const ifp_lbl_sub_entity_dynamic = '{{ lbl_sub_entity_dynamic }}';







$(document).ready(function () {

	setEventLst_multibancoEntityChange();
	setEventLst_clear_config_on_click();
	setEventLst_request_account_on_click();
	setEventLst_internal_refresh_accounts_on_click();
	setEventLst_test_callback_on_click();
});


/**
 * Sets event listener for multibanco entity change, to update subentity select by ajax to controller
 */
function setEventLst_multibancoEntityChange() {

	$('#input-entity').on('change', function (e) {
		e.preventDefault();

		$("#entity_spinner").show();

		let selectedEntity = $('#input-entity').val();

		$.ajax({
			url: url_get_sub_entities,
			dataType: 'json',
			data: {
				'entity': selectedEntity
			},
			success: function (json) {
				if (json['success']) {

					const containerSubEntidade = $("#input-sub-entity");

					// clean subEntities
					containerSubEntidade.find("option").remove();

					if (json['success'].length > 0) { // add first default option
						containerSubEntidade.append("<option value=''>" + entry_plh_sub_entity + "</option>");

						if (selectedEntity == sub_entity_dynamic) {
							$('#deadline_input_group').show();
							$('#lbl-sub-entity').text(ifp_lbl_sub_entity_dynamic);
						}
						else {
							$('#deadline_input_group').hide();
							$('#lbl-sub-entity').text(ifp_lbl_sub_entity);
						}
					}

					$.each(json['success'], function (index, subentity) {
						containerSubEntidade.append("<option value='" + subentity.value + "'>" + subentity.name + "</option>");
					});

				}
				$("#entity_spinner").hide();
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
}


function setEventLst_test_callback_on_click() {

	$('#test_callback').on('click', function (e) {
		e.preventDefault();


		if (confirm(text_are_you_sure_test_callback)) {



			let payload = {
				'reference': $('#input-reference').val() || '',
				'transaction_id': $('#input-transaction_id').val() || '',
				'amount': $('#input-amount').val() || ''
			};

			$("#test_callback_spinner").show();

			$.ajax({
				url: url_test_callback,
				dataType: 'json',
				data: payload,
				success: function (json) {
					if (json['success']) {
						alert(json['success']);
					}
					if (json['error']) {
						alert(json['error']);
					}

					$("#test_callback_spinner").hide();
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});
}

/**
 * Sets event listener for clear config button, to clear configuration by ajax to controller and reload page
 */
function setEventLst_clear_config_on_click() {

	$('#clear_config').on('click', function (e) {
		e.preventDefault();

		if (confirm(text_are_you_sure_clear)) {

			$("#clear_config_spinner").show();

			$.ajax({
				url: url_clear_configuration,
				dataType: 'json',
				success: function (json) {
					if (json['success']) {
						location.reload();
					}
					$("#clear_config_spinner").hide();
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});
}


function setEventLst_request_account_on_click() {

	$('#request_account').on('click', function (e) {
		e.preventDefault();

		if (confirm(text_are_you_sure_request_account)) {

			$("#request_account_spinner").show();

			$.ajax({
				url: url_request_account,
				dataType: 'json',
				success: function (json) {
					if (json['success']) {
						location.reload();
					}
					$("#request_account_spinner").hide();
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});
}


function setEventLst_internal_refresh_accounts_on_click() {

	$('#ifth_logo').on('click', function (e) {
		if (e.shiftKey && e.altKey) {

			if (confirm(text_are_you_sure_refresh_accounts)) {

				$.ajax({
					url: url_refresh_accounts,
					dataType: 'json',
					success: function (json) {
						if (json['success']) {
							location.reload();
						}
						if (json['error']) {
							alert(json['error']);
						}
					},
					error: function (xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		}
	});
}
