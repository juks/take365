<?php

namespace app\components;

use Yii;
use yii\web\Controller;

class MyController extends Controller {
	protected $_jsVars = [];
	public $jsVarsString = '';

	public function render($view, $params = []) {
		$this->view->params['jsVarsString'] = $this->getJsVarsString();

		return parent::render($view, $params);
	}

	public function addJsVars($values) {
		foreach($values as $key => $value) {
			$this->_jsVars[$key] = $value;
		}
	}

	public function getJsVarsString() {
		return 'var pp = ' . json_encode($this->_jsVars);
	}
}