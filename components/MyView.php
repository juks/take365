<?php

namespace app\components;

use yii\web\View;
use app\models\User;
use app\components\Helpers;
use app\assets\AppAsset;

class MyView extends View {
	public $title = 'Take365';

	public function render($view, $params = [], $context = null) {
		return parent::render($view, $params, $context);
	}

	/*
	* Quote helpers
	*/
	public function hQStart() {
		return '<blockquote style="border-left: 2px solid rgb(0,0,64); margin: 0px; padding-left: 15px; padding-right: 0px; font-style: italic">';
	}

	public function hQEnd() {
		return '</blockquote>';
	}

	/*
	* Gender-depended string
	*/
	public function hUserAction($user, $m, $f) {
		return !$user->sex || $user->sex == User::sexMale ? $m : $f;
	}
}

?>