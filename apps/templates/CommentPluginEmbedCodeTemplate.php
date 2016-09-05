<?php $base = '/js/';
$script_name = 'cmt_plugin';
if (DEBUG) {
    $script_url = $base . $script_name . '.js';
} else {
    $script_url = $base . 'min/' . $script_name . '.min.js';
} ?>
<script>(function(d, s, id) {
        if (d.getElementById(id)) return;
        var js, p = d.getElementsByTagName(s)[0];
        js = d.createElement(s);
        js.id = id;
        js.src = "<?php assign($this->setVersion($script_url)); ?>";
        p.parentNode.insertBefore(js, p);
    }(document, 'script', 'monipla_jsplugin'));</script>
<div class="jsMoniPluginContainer" data-href="<?php assign(Util::rewriteUrl('plugin', 'embed', array($data['plugin_code']))) ?>"></div>