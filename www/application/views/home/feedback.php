<?php
$params = array(
    'feedback_done' => false,
    'name' => '',
    'email' => '',
    'msg' => '',
    'err' => false,
);

extract($params, EXTR_SKIP);

$feedback_msg = 'How much wood would a woodchuck chuck if a woodchuck could chuck wood?';
?>
<div class="pg">
<?php if ($feedback_done === false): ?>
    <?php if (!empty($err)): ?>
        <span class="err"><?=$err?></span>
    <?php endif; ?>
    <form method="post" action="/<?=$myurl?>">
        <input type="text" class="jq_watermark" name="name" title="Who am I talking to?" value="<?=$name?>"/>
        <br/>
        <input type="text" class="jq_watermark" name="email" title="Email" value="<?=$email?>"/>
        <br/>
        <textarea class="jq_watermark" name="msg" title="<?=$feedback_msg?>"><?=$msg?></textarea>
        <br/>
        <input class="button" type="submit" value="Submit" />
    </form>
<?php else: ?>
    <span>Thanks for the feedback!</span>
<?php endif; ?>
</div>
