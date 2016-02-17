<?php

namespace app\ext;

use Yii;
use app\ext\AuthChoiceAsset;
use yii\helpers\Html;

class AuthChoice extends \yii\authclient\widgets\AuthChoice
{
    /**
     * Initializes the widget.
     */
    public function init()
    {
        $view = Yii::$app->getView();
        if ($this->popupMode) {
            AuthChoiceAsset::register($view);
            $view->registerJs("\$('#" . $this->getId() . "').authchoice();");
        } else {
        }
        $this->options['id'] = $this->getId();
        echo Html::beginTag('div', $this->options);
    }

}
