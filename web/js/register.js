// JSLint defined globals

function register() {
	$.ajax('/register/submit/', {
		data: $('#registerForm').serialize(),
		dataType: 'json',
		type: 'post',
		success: function(result) {
			if(!result.errors) {
				$('#registrationSuccess').removeClass('hidden');
				$('#registrationFormHolder').addClass('hidden');
			} else {
				noticeErrors(result.errors);
			}
		},
		error: function() {
			notice('Произошла какая-то ошибка!');
		}
	});
}

function passwordRecover() {
	var email = $('#email').val();
	var captcha = $('#captcha').val();

	$.ajax('/register/recoverSubmit/', {
		data: { email: email, captcha: captcha },
		dataType: 'json',
		type: 'post',
		success: function(result) {
			if(!result.errors) {
				$('#recoverSuccess').removeClass('hidden');
				$('#recoverFormHolder').addClass('hidden');
			} else {
				noticeErrors(result.errors);
			}
		},
		error: function() {
			notice('Ошибка при получении данных!');
		}
	});

	return false;
}

function passwordRecoverUpdate() {
	var userId = $('#userId').val();
	var code = $('#code').val();
	var password = $('#password').val();

	$.ajax('/register/recoverPassword/', {
		data: { userId: userId, code: code, password: password },
		dataType: 'json',
		type: 'post',
		success: function(result) {
			if(!result.errors) {
				$('#updateSuccess').removeClass('hidden');
				$('#updateFormHolder').addClass('hidden');
			} else {
				noticeErrors(result.errors);
			}
		},
		error: function() {
			notice('Ошибка при получении данных!');
		}
	});

	return false;
}

// need set custom message
var ValidateCustom = function(value, paramsObj) {
	paramsObj = paramsObj || {};
	var against = paramsObj.against || function(){ return true; },
		args = paramsObj.args || {};
	if(!against(value, args)) { // start change
		Validate.fail(paramsObj.failureMessage || "Not valid!");
	} // end change
	return true;
};

var loginCustomParams = {
	against: function(value) {
		var is_valid = true;
		$.ajax('/register/checkLogin/', {
			async: false,
			data: "json=1&login=" + value,
			method: 'POST',
			success: function(data) {
				if (data.errors && data.errors.length) {
					loginCustomParams.failureMessage = data.errors.join(" ");
					is_valid = false;
				}
			}
		});
		return is_valid;
	},
	onlyOnBlur: true
};


var emailCustomParams = {
	against: function(value) {
		var is_valid = true;

		$.ajax('/register/checkEmail/', {
			async: false,
			data: 'json=1&email=' + value,
			method: 'POST',
			success: function(data) {
				if (data.errors && data.errors.length) {
					emailCustomParams.failureMessage = data.errors.join(' ');
					is_valid = false;
				}
			}
		});

		return is_valid;
	}
};

$(function() {

	new LiveValidation('login', {
			insertAfterWhatNode: 'loginLabel',
			validMessage: false
		})
		.add( Validate.Presence, { failureMessage: 'Пожалуйста, укажите имя' } )
		.add( Validate.Length, { minimum: 2, tooShortMessage: 'Минимальная длина учётной записи — 2 символa', onlyOnBlur: true } )
		.add( ValidateCustom, loginCustomParams )
		.add( Validate.Format, { pattern: /^[a-z0-9\-]/i, failureMessage: 'Недопустимые символы в имени учётной записи' } );

	new LiveValidation('email', {
			insertAfterWhatNode: 'emailLabel',
			validMessage: false,
			onlyOnBlur: true
		})
		.add( Validate.Email, { failureMessage: 'Указан недопустимый адрес электронной почты' } )
		.add( ValidateCustom, emailCustomParams )
		.add( Validate.Presence, { failureMessage: 'Пожалуйста, укажите адрес электронной почты', onlyOnBlur: false } );

	new LiveValidation('password', {
			insertAfterWhatNode: 'passwordLabel',
			validMessage: false
		})
		.add( Validate.Length, { minimum: 6, tooShortMessage: 'Минимальная длина пароля — 6 символов', onlyOnBlur: true } )
		.add( Validate.Presence, { failureMessage: 'Пожалуйста, введите пароль' } );

	new LiveValidation('captcha', {
			insertAfterWhatNode: 'captchaLabel',
			validMessage: false
		})
		.add( Validate.Presence, { failureMessage: 'Пожалуйста, введите код с картинки' } )
		.add( Validate.Length, { minimum: 6, tooShortMessage: 'Введён неверный код', onlyOnSubmit: true } );
});
