<?php

namespace app\components;

use app\components\Jevix;

/**
 * Базовый класс для для обработки текстов
 */
class HelpersTxt {
	/**
	 * Базовая фильтрация тегов
	 */
	static function simpleText($text) {
		$jevix = new Jevix();

		# Разрешённые теги
		$jevix->cfgAllowTags(array('a', 'img', 'i', 'b', 's', 'u', 'em', 'small', 'strong', 'nobr', 'li', 'ol', 'ul', 'sup', 'sub', 'abbr', 'pre', 'acronym', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'br', 'table', 'tr', 'td', 'tbody', 'th', 'pre', 'code', 'object', 'param', 'embed'));

		# Преформатированные теги
		$jevix->cfgSetTagPreformatted(array('pre','code'));
		# Разрешённые параметры тегов
		$jevix->cfgAllowTagParams('img', array('src', 'alt', 'title', 'width', 'height'));
		$jevix->cfgAllowTagParams('a', array('title', 'href', 'target'));
		$jevix->cfgAllowTagParams('table', array('border', 'cellpadding', 'cellspacing', 'width'));
		$jevix->cfgAllowTagParams('td', array('width', 'colspan', 'rowspan'));
		$jevix->cfgAllowTagParams('th', array('width', 'colspan', 'rowspan'));
		$jevix->cfgAllowTagParams('object', array('width', 'height'));
		$jevix->cfgAllowTagParams('param', array('name', 'value'));
		$jevix->cfgAllowTagParams('embed', array('src', 'type', 'allowscriptaccess', 'allowfullscreen', 'width', 'height'));

		$jevix->cfgSetTagIsEmpty('object');
		$jevix->cfgSetTagIsEmpty('param');
		$jevix->cfgSetTagIsEmpty('embed');

		# Параметры тегов являющиеся обязяательными.
		$jevix->cfgSetTagParamsRequired('img', 'src');
		$jevix->cfgSetTagParamsRequired('a', 'href');

		# Теги которые необходимо вырезать из текста вместе с контентом
		$jevix->cfgSetTagCutWithContent(array('script', 'iframe', 'style'));

		# Вложенные теги
		$jevix->cfgSetTagChilds('ul', array('li'), true, true);
		$jevix->cfgSetTagChilds('ol', array('li'), true, true);
		$jevix->cfgSetTagChilds('table', array('tr', 'tbody'), true, true);
		$jevix->cfgSetTagChilds('tbody', array('tr'), true, true);
		$jevix->cfgSetTagChilds('tr', array('td', 'th'), true, true);
		$jevix->cfgSetTagChilds('object', array('param', 'embed'), false, false);

		# Автозамена
		$jevix->cfgSetAutoReplace(array('+/-', '(c)', '(r)', '(C)', '(R)'), array('±', '©', '®', '©', '®'));

		# Удаление </param>
		$text = preg_replace('!<\s*/\s*param>!i', "", $text);
		$text = preg_replace('!(<param[^>]+)/\s*>!i', "$1>", $text);

		$errors = null;
		$result = $jevix->parse($text, $errors);

		# Исправление косяков
		$result = preg_replace('!(</h[0-9]>)<br ?/?>!i', "\\1", $result);

		return $result;
	}
	
	/**
	 * Фильтр пожжёще
	 */
	static function strictText($text) {
		$jevix = new Jevix();

		# Разрешённые теги
		$jevix->cfgAllowTags(array('i', 'br', 'b', 'u', 'a', 'strong'));

		# Коротие теги типа
		$jevix->cfgSetTagShort(array('br'));

		$jevix->cfgAllowTagParams('a', array('title', 'href', 'target'));

		# Параметры тегов являющиеся обязяательными.
		$jevix->cfgSetTagParamsRequired('a', 'href');

		# Теги которые необходимо вырезать из текста вместе с контентом
		$jevix->cfgSetTagCutWithContent(array('script', 'iframe', 'style'));

		# Автозамена
		$jevix->cfgSetAutoReplace(array('+/-', '(c)', '(r)', '(C)', '(R)'), array('±', '©', '®', '©', '®'));

		$errors = null;
		$result = $jevix->parse($text, $errors);

		return $result;
	}

	/**
	 * Самый жёсткий фильтр
	 */
	static function plainText($text) {
		$jevix = new Jevix();

		# Разрешённые теги
		$jevix->cfgAllowTags(array('b', 'strong', 'i'));

		# Автозамена
		$jevix->cfgSetAutoReplace(array('+/-', '(c)', '(r)', '(C)', '(R)'), array('±', '©', '®', '©', '®'));

		$errors = null;
		$result = $jevix->parse($text, $errors);

		return $result;
	}

	/**
	 * Полностью без HTML
	 *
	 * @param unknown_type $text
	 * @return unknown
	 */
	static function noHtml($text) {
		$jevix = new Jevix();

		# Разрешённые теги
		$jevix->cfgAllowTags(array());

		# Автозамена
		$jevix->cfgSetAutoReplace(array('+/-', '(c)', '(r)', '(C)', '(R)'), array('±', '©', '®', '©', '®'));

		$errors = null;
		$result = $jevix->parse($text, $errors);

		return $result;
	}
}
?>
