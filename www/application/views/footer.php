<hr/>
<div id="footer" class="pg">
    <div class="links">
        <a href="http://www.twitter.com/splatnom">twitter</a>
        <a href="http://blog.splatnom.com">blog</a>
        <a href="/home/gmapmenu">map menus</a>
        <a href="/home/feedback">feedback</a>
    </div>
</div>
</body>
<?php
flush();

$printf_js = '<script type="text/javascript" src="%s"></script>';

// remtoe JS
foreach ($this->getRemoteJs() as $js):
    echo sprintf($printf_js, $js);
endforeach;

// local JS
foreach ($this->getJs() as $js):
    echo sprintf($printf_js, $js.'.js');
endforeach;

?>
</html>
