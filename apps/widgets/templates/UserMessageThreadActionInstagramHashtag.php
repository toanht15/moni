<?php if ($data['message_info']['action_status']->status == CpUserActionStatus::JOIN): ?>
    <?php $disabled = 'disabled'; ?>
<?php endif; ?>

<?php if (!$data['instagram_hashtag_user']): ?>
    <section class="message_hashtag jsMessage" id="message_<?php assign($data['message_info']["message"]->id); ?>" >
        <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

        <form class="executeInstagramHashtagActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_instagram_hashtag_action.json")); ?>" method="POST" enctype="multipart/form-data">
            <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
            <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>
            <?php write_html($this->formHidden('api_execute_instagram_hashtag_account_register', Util::rewriteUrl('messages', "api_execute_instagram_hashtag_account_register.json"))) ?>
            <?php write_html($this->csrf_tag()); ?>
            <h1 class="messageHd1">Instagramに投稿しよう！</h1>
            <div class="module">
                <dl>
                    <?php if($data["message_info"]["concrete_action"]->image_url): ?>
                        <p class="messageImg"><img src="<?php assign($data["message_info"]["concrete_action"]->image_url); ?>" alt="campaign img"></p>
                    <?php endif; ?>
                    <?php $message_text = $data['message_info']['concrete_action']->html_content ? $data['message_info']['concrete_action']->html_content : $this->toHalfContentDeeply($data['message_info']['concrete_action']->text); ?>
                    <section class="messageText"><?php write_html($message_text); ?></section>
                    <dt>Instagramのユーザーネームを登録してください。<br>登録後、投稿用のハッシュタグが表示されます。</dt>
                    <dd>
                        <p class="iconError1 instagram_user_name" style="display: none"></p>
                        <p class="entryAccount">
                            <?php if (!$disabled): ?>
                                <input type="text" name="instagram_user_name" placeholder="ユーザーネームを入力してください。">
                            <?php else: ?>
                                <input type="text" name="instagram_user_name" placeholder="ユーザーネームを入力してください。" disabled="disabled">
                            <?php endif; ?>
                        </p>
                        <p class="guide"><a href="javascript:void(0)" class="guides linkQuestion jsCheckUserName">ユーザーネームの確認方法</a></p>
                        <ul class="btnSet">
                            <li class="btn3">
                                <?php if (!$disabled): ?>
                                    <a href="javascript:void(0);" class="large1 cmd_execute_instagram_hashtag_action">登録する</a>
                                <?php else: ?>
                                    <span class="large1">登録する</span>
                                <?php endif; ?>
                            </li>
                        </ul>
                        <span class="supplement1">※ユーザーネーム登録後の変更はできません。</span>

                        <?php if (!$data['is_last_action'] && !$disabled && $data['cp_instagram_hashtag_action']->skip_flg): ?>
                            <p class="messageSkip"><a href="javascript:void(0)"><small class="cmd_execute_instagram_hashtag_action_skip">登録せず次へ</small></a></p>
                        <?php endif; ?>
                    </dd>
                <!-- /.module --></dl>
            </div>
            <?php if ($data['instagram_hashtag_user_random_posts']): ?>
                <div class="moduleItemUserImg">
                    <div class="moduleItemUserInner">
                        <h2 class="hd2">みんなの投稿写真</h2>
                        <ul>
                            <?php if ($data['instagram_hashtag_user_random_posts']): ?>
                                <?php foreach ($data['instagram_hashtag_user_random_posts'] as $instagram_hashtag_user_post): ?><li>
                                    <figure>
                                        <a href="javascript:void(0);">
                                            <img src="<?php assign($instagram_hashtag_user_post['standard_resolution']); ?>" width="123" height="123" alt="" class="jsPreviewInstagramUserPost"
                                                 data-instagram_hashtag_user_post_id="<?php assign($instagram_hashtag_user_post['id']) ?>"
                                                 data-modal_id="#instagram_modal"
                                                 data-media_url="<?php assign($instagram_hashtag_user_post['link']) ?>">
                                                    <span class="previwe jsPreviewInstagramUserPost"
                                                          data-instagram_hashtag_user_post_id="<?php assign($instagram_hashtag_user_post['id']) ?>"
                                                          data-modal_id="#instagram_modal"
                                                          data-media_url="<?php assign($instagram_hashtag_user_post['link']) ?>"
                                                        >拡大表示する</span></a>
                                    </figure>
                                    </li><?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                        <!-- /.moduleItemUserInner --></div>
                    <p class="supplement1">※Instagramのサービス状況により、全ての画像が表示されない場合があります。</p>
                    <!-- /.moduleItemUserImg --></div>
            <?php endif; ?>
        </form>
    <!-- /.message_hashtag --></section>
<?php else: ?>
    <section class="message_hashtag jsMessage inview" id="message_<?php assign($data['message_info']["message"]->id); ?>" >
        <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

        <form class="executeInstagramHashtagActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_instagram_hashtag_action.json")); ?>" method="POST" enctype="multipart/form-data">
            <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
            <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>
            <?php write_html($this->csrf_tag()); ?>
            <?php write_html($this->formHidden('status_check_url', Util::rewriteUrl('messages', "api_execute_instagram_hashtag_check_status.json"))); ?>
            <?php write_html($this->formHidden('autoload_flg', $data["message_info"]["concrete_action"]->autoload_flg)); ?>
            <h1 class="messageHd1">Instagramに投稿しよう！</h1>
            <div class="module">
                <?php if($data["message_info"]["concrete_action"]->image_url): ?>
                    <p class="messageImg"><img src="<?php assign($data["message_info"]["concrete_action"]->image_url); ?>" alt="campaign img"></p>
                <?php endif; ?>
                <?php $message_text = $data['message_info']['concrete_action']->html_content ? $data['message_info']['concrete_action']->html_content : $this->toHalfContentDeeply($data['message_info']['concrete_action']->text); ?>
                <section class="messageText"><?php write_html($message_text); ?></section>

                <p class="userId">あなたのユーザーネームは<span><?php assign($data['instagram_hashtag_user']->instagram_user_name); ?></span>です</p>
                <p class="hashtagText">
                    <?php if ($data["message_info"]["concrete_action"]->isExistsCpInstagramHashtags()):  ?>
                        <?php
                        $service_factory = new aafwServiceFactory();
                        /** CpInstagramHashtagService $cp_instagram_hashtag_service */
                        $cp_instagram_hashtag_service = $service_factory->create('CpInstagramHashtagService');
                        ?>
                        <?php foreach($cp_instagram_hashtag_service->getCpInstagramHashtagsOrderById($data["message_info"]["concrete_action"]->id) as $cp_hashtag): ?>
                            #<?php write_html($cp_hashtag->hashtag); ?>
                         <?php endforeach; ?>
                    <?php endif; ?>
                </p>
                <div class="instaAttention">
                    <p>Instagramアプリから上記のハッシュタグをつけて投稿してください。<br>
                        Instagramアカウントは公開状態にしてください。
                    </p>
                    <!-- /.instaAttention1 --></div>
                <ul class="btnSet">
                    <?php if (!$data['is_last_action'] && !$data["message_info"]["concrete_action"]->autoload_flg): ?>
                        <?php if (!$disabled): ?>
                            <li class="btn3 cmd_execute_instagram_hashtag_action_next"><a href="javascript:void(0)" class="middle1"><?php write_html($data["message_info"]["concrete_action"]->button_label_text) ?></a></li>
                        <?php else: ?>
                            <li class="btn3"><span class="middle1"><?php assign($data["message_info"]["concrete_action"]->button_label_text); ?></span></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            <!-- /.module --></div>

            <?php if ($data['instagram_hashtag_user_random_posts']): ?>
                    <div class="moduleItemUserImg">
                        <div class="moduleItemUserInner">
                            <h2 class="hd2">みんなの投稿写真</h2>
                            <ul>
                                <?php if ($data['instagram_hashtag_user_random_posts']): ?>
                                    <?php foreach ($data['instagram_hashtag_user_random_posts'] as $instagram_hashtag_user_post): ?><li>
                                            <figure>
                                                <a href="javascript:void(0);">
                                                    <img src="<?php assign($instagram_hashtag_user_post['standard_resolution']); ?>" width="123" height="123" alt="" class="jsPreviewInstagramUserPost"
                                                        data-instagram_hashtag_user_post_id="<?php assign($instagram_hashtag_user_post['id']) ?>"
                                                        data-modal_id="#instagram_modal"
                                                        data-media_url="<?php assign($instagram_hashtag_user_post['link']) ?>">
                                                    <span class="previwe jsPreviewInstagramUserPost"
                                                        data-instagram_hashtag_user_post_id="<?php assign($instagram_hashtag_user_post['id']) ?>"
                                                        data-modal_id="#instagram_modal"
                                                        data-media_url="<?php assign($instagram_hashtag_user_post['link']) ?>"
                                                        >拡大表示する</span></a>
                                            </figure>
                                        </li><?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        <!-- /.moduleItemUserInner --></div>
                        <p class="supplement1">※Instagramのサービス状況により、全ての画像が表示されない場合があります。</p>
                        <ul class="btnSet">
                            <?php if (!$data['is_last_action'] && !$data["message_info"]["concrete_action"]->autoload_flg): ?>
                                <?php if (!$disabled): ?>
                                    <li class="btn3 cmd_execute_instagram_hashtag_action_next"><a href="javascript:void(0)" class="middle1"><?php write_html($data["message_info"]["concrete_action"]->button_label_text) ?></a></li>
                                <?php else: ?>
                                    <li class="btn3"><span class="middle1"><?php assign($data["message_info"]["concrete_action"]->button_label_text); ?></span></li>
                                <?php endif; ?>
                            <?php endif; ?>
                        </ul>
                    <!-- /.moduleItemUserImg --></div>
            <?php endif; ?>

            <?php if (!$disabled && $data["message_info"]["concrete_action"]->autoload_flg): ?>
                <div class="cmd_execute_instagram_hashtag_action_autoload" data-messageid="<?php assign($data['message_info']["message"]->id); ?>"></div>
            <?php endif; ?>
        </form>
    <!-- /.message_hashtag --></section>
<?php endif; ?>

<?php write_html($this->scriptTag("user/UserActionInstagramHashtagService")); ?>