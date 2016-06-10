mkdir -p web/min/js
mkdir -p web/min/css
mkdir -p web/min-build/js
mkdir -p web/min-build/css
npm install && npm run build
./yii asset config/minify.php config/assets_min.php
find web/min/js/* -mtime +5 -exec rm {} \;
find web/min/css/* -mtime +5 -exec rm {} \;
mv web/min-build/js/* web/min/js
mv web/min-build/css/* web/min/css
