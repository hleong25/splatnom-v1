<hr/>
<div id="footer" class="pg">
    <div class="links">
        <a href="/home/gmapmenu">map menus</a>
        <a href="/home/feedback">feeback</a>
    </div>
</div>
</body>
<?php
flush();

$printf_js = '<script type="text/javascript" src="%s"></script>';

// remtoe JS
foreach ($this->getRemoteJs() as $js):
    printf($printf_js, $js);
endforeach;

// local JS
foreach ($this->getJs() as $js):
    printf($printf_js, $js.'.js');
endforeach;

?>
</html>
