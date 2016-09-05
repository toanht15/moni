<?php $data['pageStatus']['isLoginAdmin'] = false;
$data['pageStatus']['isLogin'] = false; ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>

<section class="demoMode">
    <h1 class="modeLabel">ユーザ情報入力のプレビュー画面です </h1>
    <div class="modeDetail">
        <p>
            (<a href="<?php assign($data['preview_url']) ?>"><?php assign($data['preview_url']) ?></a>)<br>
        </p>
        <!-- /.modeDetail --></div>
    <!-- /.demoMode --></section>


<article class="singleWrap">

    <?php $title = $data['required_profile_questions'] ? 'ユーザー情報' : '確認'; ?>
    <h1 class="singleWrapHd1"><?php assign($title); ?></h1>

    <?php if($data['pageSettings']->privacy_required_restricted):?>
        <span class="attention1">※登録は<?php assign($data['pageSettings']->restricted_age)?>歳以上限定です。</span>
    <?php endif;?>

    <?php if($data['required_profile_questions']):?>

        <ul class="commonTableList1">
            <?php if($data['isRequiredPrivacy']):?>
                <?php if($data['pageSettings']->privacy_required_name):?>
                    <li>
                        <p class="title1">
                            <span class="require1">氏名（かな）</span>
                            <!-- /.title1 --></p>
                        <p class="itemEdit">

                                <span class="editInput">
                                        <label class="editName"><span>姓</span><?php write_html( $this->formText('lastName', '', array('class' => 'name') ))?></label>
                                        <label class="editName"><span>名</span><?php write_html( $this->formText('firstName', '', array('class' => 'name') ))?></label>
                                    </span>
                                <span class="editInput">
                                        <label class="editName"><span>せい</span><?php write_html( $this->formText('lastNameKana', '', array('class' => 'name') ))?></label>
                                        <label class="editName"><span>めい</span><?php write_html( $this->formText('firstNameKana', '', array('class' => 'name') ))?></label>
                                    <!-- /.editInput --></span>
                            <!-- /.itemEdit --></p>
                    </li>
                <?php endif;?>
                <?php if($data['pageSettings']->privacy_required_sex):?>
                    <li>
                        <p class="title1">
                            <span class="require1">性別</span>
                            <!-- /.title1 --></p>
                        <p class="itemEdit">
                            <span class="editInput">
                                <?php write_html( $this->formRadio( 'sex', '', array('class'=>'customRadio'), $data['sex'])); ?>
                                <!-- /.editInput --></span>
                            <!-- /.itemEdit --></p>
                    </li>
                <?php endif;?>
                <?php if($data['pageSettings']->privacy_required_birthday):?>
                    <li>
                        <p class="title1">
                            <span class="require1">生年月日</span>
                            <!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <label>西暦<?php write_html( $this->formNumber('birthDay_y', '', array('class' => 'inputNum') ))?>年</label>
                                    <label><?php write_html( $this->formNumber('birthDay_m', '', array('class' => 'inputNumSmall') ))?>月</label>
                                    <label><?php write_html( $this->formNumber('birthDay_d', '', array('class' => 'inputNumSmall') ))?>日</label>
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
                                <span class="editInput">
                                    <?php write_html( $this->formText('zipCode1', '', array('class' => 'inputNum', 'maxlength' => 3) ))?>－<?php write_html( $this->formText('zipCode2', '', array('class' => 'inputNum', 'maxlength' => 4, 'onKeyUp' => "AjaxZip3.zip2addr('zipCode1','zipCode2','prefId','address1','address2');") ))?>
                                    <a href="javascript:;" onclick="AjaxZip3.zip2addr('zipCode1','zipCode2','prefId','address1','address2');">住所検索</a><span class="supplement1">※半角数字</span>
                                <!-- /.editInput --></span>
                            <!-- /.itemEdit --></p>
                    </li>
                <?php endif; ?>
                <?php if ($data['pageSettings']->privacy_required_address == BrandPageSetting::GET_ALL_ADDRESS || $data['pageSettings']->privacy_required_address == BrandPageSetting::GET_STATE_ADDRESS): ?>
                    <li>
                        <p class="title1">
                            <span class="require1">都道府県</span>
                            <!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <?php write_html( $this->formSelect('prefId',  Prefecture::PREF_TOKYO, array(), $data['prefectures']))?>
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
                                <span class="editInput">
                                    <?php write_html( $this->formText('address1', ''))?>
                                    <!-- /.editInput --></span>
                            <!-- /.itemEdit --></p>
                    </li>
                    <li>
                        <p class="title1">
                            <span class="require1">番地</span>
                            <!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <?php write_html( $this->formText('address2', ''))?>
                                    <!-- /.editInput --></span>
                            <!-- /.itemEdit --></p>
                    </li>
                    <li>
                        <p class="title1">
                            建物
                            <!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                  <?php write_html( $this->formText('address3', ''))?>
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
                                <span class="editInput">
                                    <?php write_html( $this->formTel('telNo1', '', array('class' => 'inputNum') ))?>－<?php write_html( $this->formTel('telNo2', '', array('class' => 'inputNum') ))?>－<?php write_html( $this->formTel('telNo3', '', array('class' => 'inputNum') ))?>
                                    <!-- /.editInput --></span>
                            <span class="supplement1">※半角数字</span>
                            <!-- /.itemEdit --></p>
                    </li>
                <?php endif;?>
            <?php endif;?>
            <?php if($data['mailInfo']['needMailAddress']):?>
                <li>
                    <p class="title1">
                        <span class="require1">メールアドレス</span>
                        <!-- /.title1 --></p>
                    <p class="itemEdit">
                            <span class="editInput">
                                <?php write_html( $this->formEmail('mailAddress', ''))?>
                                <!-- /.editInput --></span>
                        <!-- /.itemEdit --></p>
                </li>
            <?php endif;?>
            <?php foreach($data['profile_questions'] as $profile_question): ?>
                <li>
                    <p class="title1">
                        <span <?php if($profile_question['is_requirement']) write_html('class="require1"') ?>><?php assign($profile_question['question']) ?></span>
                        <!-- /.title1 --></p>
                    <?php if($profile_question['type_id'] == QuestionTypeService::CHOICE_ANSWER_TYPE): ?>
                        <ul class="itemEdit">
                            <?php if (!$profile_question['is_multi_answer']): ?>
                                <?php foreach ($profile_question['choices'] as $choice): ?>
                                    <li><?php write_html($this->formRadio( 'answer_'.$profile_question['id'], array(), array('class'=>'customRadio'), array($choice =>$choice))); ?>
                                        <?php if ($choice == 'その他'): ?>
                                            <?php write_html($this->formTextArea('other_answer_'.$profile_question['id'], '', array('cols'=>30, 'rows'=>10))) ?>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php foreach($profile_question['choices'] as $choice):?>
                                    <li><?php write_html($this->formCheckbox2('answer_'.$profile_question['id'].'[]', array(), array('class'=>'customCheck'), array($choice =>$choice))) ?>
                                        <?php if ($choice == 'その他'): ?>
                                            <?php write_html($this->formTextArea('other_answer_'.$profile_question['id'], '', array('cols'=>30, 'rows'=>10))) ?>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach;?>
                            <?php endif; ?>
                        </ul>
                    <?php elseif($profile_question['type_id'] == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE): ?>
                        <p class="itemEdit">
                            <?php array_unshift($profile_question['choices'], "選択してください") ?>
                            <?php write_html($this->formSelect('answer_'.$profile_question['id'], null, array(), $profile_question['choices'])); ?>
                        </p>
                    <?php else: ?>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <?php write_html($this->formTextArea('answer_'.$profile_question['id'], '', array('cols'=>30, 'rows'=>10))) ?>
                                 </span>
                        </p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>

            <!-- /.commonTableList1 --></ul>
    <?php endif;?>

    <section class="ruleAreaWrap1">
        <?php if($data['required_agreement']):?>
            <h2 class="agreementHd2">利用規約</h2>
            <p class="supplement1">※以下の内容を確認・同意の上、次にお進みください。</p>
            <div class="ruleArea">
                <?php write_html($this->nl2brAndHtmlspecialchars($data['pageSettings']->agreement));?>
                <!-- /.ruleArea --></div>
            <?php if ($data['show_agreement_checkbox']):?>
                <p class="ruleReadCheck"><strong><label>
                    <input type="checkbox" name="agree_agreement" value="1" id="checkRuleAgree">
                    <?php if($data['brand']->id == Brand::CLUB_LAVIE || $data['brand']->id == Brand::LAVIE_SPECIALFAN || $data['brand']->id == Brand::CLUB_LENOVO || $data['brand']->id == Brand::LENOVO_SPECIALFAN): // ハードコーディング ?>
                        <?php assign($data['brand']->name)?> メンバー規約に同意する
                    <?php else: ?>
                        <?php assign($data['brand']->name)?> 利用規約に同意する
                    <?php endif; ?>
                </label></strong></p>
            <?php endif;?>
        <?php endif;?>

        <?php if (!$data['required_profile_questions'] && !$data['required_agreement']): ?>
            <p class="supplement1" style="text-align: center;"><?php assign($data['pageStatus']['brand']->name); ?>に登録します。</p>
        <?php endif;?>
        <!-- /.ruleAreaWrap1 --></section>

    <div class="messageFooter">
        <p class="btnSet"><span class="btn3"><a href="javascript:;" id="submitEntry">次へ</a></span></p>
    </div>


    <script src="<?php assign(Util::getHttpProtocol()); ?>://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
    <!-- /.singleWrap --></article>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
