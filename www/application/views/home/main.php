<div id="search" class="pg">
    <form id="searchit" enctype="multipart/form-data" method="post" action="/ws/search">
        <input class="jq_watermark" type="text" name="query" title="Name of the place"/>
        <input class="jq_watermark" type="text" name="location" title="Location"/>
        <input type="submit" value="Search" />
    </form>
    <div id="results">
    </div>
</div>
<div id="new_menus" class="pg">
<?php
if (empty($recently_added))
{
?>
    <span>No menus added...</span>
    <a href="/menu/new">Click here to help me out!</a>
<?php
} // if (empty($recently_added))
else
{
    foreach ($recently_added as $menu)
    {
?>
        <div class="new_added">
            <a href="/menu/show/<?php echo $menu['id']; ?>">
                <img class="new_img" src="/ws/getimage/<?php echo $menu['file_img']; ?>" />
                <span class="new_name"><?php echo $menu['name']; ?></span>
            </a>
        </div>
<?php
    } //foreach ($recently_added as $menus)
} // else
?>
</div>