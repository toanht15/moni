<?php
$disable = ($data['is_fan_list_page'] || $data['action']->status != CpAction::STATUS_FIX) ? '' : 'disabled';
$code_auth_disable = $data['action']->status == CpAction::STATUS_FIX ? 'disabled' : '';
$code_flg_selection_disabled = $disable || $this->ActionForm['code_auth_id'] == 0 ? 'disabled' : '';
$min_code_count_disabled = ($this->ActionForm['min_code_flg'] == CpCodeAuthenticationAction::CODE_FLG_OFF) || $code_flg_selection_disabled ? 'disabled' : '';
$max_code_count_disabled = ($this->ActionForm['max_code_flg'] == CpCodeAuthenticationAction::CODE_FLG_OFF) || $code_flg_selection_disabled ? 'disabled' : '';

$select_value = array('0' => '選択してください');
foreach ($data['code_auths'] as $code_auth) {
    $select_value[$code_auth->id] = $code_auth->name;
}
if ($data['current_code_auth']) {
    $select_value[$data['current_code_auth']->id] = $data['current_code_auth']->name;
}
?>

<section class="moduleEdit1">
    <section class="moduleCont1">
        <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable' => $disable))) ?>
        <?php write_html($this->parseTemplate('CpActionModuleImage.php', array('disable' => $disable))) ?>
        <?php write_html($this->parseTemplate('CpActionModuleText.php', array('disable' => $disable))) ?>
        <h1 class="editCode1 jsModuleContTile">コード認証設定</h1>
        <div class="moduleSettingWrap jsModuleContTarget">
            <dl class="moduleSettingList">
                <dt class="moduleSettingTitle jsModuleContTile">使用するコード</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <?php if (count($select_value) <= 1): ?>
                        <p>認証コードを<a href="<?php assign(Util::rewriteUrl('admin-code-auth', 'create_code_auth')) ?>">こちら</a>から作成して下さい。</p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('code_auth_id')): ?>
                        <p class="iconError1"><?php assign ( $this->ActionError->getMessage('code_auth_id') )?></p>
                    <?php endif; ?>
                    <?php if ($code_auth_disable == 'disabled'): ?>
                        <?php write_html($this->formHidden('code_auth_id', PHPParser::ACTION_FORM)) ?>
                    <?php endif; ?>
                    <?php write_html($this->formSelect('code_auth_id', PHPParser::ACTION_FORM, array('disabled' => $code_auth_disable, "class" => "jsCodeAuthSelection"), $select_value)) ?>
                    <!-- /.moduleSettingDetail --></dd>
                <dt class="moduleSettingTitle jsModuleContTile">コード入力上限</dt>
                <dd class="moduleSettingDetail jsModuleContTarget jsCodeCountTarget">
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('max_code_count')): ?>
                        <p class="iconError1"><?php assign($this->ActionError->getMessage('max_code_count')) ?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('max_code_count2')): ?>
                        <p class="attention1"><?php assign(str_replace(array('<%param1>', '<%param2>'), array('次に進むコード数','コード入力上限数'), $this->ActionError->getMessage('max_code_count2'))) ?></p>
                    <?php endif; ?>
                    <ul class="moduleSetting">
                        <li>
                            <?php write_html($this->formRadio('max_code_flg', PHPParser::ACTION_FORM, array('disabled' => $code_flg_selection_disabled, 'class' => 'jsCodeCountSelection'), array(CpCodeAuthenticationAction::CODE_FLG_OFF => 'なし'))) ?>
                        </li>
                        <li>
                            <?php write_html($this->formRadio('max_code_flg', PHPParser::ACTION_FORM, array('disabled' => $code_flg_selection_disabled, 'class' => 'jsCodeCountSelection'), array(CpCodeAuthenticationAction::CODE_FLG_ON => ''))) ?>
                            <?php write_html($this->formText('max_code_count', PHPParser::ACTION_FORM, array('disabled' => $max_code_count_disabled, 'class' => 'inputNum jsCodeCount', 'data-type' => 'max'))) ?>
                            <small>個</small>
                        </li>
                        <!-- /.moduleSetting --></ul>
                    <!-- /.moduleSettingDetail --></dd>
                <dt class="moduleSettingTitle jsModuleContTile">次に進む条件</dt>
                <dd class="moduleSettingDetail jsModuleContTarget jsCodeCountTarget">
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('min_code_count')): ?>
                        <p class="iconError1"><?php assign ( $this->ActionError->getMessage('min_code_count') )?></p>
                    <?php endif; ?>
                    <ul class="moduleSetting">
                        <li>
                            <?php write_html($this->formRadio('min_code_flg', PHPParser::ACTION_FORM, array('disabled' => $code_flg_selection_disabled, 'class' => 'jsCodeCountSelection'), array(CpCodeAuthenticationAction::CODE_FLG_OFF => 'なし'))) ?>
                        </li>
                        <li>
                            <?php write_html($this->formRadio('min_code_flg', PHPParser::ACTION_FORM, array('disabled' => $code_flg_selection_disabled, 'class' => 'jsCodeCountSelection'), array(CpCodeAuthenticationAction::CODE_FLG_ON => ''))) ?>
                            <?php write_html($this->formText('min_code_count', PHPParser::ACTION_FORM, array('disabled' => $min_code_count_disabled, 'class' => 'inputNum jsCodeCount', 'data-type' => 'min'))) ?>
                            <small>個</small>
                        </li>
                        <!-- /.moduleSetting --></ul>
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
                <p class="messageImg"><img src="" width="600" height="300" id="imagePreview"></p>
                <section class="messageText" id="textPreview"></section>

                <div class="messageCodeInput">
                    <p class="codeInput"><input type="text"></p>
                    <ul class="btnSet">
                        <li class="btn3"><a href="javascript:void(0);" class="large1">登録</a></li>
                        <!-- /.btnSet --></ul>
                    <p class="codeAttention" id="code_count_preview">
                        <span id="min_code_count_preview" class="unconfirm">あと<strong>4</strong>個で次に進めます</span><span class="confirmed">認証済のコード（<strong>0</strong><span id="max_code_count_preview"></span>個）</span>
                    </p>
                    <div class="codeListWrap" id="code_list_preview">
                        <table class="codeList">
                            <caption></caption>
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>コード</th>
                                <th>認証日時</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1.</td>
                                <td>1254-7894-KFRD-GFRE</td>
                                <td>2015/07/03 18:32</td>
                            </tr>
                            <tr class="moreCode">
                                <td>2.</td>
                                <td>------------------</td>
                                <td>----/--/-- --:--</td>
                                <!-- /.moreCode --></tr>
                            </tbody>
                            <!-- /.codeList --></table>
                        <!-- /.codeListWrap --></div>
                    <!-- /.messageCodeInput --></div>
                <div class="messageFooter">
                    <ul class="btnSet">
                        <li class="btn3"><a href="javascript:void(0);" class="middle1">次へ</a></li>
                        <!-- /.btnSet --></ul>
                </div>
            <!-- /.messageWrap --></section>
    </div>

    <!-- /.modulePreview --></section>
