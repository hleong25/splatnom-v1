<?php
$params = array(
    'lstCss' => array(),
);

extract($params, EXTR_SKIP);
?>
<div class="pg css">
    <form id="frmCss" method="post" action="/<?=$myurl?>">
    <table class="tblDefault">
        <thead>
            <td><input id="chkAll" type="checkbox" style="display:none;"/></td>
            <td>css file</td>
            <td>exists</td>
            <td>timestamp</td>
        </thead>
        <tbody>
        <?php foreach ($lstCss as $row):
            $css = $row['css'];
            $exists = $row['exists'];
            $timestamp = $row['timestamp'];

            $exists = $exists == true ? 'true' : 'false';
        ?>
            <tr>
                <td><input class="cssitem" type="checkbox" name="css[]" value="<?=$css?>"/></td>
                <td><?=$css?></td>
                <td><?=$exists?></td>
                <td><?=$timestamp?></td>
            </tr>
        <?php endforeach; // foreach ($need_metadata as $row): ?>
        </tbody>
    </table>
    <button class="button" type="submit">Update CSS</button>
    </form>
</div>
