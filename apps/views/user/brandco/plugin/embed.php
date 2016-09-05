<?php if (!Util::isNullOrEmpty($data['comment_plugin'])): ?>
    <?php write_html($this->parseTemplate('EmbedIframeHeader.php', array('brand' => $data['pageStatus']['brand']))) ?>

    <?php if (!Util::isNullOrEmpty($data['preview_mode'])): ?>
        <article>
            <section class="previewWrap">
                <div class="ckeditorWrap">
                    <?php write_html($data['comment_plugin']->free_text) ?>
                    <!-- /.ckeditorWrap --></div>

                <p class="smapleImg">
                    <img src="<?php assign($this->setVersion('/img/comment/imgCommentPlugin.png')) ?>" alt="コメントプラグインサンプル画像">
                </p>

                <div class="ckeditorWrap">
                    <?php write_html($data['comment_plugin']->footer_text) ?>
                    <!-- /.ckeditorWrap --></div>
                <!-- /.previewWrap --></section>
        </article>
    <?php else: ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('CommentPluginWidget')->render(array('comment_plugin' => $data['comment_plugin']))) ?>
    <?php endif ?>

    <?php write_html($this->parseTemplate('EmbedIframeFooter.php')) ?>
<?php endif;