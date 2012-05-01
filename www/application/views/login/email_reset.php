<?php
$params = array(
    'user' => 'theuser',
    'code' => '80085',
    'reset_url' => Util::getDomain(),
    'code_url' => Util::getDomain(),
);

extract($params, EXTR_SKIP);

?>
<html><body>
<div style="text-align:center; ">
    <p>Oh noes!!! Secret Agent Splatterson told me you don't remember your super secret decoder ring!</p>
    <p>Click on this <a href="<?=$code_url?>">link</a> to start your journey.</p>
    <br/>
    <p>If the link doesn't work, take use this code <?=$code?> and go here <a href="<?=$reset_url?>"><?=$reset_url?></a></p>
</div>
</body></html>
