function inlineEdit(form) {
	form = $(form);
	if (!form.length) {
		return;
	}

	var editables = form.find('.editable'),
		editing = false;

	form[0].reset();

	function closeEdit() {
		editing = false;
		form
		.addClass('editable-not-editing')
		.removeClass('editable-editing');
	}

	form
	.find('.editable-cancel')
	.click(closeEdit);

	editables.click(function(){
		if (editing === true) {
			return;
		}
		editing = true;

		$(this)
		.find('.editable-input')
		.eq(0)
		.delay(0)
		.queue(function() {
			this.focus();
		});

		form
		.removeClass('editable-not-editing')
		.addClass('editable-editing');
	});

	form.submit(function(e) {
		$.ajax(form[0].action, {
		data: form.serialize(),
		dataType: 'json',
		type: 'post',
		success: function(data) {
			notice('Данные обновлены!');
			var result = data.result;

			['title'].forEach(function(key) {
				form[0][key].value = result[key];
				var editable = $(form[0][key]).closest('.editable');
				editable.find('.editable-text').text(result[key]);
				editable.find('.editable-placeholder').toggleClass('hidden', !!result[key]);
			});
		},
		error: function() {
			notice("Ошибка при отправке данных!", true);
		},
		complete: function() {
			closeEdit();
			form.find('.editable-submit').prop('disabled', false);
		}});

		form.find('.editable-submit').prop('disabled', true);
		e.preventDefault();
	});
}

function initStoriesIndex() {
	if(!$('.start-new-story').length) {
		return;
	}

	$('span.start-new-story').click(function() {
		$('#startNewStory').click();
	});

	[1, 2].forEach(function(i) {
		var today = new Date();
		var day = today.getDate();
		var month = today.getMonth()+1; //January is 0!
		var year = today.getFullYear();
		var dateString = year + '-' + month + '-' + day;

		var uploader = new plupload.Uploader({
				runtimes: "html5,html4",
				browse_button: "startNewStory" + i,
				max_file_size: "20mb",
				url: "/api/media/upload",
				flash_swf_url: "/js/plupload/plupload.flash.swf",
				silverlight_xap_url: "/js/plupload/js/plupload.silverlight.xap",
				filters: [
					{title : "Картинки", extensions : "jpeg,jpg,gif,png"}
				],
				multi_selection: false,
				multiple_queues: true,
				multipart: true,
				multipart_params: {
					date: dateString,
					targetId: 0,
					targetType: 2,
					mediaType: 'storyImage'
				}
			}),
			procces, proccesPercent, errorsNode;

		uploader.bind("FilesAdded", function(uploader, files) {
			//the timeout needed because the file isn't yet added to files collection of the uploader on some runtimes, and it has no files to upload
			setTimeout(function(){
				if (files[0].status !== plupload.FAILED) {
					procces = $('<div/>', {
						html: 'Загружается <span>0</span>%.',
						css: {
							position: 'absolute',
							top: 0,
							left: 0,
							visibility: 'visible'
						}
					});

					$('#startNewStoryWrap').css({
						position: 'relative',
						visibility: 'hidden'
					});

					procces.appendTo("#startNewStoryWrap");
					proccesPercent = procces.find("span");
					uploader.start();
				}
			}, 0);
		});

		uploader.bind("UploadProgress", function(uploader, file) {
			proccesPercent.html(file.percent);
		});

		uploader.bind("Error", function(uploader, error) {
			noticeErrors(error.message);
		});

		// for correct error (ex. 500)
		uploader.bind("UploadComplete", function() {
			$('#startNewStoryWrap').css('visibility', 'visible');
			procces.remove();
		});

		uploader.bind("FileUploaded", function(uploader, file, response) {
			response = $.parseJSON(response.response);
			if (response.redirect) {
				window.location = response.redirect;
			} else if (response.errors) {
				uploader.trigger("Error", {
					message: response.errors.join(" "),
					file: file
				});
			}
		});

		uploader.init();
	});
}

function initStory() {
	$("#userPhotos").on('click', '.story-img', function(e) {
		e.preventDefault();
		if (this.moved) {
			this.moved = false;
		} else {
			Photoview.show(e.currentTarget.parentNode.href);
		}
	});

	if (pp.canManage) {
		inlineEdit('#storyEditForm');
	}

	if (pp.canUpload) {
		var uploader = initStoryUploder();
		Story.uploader = uploader;

		StoryDragAndDrop.init({
			onDragstart: function() {
				uploader.pause();
			},
			onDragend: function() {
				uploader.play();
			}
		});

		$('.story-item.available').click(function() {
			var elem = $(this);
			if (elem.hasClass('upload')) {
				Story.openUpload(elem);
			}
		});

		$('#userPhotos').on('click', '.story-edit', function(e) {
			Story.winOpen($(e.target).closest('.story-item'));
		});
	}
}

function initStoryUploder() {
	// allow click for FF5 on link
	var origGetFeatures = plupload.runtimes.Html5.getFeatures,
		play = true;

	plupload.runtimes.Html5.getFeatures = function() {
		var ret = origGetFeatures();
		ret.canOpenDialog = true;
		return ret;
	};

	// появляется невидимый input[type=file], при нажатии на который обходится защита ie
	if (plupload.ua.ie) {
		var uploadUserPhotos = $('<div/>', {
			id: 'uploadUserPhotos',
			css: {
				position: 'absolute',
				left: -1000,
				zIndex: 1000
			}
		}).appendTo('body');

		$('#userPhotos').on('mouseenter', '.upload', function(e) {
			$('[target='+Story.uploader.id+'_iframe]').css({zIndex: 1000});

			var iUpload = $(this),
				offset = iUpload.offset();
			uploadUserPhotos
				.css({
					height: iUpload.height(),
					width: iUpload.width(),
					top: offset.top,
					left: offset.left
				});

			Story.setActive(iUpload.closest('.story-item'));
			Story.uploader.refresh();

			function mouseout(e) {
				if (!uploadUserPhotos.is(e.target) && !$(e.target).closest(iUpload).length) {
					$(document).off('mouseout', mouseout);
					uploadUserPhotos.css('left', '-1000px');
					Story.uploader.refresh();
					Story.clearActive();
				}
			}
			$(document).mouseout(mouseout);
		});
	}

	var name = "story",
		uploader = new plupload.Uploader({
			runtimes: "html5,html4",
			max_file_size: "20mb",
			url: "/api/media/upload",
			//resize: { width: 1500, height: 1500, quality: 80 },
			flash_swf_url: "/js/plupload/plupload.flash.swf",
			silverlight_xap_url: "/js/plupload/js/plupload.silverlight.xap",
			filters: [
				{title : "Картинки", extensions : "jpg,gif,png"}
			],
			browse_button: "uploadUserPhotos",
			drop_element: "userPhotos",
			multiple_queues: true,
			multipart: true,
			multipart_params: {
				targetId: pp.storyId,
				targetType: pp.targetType,
				mediaType: pp.mediaType
			}
		});

	uploader.bind("FilesAdded", function(uploader, files) {
		if (!Story.active) {
			return;
		}

		var id = Story.active[0].id;

		//the timeout needed because the file isn't yet added to files collection of the uploader on some runtimes, and it has no files to upload
		setTimeout(function() {
			$.each(files, function(i, file) {
				if (file.status !== plupload.FAILED) {
					file.procces = $('<div/>', {
						'class': 'user-photo-proggress',
						html: '<div class="user-photo-proggress-bar"></div>'
					});
					file.proccesPercent = file.procces.children();
					file.storyNodeId = id;

					Story.winClose();
					$('#' + id)
						.append(file.procces)
						.find(".story-img-restore").remove();
				}
			});
			uploader.settings.multipart_params.date = id.replace("day-", "");
			uploader.start();
		}, 0);
	});

	uploader.bind('UploadProgress', function(uploader, file) {
		file.proccesPercent.css('width', file.percent + '%');
	});

	uploader.bind("Error", function(uploader, error) {
		notice(error.message, true);
	});

	// for correct error (ex. 500)
	uploader.bind("UploadComplete", function(uploader, files) {
		$.each(files, function(i, file) {
			if (file.status === plupload.FAILED) {
				uploader.removeFile(file);
				file.procces.remove();
			}
		});
	});

	uploader.bind("FileUploaded", function(uploader, file, response) {
		uploader.removeFile(file);
		file.procces.remove();

		// for example: press escape
		if (!response) {
			return;
		}

		response = JSON.parse(response.response);
		if (response.result) {
			$('#' + file.storyNodeId + ' .story-content').remove();
			var content = $('<div/>', {
				'class': 'story-content',
				html: '<a href="'+response.result.url+'" class="story-img-wrap"><img class="story-img" src="'+response.result.thumbLarge.url+'" width="'+(response.result.thumbLarge.width/2)+'" height="'+(response.result.thumbLarge.height/2)+'"></a>'
						+'<div class="story-edit">Редактировать</div>'
						+'<div class="story-img-likes">'
							+'<a href="#" class="fa story-img-like fa-heart-o"></a>'
							+' <span class="story-img-like-total"></span>'
						+'</div>'
			});

			$('#' + file.storyNodeId)
			.attr('data-id', response.result.id)
			.removeClass('story-item-empty')
			.removeClass('upload')
			.append(content);

			StoryDragAndDrop.addContent(content);
		} else if (response.errors) {
			uploader.trigger("Error", {
				message: response.errors.join(" "),
				file: file
			});
		}
	});

	uploader.init();

	// drag&drop support
	if (uploader.runtime === "html5" && uploader.features.dragdrop) {
		var timeout, dropOver = false,
			removeDrop = function() {
				if (dropOver) {
					dropOver = false;
					$('#userPhotos .story-img-filebodyover').remove();
				}
			};
		window.addEventListener("dragover", function(e) {
			if (!play) {
				return;
			}

			var timeoutActive;
			clearTimeout(timeout);
			timeout = setTimeout(removeDrop, 500);
			if (!dropOver) {
				dropOver = true;
				$('#userPhotos .available').each(function(i, node) {
					var instruction = $('<div/>', {
						'class': 'story-img-filebodyover',
						html: '<div class="story-img-dragging-text">Перетащите файл сюда, чтобы загрузить фотографию</div>'
					}).appendTo(node);

					instruction.on('dragover', function() {
						clearTimeout(timeoutActive);
						Story.setActive($(node));
					});

					instruction.on('dragleave', function(e) {
						timeoutActive = setTimeout(function() {
							Story.clearActive();
						}, 50);
					});
				});
			}
		}, false);

		uploader.bind("FilesAdded", removeDrop);
	}

	uploader.pause = function() {
		play = false;
	};

	uploader.play = function() {
		play = true;
	};

	return uploader;
}

StoryDragAndDrop = {
	classNames: {
		isDragging: 'story-img-dragging',
		onDragenter: 'story-img-dragimageover'
	},

	selectors: {
		container: '.story-item',
		availableDrop: '.story-item.available',
		availableContent: '.story-content'
	},

	isDragging: false,

	init: function(options) {
		var availableDrop = $(this.selectors.availableDrop),
			availableContent = $(this.selectors.availableContent);

		this.options = $.extend({
			onDragstart: $.noop,
			onDragend: $.noop
		}, options);

		this.addEvents(availableDrop);
		this.addContent(availableContent);
	},

	addContent: function(availableContent) {
		// не использовать картинку, в FF14 dragstart перехватывает ссылка
		availableContent.prop('draggable', true);
		availableContent.on('dragstart', $.proxy(this.onDragstart, this));
		availableContent.on('dragend', $.proxy(this.onDragend, this));
	},

	addEvents: function(availableDrop) {
		availableDrop.on('dragover', $.proxy(this.onDragover, this));
		availableDrop.on('dragenter', $.proxy(this.onDragenter, this));
		availableDrop.on('dragleave', $.proxy(this.onDragleave, this));
		availableDrop.on('drop', $.proxy(this.onDrop, this));
	},

	removeEvents: function(availableDrop) {
		availableDrop.off('dragover', this.onDragover);
		availableDrop.off('dragenter', this.onDragenter);
		availableDrop.off('dragleave', this.onDragleave);
		availableDrop.off('drop', this.onDrop);
	},

	onDragstart: function(e) {
		var container = $(e.target).closest(this.selectors.container);
		this.startedContainer = container;
		this.isDragging = true;
		container.addClass(this.classNames.contentIsDragging);
		this.removeEvents(container);
		this.options.onDragstart();
		// need for IE<10
		if (window.attachEvent) {
			window.event.dataTransfer.effectAllowed = 'move';
			window.event.dataTransfer.dropEffect = 'move';
		}
	},

	onDragenter: function(e) {
		if (!this.isDragging) {
			return;
		}

		$(e.delegateTarget).addClass(this.classNames.onDragenter);
	},

	onDragover: function(e) {
		// need for IE<10
		if (window.attachEvent) {
			window.event.returnValue = false;
		}
	},

	onDragleave: function(e) {
		if (!this.isDragging) {
			return;
		}

		$(e.delegateTarget).removeClass(this.classNames.onDragenter);
	},

	onDragend: function(e) {
		if (!this.isDragging) {
			return;
		}

		var container = $(e.target).closest(this.selectors.container);
		this.startedContainer = false;
		this.options.onDragend();
	},

	onDrop: function(e) {
		if (!this.isDragging) {
			return;
		}

		var container = $(e.delegateTarget),
			containerFrom = this.startedContainer,
			containerTo = container,
			dateFrom = containerFrom[0].id.replace('day-', ''),
			dateTo = containerTo[0].id.replace('day-', ''),
			contentFrom = containerFrom.find(this.selectors.availableContent),
			contentTo = containerTo.find(this.selectors.availableContent);

		var fromId = containerFrom.attr('data-id');
		var toId = containerTo.attr('data-id');

		containerTo
		.attr('data-id', fromId)
		.removeClass(this.classNames.onDragenter)
		.removeClass('upload');

		containerFrom.attr('data-id', toId);

		contentFrom
		.appendTo(containerTo)
		.find('a').each(function(i, a) {
			a.href = a.href.replace(dateFrom, dateTo);
		});

		this.addEvents(containerFrom);

		contentTo
		.appendTo(containerFrom)
		.find('a').each(function(i, a) {
			a.href = a.href.replace(dateTo, dateFrom);
		});

		if (containerTo.hasClass('story-item-empty')) {
			containerTo.removeClass('story-item-empty');
			containerFrom.addClass('story-item-empty upload');
		}

		this.isDragging = false;

		$.ajax('/api/media/swap-days', {
			data: {storyId: pp.storyId, dateA: dateFrom, dateB: dateTo},
			type: 'post',
			error: function() {
				noticeErrors('Ошибка при перемещении изображения!');
			}
		});
	}
};

Story = {
	mode: 'index',
	active: null,
	setActive: function(elem) {
		if (Story.active !== elem) {
			Story.clearActive();
			Story.active = elem;
			elem.addClass('active');
		}
	},

	clearActive: function() {
		if (Story.active) {
			Story.active.removeClass('active');
			// active нельзя убирать, в IE9 при смене фокуса на окно загрузки, срабатывает mouseout
		}
	},

	openUpload: function(elem) {
		if (typeof elem === 'string') {
			elem = $('#day-' + elem);
		}
		Story.setActive(elem);
		if (Story.uploader.runtime === "html5") {
			$('#' + Story.uploader.id + '_html5').click();
		}
	},

	updateStatus: function(id) {
		$.ajax('/api/story/write', {
			data: {id: id, status: $('#storyStatusSelector').val()},
			type: 'post',
			success: function(data) {
				if (!data.errors) {
					notice('Статус сохранён');
				} else {
					noticeErrors(data.errors);
				}
			}, error: function() {
				notice("Ошибка при изменении статуса!", true);
			}
		});
	},

	updateNotify: function(id) {
		$.ajax('/api/story/write', {
			data: {id: id, notifyPeriod: $('#storyNotifySelector').val()},
			type: 'post',
			success: function(data) {
				if (!data.errors) {
					notice('Настройка сохранена');
				} else {
					noticeErrors(data.errors);
				}
			}, error: function() {
				notice("Ошибка при изменении Настроек!", true);
			}
		});
	},

	winActive: null,

	winOpen: function(container) {
		$(document).mousedown(Story.winCloseMousedown);

		var renderInLeft = window.innerWidth - container.offset().left < 520;

		var content = container.find(".story-content");
    var date = container.prop('id') ? container.prop('id').replace("day-", "") : '';
    var id = container.data('id');
    var img = content.find(".story-img").parent().clone().find('img').removeClass('story-img');
    var win = $('<div/>', {
        'class': 'story-manage' + (renderInLeft ? ' story-manage-r' : ''),
        html: '<div class="story-manage-img">'
                +img.parent().html()
                +'<a href="#" class="fa fa-trash-o ctrl-remove" title="Удалить"></a>'
                +'<div class="story-manage-img-edit ctrl-replace">Заменить</div>'
              +'</div>'
            +'<div class="story-manage-content">'
              +'<h4>Редактирование</h4>'
              +'<form class="form form-manage"></form>'
            +'</div>',
      }).appendTo(container);

    win.find(".ctrl-replace").click(function() {
      Story.openUpload(container);
    });

		$.ajax('/api/media/get', {
		data: {id: id},
		success: function(data) {
			var form = win.find('.form-manage'),
				titleNode, descNode;
			form.html(
        '<fieldset>'
				  +'<input name="t" type="text" placeholder="Добавить заголовок">'
        +'</fieldset>'
				+'<fieldset>'
				  +'<textarea name="d" placeholder="Добавить описание"></textarea>'
        +'</fieldset>'
				+'<fieldset>'
          +'<input type="submit" value="Сохранить">'
          +'<a href="#" class="cancel" onclick="Story.winClose(); return false;">Отмена</a>'
        +'</fieldset>'
			);

			titleNode = form[0].t,
			descNode = form[0].d;
			titleNode.value = data.result.title;
			descNode.value = data.result.description;

			form.submit(function(e){
				e.preventDefault();
				Story.winClose();

				if (titleNode.value === data.result.title && descNode.value === data.result.description) {
					return;
				}

				$.ajax('/api/media/write', {
				data: {id: id, title: titleNode.value, description: descNode.value},
				type: 'post',
				error: function() {
					notice('Ошибка при сохранении текста для фотографии!', true);
				}});
			});
		}});


		win.find(".ctrl-remove").on("click", function(e){
      e.preventDefault();
			Story.winClose();

			var restore = $("<div/>", {
				'class': 'story-img-restore'
			}).prependTo(content);

			function success() {
				restore.html('<div class="story-img-restore-content"><a href="#" class="ctrl ctrl-restore" onclick="Story.recoverMedia(\''+id+'\'); return false;">Восстановить</a><br>или<br><a href="#" class="ctrl ctrl-replace upload" onclick="Story.openUpload(\''+date+'\'); return false;">Заменить</a></div>');
			}

			Story.removeMedia(id, success, success/*TODO*/);
		});

		container.addClass("win-open");
		Story.winActive = win;
	},

	winClose: function () {
		if (Story.winActive) {
			Story.winActive.closest(".story-item").removeClass("win-open");
			Story.winActive.remove();
			Story.winActive = null;
			$(document).off('mousedown', Story.winCloseMousedown);
		}
	},

	winCloseMousedown: function(e) {
		if (e.target) {
			var userPhoto = $(e.target).closest('.story-item');
			if (!userPhoto.length || userPhoto[0] !== Story.winActive.closest('.story-item')[0]) {
				Story.winClose();
			}
		}
	},

	deleteRecover: function(id, success, error) {
		var status, msg;
		if (pp.storyDeleted) {
			status = 1;
			msg = "Вы действительно хотите восстановить эту историю?";
		} else {
			status = 0;
			msg = "Вы действительно хотите удалить эту историю?";
		}

		if (!confirm(msg)) return false;

		$.ajax('/api/story/delete-recover', {
		data: { id: id, doRecover: status || undefined},
		type: 'post',
		success: function(data) {
			if (!status) {
				pp.storyDeleted = 1;
				$('#delete-recover').text('Восстановить историю');
				$('#statusSelectorHoder').addClass('hidden')
				notice('История приготовлена к удалению');
			} else {
				pp.storyDeleted = 0;
				$('#statusSelectorHoder').removeClass('hidden')
				$('#delete-recover').text('Удалить историю');
				notice('История восстановлена');
			}

			if (success) success();
		}, error: function() {
			notice('Ошибка при удалении истории.', true);
		}});
	},

	removeMedia: function(id, success, error) {
		$.ajax('/api/media/delete-recover', {
		data: {idString: id},
		type: 'post',
		success: function(data) {
			if (success) success();
		}, error: function() {
			notice("Ошибка при удалении изображения!", true);
			if (error) error();
		}});
	},

	recoverMedia: function(id) {
		$.ajax('/api/media/delete-recover', {
		data: {idString: id, doRecover: 1},
		type: 'post',
		success: function(data) {
			$('[data-id=' + id + '] .story-img-restore').remove();
		}, error: function() {
			notice("Ошибка при отправке данных!", true);
		}});
	}
};

$(function() {
	$('#userPhotos').on('click', 'a.story-img-like', function(e) {
		var node = $(e.currentTarget);
		var container = node.closest('.story-item');
		var id = container.data('id');
		e.preventDefault();
		var xhr = new XMLHttpRequest();
		var like = node.hasClass('fa-heart-o');
		var url = '/api/media/'+id+'/'+(like ? '' : 'un')+'like';
		xhr.open('POST', url);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.responseType = 'json';
		xhr.onload = () => {
			var result = xhr.response.result;
			node.toggleClass('fa-heart', like);
			node.toggleClass('fa-heart-o', !like);
			container.find('.story-img-like-total').text(result || '');
		};
		xhr.send();
	});

  if (!window.location.hash) {
    return;
  }
  var hash = window.location.hash.replace('#', '');
  if (!/[0-9]{4}-[0-9]{2}-[0-9]{2}/.test(hash)) {
    return;
  }
});

/*
jQuery plugins
==============
*/

/* tooltipsy by Brian Cray
 * Lincensed under GPL2 - http://www.gnu.org/licenses/gpl-2.0.html
 * Option quick reference:
 * - alignTo: "element" or "cursor" (Defaults to "element")
 * - offset: Tooltipsy distance from element or mouse cursor, dependent on alignTo setting. Set as array [x, y] (Defaults to [0, -1])
 * - content: HTML or text content of tooltip. Defaults to "" (empty string), which pulls content from target element's title attribute
 * - show: function(event, tooltip) to show the tooltip. Defaults to a show(100) effect
 * - hide: function(event, tooltip) to hide the tooltip. Defaults to a fadeOut(100) effect
 * - delay: A delay in milliseconds before showing a tooltip. Set to 0 for no delay. Defaults to 200
 * - css: object containing CSS properties and values. Defaults to {} to use stylesheet for styles
 * - className: DOM class for styling tooltips with CSS. Defaults to "tooltipsy"
 * - showEvent: Set a custom event to bind the show function. Defaults to mouseenter
 * - hideEvent: Set a custom event to bind the show function. Defaults to mouseleave
 * Method quick reference:
 * - $('element').data('tooltipsy').show(): Force the tooltip to show
 * - $('element').data('tooltipsy').hide(): Force the tooltip to hide
 * - $('element').data('tooltipsy').destroy(): Remove tooltip from DOM
 * More information visit http://tooltipsy.com/
 */
(function(a){a.tooltipsy=function(c,b){this.options=b;this.$el=a(c);this.title=this.$el.attr("title")||"";this.$el.attr("title","");this.random=parseInt(Math.random()*10000);this.ready=false;this.shown=false;this.width=0;this.height=0;this.delaytimer=null;this.$el.data("tooltipsy",this);this.init()};a.tooltipsy.prototype.init=function(){var b=this;b.settings=a.extend({},b.defaults,b.options);b.settings.delay=parseInt(b.settings.delay);if(typeof b.settings.content==="function"){b.readify()}if(b.settings.showEvent===b.settings.hideEvent&&b.settings.showEvent==="click"){b.$el.toggle(function(c){if(b.settings.showEvent==="click"&&b.$el[0].tagName=="A"){c.preventDefault()}if(b.settings.delay>0){b.delaytimer=window.setTimeout(function(){b.show(c)},b.settings.delay)}else{b.show(c)}},function(c){if(b.settings.showEvent==="click"&&b.$el[0].tagName=="A"){c.preventDefault()}window.clearTimeout(b.delaytimer);b.delaytimer=null;b.hide(c)})}else{b.$el.bind(b.settings.showEvent,function(c){if(b.settings.showEvent==="click"&&b.$el[0].tagName=="A"){c.preventDefault()}if(b.settings.delay>0){b.delaytimer=window.setTimeout(function(){b.show(c)},b.settings.delay)}else{b.show(c)}}).bind(b.settings.hideEvent,function(c){if(b.settings.showEvent==="click"&&b.$el[0].tagName=="A"){c.preventDefault()}window.clearTimeout(b.delaytimer);b.delaytimer=null;b.hide(c)})}};a.tooltipsy.prototype.show=function(f){var d=this;if(d.ready===false){d.readify()}if(d.shown===false){if((function(h){var g=0,e;for(e in h){if(h.hasOwnProperty(e)){g++}}return g})(d.settings.css)>0){d.$tip.css(d.settings.css)}d.width=d.$tipsy.outerWidth();d.height=d.$tipsy.outerHeight()}if(d.settings.alignTo==="cursor"&&f){var c=[f.pageX+d.settings.offset[0],f.pageY+d.settings.offset[1]];if(c[0]+d.width>a(window).width()){var b={top:c[1]+"px",right:c[0]+"px",left:"auto"}}else{var b={top:c[1]+"px",left:c[0]+"px",right:"auto"}}}else{var c=[(function(e){if(d.settings.offset[0]<0){return e.left-Math.abs(d.settings.offset[0])-d.width}else{if(d.settings.offset[0]===0){return e.left-((d.width-d.$el.outerWidth())/2)}else{return e.left+d.$el.outerWidth()+d.settings.offset[0]}}})(d.offset(d.$el[0])),(function(e){if(d.settings.offset[1]<0){return e.top-Math.abs(d.settings.offset[1])-d.height}else{if(d.settings.offset[1]===0){return e.top-((d.height-d.$el.outerHeight())/2)}else{return e.top+d.$el.outerHeight()+d.settings.offset[1]}}})(d.offset(d.$el[0]))]}d.$tipsy.css({top:c[1]+"px",left:c[0]+"px"});d.settings.show(f,d.$tipsy.stop(true,true))};a.tooltipsy.prototype.hide=function(c){var b=this;if(b.ready===false){return}if(c&&c.relatedTarget===b.$tip[0]){b.$tip.bind("mouseleave",function(d){if(d.relatedTarget===b.$el[0]){return}b.settings.hide(d,b.$tipsy.stop(true,true))});return}b.settings.hide(c,b.$tipsy.stop(true,true))};a.tooltipsy.prototype.readify=function(){this.ready=true;this.$tipsy=a('<div id="tooltipsy'+this.random+'" style="position:absolute;z-index:2147483647;display:none">').appendTo("body");this.$tip=a('<div class="'+this.settings.className+'">').appendTo(this.$tipsy);this.$tip.data("rootel",this.$el);var c=this.$el;var b=this.$tip;this.$tip.html(this.settings.content!=""?(typeof this.settings.content=="string"?this.settings.content:this.settings.content(c,b)):this.title)};a.tooltipsy.prototype.offset=function(c){var b=ot=0;if(c.offsetParent){do{if(c.tagName!="BODY"){b+=c.offsetLeft-c.scrollLeft;ot+=c.offsetTop-c.scrollTop}}while(c=c.offsetParent)}return{left:b,top:ot}};a.tooltipsy.prototype.destroy=function(){this.$tipsy.remove();a.removeData(this.$el,"tooltipsy")};a.tooltipsy.prototype.defaults={alignTo:"element",offset:[0,-1],content:"",show:function(c,b){b.fadeIn(100)},hide:function(c,b){b.fadeOut(100)},css:{},className:"tooltipsy",delay:200,showEvent:"mouseenter",hideEvent:"mouseleave"};a.fn.tooltipsy=function(b){return this.each(function(){new a.tooltipsy(this,b)})}})(jQuery);

