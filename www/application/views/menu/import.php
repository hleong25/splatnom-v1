<?php
$params = array(
    'dbg' => false,
    'err_msg' => false,
    'menu_id' => false,
);

extract($params, EXTR_SKIP);
?>
<form id="frmImport" enctype="multipart/form-data" method="post" action="/menu/import">
    <div class="pg">
        <span>Import menu file: </span><input class="file" type="file" name="import_file"/>
        <br/>
        <input type="submit" name="Import!"/>
    </div>
</form>
<?php
if (!empty($menu_id))
{
    echo<<<EOHTML
    <br/><div class="pg">Successfully imported menu.  New ID is {$menu_id}</div>
EOHTML;
}

if (!empty($err_msg))
{
    echo<<<EOHTML
    <br/><div class="pg error">{$err_msg}</div>
EOHTML;
}

if (!empty($dbg))
{
    $dbg = var_export($dbg, true);
    echo<<<EOHTML
    <br/><pre class="pg">{$dbg}</pre>
EOHTML;
}
?>
