<?php if ($data['message_info']['action_status']->status == CpUserActionStatus::JOIN):
    $disabled = 'disabled';
endif; ?>

<section class="message jsMessage" id="message_<?php assign($data['message_info']["message"]->id); ?>" >
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="executePhotoActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_photo_action.json")); ?>" method="POST" enctype="multipart/form-data">
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>
        <?php write_html($this->formHidden('connect_url', Util::rewriteUrl('auth', 'campaign_login'))); ?>
        <?php $num = strpos(Util::getCurrentUrl(), '?'); ?>
        <?php write_html($this->formHidden('redirect_url', $num ? mb_substr(Util::getCurrentUrl(), 0, $num) : Util::getCurrentUrl())); ?>
        <?php write_html($this->formHidden('cp_id', $data['cp_user']->cp_id)); ?>
        <?php write_html($this->formHidden('sns_data_cache_url', Util::rewriteUrl('','') . 'sns/api_sns_connect_cache.json')); ?>
        <div class="connectFbUrl" style="display:none"><?php assign(Util::rewriteUrl('auth', 'campaign_login', array(), array('platform' => 'fb', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['cp_user']->cp_id))); ?></div>
        <div class="connectTwUrl" style="display:none"><?php assign(Util::rewriteUrl('auth', 'campaign_login', array(), array('platform' => 'tw', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['cp_user']->cp_id))); ?></div>
        <div class="targetTwId"></div>
        <div class="targetFbId"></div>

        <?php if($data["message_info"]["concrete_action"]->image_url): ?>
            <p class="messageImg"><img src="<?php assign($data["message_info"]["concrete_action"]->image_url); ?>" alt="campaign img"></p>
        <?php endif; ?>
        <?php $message_text = $data['message_info']['concrete_action']->html_content ? $data['message_info']['concrete_action']->html_content : $this->toHalfContentDeeply($data['message_info']['concrete_action']->text); ?>
        <section class="messageText"><?php write_html($message_text); ?></section>

        <p class="module">
            <span class="iconError1 js_photo_error invalid_upload_file" style="display:none"></span>
            <label class="fileUpload_img">
                <span class="thumb">
                    <img <?php write_html($data['photo_user']->photo_url ? "" : "style='display:none'" );?>
                        src="<?php assign($data['photo_user']->photo_url)?>"
                        class="photo_image">
                    <?php write_html($this->formHidden('cache_photo_url', $data['photo_user']->photo_url)); ?>
                </span>
                <input type="file" <?php assign($data["message_info"]["action_status"]->status == CpUserActionStatus::JOIN ? "disabled='disabled'" : ""); ?> name="photo_image" class="action_image" accept="image/*" multiple="multiple" />
                <!-- /.fileUpload_img --></label>
            <!-- /.module --></p>

        <dl class="module">
            <?php if($data["message_info"]["concrete_action"]->title_required): ?>
                <dt class="require1">タイトル</dt>
                <dd>
                    <p class="iconError1 js_photo_error invalid_photo_title" style="display: none;">入力してください</p>
                    <?php write_html($this->formText('photo_title', $data['photo_user']->photo_title ? $data['photo_user']->photo_title : PHPParser::ACTION_FORM, array('placeholder' => '自由記述', 'maxlength' => 50, $disabled => $disabled, 'class' => 'jsActionTitle'))); ?>
                </dd>
            <?php endif; ?>
            <?php if($data["message_info"]["concrete_action"]->comment_required): ?>
                <dt class="require1">コメント</dt>
                <dd>
                    <p class="iconError1 js_photo_error invalid_photo_comment" style="display: none">入力してください</p>
                    <?php write_html($this->formTextArea('photo_comment', $data['photo_user']->photo_comment ? $data['photo_user']->photo_comment : PHPParser::ACTION_FORM, array('placeholder' => '自由記述', 'maxlength' => 300, $disabled => $disabled, 'class' => 'jsActionComment'))); ?>
                    <span class="supplement1">（最大300文字）</span>
                </dd>
            <?php endif; ?>
        <!-- /.module --></dl>

        <?php if (($data['message_info']['concrete_action']->fb_share_required && $data['fb_has_permission'] ||
            $data['message_info']['concrete_action']->fb_share_required && !$data['fb_connect'] && !$data['fb_has_permission'] ||
            $data['message_info']['concrete_action']->tw_share_required)): ?>
            <dl class="module">
                <dt>SNSに投稿しよう！</dt>
                <dd>
                    <?php if ($data['message_info']['concrete_action']->fb_share_required || $data['message_info']['concrete_action']->tw_share_required): ?>
                        <?php write_html($this->formTextArea('share_text', $data['share_text'] ? $data['share_text'] : '', array('placeholder' => $data['message_info']['concrete_action']->share_placeholder, 'maxlength' => PhotoUserShare::SHARE_TEXT_LENGTH, $disabled => $disabled, 'class' => 'jsActionShareText'))); ?>
                    <?php endif; ?>
                    <span class="supplement1">（最大<?php assign(PhotoUserShare::SHARE_TEXT_LENGTH) ?>文字）</span>
                    <ul class="moduleSnsList">
                        <?php if ($data['message_info']['concrete_action']->fb_share_required): ?>
                            <li>
                                <?php if ($data['fb_connect'] && $data['fb_has_permission']): // 連携済みかつパーミッションあり ?>
                                    <label>
                                        <input type="checkbox" class="jsActionShareFb" <?php assign(!$disabled ? 'checked=checked' : $data['photo_user_fb_share'] ? 'checked=checked' : '') ?> name="fb_share_flg" <?php assign($disabled ? 'disabled=disabled' : '') ?> value='1'/>
                                        <img src="<?php assign($this->setVersion('/img/sns/iconSnsFB2.png')); ?>" alt="Facebook">
                                    </label>
                                <?php elseif ($data['fb_connect'] && !$data['fb_has_permission']): // 連携済みかつパーミッションなし ?>
                                <?php else: ?>
                                    <a href="javascript:void(0)" class="linkAdd jsFbConnect" data-platform="fb"><img src="<?php assign($this->setVersion('/img/sns/iconSnsFB2.png')) ?>" alt="Facebook">を追加</a>
                                <?php endif; ?>
                            </li>
                        <?php endif; ?>
                        <?php if ($data['message_info']['concrete_action']->tw_share_required): ?>
                            <li>
                                <?php if ($data['tw_connect']): ?>
                                    <label>
                                        <input type="checkbox" class="jsActionShareTw" <?php assign(!$disabled ? 'checked=checked' : $data['photo_user_tw_share'] ? 'checked=checked' : '') ?> name="tw_share_flg" <?php assign($disabled ? 'disabled=disabled' : '') ?> value='1'/>
                                        <img src="<?php assign($this->setVersion('/img/sns/iconSnsTW2.png')); ?>" alt="Twitter">
                                    </label>
                                <?php else: ?>
                                    <a href="javascript:void(0)" class="linkAdd jsTwConnect" data-platform="tw"><img src="<?php assign($this->setVersion('/img/sns/iconSnsTW2.png')) ?>" alt="Twitter">を追加</a>
                                <?php endif; ?>
                            </li>
                        <?php endif; ?>
                    <!-- /.moduleSnsList --></ul>
                </dd>
            </dl>
        <?php endif; ?>

        <div class="messageFooter">
            <ul class="btnSet">
                <?php if ($data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN) : ?>
                    <li class="btn3"><a class="cmd_execute_photo_action large1" href="javascript:void(0)"><?php assign($data["message_info"]["concrete_action"]->button_label_text); ?></a></li>
                <?php else: ?>
                    <li class="btn3"><span class="large1"><?php assign($data["message_info"]["concrete_action"]->button_label_text); ?></span></li>
                <?php endif; ?>
            <!-- /.btnSet --></ul>
            
        </div>
    </form>

    <div class="uploadCont jsUserUploadCont" <?php if (!$data['photo_user'] || $data['photo_user']->cache) write_html('style="display: none"'); ?>>
        <p>あなたの投稿<br>
            <?php if($data['photo_entry']->id && !$data['photo_entry']->hidden_flg): ?>
                <a href="<?php write_html(Util::rewriteUrl('photo', 'detail', array($data['photo_entry']->id))); ?>" target="_blank" class="openNewWindow1 jsUserPhotoLink"><?php write_html(Util::rewriteUrl('photo', 'detail', array($data['photo_entry']->id))); ?></a>
            <?php endif; ?>
        </p>
        <p class="photo">
            <span><img src="<?php assign($data['photo_user']->photo_url); ?>" alt="<?php assign($data['photo_user']->photo_title); ?>" class="jsUserPhotoImage"></span>
            <span class="comment jsUserPhotoData" <?php if (!$data['photo_user']->photo_title && !$data['photo_user']->photo_comment) write_html('style="display: none"'); ?>><strong><?php assign($data['photo_user']->photo_title); ?></strong><?php write_html($this->toHalfContentDeeply($data['photo_user']->photo_comment)); ?></span>
        </p>
        <!-- /.uploadCont --></div>
<!-- /.message --></section>
<?php write_html($this->scriptTag('user/UserActionPhotoService')); ?>