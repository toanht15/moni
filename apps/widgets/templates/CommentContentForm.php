<h1 class="<?php assign($data['parent_class_name']); ?>Hd1">投稿確認</h1>

<form id="frmEntry" name="frmEntry" action="<?php assign(Util::rewriteUrl('auth', 'popup_signup_post')); ?>" method="POST">
    <?php write_html($this->csrf_tag()); ?>

    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/moniplaComment.css'))?>">
    <h2 class="hd2">投稿内容</h2>
    <section class="commentPostPreviw">
        <div class="commentPostWrap" id="moniplaCommentPlugin">
            <div class="commentPost">
                <div class="userData">
                    <p class="userImage">
                        <img src="<?php assign($data['pageStatus']['userInfo']->socialAccounts[0]->profileImageUrl ? $data['pageStatus']['userInfo']->socialAccounts[0]->profileImageUrl :$this->setVersion('/img/base/imgUser1.jpg')) ?>" alt="" width="40" height="40" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';"></p>
                    <!-- /.userData --></div>
                <div class="postBody">
                    <p class="postUserName"><?php assign(!Util::isNullOrEmpty($data['pageStatus']['commentData']['nickname']) ? $data['pageStatus']['commentData']['nickname'] : $data['pageStatus']['userInfo']->name) ?></p>
                    <div class="postText">
                        <div class="postTextEdit jsCommentText" contenteditable="false" data-placeholder="コメントを追加">
                            <?php write_html($data['pageStatus']['commentData']['comment_text']) ?>
                            <!-- /.postTextEdit --></div>
                        <!-- /.postText --></div>
                    <!-- /.postBody --></div>
                <!-- /.commentPost --></div>
            <!-- /.commentPostWrap --></div>
        <!-- /.commentPostPreviw --></section>
    <?php if (count($data['pageStatus']['share_sns_list']) > 0): ?>
        <div id="moniplaCommentPlugin">
            <div class="commentPost">
                <div class="userActionWrap">
                    <div class="shareSns">
                        <p>共有</p>
                        <ul class="selectSns">
                            <?php foreach ($data['pageStatus']['share_sns_list'] as $share_sns): ?>
                                <?php if ($share_sns == SocialAccountService::$socialAccountLabel[SocialAccountService::SOCIAL_MEDIA_FACEBOOK]): ?>
                                    <li><label><input type="checkbox" checked="checked" name="social_media_ids[]" value="<?php assign(SocialAccountService::SOCIAL_MEDIA_FACEBOOK) ?>"><span class="iconFb1">Facebook</span></label></li>
                                <?php endif ?>
                                <?php if ($share_sns == SocialAccountService::$socialAccountLabel[SocialAccountService::SOCIAL_MEDIA_TWITTER]): ?>
                                    <li><label><input type="checkbox" checked="checked" name="social_media_ids[]" value="<?php assign(SocialAccountService::SOCIAL_MEDIA_TWITTER) ?>"><span class="iconTw1">Twitter</span></label></li>
                                <?php endif ?>
                            <?php endforeach ?>
                            <!-- /.selectSns --></ul>
                        <!-- /.shareSns --></div>
                    <!-- /.userActionWrap --></div>
                <!-- /.commentPost --></div>
            <!-- /#moniplaCommentPlugin --></div>
    <?php endif ?>

    <div class="textSetBtn">
        <p class="supplement1">以下ボタンを押すと入力したコメントが投稿されます。</p>
        <p class="btnSet"><span class="btn3"><a href="javascript:void(0);" id="submitEntry" class="large1" onclick="document.frmEntry.submit();">投稿して完了する</a>
            </span></p>
    </div>
</form>
