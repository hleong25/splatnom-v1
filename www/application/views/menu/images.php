<?php

?>
<div class="pg imgs">
<?php
    foreach ($imgs as $img)
    {
        $filename = $img['filename'];
        //$width = $img['width'];
        //$height = $img['height'];

        $img_link = "/images/get/menu/org/{$id}/{$filename}";
        $thumbnail_link = "/images/get/menu/sm/{$id}/{$filename}";

        echo<<<EOHTML
            <a href="$img_link" target="_blank"><img class="menu" src="$thumbnail_link" /></a>
EOHTML;
    }
?>
</div>
<pre class="pg">

$info=<?=var_export($info, true)?>

$imgs=<?=var_export($imgs, true)?>

</pre>
