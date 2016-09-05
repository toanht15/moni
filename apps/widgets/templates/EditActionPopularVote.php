<?php if ($data['is_fan_list_page']): ?>
    <?php $disable = ''; ?>
    <?php write_html($this->formHidden('is_fan_list_page', 1)); ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX) ? 'disabled' : ''; ?>
<?php endif; ?>
<?php if ($data['is_cp_action_fixed']): ?>
    <?php write_html($this->formHidden('is_cp_action_fixed', 1)) ?>
<?php endif; ?>
<?php write_html($this->formHidden('popular_vote_post_flg', 1)) ?>
<?php write_html($this->formHidden('static_url', config('Static.Url'))) ?>

<section class="moduleEdit1 jsPopularVoteSetting">
    <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable' => $disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleImage.php', array('disable' => $disable))); ?>

    <section class="moduleCont1">
        <h1 class="editRanking1 jsModuleContTile">人気投票</h1>
        <div class="moduleSettingWrap jsModuleContTarget">
            <ul class="moduleSetting">
                <?php if ($disable || $data['is_cp_action_fixed']): ?>
                    <li><input type="radio" name="file_type" id="file_type_1" value="1" class="jsFileType" disabled="disabled" <?php if ($this->file_type == CpPopularVoteAction::FILE_TYPE_IMAGE) assign('checked="checked"'); ?>><label for="file_type_1">画像投票</label></li>
                    <li><input type="radio" name="file_type" id="file_type_2" value="2" class="jsFileType" disabled="disabled" <?php if ($this->file_type == CpPopularVoteAction::FILE_TYPE_MOVIE) assign('checked="checked"'); ?>><label for="file_type_2">動画投票</label></li>
                    <?php write_html($this->formHidden('file_type', $this->file_type)) ?>
                <?php else: ?>
                    <li><?php write_html($this->formRadio('file_type', $this->file_type, array('class' => 'jsFileType', $disable => $disable), array(CpPopularVoteAction::FILE_TYPE_IMAGE => '画像投票'), array(), " ")); ?></li>
                    <li><?php write_html($this->formRadio('file_type', $this->file_type, array('class' => 'jsFileType', $disable => $disable), array(CpPopularVoteAction::FILE_TYPE_MOVIE => '動画投票'), array(), " ")); ?></li>
                <?php endif; ?>
            </ul>

            <dl class="moduleSettingList jsPopularVoteSettingList">
                <?php write_html($this->parseTemplate('CpActionModulePopularVote.php', array('vote_file_type' => CpPopularVoteAction::FILE_TYPE_IMAGE, 'file_type' => $this->file_type, 'random_flg' => $this->getActionFormValue('random_flg'), 'text' => $this->getActionFormValue('text'), 'disable' => $disable, 'is_cp_action_fixed' => $data['is_cp_action_fixed'], 'candidate_list' => $this->candidate_list))); ?>
                <?php write_html($this->parseTemplate('CpActionModulePopularVote.php', array('vote_file_type' => CpPopularVoteAction::FILE_TYPE_MOVIE, 'file_type' => $this->file_type, 'random_flg' => $this->getActionFormValue('random_flg'), 'text' => $this->getActionFormValue('text'), 'disable' => $disable, 'is_cp_action_fixed' => $data['is_cp_action_fixed'], 'candidate_list' => $this->candidate_list))); ?>

                <dt class="moduleSettingTitle jsModuleContTile">シェア設定</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <dl>
                        <dt>シェアページ</dt>
                        <dd>
                            <ul class="moduleSetting">
                                <li>
                                    <?php write_html($this->formRadio('share_url_type', $this->share_url_type, array('class' => 'jsShareUrlType', $disable => $disable), array(CpPopularVoteAction::SHARE_URL_TYPE_RANKING => 'ユーザが投票した候補のページ'), array(), " ")); ?><br>
                                    <?php write_html($this->formCheckBox('show_ranking_flg', array($this->getActionFormValue('show_ranking_flg')), array('class' => 'jsShowRankingFlg', $disable => $disable), array('1' => 'ランキングを表示する'), array(), " ")); ?>
                                </li>
                                <li><?php write_html($this->formRadio('share_url_type', $this->share_url_type, array('class' => 'jsShareUrlType', $disable => $disable), array(CpPopularVoteAction::SHARE_URL_TYPE_CP => 'キャンペーンページ'), array(), " ")); ?></li>
                                <!-- /.moduleSetting --></ul>
                        </dd>
                        <dt>シェアSNS</dt>
                        <dd>
                            <ul class="moduleSetting">
                                <li><?php write_html($this->formCheckBox3('fb_share_required', array($this->getActionFormValue('fb_share_required')), array($disable => $disable, 'class' => 'jsShareRequired', 'data-require_type' => 'Facebook'), array('1' => 'Facebook'), array('1' => $this->setVersion('/img/sns/iconSnsFB2.png')))); ?></li>
                                <li><?php write_html($this->formCheckBox3('tw_share_required', array($this->getActionFormValue('tw_share_required')), array($disable => $disable, 'class' => 'jsShareRequired', 'data-require_type' => 'Twitter'), array('1' => 'Twitter'), array('1' => $this->setVersion('/img/sns/iconSnsTW2.png')))); ?></li>
                                <!-- /.moduleSetting --></ul>
                        </dd>
                        <dt>プレースホルダー</dt>
                        <dd><?php write_html($this->formTextArea('share_placeholder', PHPParser::ACTION_FORM, array('maxlength'=>CpValidator::MAX_TEXT_LENGTH, 'class'=>'jsSharePlaceholder', 'cols' => 30, 'rows' => 10, 'data-file_type' => $this->file_type, $disable=>$disable))); ?></dd>
                    </dl>
                    <!-- /.moduleSettingDetail --></dd>
                <!-- /.moduleSettingList --></dl>
            <!-- /.moduleSettingWrap --></div>
        <!-- /.moduleCont1 --></section>

    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpActionModuleDeadLine')->render([
        'ActionForm'       => $data['ActionForm'],
        'ActionError'      => $data['ActionError'],
        'cp_action'        => $data['action'],
        'is_login_manager' => $data['pageStatus']['isLoginManager'],
        'disable'          => $disable,
    ])); ?>
    <!-- /.moduleEdit1 --></section>


<section class="modulePreview1">
    <header class="modulePreviewHeader">
        <p>スマートフォン<a href="#" class="toggle_switch left jsModulePreviewSwitch">toggle_switch</a>PC</p>
        <!-- /.modulePreviewHeader --></header>

    <div class="displaySP jsModulePreviewArea">
        <section class="messageWrap">
            <section class="message">
                <p class="messageImg"><img src="" alt="" id="imagePreview"></p>
                <section class="messageText jsCandidateTextPreview" data-file_type="<?php assign(CpPopularVoteAction::FILE_TYPE_IMAGE); ?>"></section>
                <ul class="messageRankingItem jsCandidateListPreview" data-file_type="<?php assign(CpPopularVoteAction::FILE_TYPE_IMAGE); ?>"></ul>
                <section class="messageText jsCandidateTextPreview" data-file_type="<?php assign(CpPopularVoteAction::FILE_TYPE_MOVIE); ?>"></section>
                <ul class="messageRankingItem jsCandidateListPreview" data-file_type="<?php assign(CpPopularVoteAction::FILE_TYPE_MOVIE); ?>"></ul>
                <dl class="module jsPopularVoteSharePreview">
                    <dt>投票理由をシェアしよう！</dt>
                    <dd>
                        <textarea class="jsShareTextPreview"  placeholder="" maxlength="94"></textarea>
                        <span class="supplement1">(最大<?php assign(PopularVoteUserShare::SHARE_TEXT_LENGTH) ?>文字)</span>
                        <ul class="moduleSnsList">
                            <li class="jsSocialMediaTypePreview" data-require_type="Facebook"><label><input type="checkbox" checked="checked"><img src="<?php assign($this->setVersion('/img/sns/iconSnsFB2.png')) ?>" alt="Facebook"></label></li>
                            <li class="jsSocialMediaTypePreview" data-require_type="Twitter"><label><input type="checkbox" checked="checked"><img src="<?php assign($this->setVersion('/img/sns/iconSnsTW2.png')) ?>" alt="Twitter"></label></li>
                            <!-- /.moduleSnsList --></ul>

                    </dd>
                </dl>

                <ul class="btnSet">
                    <li class="btn3"><a href="javascript:void(0)" class="large1" id="btnPreview"><?php assign($this->getActionFormValue('button_label_text')); ?></a></li>
                </ul>
                <!-- /.message --></section>
        </section>
    </div>
    <!-- /.modulePreview --></section>

<div class="modal2 jsModalPopularVote" id="modalConfirm">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p><span class="attention1">この投票候補を削除しますか？</span></p>
        <p class="btnSet"><span class="btn2"><a href="javascript:void(0)" data-close_modal_type="Confirm" class="middle1 jsCloseModal">キャンセル</a></span><span class="btn4"><a href="javascript:void(0)" class="middle1 jsExecuteDelete">削除する</a></span></p>
    </section>
    <!-- /#modalConfirm.modal2.jsModal --></div>
