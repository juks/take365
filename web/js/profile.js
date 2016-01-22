var profileUpdateTimer;
var currentSection = 'main';

function switchSection(name) {
	if(currentSection != name) {
		$('#' + currentSection + 'Link').removeClass('simple');
		$('#' + currentSection + 'Holder').addClass('hidden');

		$('#' + name + 'Link').addClass('simple');
		$('#' + name + 'Holder').removeClass('hidden');
		$('#mainSwHolder').trigger('show', name);

		currentSection = name;
	}
}

function passwordStrength() {
	var password = $('#password').value;
	var messageHolder = $('#ps');

	if(!password || password.length < 6) {
		messageHolder.innerHTML = '';
		messageHolder.addClass('hidden');
	} else {
		var strength = 0;

		if(password.match(/[a-zа-я]/) && password.match(/[A-ZА-Я]/) && password.match(/[0-9]/i) && password.match(/[!?@#$%^&*()_+\-.,\/"№;%:*(){}\[\]|<>~]/i)) {
			var strength = 3;
			var message = 'Ни в жизнь не отгадают!';
			messageHolder.setStyle('background-color', '#CC99CC');
		} else if(password.match(/[a-zа-я]/) && password.match(/[A-ZА-Я]/) && (password.match(/[0-9]/) || password.match(/[!@#$%^&*()_+\-.,.,\/"№;%:*(){}\[\]|<>~]/i))) {
			var strength = 2;
			var message = 'М-м-м… Неплохое сочетание!';
			messageHolder.setStyle('background-color', '#FFCC66');
		} else if(password.match(/[a-zа-я]/i) && (password.match(/[0-9]/i) || password.match(/[A-ZА-Я]/) || password.match(/[!@#$%^&*()_+\-.,.,\/"№;%:*(){}\[\]|<>~]/i))) {
			var strength = 1;
			var message = 'Пароль&nbsp;— ничего так, сойдёт';
			messageHolder.setStyle('background-color', '#99EE99');
		} else if(password.toLowerCase() == 'qwerty' || password.toLowerCase() == 'qweasdzxc' || password.toLowerCase() == 'helloworld' || password == '123456' || password == '654321' || password == pp.myLogin) {
			var strength = -1;
			var message = 'Такой вариант&nbsp;— отстой полнейший!';
			messageHolder.setStyle('background-color', '#FF0000');
		} else {
			var strength = 0;
			var message = 'Пароль&nbsp;— слабоват';
			messageHolder.setStyle('background-color', '#FFFFCC');
		}

		messageHolder.innerHTML = message;
		messageHolder.removeClass('hidden');
	}
}

function updateProfile(formName) {
	// Main form data
	document.forms[formName].submitButton.disabled = true;

	var data = $('#' + formName).serialize();

	profileUpdateTimer = setTimeout(function() {
		notice("Время ожидания ответа с сервера истекло. Попробуйте повторить попытку через некоторое время.");
		document.forms[formName].submitButton.disabled = false;
	}, 10000);


	$.ajax('/api/user/update-profile', {
	data: data,
	type: 'post',
	success: function(data) {
		notice('Данные успешно обновлены');

		clearTimeout(profileUpdateTimer);
		document.forms[formName].submitButton.disabled = false;
	},
	error: function() {
		noticeErrors("Произошла ошибка. Повторите сохранение.");

		clearTimeout(profileUpdateTimer);
		document.forms[formName].submitButton.disabled = false;
	}});
}

function addContact() {
	var id = $('#newContactId').val(),
		value = $('#newContactValue').val();

	if(id && value) {
		$.ajax("/ajax/contacts/", {
		data: {'action': 'add', 'id': id, 'value': value},
		dataType: 'json',
		type: 'post',
		success: function(result) {
			if(result.debug) {
				debugOutput(result.debug);
			}
			if(result.errors) {
				$.each(result.errors, function(i, error) {
					notice(error.value, true);
				});
			} else {
				if(result.item) {
					$('#userContacts')[0].innerHTML += result.item;
					$('#newContactValue').val('');
				}
			}
		},
		error: function() {
			noticeErrors('Ошибка добавление данных!', true);
		}});
	}
}

function deleteContact(id) {
	if(!id) return false;

	if(!confirm("В самом деле удалить этот контакт?")) return false;

	$.ajax("/ajax/contacts/", {
	data: {'action': 'delete', 'id': id},
	dataType: 'json',
	type: 'post',
	success: function(result) {
		if(result.errors) {
			$.each(result.errors, function(i, error) {
				notice(error.value, true);
			});
		} else {
			if(result.deleted) {
				smoothDelete('contact' + result.deleted);
			}
		}
	},
	error: function() {
		noticeErrors('Ошибка удаления данных!', true);
	}});
}

$(function() {
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

	var emailCustomParams = {
		against: function(value) {
			var is_valid = true;

			$.ajax("/api/user/check-email", {
			data: "email=" + value,
			async: false,
			success: function(data) {
				if (data.errors && data.errors.length) {
					emailCustomParams.failureMessage = data.errors.join(" ");
					is_valid = false;
				}
			}});

			return is_valid;
		}
	};

	window.validation = {};

	validation.email = new LiveValidation(document.getElementById('email'), {
			insertAfterWhatNode: "emailLabel",
			validMessage: " "
		})
		.add( Validate.Email, { failureMessage: "Указан недопустимый адрес электронной почты", onlyOnBlur: true } )
		//.add( ValidateCustom, emailCustomParams )
		.add( Validate.Presence, { failureMessage: "Пожалуйста, укажите адрес электронной почты" } );

	new LiveValidation(document.getElementById('password'), {
			insertAfterWhatNode: "passwordLabel",
			validMessage: " ",
			onlyOnBlur: true
		})
		.add( Validate.Length, { minimum: 6, tooShortMessage: "Минимальная длина пароля — 6 символов" } )
		.add( Validate.Presence, { failureMessage: "Пожалуйста, введите пароль" } );

	new LiveValidation(document.getElementById('password1'), {
			insertAfterWhatNode: "password1Label",
			validMessage: " "
		})
		.add( Validate.Presence, { failureMessage: "Пожалуйста, подтвердите пароль" } )
		.add( Validate.Confirmation, { match: "password", failureMessage: "Пароли не совпадают" } );


	validation.email.customError = function(message) {
		var span = document.createElement("span");
		var textNode = document.createTextNode(message);
		span.appendChild(textNode);
		this.validationFailed = true;
		this.displayMessageWhenEmpty = true;
		this.insertMessage(span);
		this.addFieldClass();
	};
});
