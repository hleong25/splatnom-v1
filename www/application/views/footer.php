<hr/>
<div id="footer" class="pg">
    <div class="feedback clearfix">
        <a href="/home/feedback">feeback</a>
    </div>
</div>
</body>
<?php flush(); ?>
<?php foreach ($this->getJs() as $js): ?>
    <script type="text/javascript" src="<?=$js?>.js"></script>
<?php endforeach; //foreach ($allJs as $Js): ?>
</html>
