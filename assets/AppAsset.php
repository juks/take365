<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
  public $basePath = '@webroot';
  public $baseUrl = '@web';
  public $css = [
    'blocks/Search/search.css',
    'css/react.css',
    'css/normalize.css',
    'css/style.css',
    'css/buttons.css',
    'css/nav.css',
    'css/forms.css',
    'css/popup.css',
    'css/blog.css'
  ];
  public $js = [
    'js/common.js',
    'js/smartdate.js',
    'js/react.js',
    'https://kit.fontawesome.com/63f6606447.js'
  ];
  public $depends = [
  //'yii\web\JqueryAsset'
    'app\assets\JqueryAsset'
  ];
  public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
