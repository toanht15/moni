<?php $questions = [null, 'ふりがな', '学年', '大学', '学部', '高校' , '電話番号', 'メールアドレス']; ?>
<div class="itemTableWrap">
    <table class="itemTable">
        <thead>
        <tr>
            <?php $is_first = true; ?>
            <?php foreach ($data['use_profile_questions'] as $i => $profile_question_relation):?>
                <?php if ($is_first) { $is_first = false; continue; } ?>
                <?php $profile_question = $data['profile_questions'][$profile_question_relation->id]?>
                <th rowspan="2" class="jsAreaToggleWrap" title="<?php assign('Q'.$profile_question_relation->number.'.'.$profile_question->question)?>">
                    <?php assign(Util::cutTextByWidth($questions[$i], 190))?>
                    <?php $template_name = $profile_question->type_id != QuestionTypeService::FREE_ANSWER_TYPE ? 'SearchChoiceQuestionnaire.php' : 'SearchFreeAnswerQuestionnaire.php'; ?>
                    <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE.'/'.$profile_question_relation->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                    <?php write_html($this->parseTemplate($template_name, array(
                        'search_questionnaire'      => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE.'/'.$profile_question_relation->id],
                        'question_id'               => $profile_question->id,
                        'relation_id'               => $profile_question_relation->id,
                        'search_questionnaire_type' => CpQuestionnaireService::TYPE_PROFILE_QUESTION
                    ))) ?>
                </th>
            <?php endforeach;?>
            <th rowspan="2" class="jsAreaToggleWrap">
                評価<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_RATE] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchMemberRate.php', array(
                    'search_rate' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_RATE]
                ))) ?>
            </th>
            <th rowspan="2" class="jsAreaToggleWrap">
                登録期間<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchRegisterPeriod.php', array(
                    'search_profile_register_period' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD]
                ))) ?>
            </th>
            <th colspan="1">
                FB連携/友達数
            </th>
            <?php $privacy_required_count = 0; ?>
            <?php if($data['page_settings']->privacy_required_sex): ?>
                <?php $privacy_required_count += 1;?>
                <th rowspan="2" class="jsAreaToggleWrap">
                    性別<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SEX] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                    <?php write_html($this->parseTemplate('SearchSex.php', array(
                        'search_profile_sex' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SEX]
                    ))) ?>
                </th>
            <?php endif; ?>
            <?php if($data['page_settings']->privacy_required_address): ?>
                <?php $privacy_required_count += 1;?>
                <th rowspan="2" class="jsAreaToggleWrap">
                    都道府県<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_ADDRESS] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                    <?php write_html($this->parseTemplate('SearchAddress.php', array(
                        'search_profile_address' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_ADDRESS]
                    ))) ?>
                </th>
            <?php endif; ?>
            <?php if($data['page_settings']->privacy_required_birthday): ?>
                <?php $privacy_required_count += 1;?>
                <th rowspan="2" class="jsAreaToggleWrap">
                    年齢<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_AGE] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                    <?php write_html($this->parseTemplate('SearchAge.php', array(
                        'search_profile_age' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_AGE]
                    ))) ?>
                </th>
            <?php endif; ?>
            <?php if($data['use_profile_questions']): ?>
                <th rowspan="2" class="jsAreaToggleWrap">
                    アンケート<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                    <?php write_html($this->parseTemplate('SearchQuestionnaireStatus.php', array(
                        'search_questionnaire_status' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS]
                    ))) ?>
                </th>
            <?php endif; ?>
        <tr>
            <th class="snsAccount jsAreaToggleWrap">
                <span class="iconFB2">Facebook</span>
                <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_FACEBOOK] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchSocialAccount.php', array(
                    'search_social_account' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_FACEBOOK],
                    'social_media_type' => SocialAccountService::SOCIAL_MEDIA_FACEBOOK
                ))) ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($data['fan_list_users'] as $fan_list_user): ?>
            <?php $user_profile = $data['user_profile'][$fan_list_user->user_id]; ?>
            <?php if($data['action']->id): ?>
                <?php $page_user_message = $data['page_user_message'][$fan_list_user->user_id]; ?>
                <?php if($page_user_message[0]): ?>
                    <tr class="sendUser">
                <?php elseif($page_user_message[1]): ?>
                    <tr class="checkedUser">
                <?php else: ?>
                    <tr>
                <?php endif; ?>
            <?php else: ?>
                <tr>
            <?php endif; ?>

            <?php $is_first = true; ?>
            <?php foreach ($data['profile_questions'] as $i => $profile_question):?>
                <?php if ($is_first) { $is_first = false; continue; } ?>
                <?php if(isset($user_profile['question_'.$profile_question->id])): ?>
                    <?php $answer = $user_profile['question_'.$profile_question->id]?>
                    <td title='<?php assign($answer) ?>'><?php assign(Util::cutTextByWidth($answer, 190)) ?></td>
                <?php else: ?>
                    <td></td>
                <?php endif; ?>
            <?php endforeach;?>

            <td class="userRating">
                <?php  $rate_info = $data['brand_user_relation_service']->getMemberRate($user_profile['rate']);  ?>
                <img src="<?php assign($this->setVersion($rate_info['image_url'])) ?>" width="<?php assign($user_profile['rate'] == BrandsUsersRelationService::BLOCK ? 18 : 24 )?>" height="<?php assign($user_profile['rate'] == BrandsUsersRelationService::BLOCK ? 18 : 24 )?>" alt="<?php assign($user_profile['rate'] == BrandsUsersRelationService::BLOCK ? 'block user' : 'rating '.$user_profile['rate'] )?>"><?php assign($rate_info['rate']) ?>
                <p class="ratingBox"><a class="ratingBlock" id="<?php assign($user_profile['brand_user_relation_id']) ?>">ブロック</a><span class="starRating" id="<?php assign($user_profile['brand_user_relation_id']) ?>" data-score="<?php assign($user_profile['rate'])?>"></span></p>
            </td>

            <td><span title="<?php assign($user_profile['history'])?>"><?php assign(date("Y/m/d", strtotime($user_profile['history_by_datetime']))) ?></span></td>

            <td>
                <?php
                if(!empty($user_profile['sa1_id'])) {
                    if(!empty($user_profile['sa1_profile_page_url'])) {
                        write_html('<a href="'.assign_str($user_profile['sa1_profile_page_url']).'" target="_blank" class="iconFB2">Facebook</a>'.assign_str($user_profile['sa1_friend_count']));
                    } else {
                        write_html('<span class="iconFB2">Facebook</span>'.assign_str($user_profile['sa1_friend_count']));
                    }
                }
                ?>
            </td>
            <?php if($data['page_settings']->privacy_required_sex): ?>
                <td>
                    <?php if($user_profile['sex'] == 'm'):?>
                    <span class = 'iconSexM'>
                        <?php elseif($user_profile['sex'] == 'f'): ?>
                        <span class = 'iconSexF'>
                        <?php else: ?>
                            <span class = 'iconSexN'>
                        <?php endif; ?>
                </td>
            <?php endif; ?>
            <?php if($data['page_settings']->privacy_required_address): ?>
                <td><?php assign($user_profile['pref_name']) ?></td>
            <?php endif; ?>
            <?php if($data['page_settings']->privacy_required_birthday): ?>
                <td><?php assign($user_profile['age']) ?></td>
            <?php endif; ?>
            <?php if($data['use_profile_questions']): ?>
                <td><?php assign($user_profile['profile_questionnaire_status']) ?></td>
            <?php endif; ?>

            <?php foreach($data['conversions'] as $conversion): ?>
                <td><?php assign($user_profile['conversion'.$conversion->id]);?></td>
            <?php endforeach; ?>

            <?php foreach ($data['definitions'] as $def): ?>
                <td><?php
                    $value = $data['user_attributes'][$def->id][$fan_list_user->user_id];
                    assign($value);
                    ?></td>
            <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        <?php
        $td_count = 1 + $privacy_required_count + ($data['use_profile_questions'] ? count($data['use_profile_questions']) + 1 : 0 ) + ($data['conversions'] ? $data['conversions']->total() : 0) + ($data['definitions'] ? $data['definitions']->total() : 0) + ($data['duplicateAddressShowType'] == CpCreateSqlService::SHIPPING_ADDRESS_DUPLICATE || ($data['duplicateAddressShowType'] == CpCreateSqlService::SHIPPING_ADDRESS_USER_DUPLICATE && $data['isShowDuplicateAddressCpUserList']) ? 1 : 0);
        ?>
        <?php for($i = 1; $i <= CpCreateSqlService::DISPLAY_20_ITEMS-count($data['fan_list_users']); $i++): ?>
            <tr>
                <?php for($td = 1; $td <= $td_count; $td++): ?>
                    <td></td>
                <?php endfor; ?>
            </tr>
        <?php endfor; ?>
        </tbody>
        <!-- /.itemTable --></table>
    <!-- /.itemTableWrap --></div>

