// getting the variables from twig

// const ifp_confirmUrl = '{{ action }}';


$(document).ready(function () {
	setEventLst_confirm_on_click();
});


/**
 * sets event listener for confirm button click, this will send ajax request to confirm controller action and redirect to success page if success
 */
function setEventLst_confirm_on_click() {
	$('#button-confirm').on('click', function () {

		let element = this;

		$.ajax({
			url: ifp_confirmUrl,
			type: 'post',
			dataType: 'json',
			cache: false,
			beforeSend: function () {
				$(element).prop('disabled', true).addClass('loading');
			},
			complete: function () {
				$(element).prop('disabled', false).removeClass('loading');
			},
			success: function (json) {
				if (json['error']) {
					$('#alert').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-circle-exclamation"></i> ' + json['error'] + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
				} else if (json['redirect']) {
					location = json['redirect'];
				}
			},
			error: function (xhr, ajaxOptions, thrownError) {
				console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	})
}
