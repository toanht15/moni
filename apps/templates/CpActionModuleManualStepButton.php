<section class="moduleCont1">
    <h1 class="editBtn1 jsModuleContTile">「次へ」ボタンの設定</h1>
    <div class="moduleSettingWrap jsModuleContTarget">
        <p>
            <?php if ($data['is_last']) : ?>
                <?php write_html($this->formHidden('manual_step_flg', Cp::FLAG_HIDE_VALUE)); ?>
                <small>※現在のモジュールの位置では設定できません。</small>
            <?php else: ?>
                <label><?php write_html($this->formCheckBox('manual_step_flg', array($this->getActionFormValue('manual_step_flg')), array($data['disable'] => $data['disable'], 'class' => "jsManualStepFlg"), array('1' => '設置する')));?></label><br>
                <small>※設置すると次のSTEPへ自動で進まなくなります。</small>
            <?php endif; ?>
        </p>
        <!-- /.moduleSettingWrap --></div>
    <!-- /.moduleCont1 --></section>

