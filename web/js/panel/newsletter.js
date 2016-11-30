function submitTest() {
    $("#newsletterTest").prop('disabled', true);
    var form = document.forms.newsletterForm;

    $.ajax('/api/panel/newsletter-test', {
        data: {id: form.elements["newsletter-id"].value},
        dataType: 'json',
        type: 'post',
        success: function(data) {
            notice('Тест выполнен!');
        },
        error: function(data) {
            noticeErrors(data);
        },
        complete: function() {
            $("#newsletterTest").prop('disabled', false);
        }
    });

    return false;
};

function submitDeliver() {
    if (!confirm("Sure?")) return false;
    
    $("#newsletterDeliver").prop('disabled', true);
    var form = document.forms.newsletterForm;

    $.ajax('/api/panel/newsletter-deliver', {
        data: {id: form.elements["newsletter-id"].value},
        dataType: 'json',
        type: 'post',
        success: function(data) {
            notice('Рассылка отправлена!');
        },
        error: function(data) {
            noticeErrors(data);
        },
        complete: function() {
            $("#newsletterDeliver").prop('disabled', false);
        }
    });

    return false;
};