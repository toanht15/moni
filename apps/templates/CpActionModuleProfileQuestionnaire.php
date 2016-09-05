<?php if ($this->hasFanList()): ?>
    <?php $force_disable = ($data['action']->status == CpAction::STATUS_FIX) && !$this->isDemo() ? 'disabled':''; ?>

    <section class="moduleCont1">
        <h1 class="editReenquete1 jsModuleContTile">ファン登録時アンケート再取得</h1>
        <div class="moduleSettingWrap jsModuleContTarget">
            <?php if ( $this->ActionError && !$this->ActionError->isValid('resend')): ?>
                <p class="iconError1"><?php assign ( $this->ActionError->getMessage('resend') )?></p>
            <?php endif; ?>
            <p class="supplement1" style="margin-bottom:10px;">※新規登録時にユーザーが回答した内容を再度聞くことで、態度変容などを記録することができます。</p>
            <dl class="moduleSettingList jsModuleContTarget">
                <dt class="moduleSettingTitle jsModuleContTile">再取得するファン登録時アンケート</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <ul class="moduleSetting jsEntryQuestionnaires">
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('questionnaire_id')): ?>
                            <p class="iconError1"><?php assign ( $this->ActionError->getMessage('questionnaire_id') )?></p>
                        <?php endif; ?>
                        <?php foreach ($data['profile_questionnaires'] as $qst): ?>
                            <li><label><?php write_html($this->formCheckBox('entry_questionnaire' . $qst->id, array($this->getActionFormValue('entry_questionnaire' . $qst->id)), array($force_disable => $force_disable, 'checked' => $data['entry_questionnaires'][$qst->id]), array('1' => $qst->question))); ?></label></li>
                        <?php endforeach; ?>
                    </ul>
                    <!-- /.moduleSettingDetail --></dd>

                <dt class="moduleSettingTitle jsModuleContTile">回答済みの内容</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('prefill_flg')): ?>
                        <p class="iconError1"><?php assign ( $this->ActionError->getMessage('prefill_flg') )?></p>
                    <?php endif; ?>
                    <p><label><?php write_html($this->formCheckBox('prefill_flg', array($data['action']->prefill_flg), array($force_disable => $force_disable, 'class' => 'jsPrefillFlg'), array('1' => '回答済みの内容を表示する'))); ?></label><br><small>※前回ご回答いただいた内容を表示しておきます</small></p>
                    <!-- /.moduleSettingDetail --></dd>
                <!-- /.moduleSettingList --></dl>
            <!-- /.moduleSettingWrap --></div>
        <!-- /.moduleCont1 --></section>
<?php endif; ?>