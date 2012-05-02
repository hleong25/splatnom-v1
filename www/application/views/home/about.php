<?php
$params = array(
    'editor_src'=>'',
);

extract($params, EXTR_SKIP);

?>
<div class="pg">
<form method="post" action="/<?=$myurl?>">
    <textarea id="editor" name="editor"><?=$editor_src?></textarea>
</form>
</div>
