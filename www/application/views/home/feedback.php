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
        <p class="err"><?=$err?></p>
    <?php endif; ?>
    <form method="post" action="/<?=$myurl?>">
        <label>
            <span class="hint">Who am I talking to?</span>
            <input type="text" class="edit watermark" name="name" placeholder="Who am I talking to?" value="<?=$name?>"/>
        </label>
        <label>
            <span class="hint">Email</span>
            <input type="text" class="edit watermark" name="email" placeholder="Email" value="<?=$email?>"/>
        </label>
        <label>
            <span class="hint">What's on your mind?</span>
            <textarea class="edit watermark" name="msg" placeholder="What's on your mind?" title="<?=$feedback_msg?>"><?=$msg?></textarea>
        </label>
        <label> <?=recaptcha_get_html(RECAPTCHA_PUBLIC_KEY)?> </label>
        <label>
            <span class="hint">&nbsp;</span>
            <input class="button" type="submit" value="Submit" />
        </label>
    </form>
<?php else: ?>
    <span>Thanks for the feedback!</span>
<?php endif; ?>
</div>
