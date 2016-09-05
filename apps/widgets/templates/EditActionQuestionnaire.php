<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
    <?php write_html($this->formHidden('is_fan_list_page', 1)) ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX) ? 'disabled' : '' ?>
<?php endif; ?>
    <section class="moduleEdit1">
        <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable'=>$disable))); ?>
        <?php write_html($this->parseTemplate('CpActionModuleImage.php', array('disable'=>$disable))); ?>
        <?php write_html($this->parseTemplate('CpActionModuleText.php', array('disable'=>$disable))); ?>
        <section class="moduleCont1">
            <h1 class="editEnquete1 jsModuleContTile">アンケート</h1>
            <div class="moduleSettingWrap jsModuleContTarget">
                <p class="supplement1">アンケートではメールアドレスなどの個人情報を取得することはできません。遵守いただけない場合サービスのご提供を停止、終了する場合があります。</p>
                <ul class="moduleEnqueteList" id="moduleEnqueteList" data-cp_questionnaire_action_id='<?php assign($this->ActionForm['id']); ?>'
                    data-url='<?php assign(Util::rewriteUrl('admin-cp', 'api_get_question.json')); ?>'
                    data-disable='<?php assign($disable);?>'>
                </ul>
            <!-- /.moduleSettingWrap --></div>
        <!-- /.moduleCont1 --></section>
        <?php write_html($this->parseTemplate('CpActionModuleButton.php', array('disable'=>$disable))); ?>

        <?php if ($data['is_opening_flg']): ?>
            <?php write_html($this->parseTemplate('CpActionModuleProfileQuestionnaire.php', $data)) ?>
        <?php endif ?>
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
                <p class="messageImg"><img src="" width="600" height="300" id="imagePreview"></p>
                <section class="messageText" id="textPreview"></section>
                <dl class="module" id='questionnairePreview'></dl>

                <div class="messageFooter">
                    <ul class="btnSet">
                        <li class="btn3"><a href="javascript:void(0)" class="large1" id="btnPreview"></a></li>
                    </ul>
                </div>
            </section>
            <?php if ($data['is_opening_flg']): ?>
                <?php write_html($this->parseTemplate('CpActionModuleProfileQuestionnairePreview.php', $data)) ?>
            <?php endif ?>
        </section>
    </div>
    <!-- /.modulePreview --></section>
