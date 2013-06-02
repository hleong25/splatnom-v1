<?php
$params = array(
    'err_msg' => false,
);

extract($params, EXTR_SKIP);
?>
<div id="new_event" class="pg">
    <?php if (!empty($err_msg)): ?>
        <span id="err"><?=$err_msg?></span>
        <br/>
    <?php endif; ?>
    <br/>
    <form id="frmNewEvent" enctype="multipart/form-data" method="post" action="/event/new">
        <label>
            <p class="hint">Event name</p>
            <input id="event_name" class="watermark txt" type="text" name="event_name" placeholder="Event name" />
        </label>
        <br/>
        <label>
            <p class="hint">Notes</p>
            <textarea id="event_notes" class="watermark txt" type="text" name="event_notes" placeholder="Notes" rows="5"></textarea>
        </label>
        <br/>
        <br/>
        <p class="cover_img">Cover image: <input type="file" name="cover_img"/></p>
        <br/>
        <input class="button" type="submit" value="Submit" />
    </form>
</div>
