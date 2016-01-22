function deleteFacebookToken() {
	if(!confirm('Вы действительно желаете отвязать свой профиль в facebook?')) return null;

	$.ajax('/apps/facebook/userDelete/', {
		success: function(result) {
			if (!result.errors) {
				$('#facebookLoggedHolder').addClass('hidden');
				$('#facebookLoginHolder').removeClass('hidden');
			} else {
				noticeErrors(result.errors);
			}
		},
		error: function() {
			notice('Произошла какая-то ошибка!');
		}
	});
}
