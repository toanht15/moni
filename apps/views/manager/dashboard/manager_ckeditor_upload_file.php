<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'KPI',
    'managerAccount' => $this->managerAccount,
))) ?>

<script type="text/javascript">
        $(function() {
            window.parent.CKEDITOR.tools.callFunction('<?php assign($data['cback'])?>', '<?php assign($data['url'])?>');
            window.close();
        });
    </script>
<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>