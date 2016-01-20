<?php

namespace app\components;

use yii\web\View;
use app\components\Helpers;
use app\assets\AppAsset;

class MyView extends View {
	public $title = 'Take365';

	public function render($view, $params = [], $context = null) {
		return parent::render($view, $params, $context);
	}
}

?>