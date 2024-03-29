<?php

namespace app\components;

use Yii;
use yii\web\Controller;

class MyController extends Controller {
	protected $_jsVars = [];
	protected $_title = '';

	public function render($view, $params = []) {
		$this->view->params['jsVarsString'] = $this->getJsVarsString();
		$this->view->params = array_merge($this->view->params, $params);
		if (empty($this->view->params['pageType'])) $this->view->params['pageType'] = null;

		if ($this->_title) $this->view->title = $this->_title . ' | ' . Helpers::getParam('projectName'); else $this->view->title = Helpers::getParam('projectName');

		return parent::render($view, $params);
	}

	public function addJsVars($values) {
		foreach($values as $key => $value) {
			$this->_jsVars[$key] = $value;
		}
	}

	public function getJsVarsString() {
		return 'var pp = ' . json_encode(array_merge($this->_jsVars, $this->getDetaulfJsVars()));
	}

	public function getDetaulfJsVars() {
		return ['userId' => $user = Yii::$app->user->id];
	}

	public function setTitle($title) {
		$this->_title = $title;
	}
}