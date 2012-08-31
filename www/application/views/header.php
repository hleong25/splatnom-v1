<?php
$site = Util::getDomain();
$params = array(
    'meta_url' => "http://{$site}/{$myurl}",
    'meta_title' => '',
    'meta_image' => '',
    'meta_desc' => '',
);

extract($params, EXTR_SKIP);

$title = SITE_NAME;

$meta_url   = htmlspecialchars($meta_url);
$meta_title = htmlspecialchars($meta_title);
$meta_desc  = htmlspecialchars($meta_desc);

if (!empty($meta_title))
    $title = "{$meta_title} - {$title}";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 //EN" "http://www.w3.org/TR/html4/loose.dtd">
<html class="no-js">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta property="og:url" content="<?=$meta_url?>" />
<meta property="og:title" content="splatnom wants to share '<?=$meta_title?>'" />
<meta property="og:type" content="website" />
<meta property="og:image" content="<?=$meta_image?>" />
<meta property="og:description" content="<?=$meta_desc?>" />
<?php
// viewport for mobile devices
// http://webdesign.tutsplus.com/tutorials/htmlcss-tutorials/quick-tip-dont-forget-the-viewport-meta-tag/
?>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title><?=$title?></title>
<?php foreach ($this->getCss() as $css): ?>
    <link rel="stylesheet" href="<?=$css?>.css" />
<?php endforeach; //foreach ($allCss as $css): ?>
</head>
<?php
flush();
?>
<body>
<div id="header">
    <div id="nav" class="pg"><?php
        $bCont = false;
        foreach ($this->getNavLinks() as $lnk)
        {
            if ($bCont)
                echo '<span class="lnkspc"> | </span>';

            echo "<a class=\"{$lnk['css']}\" href=\"/{$lnk['lnk']}\">{$lnk['lbl']}</a>";

            $bCont = true;
        }
    ?></div>
    <div class="pg welcome">
        <?php if (($myurl != '') && ($myurl != 'home/main')): ?>
        <a href="/home/main"><img src="/img/logo.mini.jpg" title="splatnom"/></a>
        <?php else: ?>
        <div class="nologo" style="height: 48px;"></div>
        <?php endif; ?>
    </div>
</div>
