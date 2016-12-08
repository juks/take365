var currentSection = 'main';
var ProfileForm = new FormBase();

ProfileForm.success = function(form, result) {
	notice('Данные успешно обновлены');
};

var SecForm = new FormBase();

SecForm.success = function(form, result) {
	notice('Данные успешно обновлены');
};

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
