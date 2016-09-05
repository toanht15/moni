<div class="commentPostWrap jsCUInputBlock">
    <from class="commentForm">
        <div class="commentPost">
            <div class="userData">
                <p class="userImage jsUserImage">
                    <img onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';"></p>
                <!-- /.userData --></div>
            <div class="postBody">
                <p class="postUserName jsUserName">
                    <?php write_html($this->formText('nickname', '', array('class' => 'jsUserNameInput', 'placeholder' => 'ニックネーム', 'maxlength' => 40))) ?></p>

                <div class="postText">
                    <div class="postTextEdit empty jsCommentText" contenteditable="true" data-placeholder="コメントを追加"><div><br></div></div>
                    <!-- /.postText --></div>

                <div class="userActionWrap">
                    <div class="shareSns jsShareAction">
                        <p>共有</p>
                        <ul class="selectSns">
                            <li class="jsShareFacebook" style="display: none;"><label><input type="checkbox" checked="checked" name="social_media_ids" value="<?php assign(SocialAccountService::SOCIAL_MEDIA_FACEBOOK) ?>"><span class="iconFb1">Facebook</span></label></li>
                            <li class="jsShareTwitter" style="display: none;"><label><input type="checkbox" checked="checked" name="social_media_ids" value="<?php assign(SocialAccountService::SOCIAL_MEDIA_TWITTER) ?>"><span class="iconTw1">Twitter</span></label></li>
                            <!-- /.selectSns --></ul>
                        <!-- /.shareSns --></div>
                    <p class="postSubmit"><span class="btnSubmit1"><a href="javascript:void(0);" class="jsCommentSubmitBtn">投稿する</a></span></p>
                    <!-- /.userActionWrap --></div>
                <!-- /.postBody --></div>
            <!-- /.commentPost --></div>
        <!-- /.commentForm --></from>
    <!-- /.commentPostWrap --></div>
