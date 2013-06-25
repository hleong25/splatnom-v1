<?php
$params = array(
    'list' => array(),
);

extract($params, EXTR_SKIP);

?>
<div class="pg">
    <div class="data">
        <table class="tblDefault">
            <thead>
                <td>status</td>
                <td>event_id</td>
                <td>name</td>
                <td>notes</td>
                <td>address</td>
                <td>dates</td>
            </thead>
            <tbody>
            <?php foreach ($list as $row):
                $status = $row['status'];
                $event_id = $row['event_id'];
                $name = $row['name'];
                $notes = $row['notes'];
                $address = $row['address'];
                $dates = $row['dates'];

                $edit_url = "/event/edit/{$event_id}";
            ?>
                <tr>
                    <td><?=$status?></td>
                    <td><a href="<?=$edit_url?>"><?=$event_id?></a></td>
                    <td><?=$name?></td>
                    <td><?=$notes?></td>
                    <td><?=$address?></td>
                    <td><?=$dates?></td>
                </tr>
            <?php endforeach; // foreach ($list as $row): ?>
            </tbody>
        </table>
    </div>
</div>
