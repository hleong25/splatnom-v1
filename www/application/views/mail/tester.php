<?php
$params = array('err'=>'', 'to'=>'', 'subject'=>'', 'message'=>'');
extract($params, EXTR_SKIP);
?>
<div class="pg pg_bottom msg">
    <span class="err"><?php echo $err; ?></span>
</div>
<form id="mail" enctype="multipart/form-data" method="post" action="/<?php echo $myurl; ?>" >
<div class="pg pg_bottom">
    <label>
        <span class="header">To: </span>
        <input class="watermark to" type="textbox" name="to" placeholder="To" value="<?php echo $to; ?>" />
    </label>
    <label>
        <span class="header">Subject: </span>
        <input class="watermark subject" type="textbox" name="subject" placeholder="Subject" value="<?php echo $subject; ?>" />
    </label>
    <br/>
    <label>
        <span class="header">Message</span><br/>
        <textarea class="watermark message" name="message" placeholder="Message" rows="10"><?php echo $message; ?></textarea>
    </label>
    <input type="submit" value="Send!"/>
    <input type="button" value="Cancel" onclick="location.href='/<?php echo $myurl; ?>'"/>
</div>
</form>
