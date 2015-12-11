function FormBase() {}

FormBase.prototype.onSubmit = function(e) {
  e.preventDefault();

  var form = $(e.target);

  form.find('.error').removeClass('error');
  form.find('.error-message').remove();

  $.ajax(form.attr('action'), {
    data: form.serialize(),
    dataType: 'json',
    type: 'POST',
    context: this,
    success: function(result) {
      if (!result.errors) {
        this.success(form, result);
      } else {
        result.errors.forEach(function(error) {
          if (form[0][error.note]) {
            $(form[0][error.note])
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
      }
    },
    error: function() {
      notice('Ошибка получения данных с сервера.');
    }
  });
};

FormBase.prototype.success = function(form, result) {
  console.log('success method need reailize');
};

var AuthForm = new FormBase();

AuthForm.success = function(form, result) {
  document.location = result.redirect;
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

// Search a book inside the library
function doLogin(mode) {
	$('#errorBox').innerHTML = '&nbsp;';
	$('#loginInput').removeClass('errorBack');
	$('#passwordInput').removeClass('errorBack');

	var url, data;

	if(mode) {
		url = '/auth/login/';
		data = $('#loginForm').serialize();
	} else {
		url = '/auth/logout/';
		data = '';
	}

	$.ajax(url, {
	data: data,
	type: 'POST',
	success: function(result) {
		if(!result.errors) {
			if(result.redirect) {
				document.location = result.redirect;
			}
		} else {
			if(mode) {
				inputs = { login: '#loginInput', password: '#passwordInput' };

				$('#errorBox').html($.map(result.errors, function(error) {
					if(error.note && inputs[error.note]) {
						$(inputs[error.note]).addClass('errorBack');
					}
					return '<p>' + error.value + '</p>';
				}).join(''));
			}
		}
	},
	error: function() {
		notice('Ошибка получения данных с сервера!');
	}});
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
