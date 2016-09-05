<div id="moniplaCommentPlugin" class="jsMoniplaCommentPlugin" <?php if ($data['comment_plugin']->status == CommentPlugin::COMMENT_PLUGIN_STATUS_PRIVATE): ?>style="display: none"<?php endif ?>>
    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/moniplaComment.css'))?>">

    <div class="threadTitleWrap">
        <p class="threadComment"><strong class="inner">0コメント</strong></p>
        <!-- /.threadTitleWrap --></div>

    <div>
        <div class="commentPostWrap">
            <from class="commentForm">
                <div class="commentPost">
                    <div class="userData">
                        <p class="userImage">
                            <img src="<?php assign($data['comment_plugin']->from['profile_img_url']) ?>" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';"></p>
                        <!-- /.userData --></div>
                    <div class="postBody">
                        <p class="postUserName">
                            <input type="text" value="<?php assign($data['comment_plugin']->from['name']) ?>" placeholder="ニックネーム" maxlength="40"></p>

                        <div class="postText">
                            <div class="postTextEdit empty" contenteditable="true" data-placeholder="コメントを追加"><div><br></div></div>
                            <!-- /.postText --></div>

                        <div class="userActionWrap">
                            <div class="shareSns" <?php if (!count($data['comment_plugin']->from['share_sns_list'])): ?>style="display: none;"<?php endif ?>>
                                <p>共有</p>
                                <ul class="selectSns">
                                    <?php foreach (CommentPluginShareSetting::$comment_plugin_share_settings as $social_media_id => $social_media_name): ?>
                                        <li class="jsShare<?php assign($social_media_id) ?>" <?php if (!in_array($social_media_id, $data['comment_plugin']->from['share_sns_list'])): ?>style="display: none;" <?php endif ?>>
                                            <label><input type="checkbox" checked="checked" value="<?php assign($social_media_id) ?>"><span class="<?php assign(SocialAccountService::$socialBigIconForPlugin[$social_media_id]) ?>"><?php assign($social_media_name) ?></span></label></li>
                                    <?php endforeach ?>
                                    <!-- /.selectSns --></ul>
                                <!-- /.shareSns --></div>
                            <p class="postSubmit"><span class="btnSubmit1"><a href="javascript:void(0);">投稿する</a></span></p>
                            <!-- /.userActionWrap --></div>
                        <!-- /.postBody --></div>
                    <!-- /.commentPost --></div>
                <!-- /.commentForm --></from>
            <!-- /.commentPostWrap --></div>
        <!-- /.jsCommentContainer --></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <?php write_html($this->scriptTag('plugin/CommentPluginUtil'))?>
    <?php write_html($this->scriptTag('plugin/CommentPluginService'))?>
    <!-- /#moniplaCommentPlugin --></div>