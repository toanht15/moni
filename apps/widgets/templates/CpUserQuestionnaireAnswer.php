    <div class="itemTableWrap">
        <table class="itemTable">
            <thead>
                <tr>
                    <th rowspan="2" class="jsAreaToggleWrap">
                        評価<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_RATE] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchMemberRate.php', array(
                            'search_rate' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_RATE]
                        ))) ?>
                    </th>
                    <?php if($data['questionnaires_questions_relations']): ?>
                        <th class="jsAreaToggleWrap " colspan="<?php assign($data['questionnaires_questions_relations']->total()) ?>">
                            <?php assign(Util::cutTextByWidth($data['questionnaire_action']->title, 750)); ?>
                        </th>
                    <?php else: ?>
                        <th class="jsAreaToggleWrap" colspan="1"></th>
                    <?php endif; ?>
                </tr>
                <tr>
                    <?php if($data['questionnaires_questions_relations']):?>
                        <?php foreach($data['questionnaires_questions_relations'] as $relation): ?>
                            <th class="jsAreaToggleWrap snsAccount" title="<?php assign('Q'.$relation->number.'.'.$data['questions'][$relation->id]->question); ?>">
                                <?php assign(Util::cutTextByWidth('Q'.$relation->number.'.'.$data['questions'][$relation->id]->question, 190)); ?>
                                <?php if(QuestionTypeService::isChoiceQuestion($data['questions'][$relation->id]->type_id)){
                                    $template_name = 'SearchChoiceQuestionnaire.php';
                                } else {
                                    $template_name = 'SearchFreeAnswerQuestionnaire.php';
                                }?>
                                <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_QUESTIONNAIRE.'/'.$relation->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                                <?php write_html($this->parseTemplate($template_name, array(
                                    'search_questionnaire'      => $data['search_condition'][CpCreateSqlService::SEARCH_QUESTIONNAIRE.'/'.$relation->id],
                                    'question_id'               => $data['questions'][$relation->id]->id,
                                    'relation_id'               => $relation->id,
                                    'search_questionnaire_type' => CpQuestionnaireService::TYPE_CP_QUESTION
                                ))) ?>
                            </th>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <th class="jsAreaToggleWrap"></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if($data['questionnaires_questions_relations']): ?>
                    <?php foreach($data['fan_list_users'] as $fan_list_user): ?>
                        <?php if($data['reservation']->id || $data['action']->id): ?>
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
                        <td class="userRating">
                            <?php  $rate_info = $data['brand_user_relation_service']->getMemberRate($fan_list_user->rate);  ?>
                            <img src="<?php assign($this->setVersion($rate_info['image_url'])) ?>" width="<?php assign($fan_list_user->rate == BrandsUsersRelationService::BLOCK ? 18 : 24 )?>" height="<?php assign($fan_list_user->rate == BrandsUsersRelationService::BLOCK ? 18 : 24 )?>" alt="<?php assign($fan_list_user->rate == BrandsUsersRelationService::BLOCK ? 'block user' : 'rating '.$fan_list_user->rate )?>"><?php assign($rate_info['rate']) ?>
                            <p class="ratingBox"><a class="ratingBlock" id="<?php assign($fan_list_user->brands_users_relations_id) ?>">ブロック</a><span class="starRating" id="<?php assign($fan_list_user->brands_users_relations_id) ?>" data-score="<?php assign($fan_list_user->rate)?>"></span></p>
                        </td>
                        <?php foreach($data['questionnaires_questions_relations'] as $relation): ?>
                            <?php if(isset($data['fan_list_question'][$fan_list_user->user_id][$data['questions'][$relation->id]->id])):?>
                                <?php $answer = $data['fan_list_question'][$fan_list_user->user_id][$data['questions'][$relation->id]->id] ?>
                                <td title='<?php assign($answer) ?>'>
                                    <?php assign(Util::cutTextByWidth($answer, 190)) ?>
                                </td>
                            <?php else: ?>
                                <td></td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    <?php for($i = 1; $i <= CpCreateSqlService::DISPLAY_20_ITEMS-count($data['fan_list_users']); $i++): ?>
                        <tr>
                            <td></td>
                            <?php if($data['questionnaires_questions_relations']): ?>
                                <?php for($td = 1; $td <= $data['questionnaires_questions_relations']->total(); $td++): ?>
                                    <td></td>
                                <?php endfor; ?>
                            <?php endif; ?>
                        </tr>
                    <?php endfor; ?>
                <?php else: ?>
                    <?php foreach($data['fan_list_users'] as $fan_list_user): ?>
                        <?php if($data['action']->id): ?>
                            <?php if($fan_list_user->mes_id):?>
                                <tr class="sendUser">
                            <?php elseif($fan_list_user->tar_id): ?>
                                <tr class="checkedUser">
                            <?php else: ?>
                                <tr>
                            <?php endif; ?>
                        <?php else: ?>
                            <tr>
                        <?php endif; ?>
                            <td class="userRating">
                                <?php  $rate_info = $data['brand_user_relation_service']->getMemberRate($fan_list_user->rate);  ?>
                                <img src="<?php assign($this->setVersion($rate_info['image_url'])) ?>" width="<?php assign($fan_list_user->rate == BrandsUsersRelationService::BLOCK ? 18 : 24 )?>" height="<?php assign($fan_list_user->rate == BrandsUsersRelationService::BLOCK ? 18 : 24 )?>" alt="<?php assign($fan_list_user->rate == BrandsUsersRelationService::BLOCK ? 'block user' : 'rating '.$fan_list_user->rate )?>"><?php assign($rate_info['rate']) ?>
                                <p class="ratingBox"><a class="ratingBlock" id="<?php assign($fan_list_user->brands_users_relations_id) ?>">ブロック</a><span class="starRating" id="<?php assign($fan_list_user->brands_users_relations_id) ?>" data-score="<?php assign($fan_list_user->rate)?>"></span></p>
                            </td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php for($i = 1; $i <= CpCreateSqlService::DISPLAY_20_ITEMS-count($data['fan_list_users']); $i++): ?>
                        <tr><td></td><td></td></tr>
                    <?php endfor; ?>
                <?php endif; ?>
            </tbody>
        <!-- /.itemTable --></table>
    <!-- /.itemTableWrap --></div>
