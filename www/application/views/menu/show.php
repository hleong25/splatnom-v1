<pre>
<?php
var_export($info);
?>
</pre>
<?php

function addline(&$line, $str)
{
    if (empty($str))
        return;
    
    if (!empty($line))
        $line .= '<br/>';
    
    $line .= $str;
}

function formatAddy($info)
{
    $addy = '';
    
    addline($addy, $info['addy1']);
    addline($addy, $info['addy2']);
    
    $loc = $info['city'];
    
    if (!empty($info['state']))
    {
        if (!empty($loc))
            $loc .= ', ';
        
        $loc .= $info['state'];
    }
    
    if (!empty($info['zip']))
    {
        if (!empty($loc))
            $loc .= '  ';
        
        $loc .= $info['zip'];
    }
    
    addline($addy, $loc);
    
    return $addy;
}


?>
<div class="info">
    <h2><?php echo $info['name']; ?></h2>
    <a href="/menu/edit/<?php echo $menu_id; ?>">edit menu [check if admin first]</a>
    <span><?php echo formatAddy($info); ?></span>
</div>