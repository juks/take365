// JSLint defined globals

var controlsObj = {
	userPhoto: {
		resize: { width: 1500, height: 1500, quality: 80 },
		dropText: "Поместите файл сюда что бы загрузить фотографию",
		deleteText: "Удалить фотографию?"
	}
};

function initUploder() {
	$.each(controlsObj, function(name, conf) {
		var uploader = new plupload.Uploader({
				runtimes: "html5,gears,flash,silverlight,browserplus,html4",
				browse_button: name + "Pick",
				max_file_size: "10mb",
				url: "/api/media/upload",
				resize: conf.resize,
				flash_swf_url: "/js/plupload/plupload.flash.swf",
				silverlight_xap_url: "/js/plupload/js/plupload.silverlight.xap",
				filters: [
					{title : "Картинки", extensions : "jpg,gif,png"}
				],
				drop_element: name + "Drop",
				multi_selection: false,
				multipart: true,
				multipart_params: {
					targetId: document.forms[name + "Upload"].targetId.value,
					targetType: document.forms[name + "Upload"].targetType.value,
					mediaType: document.forms[name + "Upload"].mediaType.value
				}
			}),
			procces, proccesPercent;

		uploader.bind("FilesAdded", function(uploader, files) {
			//the timeout needed because the file isn't yet added to files collection of the uploader on some runtimes, and it has no files to upload
			setTimeout(function(){
				if (files[0].status !== plupload.FAILED) {
					procces =$("<div/>", {
						html: "Загружается <span>0</span>%.",
						css: {
							position: 'absolute',
							top:0,
							left:0,
							visibility:'visible'
						}
					});
					$('#' + name + 'UploadWrap').css({
						position: 'relative',
						visibility: 'hidden'
					});
					procces.appendTo(name + 'UploadWrap');
					proccesPercent = procces.find('span');
					uploader.start();
				}
			}, 0);
		});

		uploader.bind("UploadProgress", function(uploader, file) {
			proccesPercent.html(file.percent);
		});

		uploader.bind("Error", function(uploader, error) {
			if (error.response) {
				noticeErrors(
					JSON.parse(error.response)
						.errors
						.map(function(e){ return e.value; })
					);
			} else {
				noticeErrors(error.message);
			}
		});

		// for correct error (ex. 500)
		uploader.bind("UploadComplete", function() {
			$('#' + name + 'UploadWrap').css('visibility', 'visible');
			procces.remove();
		});

		uploader.bind("FileUploaded", function(uploader, file, response) {
			response = $.parseJSON(response.response);
			if (response.result) {
				$('#userPhoto').css({backgroundImage: 'url("'+response.result.thumbLarge.url+'")'});

				$('#' + name + 'Delete')
				.removeClass('hidden')
					.find('a')[0]
					.onclick = function() {
						deleteMedia(response.result.id, name);
					};

				uploader.refresh();
			} else if (response.errors) {
				uploader.trigger("Error", {
					message: response.errors.join(" "),
					file: file
				});
			}
		});

		uploader.init();

		conf.uploader = uploader;
	});

	// drag&drop support
	if (controlsObj.userPhoto.uploader.runtime === "html5" && controlsObj.userPhoto.uploader.features.dragdrop) {
		var timeout,
			removeDrop = function() {
				$.each(controlsObj, function(name, conf) {
					if (conf.dropOver) {
						conf.dropOver.remove();
						conf.dropOver = null;
					}
				});
			};

		window.addEventListener("dragover", function() {
			clearTimeout(timeout);
			timeout = setTimeout(removeDrop, 500);
			$.each(controlsObj, function(name, conf) {
				if (!conf.dropOver) {
					conf.dropOver = $('<div/>', {
						'class': 'fileDragover',
						html: conf.dropText
					}).appendTo('#' + name + 'Drop');
					conf.dropOver.style.lineHeight = conf.dropOver.offsetHeight + "px";
				}
			});
		}, false);
		$.each(controlsObj, function(name, conf) {
			conf.uploader.bind("FilesAdded", removeDrop);
		});
	}
}

$(function() {
	// fix: кнопка загрузки висит невидимой в том же месте
	if (window.controlsObj) {
		$.each(controlsObj, function(name, conf) {
			if (conf.uploader) {
				conf.uploader.destroy();
			}
		});
	}
	initUploder();
});

function deleteMedia(id, name) {
	if (confirm(controlsObj[name].deleteText)) {
		$.ajax('/api/media/delete-recover', {
		data: {idString: id},
		dataType: 'json',
		type: 'post',
		success: function(data) {
			if (!data.errors) {
				$('#' + name).css({backgroundImage: ''});
				$('#' +name + 'Delete').addClass('hidden');

				notice("Изображение удалено");
				controlsObj[name].uploader.refresh();
			} else {
				data.errors.each(function(error) {
					notice(error, true);
				});
			}
		},
		error: function() {
			notice("Ошибка при удалении изображения!", true);
		}});
	}
}

// .po file like language pack
plupload.addI18n({
	"Select files" : "Выберите файлы",
	"Add files to the upload queue and click the start button." : "Добавьте файлы в очередь и нажмите кнопку «Загрузить файлы».",
	"Filename" : "Имя файла",
	"Status" : "Статус",
	"Size" : "Размер",
	"Add files" : "Добавить файлы",
	"Stop current upload" : "Остановить загрузку",
	"Start uploading queue" : "Загрузить файлы",
	"Uploaded %d/%d files": "Загружено %d из %d файлов",
	"N/A" : "N/D",
	"Drag files here." : "Перетащите файлы сюда.",
	"File extension error.": "Неправильное расширение файла.",
	"File size error.": "Неправильный размер файла.",
	"Init error.": "Ошибка инициализации.",
	"HTTP Error.": "Ошибка HTTP.",
	"Security error.": "Ошибка безопасности.",
	"Generic error.": "Общая ошибка.",
	"IO error.": "Ошибка ввода-вывода."
});
