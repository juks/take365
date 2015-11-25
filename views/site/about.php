<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
<?php

$form = ActiveForm::begin([
									'options' 	=> ['enctype' 	=> 'multipart/form-data'],
									'action'	=> '/api/media/upload'
							]); ?>

<input type="hidden" name="targetId" value="1">
<input type="hidden" name="targetType" value="2">
<input type="hidden" name="mediaType" value="storyImage">
<input type="hidden" name="date" value="2015-11-24">
<input type="file" name="file">

<button>Submit</button>

<?php ActiveForm::end(); ?>

    <code><?= __FILE__ ?></code>
</div>
