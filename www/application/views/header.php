<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo SITE_NAME; ?></title>
<?php $this->includeCss(); ?>
</head>
<?php
flush();
?>
<body>
<div id="header">
    <div class="pg">
        <div id="top_nav">
        <?php $this->includeNavLinks(); ?>
        </div>
        <span id="welcome"><a href="/home/main"><?php echo SITE_NAME; ?></a></span> 
    </div>
</div>
