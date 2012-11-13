<?php
$params = array(
    'queue' => array(),
);

extract($params, EXTR_SKIP);
?>
<div class="pg">
<?php if (empty($queue)): ?>
    <p>No emails to process.</p>
<?php else: ?>
    <p><button onclick="location.href='/admin/process_email_queue'">process all emails</button></p>
    <br/>
    <table class="tblDefault">
    <thead>
        <td>timestamp</td>
        <td>status / attempts</td>
        <td>email info</td>
        <td>details</td>
    </thead>
    <tbody>
        <?php foreach ($queue as $q): ?>
            <tr>
                <td class="small ts"><?=$q['ts']?></td>
                <td class="small status"><?=$q['mail_status']?> / <?=$q['attempts']?></td>
                <td class="small emails">
                    <p>from: <?=$q['from_addy']?></p>
                    <p>to: <?=$q['to_addy']?></p>
                    <p>subject: <?=$q['subject']?></p>
                    <br/>
                    <button onclick="window.open('/admin/preview_email/<?=$q['mail_id']?>');">view</button>
                </td>
                <td class="msg">
                    <textarea rows="5"><?=$q['message']?></textarea><br/>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    </table>
<?php endif; ?>
</div>
