<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array (
		'brand' => $data ['brand'] 
) ) )?>
<script type="text/javascript">
$(function() {
	window.parent.CKEDITOR.tools.callFunction('<?php assign($data['cback'])?>', '<?php assign($data['url'])?>');
	window.close();
});
</script>
<?php write_html($this->parseTemplate('BrandcoModalFooter.php')) ?>