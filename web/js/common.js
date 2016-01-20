function FormBase() {}

FormBase.prototype.onSubmit = function(e) {
  e.preventDefault();

  var form = $(e.target);

  form.find('.error').removeClass('error');
  form.find('.error-message').remove();

  $.ajax(form.attr('action'), {
    data: form.serialize(),
    type: 'POST',
    context: this,
    success: function(result) {
      this.success(form, result);
    },
    error: function(result) {
      if (result.status === 406) {
        var data = JSON.parse(result.responseText);
        data.errors.forEach(function(error) {
          if (form[0][error.field]) {
            $(form[0][error.field])
              .one('input', function() {
                $(this.parentNode)
                  .removeClass('error')
                  .find('.error-message').remove();
              })
              .after('<span class="error-message">' + error.value + '</span>')
              .parent().addClass('error');
          } else {
            noticeErrors(error);
            console.log('Not found input with name: ' + error.note);
          }
        });
      } else {
        notice('Ошибка получения данных с сервера.');
      }
    }
  });
};

FormBase.prototype.success = function(form, result) {
  console.log('success method need reailize');
};

var AuthForm = new FormBase();

AuthForm.success = function(form, result) {
  window.location = result.redirect;
};

var Auth = {};

Auth.close = function(e) {
  $('.auth-popup').hide();
};

Auth._onEsc = function(e) {
  if (e.keyCode === 27) {
    Auth.close();
    $(document).off('keyup', Auth._onEsc);
  }
};

Auth.open = function(e) {
  $('.auth-popup')
    .show()
    .find('input:first').focus();

  $(document).keyup(Auth._onEsc);
};

Auth.onClickRegister = function(e) {
  if (location.pathname === '/') {
    e.preventDefault();
    Auth.close();
    $('.content').moveTo(2);
  }
};

function logout() {
  $.ajax('/api/auth/logout', {
    type: 'POST',
    success: function(result) {
      window.location = result.redirect || document.location.href;
    },
    error: function(result) {
      notice('Ошибка получения данных с сервера!');
    }
  });
}

// Make popup
function popup(id, keepPosition) {
	if($(id).hasClass('hidden')) {
		$('#shadow').removeClass('hidden');
	} else {
		$('#shadow').addClass('hidden');
	}

	$(id).toggleClass('hidden');
	if (!keepPosition) {
		center(id);
	}

	return true;
}

// Yet another popup
function popup2(option) {
	var bodyMousedown,
		ret = {
			close: function() {
				if (option.close) { option.close(); }
				popup.destroy();
				$(document).off('mousedown', bodyMousedown);
			}
		},
		popup = $('<div/>', {
			html: '<div class="popup__box"><div class="popup__box__content"></div></div>',
			"class": "popup popup" + ( " " + option["class"] || "")
		}),
		content = popup[0].firstChild.firstChild;

	if (typeof option.content === "string") {
		content.innerHTML = option.content;
	} else {
		content.appendChild(option.content);
	}

	popup.mousedown(function(e) {
		e.stopPropagation();
	});

	bodyMousedown = function(e) {
		if (!e.wheel) {
			ret.close();
		}
	};
	$(document).mousedown(bodyMousedown);

	popup.appendTo(option.inject || document.body);

	popup.position({relativeTo: option.relativeTo});

	return ret;
}

function moveToCenter(id) {
	var maxWidth = $(window).width();
	var maxHeight = $(window).height();

	var width = $(id).width();
	var height = $(id).height();

	var moveToX = Math.round((maxWidth - width) / 2);
	var moveToY = Math.round((maxHeight - height) / 2);

	$(id)[0].style.left = moveToX + document.documentElement.scrollLeft + 'px';
	$(id)[0].style.top = moveToY + document.documentElement.scrollTop + 'px';
}

function notice(message, error) {
	if (!$('#noticeContainer').length) {
		var noticeLevel = $('<div/>', {
			'class': 'noticeLevel'
		}).appendTo('body');

		var holder = $('<div/>', {
			id: 'noticeHolder',
			'class': 'noticeHolder'
		}).appendTo(noticeLevel);

		var container = $('<div/>', {
			id: 'noticeContainer',
			'class': 'noticeContainer'
		}).appendTo(holder);
	}

  var left = '40%';
  // before redesign 2014 pages
  if ($('#headerRight').length) {
    left = $('#headerRight').offset().left - 320;
  }
  $('#noticeContainer').css('left', left);

  var className = 'popup-alert';
	if (!error) {
		className += ' success';
	}

	var noticeElem = $('<div/>', {
		'class': className,
		html: message
	})
	.prependTo('#noticeContainer');

	noticeElem.css({
		position: 'relative',
		top: 0,
		marginTop: -noticeElem.height(),
		opacity: 0
	})
	.animate({
		'margin-top': 0,
		'opacity': 1
	});

	// Hide the message
	setTimeout(function() {
		noticeElem.fadeOut(function() {
			noticeElem.remove();
		});
	}, 6000);
}

function noticeErrors(errors) {
	if (typeof errors === "string") {
		notice(errors, true);
		return;
	}
	$.each(errors, function(i, error) {
		if (error.value) {
			notice(error.value, true);
		} else {
			notice(error, true);
		}
	});
}

// production error logging
if (window.location.host === 'take365.org') {
	window.onerror = function(message, filename, lineno) {
		_gaq.push(['_trackEvent', 'JsErr', message, filename + ':' + lineno, null, true]);
	};
}
