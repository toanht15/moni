<?php if ($data['page_settings']->tag_text) write_html($data['page_settings']->tag_text)?>
<?php if ($data['extend_tag']) write_html($data['extend_tag']) ?>

<?php // campaign 期間中のみ設定 ?>
<?php if ($_SERVER['REQUEST_URI'] == '/amex/page/smartgift' || $_SERVER['REQUEST_URI'] == '/amex/page/smartgift/'): ?>
    <?php if (Util::isSmartPhone()): ?>
        <script type="text/javascript">
            var axel = Math.random() + "";
            var a = axel * 10000000000000;
            document.write('<iframe src="https://4192053.fls.doubleclick.net/activityi;src=4192053;type=13_No0;cat=140_n0;ord=' + a + '?" width="1" height="1" frameborder="0" style="display:none"></iframe>');
        </script>
        <noscript>
            <iframe src="https://4192053.fls.doubleclick.net/activityi;src=4192053;type=13_No0;cat=140_n0;ord=1?" width="1" height="1" frameborder="0" style="display:none"></iframe>
        </noscript>
    <?php else:?>
        <script type="text/javascript">
            var axel = Math.random() + "";
            var a = axel * 10000000000000;
            document.write('<iframe src="https://4192053.fls.doubleclick.net/activityi;src=4192053;type=13_No0;cat=139_n0;ord=' + a + '?" width="1" height="1" frameborder="0" style="display:none"></iframe>');
        </script>
        <noscript>
            <iframe src="https://4192053.fls.doubleclick.net/activityi;src=4192053;type=13_No0;cat=139_n0;ord=1?" width="1" height="1" frameborder="0" style="display:none"></iframe>
        </noscript>
    <?php endif; ?>
<?php endif; ?>
