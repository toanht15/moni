<?php if ($this->isResend($data)): ?>
    <h1 class="<?php assign($data['parent_class_name']); ?>Hd1">下記のアンケートにご回答ください。</h1>
<?php else: ?>
    <?php
        if ($data['pageStatus']['is_cmt_plugin_mode']) {
            $title = 'ユーザー情報確認・投稿';
        } else {
            $title = $data['required_profile_questions'] ? 'ユーザー情報' : '確認';
        }
    ?>
    <h1 class="<?php assign($data['parent_class_name']); ?>Hd1"><?php assign($title); ?></h1>
<?php endif; ?>
<?php if ($data['attentions']): ?>
    <p class="attention1"><?php write_html($data['attentions']) ?></p>
<?php endif ?>

    <form id="frmEntry" name="frmEntry" action="<?php if (!$data['is_api']) assign(Util::rewriteUrl('auth', 'signup_post')); ?>" method="POST">
        <?php write_html($this->csrf_tag()); ?>

        <?php if($this->hasQuestionnaire($data)): ?>
            <ul class="commonTableList1">
                <?php if (!$data['entry_questionnaire_only']): ?>
                    <?php if($data['isRequiredPrivacy']):?>
                        <?php if($data['pageSettings']->privacy_required_name):?>
                            <li>
                                <p class="title1">
                                    <span class="require1">氏名（かな）</span>
                                    <!-- /.title1 --></p>
                                <p class="itemEdit">
                                    <?php if ($this->ActionError): ?>
                                        <?php if (!$this->ActionError->isValid('lastName')):?>
                                            <?php  $error_msg = $this->ActionError->getMessage('lastName') ?>
                                            <span class="iconError1"><?php assign($error_msg) ?></span>
                                        <?php endif; ?>
                                        <?php if (!$this->ActionError->isValid('firstName')):?>
                                            <?php  $error_msg = $this->ActionError->getMessage('firstName') ?>
                                            <span class="iconError1"><?php assign($error_msg) ?></span>
                                        <?php endif; ?>
                                        <?php if (!$this->ActionError->isValid('lastNameKana')):?>
                                            <?php  $error_msg = $this->ActionError->getMessage('lastNameKana') ?>
                                            <span class="iconError1"><?php assign($error_msg) ?></span>
                                        <?php endif; ?>
                                        <?php if (!$this->ActionError->isValid('firstNameKana')):?>
                                            <?php  $error_msg = $this->ActionError->getMessage('firstNameKana') ?>
                                            <span class="iconError1"><?php assign($error_msg) ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <span class="editInput">
                                        <label class="editName"><span>姓</span><?php write_html( $this->formText('lastName', PHPParser::ACTION_FORM, array('class' => 'name') ))?></label>
                                        <label class="editName"><span>名</span><?php write_html( $this->formText('firstName', PHPParser::ACTION_FORM, array('class' => 'name') ))?></label>
                                    </span>
                                <span class="editInput">
                                        <label class="editName"><span>せい</span><?php write_html( $this->formText('lastNameKana', PHPParser::ACTION_FORM, array('class' => 'name') ))?></label>
                                        <label class="editName"><span>めい</span><?php write_html( $this->formText('firstNameKana', PHPParser::ACTION_FORM, array('class' => 'name') ))?></label>
                                    <!-- /.editInput --></span>
                                    <!-- /.itemEdit --></p>
                            </li>
                        <?php endif;?>
                        <?php if($data['pageSettings']->privacy_required_sex || $data['cp']->restricted_gender_flg):?>
                            <li>
                                <p class="title1">
                                    <span class="require1">性別</span>
                                    <!-- /.title1 --></p>
                                <p class="itemEdit">
                            <span class="editInput">
                                <?php if ( $this->ActionError && !$this->ActionError->isValid('sex') ):?>
                                    <span class="iconError1">選択してください</span>
                                <?php endif; ?>
                                <?php write_html( $this->formRadio( 'sex', PHPParser::ACTION_FORM, array('class'=>'customRadio'), $data['sex'])); ?>
                                <!-- /.editInput --></span>
                                    <!-- /.itemEdit --></p>
                            </li>
                        <?php endif;?>
                        <?php if($data['pageSettings']->privacy_required_birthday || $data['cp']->restricted_age_flg):?>
                            <li>
                                <p class="title1">
                                    <span class="require1">生年月日</span>
                                    <!-- /.title1 --></p>
                                <p class="itemEdit">
                                    <?php if ( $this->ActionError && !$this->ActionError->isValid('birthDay') ):?>
                                        <span class="iconError1"><?php assign ( $this->ActionError->getMessage('birthDay') )?></span>
                                    <?php endif; ?>
                                    <?php if ( $this->ActionError && !$this->ActionError->isValid('restrictedAge') ):?>
                                        <span class="iconError1"><?php assign ( $this->ActionError->getMessage('restrictedAge') )?></span>
                                    <?php endif; ?>
                                    <span class="editInput">
                                        <?php $input_class = $data['pageStatus']['is_cmt_plugin_mode'] ? 'inputNumMiddle' : 'inputNum' ?>
                                    <label>西暦<?php write_html( $this->formNumber('birthDay_y', PHPParser::ACTION_FORM, array('class' => $input_class, 'autocorrect' => 'off', 'autocapitalize' => 'off') ))?>年</label>
                                    <label><?php write_html( $this->formNumber('birthDay_m', PHPParser::ACTION_FORM, array('class' => 'inputNumSmall', 'autocorrect' => 'off', 'autocapitalize' => 'off') ))?>月</label>
                                    <label><?php write_html( $this->formNumber('birthDay_d', PHPParser::ACTION_FORM, array('class' => 'inputNumSmall', 'autocorrect' => 'off', 'autocapitalize' => 'off') ))?>日</label>

                                <span class="supplement1">※半角数字</span>
                                <!-- /.editInput --></span>
                                    <!-- /.itemEdit --></p>
                            </li>
                        <?php endif;?>
                        <?php if($data['pageSettings']->privacy_required_address == BrandPageSetting::GET_ALL_ADDRESS):?>
                            <li>
                                <p class="title1">
                                    <span class="require1">郵便番号</span>
                                    <!-- /.title1 --></p>
                                <p class="itemEdit">
                                    <?php if ( $this->ActionError && !$this->ActionError->isValid('zipCode') ):?>
                                        <span class="iconError1"><?php assign ( $this->ActionError->getMessage('zipCode') )?></span>
                                    <?php endif; ?>
                                    <span class="editInput">
                                    <?php write_html( $this->formText('zipCode1', PHPParser::ACTION_FORM, array('class' => 'inputNum', 'maxlength' => 3, 'autocorrect' => 'off', 'autocapitalize' => 'off') ))?>－<?php write_html( $this->formText('zipCode2', PHPParser::ACTION_FORM, array('class' => 'inputNum', 'maxlength' => 4, 'autocorrect' => 'off', 'autocapitalize' => 'off', 'onKeyUp' => "AjaxZip3.zip2addr('zipCode1','zipCode2','prefId','address1','address2');") ))?>
                                        <a href="javascript:;" onclick="AjaxZip3.zip2addr('zipCode1','zipCode2','prefId','address1','address2');">住所検索</a><span class="supplement1">※半角数字</span>
                                <!-- /.editInput --></span>
                                    <!-- /.itemEdit --></p>
                            </li>
                        <?php endif; ?>
                        <?php if ($data['pageSettings']->privacy_required_address == BrandPageSetting::GET_ALL_ADDRESS || $data['pageSettings']->privacy_required_address == BrandPageSetting::GET_STATE_ADDRESS || $data['cp']->restricted_address_flg): ?>
                            <li>
                                <p class="title1">
                                    <span class="require1">都道府県</span>
                                    <!-- /.title1 --></p>
                                <p class="itemEdit">
                                    <?php if ( $this->ActionError && !$this->ActionError->isValid('prefId') ):?>
                                        <span class="iconError1"><?php assign ( $this->ActionError->getMessage('prefId') )?></span>
                                    <?php endif; ?>
                                    <span class="editInput">
                                        <?php write_html( $this->formSelect('prefId', $this->getActionFormValue('prefId') ?: Prefecture::PREF_TOKYO, array(), $this->prefectures))?>
                                        <!-- /.editInput --></span>
                                    <!-- /.itemEdit --></p>
                            </li>
                        <?php endif; ?>
                        <?php if($data['pageSettings']->privacy_required_address == BrandPageSetting::GET_ALL_ADDRESS):?>
                            <li>
                                <p class="title1">
                                    <span class="require1">市区町村</span>
                                    <!-- /.title1 --></p>
                                <p class="itemEdit">
                                    <?php if ( $this->ActionError && !$this->ActionError->isValid('address1') ):?>
                                        <span class="iconError1"><?php assign ( $this->ActionError->getMessage('address1') )?></span>
                                    <?php endif; ?>
                                    <span class="editInput">
                                        <?php write_html( $this->formText('address1', PHPParser::ACTION_FORM))?>
                                        <!-- /.editInput --></span>
                                    <!-- /.itemEdit --></p>
                            </li>
                            <li>
                                <p class="title1">
                                    <span class="require1">番地</span>
                                    <!-- /.title1 --></p>
                                <p class="itemEdit">
                                    <?php if ( $this->ActionError && !$this->ActionError->isValid('address2') ):?>
                                        <span class="iconError1"><?php assign ( $this->ActionError->getMessage('address2') )?></span>
                                    <?php endif; ?>
                                    <span class="editInput">
                                        <?php write_html( $this->formText('address2', PHPParser::ACTION_FORM))?>
                                        <!-- /.editInput --></span>
                                    <!-- /.itemEdit --></p>
                            </li>
                            <li>
                                <p class="title1">
                                    建物
                                    <!-- /.title1 --></p>
                                <p class="itemEdit">
                                    <?php if ( $this->ActionError && !$this->ActionError->isValid('address3') ):?>
                                        <span class="iconError1"><?php assign ( $this->ActionError->getMessage('address3') )?></span>
                                    <?php endif; ?>
                                    <span class="editInput">
                                      <?php write_html( $this->formText('address3', PHPParser::ACTION_FORM))?>
                                        <!-- /.editInput --></span>
                                    <!-- /.itemEdit --></p>
                            </li>
                        <?php endif;?>
                        <?php if($data['pageSettings']->privacy_required_tel):?>
                            <li>
                                <p class="title1">
                                    <span class="require1">電話番号</span>
                                    <!-- /.title1 --></p>
                                <p class="itemEdit">
                                    <?php if ( $this->ActionError && !$this->ActionError->isValid('telNo') ):?>
                                        <span class="iconError1"><?php assign ( $this->ActionError->getMessage('telNo') )?></span>
                                    <?php endif; ?>
                                    <span class="editInput">
                                        <?php write_html( $this->formTel('telNo1', PHPParser::ACTION_FORM, array('class' => 'inputNum', 'autocorrect' => 'off', 'autocapitalize' => 'off') ))?>－<?php write_html( $this->formTel('telNo2', PHPParser::ACTION_FORM, array('class' => 'inputNum', 'autocorrect' => 'off', 'autocapitalize' => 'off') ))?>－<?php write_html( $this->formTel('telNo3', PHPParser::ACTION_FORM, array('class' => 'inputNum', 'autocorrect' => 'off', 'autocapitalize' => 'off') ))?>
                                        <!-- /.editInput --></span>
                                    <span class="supplement1">※半角数字</span>
                                    <!-- /.itemEdit --></p>
                            </li>
                        <?php endif;?>
                    <?php endif;?>
                <?php endif; ?>
                <?php foreach($data['profile_questions_relations'] as $profile_question_relation): ?>
                    <?php if ($this->canRenderQuestionnaire($profile_question_relation, $data['entry_questionnaire_only'], $data['entry_questionnaires'])): ?>
                        <?php $profile_questionnaire = $this->getQuestionById($profile_question_relation->question_id); ?>
                        <li>
                            <p class="title1">
                                <span <?php if($profile_question_relation->requirement_flg) write_html('class="require1"') ?>><?php assign($profile_questionnaire->question) ?></span>
                                <!-- /.title1 --></p>

                            <?php if($this->isProfileChoice($profile_questionnaire)): ?>
                                <?php $question_requirement = $this->getRequirementByQuestionId($profile_questionnaire->id); ?>
                                <?php $choices = $this->getChoicesByQuestionId($profile_questionnaire->id); ?>
                            <?php endif; ?>

                            <?php if($this->isChoiceAnswer($profile_questionnaire)): ?>
                                <ul class="itemEdit">
                                    <?php if($this->hasChoiceAnswer($profile_question_relation->id)): ?>
                                        <li class="supplement1">※前回の回答が入力済</li>
                                    <?php endif; ?>
                                    <?php if ( $this->ActionError && !$this->ActionError->isValid('answer_'.$profile_questionnaire->id) ):?>
                                        <span class="iconError1"><?php assign ( $this->ActionError->getMessage('answer_'.$profile_questionnaire->id) )?></span>

                                    <?php elseif ($this->ActionError && !$this->ActionError->isValid('other_answer_'.$profile_questionnaire->id)): ?>
                                        <span class="iconError1"><?php assign ( $this->ActionError->getMessage('other_answer_'.$profile_questionnaire->id) )?></span>
                                    <?php endif; ?>

                                    <?php if (!$question_requirement->multi_answer_flg): ?>

                                        <?php foreach ($choices as $choice): ?>
                                            <li><?php write_html($this->formRadio( 'answer_'.$profile_questionnaire->id, $this->getSingleChoiceAnswer($profile_question_relation->id, $choice->id), array('class'=>'customRadio'), array($choice->id =>$choice->choice))); ?>
                                                <?php if ($choice->other_choice_flg): ?>
                                                    <?php write_html($this->formTextArea('other_answer_'.$profile_questionnaire->id, $this->getOtherText($profile_question_relation->id, $choice->id), array('cols'=>30, 'rows'=>10))) ?>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <?php foreach($choices as $choice):?>
                                            <li><?php write_html($this->formCheckbox2('answer_'.$profile_questionnaire->id.'[]', $this->getMultiChoiceAnswer($profile_question_relation->id,'answer_'.$profile_questionnaire->id), array('class'=>'customCheck'), array($choice->id =>$choice->choice))) ?>
                                                <?php if ($choice->other_choice_flg): ?>
                                                    <?php write_html($this->formTextArea('other_answer_'.$profile_questionnaire->id, $this->getOtherText($profile_question_relation->id, $choice->id), array('cols'=>30, 'rows'=>10))) ?>
                                                    
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach;?>

                                    <?php endif; ?>
                                </ul>
                            <?php elseif($this->isChoicePulldown($profile_questionnaire)): ?>
                                <p class="itemEdit">
                                    <?php if($this->hasChoiceAnswer($profile_question_relation->id)): ?>
                                        <span class="supplement1">※前回の回答が入力済</span>
                                    <?php endif; ?>

                                    <?php if ( $this->ActionError && !$this->ActionError->isValid('answer_'.$profile_questionnaire->id) ):?>
                                        <span class="iconError1"><?php assign ( $this->ActionError->getMessage('answer_'.$profile_questionnaire->id) )?></span>
                                    <?php endif; ?>
                                    <?php write_html($this->formSelect('answer_'.$profile_questionnaire->id, $this->getMultiChoiceAnswer($profile_question_relation->id), array(), $this->convertChoicesToMap($choices))); ?>
                                </p>
                            <?php else: ?>
                                <p class="itemEdit">
                                    <?php if($this->hasFreeAnswer($profile_question_relation->id)): ?>
                                        <span class="supplement1">※前回の回答が入力済</span>
                                    <?php endif; ?>
                                    <?php if ( $this->ActionError && !$this->ActionError->isValid('answer_'.$profile_questionnaire->id) ):?>
                                        <span class="iconError1"><?php assign ( $this->ActionError->getMessage('answer_'.$profile_questionnaire->id) )?></span>
                                    <?php endif; ?>

                                    <span class="editInput">
                                        <?php write_html($this->formTextArea('answer_'.$profile_questionnaire->id, $this->getFreeAnswer($profile_question_relation->id), array('cols'=>30, 'rows'=>10))) ?>
                                    </span>
                                </p>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>

                <!-- /.commonTableList1 --></ul>
        <?php endif;?>
        <?php write_html($this->formHidden('skip', 0)); ?>

        <!-- 仕様規約を確認する-->
        <?php if (!$data['entry_questionnaire_only']): ?>
            <section class="ruleAreaWrap1">
                <?php if ($data['required_agreement']):?>
                    <h2 class="agreementHd2">利用規約</h2>
                    <p class="supplement1">※以下の内容を確認・同意の上、次にお進みください。</p>
                    <div class="ruleArea">
                        <?php write_html($this->nl2brAndHtmlspecialchars($data['pageSettings']->agreement));?>
                        <!-- /.ruleArea --></div>
                    <?php if ($data['show_agreement_checkbox']):?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('agree_agreement') ):?>
                            <span class="iconError1">
                                <?php if($data['brand']->id == Brand::CLUB_LAVIE || $data['brand']->id == Brand::LAVIE_SPECIALFAN || $data['brand']->id == Brand::CLUB_LENOVO || $data['brand']->id == Brand::LENOVO_SPECIALFAN): // ハードコーディング ?>
                                    <?php assign('次へ進むには'.$data['brand']->name.' メンバー規約への同意にチェックを入れてください')?>
                                <?php else: ?>
                                    <?php assign('次へ進むには'.$data['brand']->name.' 利用規約への同意にチェックを入れてください')?>
                                <?php endif; ?>
                            </span>
                        <?php endif; ?>
                        <p class="ruleReadCheck">
                            <strong>
                                <label>
                                    <input type="checkbox" name="agree_agreement" value="1" id="checkRuleAgree">
                                    <?php if($data['brand']->id == Brand::CLUB_LAVIE || $data['brand']->id == Brand::LAVIE_SPECIALFAN || $data['brand']->id == Brand::CLUB_LENOVO || $data['brand']->id == Brand::LENOVO_SPECIALFAN): // ハードコーディング ?>
                                        <?php assign($data['brand']->name)?> メンバー規約に同意する
                                    <?php else: ?>
                                        <?php assign($data['brand']->name)?> 利用規約に同意する
                                    <?php endif; ?>
                                </label>
                            </strong>
                        </p>
                    <?php endif;?>
                <?php endif;?>
                <?php if (!$data['required_profile_questions'] && !$data['required_agreement']):?>
                    <p class="supplement1" style="text-align: center;"><?php assign($data['brand']->name); ?>に登録します。</p>
                <?php endif;?>
                <!-- /.ruleAreaWrap1 --></section>
        <?php endif; ?>

        <?php if ($data['pageStatus']['is_cmt_plugin_mode']): ?>
            <link rel="stylesheet" href="<?php assign($this->setVersion('/css/moniplaComment.css'))?>">
            <h2 class="hd2">投稿内容</h2>
            <section class="commentPostPreviw">
                <div class="commentPostWrap" id="moniplaCommentPlugin">
                    <div class="commentPost">
                        <div class="userData">
                            <p class="userImage">
                                <img src="<?php assign($data['pageStatus']['userInfo']->socialAccounts[0]->profileImageUrl ? $data['pageStatus']['userInfo']->socialAccounts[0]->profileImageUrl :$this->setVersion('/img/base/imgUser1.jpg')) ?>" alt="" width="40" height="40" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';"></p>
                            <!-- /.userData --></div>
                        <div class="postBody">
                            <p class="postUserName"><?php assign(!Util::isNullOrEmpty($data['pageStatus']['commentData']['nickname']) ? $data['pageStatus']['commentData']['nickname'] : $data['pageStatus']['userInfo']->name) ?></p>
                            <div class="postText">
                                <div class="postTextEdit jsCommentText" contenteditable="false" data-placeholder="コメントを追加">
                                    <?php write_html($data['pageStatus']['commentData']['comment_text']) ?>
                                    <!-- /.postTextEdit --></div>
                                <!-- /.postText --></div>
                            <!-- /.postBody --></div>
                        <!-- /.commentPost --></div>
                    <!-- /.commentPostWrap --></div>
                <!-- /.commentPostPreviw --></section>
            <?php if (count($data['pageStatus']['share_sns_list']) > 0): ?>
                <div id="moniplaCommentPlugin">
                    <div class="commentPost">
                        <div class="userActionWrap">
                            <div class="shareSns">
                                <p>共有</p>
                                <ul class="selectSns">
                                    <?php foreach ($data['pageStatus']['share_sns_list'] as $share_sns): ?>
                                        <?php if ($share_sns == SocialAccountService::$socialAccountLabel[SocialAccountService::SOCIAL_MEDIA_FACEBOOK]): ?>
                                            <li><label><input type="checkbox" checked="checked" name="social_media_ids[]" value="<?php assign(SocialAccountService::SOCIAL_MEDIA_FACEBOOK) ?>"><span class="iconFb1">Facebook</span></label></li>
                                        <?php endif ?>
                                        <?php if ($share_sns == SocialAccountService::$socialAccountLabel[SocialAccountService::SOCIAL_MEDIA_TWITTER]): ?>
                                            <li><label><input type="checkbox" checked="checked" name="social_media_ids[]" value="<?php assign(SocialAccountService::SOCIAL_MEDIA_TWITTER) ?>"><span class="iconTw1">Twitter</span></label></li>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                    <!-- /.selectSns --></ul>
                                <!-- /.shareSns --></div>
                            <!-- /.userActionWrap --></div>
                        <!-- /.commentPost --></div>
                    <!-- /#moniplaCommentPlugin --></div>
            <?php endif ?>

            <div class="textSetBtn">
                <p class="supplement1">以下ボタンを押すと入力したコメントが投稿されます。</p>
                <p class="btnSet"><span class="btn3"><a href="javascript:void(0);" id="submitEntry" class="large1">投稿して完了する</a>
            </span></p>
            </div>
        <?php else: ?>
            <div class="messageFooter">
                <p class="btnSet">
        <span class="btn3">
            <a href="javascript:void(0);" id="submitEntry" class="large1">
                <?php assign($this->getButtonText($data['entry_questionnaire_only'])); ?>
            </a>
        </span>
                </p>
            </div>
        <?php endif ?>
    </form>

    <script src="<?php assign(Util::getHttpProtocol()); ?>://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>

<?php if (!$data['is_api']): ?>

    <?php write_html($this->scriptTag('ISignupService')) ?>

    <?php if (Util::isSmartPhone()): ?>
        <?php write_html($this->scriptTag('SubmitFormService'))?>
    <?php endif; ?>

<?php endif; ?>