var Register = new FormBase();

Register.success = function(form, result) {
    form.html('<p>Регистрация завершена. Пожалуйста, подтвердите указанный адрес электронной почты, воспользовавшись отправленной по этому адресу ссылкой.</p>');
};