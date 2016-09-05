<section class="message jsMessage inView" id="message_<?php assign($data['message_info']["message"]->id); ?>" >
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="executePopularVoteActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_popular_vote_action.json")); ?>" method="POST">

        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']['cp_action']->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>
        <?php write_html($this->formHidden('share_url', $data['share_url'])); ?>
        <?php write_html($this->formHidden('share_url_type', $data['message_info']['concrete_action']->share_url_type)); ?>
        <?php write_html($this->formHidden('connect_fb_url', Util::rewriteUrl('auth', 'campaign_login', array(), array('platform' => 'fb', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['cp_user']->cp_id)))); ?>
        <?php write_html($this->formHidden('connect_tw_url', Util::rewriteUrl('auth', 'campaign_login', array(), array('platform' => 'tw', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['cp_user']->cp_id)))); ?>

        <?php if($data['message_info']['concrete_action']->image_url): ?>
            <p class="messageImg"><img src="<?php assign($data['message_info']['concrete_action']->image_url); ?>" alt="campaign img"></p>
        <?php endif; ?>

        <section class="messageText">
            <?php write_html($data['message_info']['concrete_action']->html_content ? : $this->toHalfContentDeeply($data['message_info']['concrete_action']->text)); ?>
            <!-- /.messageText --></section>

        <ul class="messageRankingItem jsCandidateList" data-file_type="<?php assign($data['message_info']['concrete_action']->file_type); ?>">
            <?php foreach($data['candidate_list'] as $key => $candidate): ?>
                <li class="jsCandidate">
                    <input type="radio" id="rankingRadio<?php assign($data['message_info']['concrete_action']->id . "-" . ($key + 1)); ?>" class="customRadio jsCandidateRadio" name="cp_popular_vote_candidate_id" value="<?php assign($candidate->id); ?>" <?php if ($data['selected_id'] == $candidate->id) write_html('checked="checked"'); ?> <?php if (strlen($data['disable'])) write_html('disabled="disabled"'); ?>>
                    <label for="rankingRadio<?php assign($data['message_info']['concrete_action']->id . "-" . ($key + 1)); ?>">
                        <figure class="itemCont">
                            <figcaption class="itemTitle jsCandidateTitle"><?php assign($candidate->title); ?></figcaption>
                            <span class="contImg"><img src="<?php assign($candidate->thumbnail_url); ?>" data-modal_content="<?php assign($candidate->original_url); ?>" alt="image title" class="jsCandidateImage"></span>
                        </figure>
                        <p class="itemText jsCandidateDescription"><?php assign($candidate->description); ?></p>
                    </label>
                    <a href="javascript:void(0);" data-open_modal_type="CandidatePreview" class="<?php ($data['message_info']['concrete_action']->file_type == CpPopularVoteAction::FILE_TYPE_IMAGE) ? assign('imgPreview') : assign('moviePreview'); ?> jsOpenModal">拡大表示する</a>
                </li>
            <?php endforeach; ?>
            <!-- /.messageRankingItem --></ul>

        <?php if ($data['message_info']['concrete_action']->fb_share_required || $data['message_info']['concrete_action']->tw_share_required): ?>
            <dl class="module">
                <?php if ($data['cp_user']->cp_id == 6740): // 文言ハードコーディング（SUBWAY-15）?>
                    <dt>投票理由を教えてね！</dt>
                <?php else: ?>
                    <dt>投票理由をシェアしよう！</dt>
                <?php endif; ?>
                <dd>
                    <?php if ($data['cp_user']->cp_id == 6740): // 文言ハードコーディング（SUBWAY-15）?>
                        <?php write_html($this->formTextArea('share_text', $data['share_text'] ? $data['share_text'] : '', array('placeholder' => $data['message_info']['concrete_action']->share_placeholder, 'maxlength' => 32, 'class' => 'jsShareText', $data['disable'] => $data['disable']))); ?>
                        <span class="supplement1 jsLimitText" data-limit_count="32">（<?php assign(strlen($data['share_text'])); ?>文字/32文字）</span>
                    <?php else: ?>
                        <?php write_html($this->formTextArea('share_text', $data['share_text'] ? $data['share_text'] : '', array('placeholder' => $data['message_info']['concrete_action']->share_placeholder, 'maxlength' => PopularVoteUserShare::SHARE_TEXT_LENGTH, 'class' => 'jsShareText', $data['disable'] => $data['disable']))); ?>
                        <span class="supplement1 jsLimitText" data-limit_count="<?php assign(PopularVoteUserShare::SHARE_TEXT_LENGTH) ?>">（<?php assign(strlen($data['share_text'])); ?>文字/<?php assign(PopularVoteUserShare::SHARE_TEXT_LENGTH) ?>文字）</span>
                    <?php endif; ?>

                    <?php if ($data['cp_user']->cp_id == 6740): // 文言ハードコーディング（SUBWAY-15）?>
                        <ul class="moduleSnsList">
                            <li>
                                <?php if ($data['message_info']['concrete_action']->tw_share_required): ?>
                                    <?php if ($data['tw_connect']): ?>
                                        <label style="display: none;"><input type="checkbox" checked="checked" name="tw_share_flg" value="1" class="jsShareFlg" <?php if ($data['disable']) write_html('disabled="disabled"'); ?>><img src="//s0.monipla.com/img/sns/iconSnsTW2.png?1432489529" alt="Twitter"></label>
                                    <?php elseif ($data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN): ?>
                                        <a href="javascript:void(0)" class="linkAdd jsTwConnect" data-platform="tw" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'tw-login-vote', '<?php assign('campaigns_' . $data['cp_user']->cp_id);?>', location.href, {'page': '<?php assign($data['cp_url']) ?>'});"><img src="<?php assign($this->setVersion('/img/sns/iconSnsTW2.png')) ?>" alt="Twitter">を追加</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </li>
                        <!-- /.moduleSnsList --></ul>
                    <?php else: ?>
                        <ul class="moduleSnsList">
                            <li style="display: inline;">
                                <?php if ($data['message_info']['concrete_action']->fb_share_required): ?>
                                    <?php if ($data['fb_connect'] && $data['fb_has_permission']): ?>
                                        <label><input type="checkbox" checked="checked" name="fb_share_flg" value="1" class="jsShareFlg" <?php if ($data['disable']) write_html('disabled="disabled"'); ?>><img src="//s0.monipla.com/img/sns/iconSnsFB2.png?1432489529" alt="Facebook"></label>
                                    <?php elseif ($data['fb_connect'] && !$data['fb_has_permission']): ?>

                                    <?php elseif ($data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN): ?>
                                        <a href="javascript:void(0)" class="linkAdd jsFbConnect" data-platform="fb" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'fb-login-vote', '<?php assign('campaigns_' . $data['cp_user']->cp_id);?>', location.href, {'page': '<?php assign($data['cp_url']) ?>'});"><img src="<?php assign($this->setVersion('/img/sns/iconSnsFB2.png')) ?>" alt="Facebook">を追加</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </li>
                            <li style="display: inline;">
                                <?php if ($data['message_info']['concrete_action']->tw_share_required): ?>
                                    <?php if ($data['tw_connect']): ?>
                                        <label><input type="checkbox" checked="checked" name="tw_share_flg" value="1" class="jsShareFlg" <?php if ($data['disable']) write_html('disabled="disabled"'); ?>><img src="//s0.monipla.com/img/sns/iconSnsTW2.png?1432489529" alt="Twitter"></label>
                                    <?php elseif ($data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN): ?>
                                        <a href="javascript:void(0)" class="linkAdd jsTwConnect" data-platform="tw" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'tw-login-vote', '<?php assign('campaigns_' . $data['cp_user']->cp_id);?>', location.href, {'page': '<?php assign($data['cp_url']) ?>'});"><img src="<?php assign($this->setVersion('/img/sns/iconSnsTW2.png')) ?>" alt="Twitter">を追加</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </li>
                        <!-- /.moduleSnsList --></ul>
                    <?php endif; ?>
                </dd>
            </dl>
        <?php endif; ?>

        <div class="messageFooter">
            <ul class="btnSet">
                <?php if ($data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN): ?>
                    <li class="btn3">
                        <a class="jsExecutePopularVoteAction large1" data-messageid="<?php assign($data['message_info']["message"]->id); ?>" href="javascript:void(0);">
                            <?php assign($data["message_info"]["concrete_action"]->button_label_text) ?>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="btn3">
                        <span class="large1"><?php assign($data["message_info"]["concrete_action"]->button_label_text) ?></span>
                    </li>
                <?php endif; ?>
                <!-- /.btnSet --></ul>
        </div>
    </form>

    <?php if ($data['popular_vote_user']) : ?>
        <span class="jsSetPopularVoteResult"></span>
    <?php endif; ?>

    <div class="uploadCont jsPopularVoteResult" <?php if (!$data['popular_vote_user']) write_html('style="display: none"'); ?>>
        <?php if ($data['cp_user']->cp_id == 6740): // 文言ハードコーディング（SUBWAY-15）?>
            <p>あなたが選んだサブウェイクラブに試してみたいトッピングはこちら</p>
        <?php else: ?>
            <p>あなたが選んだ投票候補はこちら</p>
        <?php endif; ?>
        <div class="votedItem">
            <figure class="itemCont">
                <figcaption class="itemTitle jsCandidateTitle"></figcaption>
                <?php if ($data['message_info']['concrete_action']->file_type == CpPopularVoteAction::FILE_TYPE_IMAGE): ?>
                    <span class="contImg"><img src="" class="jsCandidateImage" alt="image title"></span>
                <?php else: ?>
                    <p class="contMovie jsCandidateMovie"></p>
                <?php endif; ?>
            </figure>
            <p class="itemText jsCandidateDescription"></p>
            <!-- /.votedItem --></div>
        <!-- /.uploadCont --></div>
    <!-- /.message --></section>

<?php write_html($this->scriptTag("user/UserActionPopularVoteService")); ?>

