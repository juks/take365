<a href="/panel/newsletter/">К списку</a>
<?php

\app\assets\NewsletterAsset::register($this);

echo $this->render('NewsletterForm', [
    'model' => $newsletter,
]);
$this->registerJs('$("#newsletterTest").on("click", submitTest)');
$this->registerJs('$("#newsletterDeliver").on("click", submitDeliver)');
?>
