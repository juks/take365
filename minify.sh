mkdir -p web/min/js
mkdir -p web/min/css
rm web/min/js/*
rm web/min/css/*
./yii asset config/minify.php config/assets_min.php
