<?php

namespace app\components;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\rest\Serializer;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use app\components\JsonResponse;

// To detect types
use yii\base\Arrayable;
use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class MyJsonController extends Controller {
	protected $_jsonResponse;

	public $disableSend = false;

	public $serializer = 'yii\rest\Serializer';

	/**
	*	Constructor
	**/
	public function __construct($id, $module, $config = []) {
		$this->_jsonResponse = new JsonResponse();

		parent::__construct($id, $module, $config = []);
	}

	/**
	 * Before action
	 *
	 * @param string $action
	 */
	public function beforeAction($action) {
		$result = parent::beforeAction($action);

		Yii::$app->response->format = Response::FORMAT_JSON;

		return $result;
	}

	/**
	 * After action 
	 *
	 * @param string $action
	 * @param mixed $result
	 */
	public function afterAction($action, $result) {
		if (!$this->disableSend) {
			return $this->_jsonResponse->send(['return' => true]);
		} else {
			return $result;
		}
	}

	/**
	 * Action itself execution
	 *
	 * @param string $route
	 * @param mixed $params
	 */
	public function run($route, $params = []) {
		try {
			parent::run($route, $params);
		} catch (Exception $e) {
			echo 'exception (remove me at MyJsonCOntroller)';
		}
	}

	/**
	 * Adds content for output
	 *
	 * @param string $name
	 * @param mixed $value
	 * @param array $extra
	 */
	public function addContent($value, $name = 'result', $extra = null) {      
    	$s = Yii::createObject($this->serializer)->serialize($value);

    	if ($value instanceof Model && $value->hasErrors()) {
            $this->_jsonResponse->addErrorMessage($s); 
        } else {
        	$this->_jsonResponse->addContent($name, $s, $extra);
        }
    }

	/**
	 * Adds message for output
	 *
	 * @param string $message
	 */
    public function addMessage($message) {
        $this->_jsonResponse->addMessage($message);
    }

	/**
	 * Adds error message for output
	 *
	 * @param string $message
	 * @param array $params
	 */
    protected function addErrorMessage($message, $params = []) {
        $this->_jsonResponse->addErrorMessage($message, $params);
    }

	/**
	 * Check the existance of content, named $name
	 *
	 * @param string $name
	 */
    public function hasContent($name) {
        if ($this->_jsonResponse) {
            $this->_jsonResponse->hasContent($name);
        }
    }

	/**
	 * Checks if given user can perform a $permission action on the controller model
	 *
	 * @param int $id
	 */
    public function checkModelPermission($id, $permission, $extra = []) {
    	if (empty($extra['user'])) $extra['user'] = Yii::$app->user;

		$modelInstance = Yii::createObject($this->getModelClass());

		if (is_int($id)) {
			$cond = ['id' => $id];
		} else {
			$cond = ['username' => $id];
		}

		if (method_exists($modelInstance, 'getActiveCondition')) {
			$cond = array_merge($cond, call_user_func($this->getModelClass() . '::getActiveCondition'));
		} 

		$model = call_user_func($this->getModelClass() . '::find')->where($cond)->one();

		if (!$model) {
			throw new NotFoundHttpException();
		} elseif (!$model->hasPermission($extra['user'], $permission)) {
			throw new ForbiddenHttpException();
		}

		return $model;
    }

	/**
	 * Checks if given user can perform a $permission action on the controller model
	 *
	 * @param int $id
	 */
    public function checkParentModelPermission($id, $permission, $extra = []) {
    	if (empty($extra['user'])) $extra['user'] = Yii::$app->user;

		$parentModel = Yii::createObject($extra['parentModelClass'])->findOne($id);

		if (!$parentModel) {
			throw new NotFoundHttpException();
		} elseif (!$parentModel->hasPermission($extra['user'], $permission)) {
			throw new ForbiddenHttpException();
		}

		return $parentModel;
    }
}