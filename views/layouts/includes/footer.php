<footer class="main-footer">
  <address class="main-footer-copyright">
    &copy; 2011&mdash;<?= date("Y") ?> <a href="mailto:bang@take365.org"><?= Yii::$app->params['projectName'] ?></a>
  </address>
  <ul class="main-footer-list">
    <li class="main-footer-list-item">
      <a href="/api/">Разработчикам</a>
    </li>
    <li class="main-footer-list-item">
      <a href="http://take365.reformal.ru" onclick="Reformal.widgetOpen();return false;" onmouseover="Reformal.widgetPreload();">Отзывы и предложения</a>
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
    <li class="main-footer-list-item">
      <a href="/help/privacy">Положение о конфеденциальности</a>
    </li>
  </ul>
</footer>
