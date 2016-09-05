<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<article>
    <?php write_html($this->formHidden('static_url', config('Static.Url'))) ?>
    <?php write_html($this->formHidden('all_fan_count', $data['all_fan_count'])) ?>
    <?php write_html($this->formHidden('get_dashboard_info_url', Util::rewriteUrl('admin-dashboard', 'api_get_dashboard_info.json'))) ?>
    <?php write_html($this->formHidden('date_error', $data['error']['date'])) ?>
    <?php write_html($this->formHidden('min_date', $data['min_date'])) ?>
    <?php write_html($this->formHidden('max_date', $data['max_date'])) ?>
    <h1 class="hd1">Dashboard</h1>
    <div  class="dashboardFliter">
        <?php if(!$data['error']['date']):?>
            <?php write_html($this->formHidden('title_date', $data['title_date'])) ?>
            <h2><?php assign($data['title_date_text'])?></h2>
        <?php endif; ?>
        <div class="filter">
            <ul class="type">
                <li><label><input type="radio" name="date_type" value="<?php assign(DashboardService::DATE_SUMMARY)?>" <?php assign((!$data['date_type'] || $data['date_type'] == DashboardService::DATE_SUMMARY) ? 'checked' : '')?>>累計</label></li
                ><li><label><input type="radio" name="date_type" value="<?php assign(DashboardService::DATE_TERM)?>" <?php assign($data['date_type'] == DashboardService::DATE_TERM ? 'checked' : '')?>>期間</label></li>
            <!-- /.type --></ul
            ><p class="term jsSummaryDate" style="<?php assign($data['summary_date_li_style']);?>">
                <?php write_html($this->formSelect(
                    'selectSummaryDate',
                    $data['summary_date_type'] ? $data['summary_date_type'] : PHPParser::ACTION_FORM,
                    array('id' => 'selectSummaryDate'),
                    $data['summary_options']
                ));?><span style="<?php assign($data['summary_date_span_style']);?>"><?php write_html($this->formText(
                    'summary_date',
                    PHPParser::ACTION_FORM,
                    array('class'=>'jsSummaryDate inputDate', 'placeholder'=> '年/月/日')
                )); ?></span><!-- /.term --></p
            ><p class="term jsTermDate" style="<?php assign($data['term_date_li_style']);?>">
                <?php write_html($this->formSelect(
                    'selectTermDate',
                    $data['term_date_type'] ? $data['term_date_type'] : PHPParser::ACTION_FORM,
                    array('id' => 'selectTermDate'),
                    $data['term_options']
                ));?><span style="<?php assign($data['term_date_span_style'])?>"><?php write_html($this->formText(
                    'from_date',
                    PHPParser::ACTION_FORM,
                    array('class'=>'jsTermDate inputDate', 'placeholder'=> '年/月/日')
                )); ?><span class="dash">〜</span><?php write_html($this->formText(
                    'to_date',
                    PHPParser::ACTION_FORM,
                    array('class'=>'jsTermDate inputDate', 'placeholder'=> '年/月/日')
                )); ?></span><!-- /.term --></p
            <?php if($data['error']['date']): ?>
                ><p class="iconError1"><?php assign($data['error']['date']) ?></p
            <?php endif; ?>
            ><p class="btn3"><a href="javascript:void(0)" class="small1 jsConditionApply" data-redirect_url="<?php assign(Util::rewriteUrl('admin-dashboard', 'dashboard_list'))?>">適用</a></p>
        <!-- /.filter --></div>
    <!-- /.dashboardFliter --></div>

    <div class="dashboardGrafWrap jsMasonry">
        <section class="dashboardGraf_fan jsPanel">
            <h1>ファン数<?php assign($data['date_type'] == DashboardService::DATE_SUMMARY ? '(累計)' : '(期間増分)') ?></h1>
            <div class="grafData jsDisplaySubject" data-dashboard_type="<?php assign(DashboardService::DATE_BRAND_FAN_COUNT) ?>">
                <p class="loading"><img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" width="50" height="50" display="block" margin="0 auto"></p>
            </div>
        </section>

        <section class="dashboardGraf_account jsPanel">
            <h1 class="title">連携済アカウント</h1>
            <div class="grafData jsDisplaySubject" data-dashboard_type="<?php assign(DashboardService::SNS_FAN_COUNT) ?>">
                <p class="loading"><img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" width="50" height="50" display="block" margin="0 auto"></p>
            </div>
        </section>

        <?php if($data['page_settings']->privacy_required_sex): ?>
            <section class="dashboardGraf_sex jsPanel">
                <h1 class="title">性別</h1>
                <div class="grafData jsDisplaySubject" data-dashboard_type="<?php assign(DashboardService::SEX_FAN_COUNT) ?>">
                    <p class="loading"><img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" width="50" height="50" display="block" margin="0 auto"></p>
                </div>
            </section>
        <?php endif; ?>

        <?php if($data['page_settings']->privacy_required_address): ?>
            <section class="dashboardGraf_address jsPanel">
                <h1 class="title">都道府県</h1>
                <div class="grafData jsDisplaySubject" data-dashboard_type="<?php assign(DashboardService::AREA_FAN_COUNT) ?>">
                    <p class="loading"><img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" width="50" height="50" display="block" margin="0 auto"></p>
                </div>
                <p class="moreData"><a style="display:none" href="#prefecture_modal" class="jsOpenModal">もっと見る</a></p>
            </section>
        <?php endif; ?>

        <?php if($data['page_settings']->privacy_required_birthday): ?>
            <section class="dashboardGraf_age jsPanel">
                <h1 class="title">年齢</h1>
                <div class="grafData jsDisplaySubject" data-dashboard_type="<?php assign(DashboardService::AGE_FAN_COUNT) ?>">
                    <p class="loading"><img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" width="50" height="50" display="block" margin="0 auto"></p>
                </div>
            </section>
        <?php endif; ?>
    <!-- /.dashboardGrafWrap --></div>

    <div class="dashboardGrafWrap jsMasonry">
        <?php foreach($data['use_profile_question'] as $relation): ?>
            <?php $questionnaires_question = $this->action->getQuestionnaireQuestion($relation->question_id); ?>
            <?php if(QuestionTypeService::isChoiceQuestion($questionnaires_question->type_id)):?>
                <?php $question_requirement = $this->action->getQuestionRequirement($relation->question_id); ?>
                <section class="dashboardGraf_medium jsPanel">
                    <h1 class="title" title="<?php assign('Q'.$relation->number.'.'.$questionnaires_question->question) ?>"><?php assign(Util::cutTextByWidth('Q'.$relation->number.'.'.$questionnaires_question->question, 430)) ?></h1>
                    <div class="grafData jsDisplaySubject" data-dashboard_type="<?php assign(DashboardService::PROFILE_QUESTIONNAIRE_FAN_COUNT.'/'.$relation->id) ?>"
                         data-multi_answer="<?php assign($question_requirement->multi_answer_flg)?>">
                        <svg width="450" height="<?php assign($this->action->getQuestionHeight($question_requirement))?>"></svg>
                        <p class="loading"><img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" width="50" height="50" display="block" margin="0 auto"></p>
                    </div>
                </section>
            <?php endif; ?>
        <?php endforeach; ?>
    <!-- /.dashboardGrafWrap --></div>

    <div class="dashboardGrafWrap jsMasonry">
        <?php if($data['can_download_brand_user_list']): ?>
            <section class="dashboardGraf_pv jsPanel">
                <h1 class="title">PV</h1>
                <div class="grafData jsDisplaySubject" data-dashboard_type="<?php assign(DashboardService::BRAND_PV_COUNT) ?>">
                    <p class="loading"><img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" width="50" height="50" display="block" margin="0 auto"></p>
                </div>
            </section>
        <?php endif; ?>
    <!-- /.dashboardGrafWrap --></div>
</article>
<div class="modal1 jsModal" id="prefecture_modal">
    <section class="modalCont-large jsModalCont">
        <h1>都道府県</h1>
        <div data-modal="prefecture_modal"></div>
        <p>
            <a href="#closeModal" class="modalCloseBtn">キャンセル</a>
        </p>
    </section>
<!-- /.modal1 --></div>
<?php write_html($this->scriptTag('d3', false))?>
<?php $param = array_merge($data['pageStatus'], array('script' => array(
    'admin-dashboard/DashboardListService',
    'admin-dashboard/DrawLineChartService',
    'admin-dashboard/DrawPieChartService',
    'admin-dashboard/DrawBarChartService',
))) ?>
<link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css'))?>">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
