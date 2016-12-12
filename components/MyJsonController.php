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
use app\components\Ml;

// To detect types
use yii\base\Arrayable;
use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class MyJsonController extends Controller {
	protected $_jsonResponse;
	protected $_responseCode = 0;

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
		if ($this->_jsonResponse->hasErrors) Yii::$app->response->statusCode = $this->_responseCode ? : 406;

		if (!$this->_jsonResponse->hasContent()) $this->addContent('OK');

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
	/*public function run($route, $params = []) {
		try {
			parent::run($route, $params);
		} catch (\app\components\ModelException $e) {
			$this->addErrorMessage($e->getMessage(), ['field' => $e->getFieldName()]);
		} catch (\Exception $e) {
			$this->addErrorMessage($e->getMessage());
		}
	}*/

	public function runAction($id, $params = []) {
		try {
			return parent::runAction($id, $params);
		} catch (\app\components\ModelException $e) {
			$this->addErrorMessage($e->getMessage(), ['field' => $e->getFieldName()]);

			return $this->afterAction($id, null);
		} catch (\app\components\ControllerException $e) {
			$this->addErrorMessage($e->getMessage());

			return $this->afterAction($id, null);
		} catch (\yii\web\BadRequestHttpException $e) {
			$this->_responseCode = $e->statusCode;
			$this->addErrorMessage($e->getMessage());

			return $this->afterAction($id, null);
		} catch (\yii\web\NotFoundHttpException $e) {
			$this->_responseCode = $e->statusCode;
			$this->addErrorMessage($e->getMessage());

			return $this->afterAction($id, null);
		} catch (yii\web\ForbiddenHttpException $e) {
			$this->_responseCode = $e->statusCode;
			$this->addErrorMessage($e->getMessage());

			return $this->afterAction($id, null);
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

		if (is_int($id)) {
			$cond = ['id' => $id];
		} else {
			$cond = ['username' => $id];
		}

		$model = call_user_func($this->getModelClass() . '::find')->where($cond);

		if (!empty($extra['with'])) {
			foreach ($extra['with'] as $relationName) {
				$model = $model->with($relationName);
			}
		}

		$model = $model->one();

		if (!$model) {
			throw new NotFoundHttpException(Ml::t('Object not found'));
		} elseif (!$model->hasPermission($extra['user'], $permission)) {
			throw new ForbiddenHttpException(Ml::t('Forbidden'));
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
			throw new NotFoundHttpException(Ml::t('Object not found'));
		} elseif (!$parentModel->hasPermission($extra['user'], $permission)) {
			throw new ForbiddenHttpException(Ml::t('Forbidden'));
		}

		return $parentModel;
    }
}