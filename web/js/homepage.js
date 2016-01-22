var Bg = {};

Bg.isRetina = window.devicePixelRatio > 1;

Bg.calcImagesSize = function(imgs) {
  var width = $(window).width();
  var height = $(window).height();
  var imgSize = 70;

  // если меньше 150 картинок на экране, то уменьшаем, чтобы было больше
  if (width * height / (70 * 70) < 150) {
    imgSize = Math.max(Math.ceil(Math.sqrt((width * height) / 150)), 10);
  }

  var rowCount = Math.ceil(height / imgSize);
  var bottomGap = imgSize - height % imgSize;
  var imgSizeCorrected = imgSize - Math.floor(bottomGap / rowCount);

  var colCount = Math.ceil(width / imgSizeCorrected);

  var cellsSum = rowCount * colCount;
  var onlyNeededImgs = imgs.slice(0, cellsSum);

  var lists = onlyNeededImgs.map(function(img, i) {
    var src = Bg.isRetina ? img.srcRerina: img.src;

    var offset = imgSizeCorrected * img.offsetIndex;

    return '<a '+
      'class="matrix-link" '+
      'href="' + img.url + '" '+
      'style="'+
        'width:'+ imgSizeCorrected +'px;'+
        'height:' + imgSizeCorrected + 'px;'+
        'background-image:url(' + src + ');'+
        'background-position:0 -' + offset + 'px"'+
    '></a>';
  });

  Bg.container.html(lists.join(''));

  // что бы справа не было пустоты
  Bg.container.css({
    marginRight: -imgSizeCorrected + 1
  });
};

Bg.container = null;

/**
 * @param  {Array} ids идентификаторы историй в порядке генерации спрайтов
 * @param  {Array} urls словарь урлов историй, где ключ - идентификатор истории, значение -- урл
 * @param {integer} maxSpritesPerFile максимальное количество спрайтов в файле, то есть если maxSprites == 500, а maxSpritesPerFile == 50, то файлов на диске 10
 * @param {integer} currentMosaicId идентификатор спрайта, который надо использовать (он определяет имя файла типа 29_4_140.jpg, где 29 и есть currentMosaicId)
 */
Bg.create = function(ids, urls, maxSpritesPerFile, currentMosaicId) {
  Bg.container = $('.matrix');

  /* prepare data for human */
  var imgs = ids.map(function(id, i) {
    var sprite = Math.floor(i/maxSpritesPerFile) + 1;

    return {
      offsetIndex: i % maxSpritesPerFile,
      src: '/media/mosaics/' + currentMosaicId + '_' + sprite + '_70.jpg',
      srcRerina: '/media/mosaics/' + currentMosaicId + '_' + sprite + '_140.jpg',
      url: urls[id]
    };
  });

  Bg.calcImagesSize(imgs);

  $(window).on('resize', function() {
    clearTimeout(Bg._resizeTimeout);
    Bg._resizeTimeout = setTimeout(function() {
      Bg.calcImagesSize(imgs);
    }, 200);
  });
};



var Register = new FormBase();

Register.success = function(form, result) {
  form.html('<p>Для завершении регистрации, проверьте свой адрес электронной почты, щелкнув ссылку в подтверждающем сообщении, отправленном по электронной почте.</p>');
};
