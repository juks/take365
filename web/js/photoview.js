var Photoview = (function(){
	var bgNode, viewNode, viewIn, request, images, now, loadFx, option,
		MAX_HEIGHT = 700, MAX_WIDTH = 700,
		keyListener = function(e) {
			switch(e.which) {
				case 27: // esc
					t.close();
					break;
				case 39: // right
					if (!loadFx) {
						t.next();
					}
					break;
				case 37: // left
					if (!loadFx) {
						t.prev();
					}
					break;
			}
		},
	t = {
		show: function(date, imgSrc, additionalOption) {
			option = additionalOption || {};
			now = 0;
			images = [];

			bgNode = $('<div/>', {
				css: {
					background: '#000',
					position: 'fixed',
					top: 0,
					left: 0,
					right: 0,
					bottom: 0,
					zIndex: 1001,
					opacity: 0.7
				}
			}).appendTo('body');

			viewNode = $('<div/>', {
				'class': 'photoview',
				css: {
					position: 'fixed',
					top: 0,
					bottom: 0,
					width: '100%',
					zIndex: 1002,
					overflow: 'auto'
				},
				click: function(e) {
					if (viewNode.is(e.target) || viewIn.is(e.target)) {
						t.close();
					}
				}
			}).appendTo('body');

			viewIn = $('<div class="photoview-in"/>').appendTo(viewNode);

			t.get(date, -10, function(items) {
				t.showImage(items[0]);
				return items;
			}, true);

			$(document).keydown(keyListener);
		},

		next: function() {
			if (images[now+1]) {
				t.beforeShowImage();
				t.showImage(images[++now]);
			} else if (!images[now].isLast) {
				t.beforeShowImage();
				t.get(images[now].date, -10, function(items) {
					if (items.length) {
						t.showImage(images[++now]);
					} else {
						viewIn.find(".photoview-next").hide();
					}
				});
			}
		},

		prev: function() {
			if (images[now-1]) {
				t.beforeShowImage();
				t.showImage(images[--now]);
			} else if (!images[now].isFirst) {
				t.beforeShowImage();
				t.get(images[now].date, 10, function(items) {
					if (items.length) {
						t.showImage(images[--now]);
					} else {
						viewIn.find('.photoview-prev').hide();
					}
				});
			}
		},

		get: function(date, span, callback, isFirstReq) {
			request = $.ajax('/mediaActions/getPlayerData/', {
			data: {json: 1, date: date, storyId: pp.storyId, span: span},
			dataType: 'json',
			success: function(data) {
				if (!data.errors) {
					if (span > 0) {
						images = [].concat(data.items, images);
						now += data.items.length;
					} else {
						if (!isFirstReq) {
							data.items.shift(); // TODO хак, убирает из фоток саму себя, которая нужна при первом запросе
						}
						images = images.concat(data.items);
					}
					if (callback) {
						callback(data.items);
					}
				} else {
					noticeErrors(data.errors);
				}
			}, error: function() {
				notice("Ошибка при запросе данных!", true);
			}});
		},

		beforeShowImage: function() {
			viewIn
			.find('.photoview-data')
			.fadeOut(200, function() {
				$(this.parentNode).remove();
			});
		},

		showImage: function(item) {
			// inline for onclick in backgrpund
			var prev, next,
				itemContainer = $('<div class="photoview-item"/>'),
				dataContainer = $('<div/>', {
					'class': 'photoview-data',
					css: { opacity: 0 },
					html: '<h1 class="photoview-title" style="display:inline">'+(item.title || '')+'</h1><br>'
						+'<p class="photoview-img-container"><img src="'+item.url+'" width="'+item.width+'" height="'+item.height+'"></p>'
						+'<p style="display:inline">'+(item.description || '')+'</p>'
				}).appendTo(itemContainer);

			if (!item.isFirst) {
				prev = $('<div/>', {
					'class': 'photoview-prev',
					css: { top: Math.floor(item.height/2) },
					html: '<div class="photoview-prev-in"></div>'
				})
				.appendTo(itemContainer)
				.click(t.prev);
			}

			if (!item.isLast) {
				next = $('<div/>', {
					'class': 'photoview-next',
					css: { top: Math.floor(item.height/2) },
					html: '<div class="photoview-next-in"></div>'
				})
				.appendTo(itemContainer)
				.click(t.next);
			}

			dataContainer.find('.photoview-img-container').click(function(e) {
				if (e.target === this) {
					t.close();
				}
			});

			if (!viewIn[0].firstChild) {// first load
				dataContainer.css('opacity', 1);
				itemContainer
					.css({
						top: Math.ceil(MAX_HEIGHT/2 - item.height/2)
					})
					.appendTo(viewIn);
			} else {
				itemContainer
				.appendTo(viewIn)
				.css({
					top: Math.ceil(MAX_HEIGHT/2 - item.height/2),
					visibility: 'visible'
				});

				dataContainer
				.delay(200)
				.animate({opacity: 1} , function() {
					dataContainer.fadeIn();
				});
			}

			if (!item.isLast) {
				viewIn.find('img').click(t.next);
			}
		},

		close: function() {
			/*if (request) {
				request.abort();
			}*/
			bgNode.remove();
			viewNode.remove();
			$(document).off('keydown', keyListener);
			if (option.close) {option.close(); }
		}
	};

	return t;
})();
