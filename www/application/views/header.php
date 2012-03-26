<?php
$site = Util::getTopLevelDomain();
$params = array(
    'meta_url' => "http://{$site}/{$myurl}",
    'meta_title' => '',
    'meta_image' => '',
    'meta_desc' => '',
);

extract($params, EXTR_SKIP);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 //EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta property="og:url" content="<?=$meta_url?>" />
<meta property="og:title" content="splatnom wants to share '<?=$meta_title?>'" />
<meta property="og:type" content="website" />
<meta property="og:image" content="<?=$meta_image?>" />
<meta property="og:description" content="<?=$meta_desc?>" />
<title><?=SITE_NAME?><?=(empty($meta_title)?'':" - {$meta_title}")?></title>
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
                printf('<span class="lnkspc"> | </span>');

            printf('<a class="%s" href="/%s">%s</a>', $lnk['css'], $lnk['lnk'], $lnk['lbl']);

            $bCont = true;
        }
    ?></div>
    <div class="pg">
        <span id="welcome"><a href="/home/main"><?=SITE_NAME?></a></span>
        <br/>
        <span style="font-size: 0.75em;">find. look. eat. comment.</span>
    </div>
</div>
