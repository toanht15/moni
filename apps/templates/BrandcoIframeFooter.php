<?php write_html($this->parseTemplate('GoogleAnalytics.php'));?>
<?php write_html($this->parseTemplate('Rtoaster.php'));?>

<?php if (extension_loaded ('newrelic')) {
    $config = aafwApplicationConfig::getInstance();
    if($config->NewRelic['use']) {
        write_html(newrelic_get_browser_timing_footer());
    }
} ?>
<script>
    jQuery(function($){
        var pageHeight = $(this).height();
        $('#signupIframe1', parent.document).css('height', pageHeight);

        if (window.addEventListener) {
            function aa_submit(event) {

                if (!$(this).data('submitted')) {
                    $(this).data('submitted', true);
                    this._submit();
                }
            }

            // onsubmitイベント対応
            window.addEventListener('submit', aa_submit, true);

            //.submit()対応
            HTMLFormElement.prototype._submit = HTMLFormElement.prototype.submit;
            HTMLFormElement.prototype.submit = aa_submit;
        }
    });
</script>
</body>
</html>