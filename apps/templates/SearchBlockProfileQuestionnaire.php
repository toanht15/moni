<?php
$service_factory = new aafwServiceFactory();
/** @var CpQuestionnaireService $profile_questionnaire_service */
$profile_questionnaire_service = $service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
$profile_question_relations = $profile_questionnaire_service->getPublicProfileQuestionRelationByBrandId($data['brand_id']);
$use_profile_questions = $profile_questionnaire_service->useProfileQuestion($profile_question_relations);
?>
<?php if($use_profile_questions): ?>
<div class="customaudienceRefinement jsModuleContWrap">
    <div class="categoryLabel jsModuleContTile close">
        <p>カスタムプロフィール</p>
        <!-- /.categoryLabel --></div>
    <div class="refinementWrap jsModuleContTarget">
        <div class="otherItem">
            <div class="jsSearchInputBlock">
                <p class="settingLabel">アンケート回答</p>
                <ul>
                    <form>
                        <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS)) ?>
                        <?php $key = 'search_questionnaire_status' ?>
                        <li><label><input type="checkbox" name='<?php assign($key.'/'.BrandsUsersRelation::SIGNUP_WITHOUT_INFO.'/'.$data["search_no"])?>' <?php assign($data[$key][$key.'/'.BrandsUsersRelation::SIGNUP_WITHOUT_INFO] ? 'checked' : '')?>>未取得</label></li>
                        <li><label><input type="checkbox" name='<?php assign($key.'/'.BrandsUsersRelation::SIGNUP_WITH_INFO.'/'.$data["search_no"])?>' <?php assign($data[$key][$key.'/'.BrandsUsersRelation::SIGNUP_WITH_INFO] ? 'checked' : '')?>>取得済み</label></li>
                        <li><label><input type="checkbox" name='<?php assign($key.'/'.BrandsUsersRelation::FORCE_WITH_INFO.'/'.$data["search_no"])?>' <?php assign($data[$key][$key.'/'.BrandsUsersRelation::FORCE_WITH_INFO] ? 'checked' : '')?>>要再取得</label></li>
                    </form>
                </ul>
            </div>
            <!-- /.otherItem --></div>
        <div class="setting">
            <?php $data['search_questionnaire_type'] = CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE; ?>

            <?php foreach ($use_profile_questions as $profile_relation):?>
                <?php $data['relation_id'] = $profile_relation->id ?>
                <?php write_html($this->parseTemplate("SearchBlockQuestionnaireQuestion.php", $data)) ?>
            <?php endforeach; ?>
            <!-- /.setting --></div>
        <!-- /.refinementWrap --></div>
    <!-- /.customaudiencRefinement --></div>
<?php endif; ?>