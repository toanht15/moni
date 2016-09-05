<?php
$cp =  CpInfoContainer::getInstance()->getCpById($data['cp_user']->cp_id);
$is_opening_flg = $data['message_info']['cp_action']->isOpeningCpAction();

$api_url = Util::rewriteUrl('messages', "api_execute_questionnaire_action.json");
if ($cp->isCpTypeCampaign() && $is_opening_flg) {
    $entry_questionnaires = $data['entry_questionnaire_data']['entry_questionnaires'];
    $has_entry_questionnaire = $data['entry_questionnaire_data']['has_entry_questionnaire'];
    if ($cp->requireRestriction($data['cp_user'])) {
        $api_url = Util::rewriteUrl('messages', 'api_update_demography.json');
    } elseif ($has_entry_questionnaire || $data['pageStatus']['needDisplayPersonalForm']) {
        $api_url = Util::rewriteUrl('messages', "api_update_personal_info_and_execute_entry.json");
    }
}
?>

<section class="message jsMessage" id="message_<?php assign($data['message_info']["message"]->id); ?>" >
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="executeQuestionnaireActionForm" action="<?php assign($api_url); ?>" method="POST" enctype="multipart/form-data" >

        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>

        <?php if($data["message_info"]["concrete_action"]->image_url): ?>
            <p class="messageImg"><img src="<?php assign($data["message_info"]["concrete_action"]->image_url); ?>" alt="campaign img"></p>
        <?php endif; ?>

        <?php $message_text = $data['message_info']['concrete_action']->html_content ? $data['message_info']['concrete_action']->html_content : $this->toHalfContentDeeply($data['message_info']['concrete_action']->text); ?>
        <section class="messageText"><?php write_html($message_text); ?></section>

        <?php if ($data['pageStatus']['isNotMatchDemography']): ?>
            <p class="joinLimit" id="joinLimit"><?php write_html($data['pageStatus']['demographyErrors']) ?></p>
        <?php endif ?>

        <dl class="module">

        <?php
            $service_factory = new aafwServiceFactory();
            /** @var CpQuestionnaireService $cp_questionnaire_service */
            $cp_questionnaire_service = $service_factory->create('CpQuestionnaireService');

            /** @var BrandsUsersRelationService $brands_users_relation_service */
            $brands_users_relation_service = $service_factory->create('BrandsUsersRelationService');
            $brands_users_relation = $brands_users_relation_service->getBrandsUsersRelationsByBrandIdAndUserIds($data['pageStatus']['brand']->id, $data['cp_user']->user_id)->current();

            // アンケートの設問を並び順通りに取得
            $questionnaire_action = $cp_questionnaire_service->getCpQuestionnaireAction($data['message_info']["cp_action"]->id);
            if($questionnaire_action->id) {
                $questionnaire_question_relations = $cp_questionnaire_service->getRelationsByQuestionnaireActionId($questionnaire_action->id);
            }
            $join_status = $data["message_info"]["action_status"]->status;


            // まとめてSELECT
            $qst_relation_ids = array();
            foreach($questionnaire_question_relations as $relation) {
                $qst_relation_ids[] = $relation->id;
            }
            $single_answer_map = array();
            $multi_answer_map = array();
            if (count($qst_relation_ids) > 0) {
                $choice_answers = $cp_questionnaire_service->getChoiceAnswers($brands_users_relation->id, $qst_relation_ids);
                foreach ($choice_answers as $ans) {
                   $single_answer_map[$ans->questionnaires_questions_relation_id] = $ans;
                   $multi_answer_map[$ans->questionnaires_questions_relation_id . '-' . $ans->choice_id] = $ans;
                }
            }
        ?>
        <?php write_html($this->formHidden('cp_questionnaire_action_id', $questionnaire_action->id)); ?>
        <?php foreach($questionnaire_question_relations as $relation): ?>
            <?php $question = $cp_questionnaire_service->getQuestionById($relation->question_id)?>
            <dt data-questionId=<?php assign('question/' . $question->id); ?>>
                <span class="num">Q<?php assign($relation->number); ?></span>
                <span class="<?php assign($relation->requirement_flg == CpQuestionnaireService::QUESTION_REQUIRED ? 'require1' : ''); ?>"><?php write_html($this->toHalfContentDeeply($question->question, false)); ?></span>
            </dt>
            <?php if($question->type_id == QuestionTypeService::CHOICE_ANSWER_TYPE): ?>
                <dd>
                    <ul class="moduleItemList">
                        <?php
                            $requirement = $cp_questionnaire_service->getRequirementByQuestionId($question->id);
                            $choices = $cp_questionnaire_service->getChoicesExceptOtherChoiceByQuestionId($question->id)->toArray();
                        ?>
                        <?php if($requirement->random_order_flg == CpQuestionnaireService::RANDOM_ORDER) shuffle($choices); //設問のランダム表示 ?>
                        <?php if($requirement->multi_answer_flg != CpQuestionnaireService::MULTI_ANSWER): ?>
                            <?php foreach($choices as $choice): ?>
                                <li>
                                    <?php write_html($this->formRadio(
                                        'single_answer/' . $question->id,
                                        $join_status ? $single_answer_map[$relation->id]->choice_id : PHPParser::ACTION_FORM,
                                        array('class'=>'customRadio', 'disabled' => $join_status ? 'disabled' : ''),
                                        array($choice->id => $choice->choice)
                                    ))?>
                                </li>
                            <?php endforeach;?>
                            <?php if($requirement->use_other_choice_flg == CpQuestionnaireService::USE_OTHER_CHOICE): ?>
                                <?php $other_choice = $cp_questionnaire_service->getOtherChoice($question->id); ?>
                                <li>
                                    <?php write_html($this->formRadio(
                                        'single_answer/' . $question->id,
                                        $join_status ? $single_answer_map[$relation->id]->choice_id : PHPParser::ACTION_FORM,
                                        array('class'=>'customRadio', 'disabled' => $join_status ? 'disabled' : ''),
                                        array($other_choice->id => $other_choice->choice)
                                    ))?>
                                    <?php write_html($this->formTextArea(
                                        'single_answer_othertext/' . $question->id,
                                        $join_status ? $single_answer_map[$relation->id]->answer_text : PHPParser::ACTION_FORM,
                                        array('cols' => '30', 'rows' => '10', 'maxlength'=>'255', 'disabled' => $join_status ? 'disabled' : '')
                                    ))?>
                                </li>
                            <?php endif;?>
                        <?php else: ?>
                            <?php foreach($choices as $choice): ?>
                                <li>
                                    <?php write_html($this->formCheckbox(
                                        'multi_answer/' . $question->id . '/' . $choice->id,
                                        PHPParser::ACTION_FORM,
                                        array('class'=>'customCheck',
                                            'checked' => $join_status && $multi_answer_map[$relation->id . '-' . $choice->id] ? 'checked' : '',
                                            'disabled' => $join_status ? 'disabled' : ''),
                                        array($choice->id => $choice->choice)
                                    ))?>
                                </li>
                            <?php endforeach;?>
                            <?php if($requirement->use_other_choice_flg == CpQuestionnaireService::USE_OTHER_CHOICE): ?>
                                <?php $other_choice = $cp_questionnaire_service->getOtherChoice($question->id); ?>
                                <li>
                                    <?php write_html($this->formCheckbox(
                                        'multi_answer/' . $question->id . '/' . $other_choice->id,
                                        PHPParser::ACTION_FORM,
                                        array('class'=>'customCheck',
                                            'checked' => $join_status && $multi_answer_map[$relation->id . '-' . $other_choice->id] ? 'checked' : '',
                                            'disabled' => $join_status ? 'disabled' : ''),
                                        array($other_choice->id => $other_choice->choice)
                                    ))?>
                                    <?php write_html($this->formTextArea(
                                        'multi_answer_othertext/' . $question->id . '/' . $other_choice->id,
                                        $join_status ? $multi_answer_map[$relation->id . '-' . $other_choice->id]->answer_text : PHPParser::ACTION_FORM,
                                        array('cols' => '30', 'rows' => '10', 'maxlength'=>'255', 'disabled' => $join_status ? 'disabled' : '')
                                    ))?>
                                </li>
                            <?php endif;?>
                        <?php endif;?>
                    </ul>
                </dd>
            <?php elseif($question->type_id == QuestionTypeService::FREE_ANSWER_TYPE): ?>
                <dd>
                    <?php write_html($this->formTextarea(
                        'free_answer/' . $question->id,
                        $join_status ? $cp_questionnaire_service->getFreeAnswer($brands_users_relation->id, $relation->id)->answer_text : PHPParser::ACTION_FORM,
                        array('maxlength'=>'2048', 'disabled' => $join_status ? 'disabled' : '')
                    )) ?>
                </dd>
            <?php elseif($question->type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE): ?>
                <dd>
                    <?php
                    $requirement = $cp_questionnaire_service->getRequirementByQuestionId($question->id);
                    $choices = $cp_questionnaire_service->getChoicesExceptOtherChoiceByQuestionId($question->id)->toArray();
                    if($requirement->random_order_flg == CpQuestionnaireService::RANDOM_ORDER) shuffle($choices);
                    ?>
                    <?php
                    $option = array();
                    $option[''] = '選択してください';
                    foreach($choices as $choice) {
                        $option[$choice->id] = $choice->choice;
                    } ?>
                    <?php write_html($this->formSelect(
                        'single_answer/' . $question->id,
                        $join_status ? $single_answer_map[$relation->id]->choice_id : PHPParser::ACTION_FORM,
                        array('disabled' => $join_status ? 'disabled' : ''),
                        $option
                    )) ?>
                </dd>
            <?php else: ?>
                <dd>
                    <ul class="moduleItemImg">
                        <?php
                            $requirement = $cp_questionnaire_service->getRequirementByQuestionId($question->id);
                            $choices = $cp_questionnaire_service->getChoicesExceptOtherChoiceByQuestionId($question->id)->toArray();
                            $choice_count = count($choices);
                            $choice_no = 1
                        ?>
                        <?php if($requirement->random_order_flg == CpQuestionnaireService::RANDOM_ORDER) shuffle($choices); //設問のランダム表示 ?>
                        <?php if($requirement->multi_answer_flg != CpQuestionnaireService::MULTI_ANSWER): ?>
                            <?php foreach($choices as $choice): ?>
                                <?php $choice_no == 1 ? write_html('<li>') : write_html('--><li>') ?>
                                <input type="radio" class="customRadio" name="<?php assign('single_answer/'.$question->id) ?>" id="<?php assign('single_answer/'.$question->id.'_'.$choice->id)?>"
                                       value="<?php assign($choice->id) ?>" <?php assign($join_status ? 'disabled=disabled' : '') ?>
                                       <?php assign($join_status && $choice->id == $single_answer_map[$relation->id]->choice_id ? 'checked=checked' : '') ?>>
                                <label for="<?php assign('single_answer/'.$question->id.'_'.$choice->id)?>">
                                    <figure>
                                        <figcaption class="title" data-action_type="questionnaire">　</figcaption>
                                            <span class="img"><img src="<?php assign($choice->image_url);?>" alt="<?php assign($choice->choice);?>"></span>
                                    </figure>
                                </label>
                                <a href="javascript:void(0)" class="previwe">拡大表示する</a>
                                <?php $choice_no == $choice_count ? write_html('</li>') : write_html('</li><!--') ?>
                                <?php $choice_no += 1; ?>
                            <?php endforeach;?>
                        <?php else: ?>
                            <?php foreach($choices as $choice): ?>
                                <?php $choice_no == 1 ? write_html('<li>') : write_html('--><li>') ?>
                                <input type="checkbox" class="customCheck" name="<?php assign('multi_answer/'.$question->id.'/'.$choice->id) ?>" id="<?php assign('multi_answer/'.$question->id.'_'.$choice->id)?>"
                                       value="<?php assign($choice->id) ?>" <?php assign($join_status ? 'disabled=disabled' : '') ?>
                                       <?php assign($join_status && $multi_answer_map[$relation->id . '-' . $choice->id] ? 'checked=checked' : '')?>>
                                <label for="<?php assign('multi_answer/'.$question->id.'_'.$choice->id)?>">
                                    <figure>
                                        <figcaption class="title" data-action_type="questionnaire">　</figcaption>
                                        <span class="img"><img src="<?php assign($choice->image_url);?>" alt="<?php assign($choice->choice);?>"></span>
                                    </figure>
                                </label>
                                <a href="javascript:void(0)" class="previwe">拡大表示する</a>
                                <?php $choice_no == $choice_count ? write_html('</li>') : write_html('</li><!--') ?>
                                <?php $choice_no += 1; ?>
                            <?php endforeach;?>
                        <?php endif;?>
                    </ul>
                </dd>
            <?php endif; ?>
        <?php endforeach; ?>
        </dl>

        <ul class="btnSet">
            <?php if ($join_status == CpUserActionStatus::NOT_JOIN) : ?>
                <li class="btn3"><a class="cmd_execute_questionnaire_action large1" href="javascript:void(0)"><?php assign($data["message_info"]["concrete_action"]->button_label_text); ?></a></li>
            <?php else: ?>
                <?php if (!$cp->isNonIncentiveCp()): ?>
                    <li class="btn3"><span class="large1"><?php assign($data["message_info"]["concrete_action"]->button_label_text); ?></span></li>
                <?php elseif ($cp->isAuCampaign()): ?>
                    <li class="btn3"><span class="large1">応募済み</span></li>
                <?php elseif (!$cp->canEntry(RequestuserInfoContainer::getInstance()->getStatusByCp($cp))): ?>
                    <li class="btn1"><span class="large1">終了しました</span></li>
                <?php elseif ($cp->join_limit_sns_flg == Cp::JOIN_LIMIT_SNS_ON): ?>
                    <?php if ($data['cp_user']->join_sns == SocialAccountService::SOCIAL_MEDIA_FACEBOOK): ?>
                        <li class="btnSnsFb1"><span class="arrow1"><span class="inner">Facebook<br>で応募</span></span></li>
                    <?php elseif ($data['cp_user']->join_sns == SocialAccountService::SOCIAL_MEDIA_TWITTER): ?>
                        <li class="btnSnsTw1"><span class="arrow1"><span class="inner">Twitter<br>で応募</span></span></li>
                    <?php elseif ($data['cp_user']->join_sns == SocialAccountService::SOCIAL_MEDIA_LINE): ?>
                        <li class="btnSnsLn1"><span class="arrow1"><span class="inner">LINE<br>で応募</span></span></li>
                    <?php elseif ($data['cp_user']->join_sns == SocialAccountService::SOCIAL_MEDIA_INSTAGRAM): ?>
                        <li class="btnSnsIg1"><span class="arrow1"><span class="inner">Instagram<br>で応募</span></span></li>
                    <?php elseif ($data['cp_user']->join_sns == SocialAccountService::SOCIAL_MEDIA_GOOGLE): ?>
                        <li class="btnSnsGp1"><span class="arrow1"><span class="inner">Google<br>で応募</span></span></li>
                    <?php elseif ($data['cp_user']->join_sns == SocialAccountService::SOCIAL_MEDIA_YAHOO): ?>
                        <li class="btnSnsYh1"><span class="arrow1"><span class="inner">Yahoo!<br><span class="space"> </span>JAPAN ID<br>で応募</span></span></li>
                    <?php elseif ($data['cp_user']->join_sns == SocialAccountService::SOCIAL_MEDIA_LINKEDIN): ?>
                        <li class="btnSnsIn1"><span class="arrow1"><span class="inner">LinkedIn<br>で応募</span></span></li>
                    <?php else : ?>
                        <li class="btn3"><span class="large1"><?php assign($data["message_info"]["concrete_action"]->button_label_text); ?></span></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="btn3"><span class="large1"><?php assign($data["message_info"]["concrete_action"]->button_label_text); ?></span></li>
                <?php endif; ?>
            <?php endif; ?>
            <!-- /.btnSet --></ul>

        <?php if ($is_opening_flg): ?>
            <?php if($cp->join_limit_flg == Cp::JOIN_LIMIT_OFF && $cp->share_flg == Cp::FLAG_SHOW_VALUE):?>
                <div class="campaignShare">
                    <p>このキャンペーンを友達に知らせよう</p>
                    <ul class="snsBtns-box">
                        <li><div class="fb-like" data-href="<?php assign($data["action_info"]["cp"]["url"]) ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div></li
                            ><li><a href="https://twitter.com/share" data-url="<?php assign($data["cp_info"]["cp"]["url"]) ?>" class="twitter-share-button" data-lang="ja" data-count="vertical" data-text="<?php assign($data['cp_info']['tweet_share_text']) ?>">ツイート</a></li
                            ><li class="line"><span><script type="text/javascript" src="//media.line.me/js/line-button.js?v=20140411" ></script><script type="text/javascript">new media_line_me.LineButton({"pc":false,"lang":"ja","type":"a", "withUrl":false, "text": "<?php assign($data["cp_info"]["cp"]["url"]) ?>"});</script></span></li
                            ><li><div class="g-plusone" data-size="medium" data-href="<?php assign($data["cp_info"]["cp"]["url"]) ?>"></div></li
                            <!-- /.snsBtns --></ul>
                    <!-- /.campaignShare --></div>
            <?php endif;?>
            <ul class="campaignData">
            <?php if (!$cp->isNonIncentiveCp()): ?>
                <?php if($data["cp_info"]["cp"]["show_winner_label"] == Cp::FLAG_SHOW_VALUE): ?>
                    <li class="present"><span class="itemTitle">プレゼント</span><span class="itemData"><?php assign($data["cp_info"]["cp"]["winner_label"]); ?></span></li>
                <?php else : ?>
                    <li class="present"><span class="itemTitle">プレゼント</span><span class="itemData"><?php assign($data["cp_info"]["cp"]["winner_count"]); ?>名様</span></li>
                <?php endif; ?>

                <li class="term"><span class="itemTitle">開催期間</span><span class="itemData"><?php assign($data["cp_info"]["cp"]["start_datetime"]); ?> ～ <?php assign($data["cp_info"]["cp"]["end_datetime"]); ?></span></li>
                <?php if (!($data["cp_info"]["cp"]["id"] == 3962 || $data["cp_info"]["cp"]["id"] == 4550)): ?>
                    <li class="result">
                        <?php if ($data['cp_info']['cp']['shipping_method'] == Cp::SHIPPING_METHOD_PRESENT): ?>
                            <span class="itemTitle">発表</span>
                        <?php else: ?>
                            <span class="itemTitle">発表日</span>
                        <?php endif; ?>

                        <span class="itemData">
                            <?php if ($data['cp_info']['cp']['announce_display_label_use_flg'] == 1): ?>
                                <?php asign($data['cp_info']['cp']['announce_display_label']) ?>
                            <?php elseif ($data["cp_info"]["cp"]["shipping_method"] == Cp::SHIPPING_METHOD_PRESENT): ?>
                                賞品の発送をもって発表
                            <?php else: ?>
                                <?php assign($data["cp_info"]["cp"]["announce_date"]); ?>
                            <?php endif; ?>
                        </span>
                    </li>
                <?php endif ?>
            <?php elseif (!$cp->isPermanent()): ?>
                <li class="term"><span class="itemTitle">開催期間</span><span class="itemData"><?php assign($data["cp_info"]["cp"]["start_datetime"]); ?> ～ <?php assign($data["cp_info"]["cp"]["end_datetime"]); ?></span></li>
            <?php endif ?>
            <li class="sponsor"><span class="itemTitle">開催</span><span class="itemData"><?php assign($data["cp_info"]["cp"]["sponsor"]); ?></span></li>
            <?php if($data["cp_info"]["cp"]["show_recruitment_note"] == Cp::FLAG_SHOW_VALUE): ?>
                <li class="attention"><span class="itemTitle">注意事項</span><span class="itemData"><?php write_html($this->toHalfContent($data["cp_info"]["cp"]["recruitment_note"], false)); ?></span></li>
            <?php endif; ?>

            <!-- /.campaignData --></ul>
        <?php endif ?>
    </form>

<!-- /.message --></section>
<?php
if ($cp->isCpTypeCampaign()
    && $cp->isNonIncentiveCP()
    && !$data['cp_user']->isNotMatchDemography()
    && (($data['pageStatus']['isFirstEntryRead'] && $has_entry_questionnaire) || $data['pageStatus']['needDisplayUserProfileForm'] || $data['pageStatus']['needDisplayPersonalForm']
    )) {
    if ($data['pageStatus']['needDisplayUserProfileForm']) {
        write_html('<section class="message jsMessage">');
        write_html($this->parseTemplate('auth/UserProfileForm.php', array(
            'is_api' => true,
            'need_display_personal_form' => $data['pageStatus']['needDisplayPersonalForm'],
            'cp_id' => $data['cp_user']->cp_id,
            'cp_action_id' => $data['message_info']["cp_action"]->id,
            'cp_user_id' => $data['cp_user']->id,
            'parent_class_name' => 'message',
            'brand' => $data['brand'],
            'pageStatus' => $data['pageStatus'],
            'ActionForm' => $this->ActionForm,
            'ActionError' => $this->ActionError
        )));
        write_html('</section>');
    }

    if ($data['pageStatus']['needDisplayPersonalForm']) {
        if ($data['pageStatus']['needDisplayUserProfileForm']) {
            write_html('<section class="message jsMessageHidden" style="display: none;">');
        } else {
            write_html('<section class="message jsMessage">');
        }

        write_html(aafwWidgets::getInstance()->loadWidget('BrandcoSignupForm')->render(array(
            'is_api' => true,
            'cp' => $cp,
            'parent_class_name' => 'message',
            'brand' => $data['pageStatus']['brand'],
            'ActionForm' => $this->ActionForm,
            'ActionError' => $this->ActionError,
            'entry_questionnaires' => $entry_questionnaires,
            'entry_questionnaire_only' => $has_entry_questionnaire && $data['pageStatus']['isFirstEntryRead'] && !$cp->requireRestriction($data['cp_user']),
            'brands_users_relation_id' => $data['brands_users_relation_id'],
            'ignore_prefill' => $data['ignore_prefill']
        )));
        write_html('</section>');
    }
}
?>
<?php write_html($this->scriptTag('user/UserActionQuestionnaireService')); ?>