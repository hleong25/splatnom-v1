<hr/>
<div class="pg">
    <pre><?php echo date('r'); ?></pre>
    <span style="font-size: 0.7em">Execution time: <?php printf('%.03f', scriptExecutionTime()); ?> seconds</span>
</div>
</body>
<?php flush(); ?>
<?php $this->includeJs(); ?>
</html>
