<?php if($data['share_media_type'] == SocialAccountService::SOCIAL_MEDIA_FACEBOOK): //Facebookへのシェア画面表示?>
    <section class="message_share jsMessage" id="message_<?php assign($data['message_info']["message"]->id); ?>">
        <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

        <form class="executeShareActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_share_action.json")); ?>" method="POST">
            <?php write_html($this->csrf_tag()); ?>
            <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
            <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>
            <?php write_html($this->formHidden('shared_flg', $data['shared_flg'])); ?>
            <h1 class="messageHd1"><?php assign($data['cp_share_action']->title); ?></h1>
            <div class="shareInner">
                <?php if($data["message_info"]["cp_action"]->id != 33824 && $data["message_info"]["cp_action"]->id != 35260):// TODO: プリマハムHC ?>
                    <div class="targettPost">
                        <div class="figure">
                            <figure><img src="<?php assign($data['cp_share_action']->meta_data ? $data['meta_tags']->image : $data['og_info']['image']); ?>" alt="" <?php write_html( ($data['cp_share_action']->meta_data && !$data['meta_tags']->image) ? "style='display: none'" : "")?>></figure>
                        </div>
                        <p class="title">
                            <strong><?php assign($data['cp_share_action']->meta_data ? $data['meta_tags']->title : $data['og_info']['title']); ?></strong>
                        </p>
                    <!-- /.targettPost --></div>
                <?php endif; ?>
                <p>
                    <textarea name="share_message" placeholder="<?php assign($data['cp_share_action']->placeholder); ?>" <?php if($data['shared_flg']): ?>readonly<?php endif; ?> ><?php assign($data['share_text']); ?></textarea>
                </p>
                <!-- /.shareInner --></div>

            <div class="messageFooter">
                <ul class="btnSet">
                    <?php if($data['shared_flg']): ?>
                        <li class="btnShareFb"><span class="large1"><?php assign($data['is_last_action'] ? 'シェアする' : 'シェアして次へ'); ?></span></li>
                    <?php else: ?>
                        <li class="btnShareFb"><a class="cmd_execute_share_action large1" href="javascript:void(0)"><?php assign($data['is_last_action'] ? 'シェアする' : 'シェアして次へ'); ?></a></li>
                    <?php endif; ?>
                </ul>
                <?php if(!$data['shared_flg']): ?>
                    <p class="skip"><a class="cmd_execute_share_skip_action" href="javascript:void(0)"><small>シェアせず次へ</small></a></p>
                <?php endif; ?>
            </div>
        </form>
        <!-- /.message_share --></section>
<?php else: //画面は表示せずに次のモジュールを呼び出す?>
    <section class="inview jsMessage" id="message_<?php assign($data['message_info']["message"]->id); ?>">
        <?php if(!$data['shared_flg']): ?>
            <form class="executeShareUnreadForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_share_action.json")); ?>" method="POST">
                <?php write_html($this->csrf_tag()); ?>
                <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
                <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>
                <?php write_html($this->formHidden('shared_flg', $data['shared_flg'])); ?>
            </form>
        <?php endif; ?>
        <!-- /.message_share --></section>
<?php endif; ?>

<?php //TODO CP ID:3052の特例対応、ここから ?>
    <?php if($data['message_info']['conversion_tag']) write_html($data["message_info"]["conversion_tag"]);/*特例対応タグ*/?>
<?php //TODO CP ID:3052の特例対応、ここまで ?>

<?php write_html($this->scriptTag('user/UserActionShareService')); ?>