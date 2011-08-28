<hr/>
<div class="pg"><pre><?php echo date('r'); ?></pre></div>
</body>
<?php $this->includeJs(); ?>
<script type="text/javascript">
$(document).ready(function() {
<?php 
$clsName = "js_{$this->m_base_name}";
$funcName = "{$clsName}.onDocReady"; 
echo<<<EOL
   if ((typeof({$clsName}) != 'undefined') && (typeof({$funcName}) != 'undefined'))
       {$funcName}();

EOL;
?>
});
</script>
</html>