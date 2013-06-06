<?php
$params = array(
    'upcoming_list' => array(),
);

extract($params, EXTR_SKIP);

?>
<div class="pg">
    <div class="data">
        <table class="tblDefault">
            <thead>
                <td>event_id</td>
                <td>name</td>
                <td>notes</td>
                <td>address</td>
                <td>dates</td>
            </thead>
            <tbody>
            <?php foreach ($upcoming_list as $row):
                $event_id = $row['event_id'];
                $name = $row['name'];
                $notes = $row['notes'];
                $address = $row['address'];
                $dates = $row['dates'];

                $edit_url = "/event/edit/{$event_id}";
            ?>
                <tr>
                    <td><a href="<?=$edit_url?>"><?=$event_id?></a></td>
                    <td><?=$name?></td>
                    <td><?=$notes?></td>
                    <td><?=$address?></td>
                    <td><?=$dates?></td>
                </tr>
            <?php endforeach; // foreach ($upcoming_list as $row): ?>
            </tbody>
        </table>
    </div>
</div>
