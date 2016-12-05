var Recover = new FormBase();
Recover.success = function(form) {
  form.html('<p>Письмо с инструкциями выслано на ваш почтовый адрес.</p>');
};

RecoverUpdate = new FormBase();

RecoverUpdate.success = function(form) {
  form.html('Ваш пароль изменён.');
};

RecoverUpdate._onPasswordTimeout;
RecoverUpdate.onPassword = function() {
  var form = document.forms.recoverForm;

  if (form.password[0].value !== form.password[1].value && form.password[1].value) {
    RecoverUpdate._onPasswordTimeout = setTimeout(function() {
      $(form.password[1])
        .after('<span class="error-message">Пароли не совпадают</span>')
        .parent().addClass('error');
    }, 200);

    return false;
  } else {
    RecoverUpdate.clearError();

    return true;
  }

  return false;
};

RecoverUpdate.clearError = function() {
  var form = document.forms.recoverForm;

  $(form.password[1].parentNode)
      .removeClass('error')
      .find('.error-message').remove();
}

$(function() {
  // удаляем запомненые пароли, чтобы были видны битки
  $('.remove-autocomplete-password').val('');
});
