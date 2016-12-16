$('#postForm').submit(function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    $(this).prop('disabled', true);

    $.ajax('/api/panel/post-write', {
        data: $(this).serialize(),
        dataType: 'json',
        type: 'post',
        success: function(data) {
            notice('Запись сохранена!');
        },
        error: function(data) {
            noticeErrors(data);
        },
        complete: function() {
            $(this).prop('disabled', false);
        }
    });

    return false;
});

$('a.post-delete').click(function(e) {
    if (!confirm("Sure?")) return false;

    e.preventDefault();

    var id = $(this).data('id');

    $.ajax('/api/panel/post-delete', {
        data: {id: id},
        dataType: 'json',
        type: 'post',
        success: function(data) {
            notice('Запись удалена!');
            $('#post-holder-' + id).remove();
        },
        error: function(data) {
            noticeErrors(data);
        },
        complete: function() {
            $(this).prop('disabled', false);
        }
    });
});