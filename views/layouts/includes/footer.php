<footer class="footer">
    <ul>
        <li>&copy; 2011&mdash;<?= date("Y") ?> <a href="mailto:bang@take365.org"><?= Yii::$app->params['projectName'] ?></a></li>
        <li><a href="/help">О&nbsp;проекте</a></li>
        <!-- <li><a href="/blog">Блог</a></li> -->
        <li><a href="/api/">Разработчикам</a></li>
        <li>
            <a href="http://take365.reformal.ru" onclick="Reformal.widgetOpen();return false;" onmouseover="Reformal.widgetPreload();">Отзывы и&nbsp;предложения</a>
            <script>
                var reformalOptions = { project_id: 66526, show_tab: false, project_host: "take365.reformal.ru" };
                (function() {
                    var script = document.createElement('script');
                    script.type = 'text/javascript';
                    script.async = true;
                    script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'media.reformal.ru/widgets/v3/reformal.js';
                    document.getElementsByTagName('head')[0].appendChild(script); })
                ();
            </script>
        </li>
        <li><a href="https://itunes.apple.com/ru/app/take365/id1082676900"><span class="fa fa-apple"></span> iOS</a></li>
        <li><a href="https://play.google.com/store/apps/details?id=org.take365.take365"><span class="fa fa-android"></span> Android</a></li>
        <li><a href="https://telegram.me/take365"><span class="fa fa-telegram"></span> Telegram</a></li>
    </ul>
</footer>