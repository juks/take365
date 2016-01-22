function commentAction(mode, id) {
    if(!mode) mode = 'add';

    if(mode == 'add' || mode == 'preview') {
        var text = document.forms['commentForm'].commentText.value;

        if(!text) {
            notice('Пожалуйста, введите текст комментария', true);
            return false;
        }

        var formData = $('commentForm').toQueryString();
        var data = $('commentForm').toQueryString();

        if (mode == 'add') var url = "/ajax/comments/add/"; else var url = "/ajax/comments/preview/";
    }

    if(mode == 'delete') {
        var url = "/ajax/comments/delete/"
        var data = 'id=' + id;
    }

    commentControlsBlock(true);

    var jsonRequest = new Request.JSON({url: url, data: data, onSuccess: function(result) {
        if(!result['errors']) {
            // New comment added
            if(mode == 'add') {
                moveFormTo();

                insertComment(result['comment']);

                cc = parseInt(result['cc']);
                var targetId = document.forms['commentForm'].targetId.value;

                // Updating comments count
                emptySign = $('noComments');
                rootHolder = $('ch' + targetId);
                holder = $('cc' + targetId);

                if(holder) holder.innerHTML = cc;
                if(rootHolder) rootHolder.removeClass('hidden');
                if(emptySign) emptySign.addClass('hidden');

                var FXScroll = new Fx.Scroll(window, {
                    wait: false,
                    duration: 1000,
                    offset: {x: 0, y: -150},
                    transition: Fx.Transitions.Quart.easeInOut
                });

                FXScroll.toElement('commentHolder' + result['comment']['id']);

                // Comment preview
            } else if(mode == 'preview') {
                $('commentPreview').removeClass('hidden');
                if(result['comment'] && result['comment']['body']) $('commentPreview').innerHTML = result['comment']['body'];
                // Comment delete
            } else if(mode == 'delete') {
                if(result['status']) {
                    $('commentBody' + result['id']).addClass('deletedCommentMine');
                    $('commentControl' + result['id']).setProperties({'html':'¤', 'title': 'Восстановить комментарий'});
                } else {
                    $('commentBody' + result['id']).removeClass('deletedCommentMine');
                    $('commentBody' + result['id']).addClass('myComment');
                    $('commentControl' + result['id']).setProperties({'html':'×', 'title': 'Удалить комментарий'});
                }
            }
        } else {
            for(var i=0; i<result['errors'].length; i++) notice(result['errors'][i]['value'], 1);
        }

        commentControlsBlock(false);
    }, onFailure: function() {
        logger('Ошибка при получении данных!');
    }}).post();
}

function insertComment(data) {
    var comment = data['body'];
    var commentId = parseInt(data['id']);
    var parentId = parseInt(data['parentId']);

    if(!comment || !commentId) return false;

    if(parentId) holder = $('commentHolder' + parentId); else holder = $('commentsBlock');
    holder.innerHTML = holder.innerHTML + comment;
}

function moveFormTo(commentId) {
    if(commentId) {
        holder = $('replyPlace' + commentId);
        // Updating form parameters
        document.forms['commentForm'].parentId.value = commentId;
    } else {
        holder = $('bottomPlace');
        // Updating form parameters
        document.forms['commentForm'].commentText.value = '';
        document.forms['commentForm'].parentId.value = 0;
    }

    // Moving the form
    form = $('commentFormHolder');
    holder.appendChild(form);

    $('commentPreview').addClass('hidden');
    $('commentPreview').innerHTML = '';

    commentControlsBlock(false);

    if(commentId) document.forms['commentForm'].commentText.focus();
}

function commentControlsBlock(m) {
    if(m) {
        $('commentText').addClass('ajaxProgress');
    } else {
        $('commentText').removeClass('ajaxProgress');
    }

    document.forms['commentForm'].commentSubmitButton.disabled = m;
    document.forms['commentForm'].commentPreviewButton.disabled = m;
}