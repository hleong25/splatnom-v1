<?php
$params = array('err'=>'', 'to'=>'', 'subject'=>'', 'message'=>'');
extract($params, EXTR_SKIP);
?>
<div class="pg pg_bottom msg">
    <span class="err"><?php echo $err; ?></span>
</div>
<form id="mail" enctype="multipart/form-data" method="post" action="/<?php echo $myurl; ?>" >
<div class="pg pg_bottom">
    <span class="header">To: </span>
    <input class="jq_watermark to" type="textbox" name="to" title="To" value="<?php echo $to; ?>" /><br/>

    <span class="header">Subject: </span>
    <input class="jq_watermark subject" type="textbox" name="subject" title="Subject" value="<?php echo $subject; ?>" /><br/>
    <br/>

    <span class="header">Message</span><br/>
    <textarea class="jq_watermark message" name="message" title="Message" rows="10"><?php echo $message; ?></textarea><br/>
    <br/>

    <input type="submit" value="Send!"/>
    <input type="button" value="Cancel" onclick="location.href='/<?php echo $myurl; ?>'"/>
</div>
</form>
