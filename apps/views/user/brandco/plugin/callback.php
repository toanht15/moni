<html><head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Please wait …</title>
    <script type="text/javascript">
        window.onload = function () {
            var notifyParent = function () {
                var opener = window.opener;

                if (opener && opener.CommentPluginService) {
                    opener.CommentPluginService.reloadBrowser('<?php write_html($data['anchor_hash']) ?>');
                }
            };

            try {
                notifyParent();
            } catch (err) {
            } finally {
                setTimeout('close()', 300);  // close the window no matter what
            }
        }

    </script>
</head>
<body></body></html>