<?php
$params = array(
    'username' => 'username',
    'email' => 'e@mail.com',
    'firstname' => 'first',
    'lastname' => 'last',
);

extract($params, EXTR_SKIP);

?>
<html>
    <style type="text/css">
        span.key {
            font-weight: bold;
        }
    </style>
<body>
    <div class="info">
        <p><span class="key">Name: </span><span class="val"><?=$firstname?> <?=$lastname?></span></p>
        <p><span class="key">Username: </span><span class="val"><?=$username?></span></p>
        <p><span class="key">Email: </span><span class="val"><?=$email?></span></p>
    </div>
</body>
</html>
