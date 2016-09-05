<div id="moniplaCommentPlugin">
    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/moniplaComment.css'))?>">
    <?php write_html($this->csrf_tag()); ?>
    <?php write_html($this->formHidden('comment_plugin_id', $data['comment_plugin']->id)) ?>
    <?php write_html($this->formHidden('loading_url', Util::rewriteUrl('plugin', 'loading'))) ?>
    <?php write_html($this->formHidden('device', Util::isSmartPhone() ? 'sp' : 'pc')) ?>

    <?php if ($data['comment_plugin']->isEmbedPlugin()): ?>
        <div class="pluginHeader">
            <p class="commentCopyright"><small class="copy">powered by <img src="<?php assign($this->setVersion('/img/comment/imgLogoMonipla_lg.png')) ?>" alt="monipla"></small></p>
            <!-- /.pluginHeader --></div>
    <?php endif ?>

    <div class="ckeditorWrap">
        <?php write_html($data['comment_plugin']->free_text) ?>
        <!-- /.ckeditorWrap --></div>

    <div class="threadTitleWrap">
        <p class="threadComment"><strong class="inner"><span class="jsCommentCounter">0</span>コメント</strong></p>
        <!-- /.threadTitleWrap --></div>

    <div class="jsCommentContainer">
        <!-- /.jsCommentContainer --></div>

    <div class="ckeditorWrap">
        <?php write_html($data['comment_plugin']->footer_text) ?>
        <!-- /.ckeditorWrap --></div>

    <script type="text/html" id="cu_input_template">
        <?php write_html($this->parseTemplate('plugin/CommentUserInputTemplate.php')); ?>
    </script>
    <script type="text/html" id="cur_input_template">
        <?php write_html($this->parseTemplate('plugin/CommentUserReplyInputTemplate.php')); ?>
    </script>
    <script type="text/html" id="cu_container_template">
        <?php write_html($this->parseTemplate('plugin/CommentUserContainerTemplate.php')); ?>
    </script>
    <script type="text/html" id="cu_content_container_template">
        <?php write_html($this->parseTemplate('plugin/CommentUserContentContainerTemplate.php')); ?>
    </script>
    <script type="text/html" id="hide_cu_content_template">
        <div class="commentNotdisplay jsContentContainer">
            <p class="innerText">この投稿は非表示になりました。<a href="javascript:void(0);" class="jsShowLink">元に戻す</a></p>
            <!-- /.commentNotdisplay --></div>
    </script>
    <script type="text/html" id="reply_load_more_template">
        <div class="commentNotdisplay jsLoadMoreReplyContainer">
            <p class="innerText"><a href="javascript:void(0);" class="jsLoadMoreReplyLink">他<span class="jsRemainingReplyCounter">0</span>件の返信を見る</a></p>
            <!-- /.commentNotdisplay --></div>
    </script>
    <script type="text/html" id="cmt_load_more_template">
        <p class="commentReadMore jsLoadMoreCmt"><span class="btnReadmore1"><a href="javascript:void(0);" class="jsLoadMoreLink">もっとコメントを見る</a></span></p>
    </script>

    <p id="loading" class="loading" style="display:none">
        <img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" alt="loading">
        <!-- /.loading --></p>
    <a id="pinAction" href="#scrollTarget"></a>
    <a id="scrollTarget"></a>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <?php write_html($this->scriptTag('plugin/CommentPluginUtil'))?>
    <?php write_html($this->scriptTag('plugin/CommentPluginService'))?>
    <!-- /#moniplaCommentPlugin --></div>