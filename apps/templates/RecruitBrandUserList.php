<section class="campaignEditCont">
    <section class="userListWrap">
        <section class="userList">
            <div class="pager1">
                <p><strong>件数計算中...</strong></p>
            </div>

            <?php
            $service_factory = new aafwServiceFactory();
            /** @var SocialLikeService $social_like_service */
            $social_like_service = $service_factory->create('SocialLikeService');
            ?>

            <?php $this->search_no = 1; ?>
            <?php
                /** @var CpQuestionnaireService $profile_questionnaire_service */
                $profile_questionnaire_service = $this->getService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
                $profile_question_relations = $profile_questionnaire_service->getPublicProfileQuestionRelationByBrandId($data['brand']->id);
                $data['profile_questions'] = array();
                $data['use_profile_questions'] = $profile_questionnaire_service->useProfileQuestion($profile_question_relations);
                foreach($data['use_profile_questions'] as $relation) {
                    $data['profile_questions'][$relation->id] = $profile_questionnaire_service->getQuestionById($relation->question_id);
                }
                foreach($data['fan_list_users'] as $fan_list_user) {
                    $user_ids[] = $fan_list_user->user_id;
                }
                /** @var CpUserListService $cp_user_list_service */
                $cp_user_list_service = $this->getService('CpUserListService');
                $data['user_profile'] = $cp_user_list_service->getFanListProfile($user_ids, $data['brand']->id, $data['profile_questions'], null, null, null);
            ?>

            <div class="userListCont">
                <ul class="userName">
                    <li class="allUser"><label></label></li>
                    <?php foreach($data['fan_list_users'] as $fan_list_user): ?>
                        <?php $user_profile = $data['user_profile'][$fan_list_user->user_id]; ?>
                        <?php if(isset($user_profile['question_'.$data['profile_questions'][598]->id])): ?>
                            <?php $answer = $user_profile['question_'.$data['profile_questions'][598]->id]?>
                            <li>
                                <label title="<?php assign($answer) ?>"><?php assign(Util::cutTextByWidth($answer, 102)) ?></label>
                            </li>
                        <?php else: ?>
                            <li></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php for($i = 1; $i <= CpCreateSqlService::DISPLAY_20_ITEMS-count($data['fan_list_users']); $i++): ?>
                        <li></li>
                    <?php endfor; ?>
                </ul>

                <form id="frmSearchFan" name="frmSearchFan">
                    <?php write_html($this->formHidden('page_info', $data['list_page']['page_no'].'/'.$data['list_page']['tab_no']))?>
                    <?php if($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_PROFILE): ?>
                        <?php write_html(aafwWidgets::getInstance()->loadWidget('RecruitCpUserProfile')->render(array(
                            'brand'              => $data['brand'],
                            'fan_list_users'     => $data['fan_list_users'],
                            'search_condition'   => $data['search_condition'],
                            'fan_limit'          => $data['list_page']['limit'],
                            'search_no'          => $this->search_no,
                            'duplicateAddressShowType' => CpCreateSqlService::SHIPPING_ADDRESS_DUPLICATE,
                            'list_page'          => $data['list_page'],
                        ))) ?>
                    <?php endif; ?>
                    <?php write_html($this->csrf_tag()); ?>
                </form>
            <!-- /.userListCont --></div>

            <div class="pager1">
                <p><strong>件数計算中...</strong></p>
            </div>

            <?php write_html($this->parseTemplate('UserListItemCount.php', array(
                'limit'         => $data['list_page']['limit'],
            ))) ?>
            <!-- /.userList --></section>
        <!-- /.userListWrap --></section>
    <!-- /.campaignEditCont --></section>
