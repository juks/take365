<?php

namespace app\components;

class JsonResponse {
	# Есть ли ошибки
	public $hasErrors = false;

	# Данные
	protected $data = [];

	# Ошибки
	protected $errors = [];

	/**
	 * Adds content to response
	 *
	 * @param string $key key name
	 * @param string $value value
	 * @param array $params values array name=>value
	 * @param bool $forceArray make it an array no matter what
	 */
	function addContent($key, $value = null, $extra = null) {
		if(!$key) 				$key = 'root';
		if($key == 'message') 	$key = 'messages';
		if($key == 'error') 	$key = 'errors';

        if (empty($extra['attributes'])) {
            $drop = $value;
        } else {
            $drop = ['value' => $value];
            foreach ($extra['attributes'] as $attributeName => $attributeValue) {
                $drop[$attributeName] = $attributeValue;
            }
        }

        if (!empty($extra['field'])) {
            if (!is_array($drop) || !isset($drop['value'])) $drop = ['value' => $drop];
            $drop['field'] = $extra['field'];
        }

		if(!isset($this->data[$key])) {
			if(!empty($extra['forceArray']) || $key == 'errors' || $key == 'messages') {
				$this->data[$key] = [$drop];
			} else {
				$this->data[$key] = $drop;
			}
		} elseif(!is_array($this->data[$key]) || !isset($this->data[$key][0])) {
			$currentValue = $this->data[$key];
			$this->data[$key] = [];
			$this->data[$key][] = $currentValue;
			$this->data[$key][] = $drop;
		} else {
			$this->data[$key][] = $drop;
		}
	}

    public function hasContent($name = null) {
        if ($name) {
        	return !empty($this->data[$name]);
        } else {
        	return !empty($this->data);
        }
    }

    public function hasErrors($name) {
        return $this->hasErrors;
    }

    public function addMessage($message) {
        $this->addContent('messages', $message);
    }

	/**
	 * Добавляет сообщение об ошибке в AJAX-Response
	 *
	 * @param string $message сообщение об ошибке
	 * @param array $params код ошибки
	 */
	function addErrorMessage($message, $params = []) {
		$this->hasErrors = true;

		if (is_array($message)) {
			foreach ($message as $messageItem) {
				if (!empty($messageItem['field']) && !empty($messageItem['message'])) {
					$p = array_merge($params, ['field' => $messageItem['field']]);
					$this->addErrorMessage($messageItem['message'], $p);
				} else {
					$this->addErrorMessage($messageItem);
				}
                
			}
		} else {
            $this->addContent('errors', $message, $params);
		}
	}

	function setRedirect($value){
		$this->addContent('redirect', $value);
	}

	# Отправка данных
	function send($renderOptions = null) {
        header('Content-type: application/json; charset=utf-8');

		# Добавление ошибок
        if ($this->hasErrors) $fields = ['errors']; else $fields = [];
        $fields[] = 'messages';

		# Вернуть результат
		if(!empty($renderOptions['return'])) {
			return $this->data ? $this->data : new \stdClass();
		# Просто вывод
		} elseif(!isset($renderOptions['jsonp'])) {
			echo $this->data ? json_encode($this->data) : '{}';
		# Вывод jsonp
		} else {
			if(empty($renderOptions['jsonp'])) $renderOptions['jsonp'] = 'foo';
			echo $renderOptions['jsonp'] . '(' . ($this->data ? json_encode($this->data): '{}') . ')';
		}
	}
}