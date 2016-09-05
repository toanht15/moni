<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'キャンペーン一覧',
    'managerAccount' => $this->managerAccount,
))) ?>


    <div class="container-fluid">
        <div class="row">
            <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

            <div class="col-md-10 col-md-offset-2 main">
                <h1 class="sub-header">キャンペーン一覧</h1>
                <form id="csv_campaign_list" action="<?php assign(Util::rewriteUrl('dashboard', 'csv_campaign_list', array(), array(), '', true)); ?>" method="GET">
                    <?php foreach($data['status'] as $status): ?>
                        <?php write_html($this->formHidden('status[]', $status)); ?>
                    <?php endforeach; ?>
                    <?php foreach($data['show'] as $show): ?>
                        <?php write_html($this->formHidden('show[]', $show)); ?>
                    <?php endforeach; ?>
                    <?php foreach($data['module'] as $module): ?>
                        <?php write_html($this->formHidden('module[]', $module)); ?>
                    <?php endforeach; ?>
                    <?php foreach($data['range_type'] as $range_type): ?>
                        <?php write_html($this->formHidden('range_type[]', $range_type)); ?>
                    <?php endforeach; ?>
                    <?php write_html($this->formHidden('cp_type', $data['cp_type'])); ?>
                    <?php write_html($this->formHidden('order', $data['order'])); ?>
                    <?php write_html($this->formHidden('brands', $data['brands'])); ?>
                    <?php write_html($this->formHidden('brand_test', $data['brand_test'])); ?>
                    <?php write_html($this->formHidden('cp_status', $this->params['cp_status'])); ?>
                    <?php write_html($this->formHidden('from_date', $this->params['from_date'])); ?>
                    <?php write_html($this->formHidden('to_date', $this->params['to_date'])); ?>
                    <?php write_html($this->formHidden('brand_name', $this->params['brand_name'])); ?>
                    <?php write_html($this->formHidden('cp_id', $this->params['cp_id'])); ?>
                    <?php write_html($this->formHidden('winner_count_from', $this->params['winner_count_from'])); ?>
                    <?php write_html($this->formHidden('winner_count_to', $this->params['winner_count_to'])); ?>
                    <?php write_html($this->formRadio(
                        'step_info', '0', array(),
                        $this->download)); ?>
                    <p><small>※キャンペーン、STEPごとに出力する情報を選択できます</small></p>
                    <div style="text-align:left">
                        <b><a href="javascript:void(0);" id="btn_csv_campaign_list" class="btn btn-primary btn-large">CSVダウンロード</a>&nbsp;</b>
                    </div>
                </form>
                <h4><span class="edit"><a href="#" class="jsMessageSetting">詳細検索条件</a></span></h4>
                <form name="brand_list" action="<?php assign(Util::rewriteUrl('dashboard', 'campaigns', array(), array(), '', true)); ?>" method="GET" class="form-horizontal row-border">
                <div class="jsMessageSettingTarget" style="display:none">
                    <div class="container">
                        <!-- Brand and toggle get grouped for better mobile display -->
                        <div class="container">
                            <!-- Row start -->
                            <div class="row">
                                <div class="col-md-11 col-sm-6 col-xs-12">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label class="col-md-2 control-label">CP ID</label>
                                                <div class="col-md-10">
                                                    <label class="checkbox-inline">
                                                        <?php write_html( $this->formText(
                                                            'cp_id',
                                                            PHPParser::ACTION_FORM,
                                                            array('maxlength'=>'255', 'placeholder'=> 'CP ID')
                                                        )); ?>
                                                    </label>
                                                </div>
                                                <label class="col-md-2 control-label">Brand ID</label>
                                                <div class="col-md-10">
                                                    <label class="checkbox-inline">
                                                        <?php write_html($this->formSelect( 'brands', PHPParser::ACTION_FORM, array(),$this->brand_name)); ?>
                                                        <?php write_html( $this->formText(
                                                            'brand_name',
                                                            PHPParser::ACTION_FORM,
                                                            array('maxlength'=>'255', 'placeholder'=> 'Brand Name')
                                                        )); ?>
                                                    </label>
                                                </div>
                                                <label class="col-md-2 control-label">ステータス</label>
                                                <div class="col-md-10">
                                                    <?php foreach($this->select_status_data as $key => $value):?>
                                                        <label class="checkbox-inline">
                                                            <?php write_html($this->formCheckBox( 'checkAllStatus', PHPParser::ACTION_FORM,
                                                                array('checked' => in_array($key, $data['checkAllStatus']) ? 'checked' : ''), array($key => $value))); ?>
                                                        </label>
                                                    <?php endforeach?>
                                                    <?php foreach($this->status_data as $key => $val):?>
                                                        <label class="checkbox-inline">
                                                            <?php write_html($this->formCheckBox( 'status[]', PHPParser::ACTION_FORM,
                                                                array('checked' => in_array($key, $data['status']) ? 'checked' : ''), array($key => $val))); ?>
                                                        </label>
                                                    <?php endforeach?>
                                                </div>
                                                <label class="col-md-2 control-label">公開範囲</label>
                                                <div class="col-md-10">
                                                    <?php foreach($this->range as $key => $val):?>
                                                        <label class="checkbox-inline">
                                                            <?php write_html($this->formCheckBox( 'range_type[]', PHPParser::ACTION_FORM,
                                                                array('checked' => in_array($key, $data['range_type']) ? 'checked' : ''), array($key => $val))); ?>
                                                        </label>
                                                    <?php endforeach?>
                                                </div>
                                                <label class="col-md-2 control-label">アカウント</label>
                                                <div class="col-md-10">
                                                    <?php foreach($this->brand_test_data as $key=> $val):?>
                                                        <label class="radio-inline">
                                                            <?php write_html($this->formRadio(
                                                                'brand_test',
                                                                PHPParser::ACTION_FORM,
                                                                array('checked' => $data['brand_test'] == $key ? 'checked' : ''),
                                                                array($key=> $val))); ?>
                                                        </label>
                                                    <?php endforeach?>
                                                </div>
                                                <label class="col-md-2 control-label">表示</label>
                                                <div class="col-md-10">
                                                    <?php foreach($this->select_status_data as $key => $value):?>
                                                        <label class="checkbox-inline">
                                                            <?php write_html($this->formCheckBox( 'checkAllShow', PHPParser::ACTION_FORM,
                                                                array('checked' => in_array($key, $data['checkAllShow']) ? 'checked' : ''), array($key => $value))); ?>
                                                        </label>
                                                    <?php endforeach?>
                                                    <?php foreach($this->search_contents as $key=>$value):?>
                                                        <label class="checkbox-inline">
                                                            <?php write_html($this->formCheckBox( 'show[]', PHPParser::ACTION_FORM,
                                                                array('checked' => in_array($key, $data['show']) ? 'checked' : '') ,array($key=>$value))); ?>
                                                        </label>
                                                    <?php endforeach?>
                                                </div>
                                                <label class="col-md-2 control-label">種別</label>
                                                <div class="col-md-10">
                                                    <?php foreach($this->type as $key => $val):?>
                                                        <label class="radio-inline">
                                                            <?php write_html($this->formRadio(
                                                                'cp_type',
                                                                PHPParser::ACTION_FORM,
                                                                array('checked' => $data['cp_type'] == $key ? 'checked' : ''),
                                                                array($key => $val))); ?>
                                                        </label>
                                                    <?php endforeach?>
                                                </div>
                                                <label class="col-md-2 control-label">並び順</label>
                                                <div class="col-md-10">
                                                    <?php foreach($this->arrangment as $key => $val):?>
                                                        <label class="radio-inline">
                                                            <?php write_html($this->formRadio( 'order', PHPParser::ACTION_FORM,
                                                                array('checked' => $data['order'] == $key ? 'checked' : ''), array($key => $val))); ?>
                                                        </label>
                                                    <?php endforeach ?>
                                                </div>
                                                <div class="col-md-10">
                                                    <label class="col-md-2 control-label">モジュール種類</label>
                                                    <div class="col-md-10">
                                                        <?php foreach($this->select_status_data as $key => $value):?>
                                                            <label class="checkbox-inline">
                                                                <?php write_html($this->formCheckBox( 'checkAllModule', PHPParser::ACTION_FORM,
                                                                    array('checked' => in_array($key, $data['checkAllModule']) ? 'checked' : ''), array($key => $value))); ?>
                                                            </label>
                                                        <?php endforeach?>
                                                        <?php foreach($this->modules as $value => $module):?>
                                                            <label class="checkbox-inline">
                                                                <?php write_html($this->formCheckBox( 'module[]', PHPParser::ACTION_FORM,
                                                                    array('checked' => in_array($value, $data['module']) ? 'checked' : '') ,array( $value => $module))); ?>
                                                            </label>
                                                        <?php endforeach?>
                                                    </div>
                                                </div>
                                                <div class="col-md-10">
                                                    <label class="col-md-2 control-label">参加期間</label>
                                                    <label>
                                                        <?php write_html($this->formSelect( 'cp_status', PHPParser::ACTION_FORM, array(),$this->cp_status)); ?>
                                                        <?php write_html($this->formText(
                                                            'from_date',
                                                            PHPParser::ACTION_FORM,
                                                            array('maxlength' => '20', 'class' => 'jsDate inputDate', 'placeholder' => 'YY-MM-DD'))); ?>
                                                        ～
                                                        <?php write_html($this->formText(
                                                            'to_date',
                                                            PHPParser::ACTION_FORM,
                                                            array('maxlength' => '20', 'class' => 'jsDate inputDate', 'placeholder' => 'YY-MM-DD'))); ?>
                                                    </label>
                                                </div>
                                                <div class="col-md-11">
                                                    <label class="col-md-2 control-label">当選人数</label>
                                                    <label>
                                                        <?php write_html( $this->formText(
                                                            'winner_count_from',
                                                            PHPParser::ACTION_FORM,
                                                            array('maxlength'=>'10')
                                                        )); ?>
                                                        ～
                                                        <?php write_html( $this->formText(
                                                            'winner_count_to',
                                                            PHPParser::ACTION_FORM,
                                                            array( 'maxlength'=>'10')
                                                        )); ?>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-2 col-md-offset-4">
                    <a href="javascript:void(0);" onclick="document.brand_list.submit();return false;" class="btn btn-primary btn-lg btn-block">検索</a>
                </form>
                </div>
                <div class="col-md-7">
                <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoManagerPager')->render(array(
                    'TotalCount' => $data['allCpsCount'],
                    'CurrentPage' => $this->params['p'],
                    'Count' => $data['limit'],
                ))) ?>
                </div>
                <div class="table-responsive" style="white-space:nowrap">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <?php foreach($this->search_titles as $key => $val):?>
                            <?php if (in_array($key, $data['show'])&& $key <= campaigns::SEARCH_JOIN):?>
                                <th><?php assign($val);?></th>
                            <?php elseif(in_array($key, $data['show']) && $key == campaigns::SEARCH_STEP):?>
                                <?php foreach($val as $step):?>
                                <th><?php assign($step);?></th>
                                <?php endforeach?>
                                <?php endif;?>
                            <?php endforeach ?>
                        </tr>
                        </thead>
                        <tbody>

                        <?php foreach($this->cps as $cp):?>
                            <tr>
                                <?php if (in_array(campaigns::SEARCH_ID, $data['show'])):?>
                                    <td><?php assign($cp['id']);?></td>
                                <?php endif;?>
                                <?php if (in_array(campaigns::SEARCH_STATUS, $data['show'])):?>
                                    <td><?php assign($cp['campaign_status']);?></td>
                                <?php endif;?>
                                <?php if (in_array(campaigns::SEARCH_OPEN_RANGE, $data['show'])):?>
                                    <td><?php assign($cp['open_range']);?></td>
                                <?php endif;?>
                                <?php if (in_array(campaigns::SEARCH_TITLE, $data['show'])):?>
                                <?php if ($cp['type'] == Cp::TYPE_CAMPAIGN):?>
                                    <?php if ($cp['status'] <= Cp::CAMPAIGN_STATUS_SCHEDULE):?>
                                        <td><?php assign($cp['title']);?></td>
                                    <?php elseif($cp['status'] == Cp::STATUS_DEMO):?>
                                        <td><a href="<?php assign(Util::constructBaseURL($cp['brand_id'], $cp['directory_name'], true) .'campaigns'.'/'.$cp['id'].'?demo_token='.hash("sha256",  $cp['created_at']))?>" target="_blank"><?php assign($cp['title'])?></a></td>
                                    <?php else:?>
                                        <td><a href="<?php assign(Util::constructBaseURL($cp['brand_id'], $cp['directory_name'], true) .'campaigns'.'/'.$cp['id'])?>" target="_blank"><?php assign($cp['title'])?></a></td>
                                    <?php endif;?>
                                <?php else:?>
                                        <td><?php assign($cp['title']);?></td>
                                <?php endif;?>
                                <?php endif;?>
                                <?php if (in_array(campaigns::SEARCH_BRAND, $data['show'])):?>
                                    <td><a href="<?php assign(Util::constructBaseURL($cp['brand_id'], $cp['directory_name'], true))?>" target="_blank"><?php assign($cp['name'])?></a></td>
                                <?php endif;?>
                                <?php if($cp['public_date'] == "0000-00-00 00:00:00"):?>
                                    <?php if (in_array(campaigns::SEARCH_PUBLIC_DATE, $data['show'])):?>
                                        <td><?php assign(" - ");?></td>
                                    <?php endif; ?>
                                    <?php if (in_array(campaigns::SEARCH_OPENING_DATE, $data['show'])):?>
                                        <td><?php assign(" - ");?></td>
                                    <?php endif; ?>
                                    <?php if (in_array(campaigns::SEARCH_CLOSING_DATE, $data['show'])):?>
                                        <td><?php assign(" - ");?></td>
                                    <?php endif; ?>
                                    <?php if (in_array(campaigns::SEARCH_ANNOUNCE_DATE, $data['show'])):?>
                                        <td><?php assign(" - ");?></td>
                                    <?php endif; ?>
                                <?php else :?>
                                    <?php if (in_array(campaigns::SEARCH_PUBLIC_DATE, $data['show'])):?>
                                        <td><?php assign($cp['public_date']);?></td>
                                    <?php endif; ?>
                                    <?php if (in_array(campaigns::SEARCH_OPENING_DATE, $data['show'])):?>
                                        <td><?php assign($cp['start_date']);?></td>
                                    <?php endif; ?>
                                    <?php if (in_array(campaigns::SEARCH_CLOSING_DATE, $data['show'])):?>
                                        <td><?php assign($cp['end_date']);?></td>
                                    <?php endif; ?>
                                    <?php if (in_array(campaigns::SEARCH_ANNOUNCE_DATE, $data['show'])):?>
                                        <td><?php assign($cp['announce_date']);?></td>
                                    <?php endif; ?>
                                <?php endif;?>
                                <?php if (in_array(campaigns::SEARCH_COM, $data['show'])):?>
                                    <td><?php assign($cp['show_monipla_com_flg'] == 1 ? "掲載" : " - ");?></td>
                                <?php endif; ?>
                                <?php if (in_array(campaigns::SEARCH_PARTICIPANT, $data['show'])):?>
                                    <td><?php assign($cp['show_winner_label'] ? $cp['winner_label'] : $cp['winner_count']);?></td>
                                <?php endif; ?>
                                <?php if (in_array(campaigns::SEARCH_JOIN, $data['show'])):?>
                                    <td><?php assign($cp['join_count']);?></td>
                                <?php endif; ?>
                                <?php if (in_array(campaigns::SEARCH_STEP, $data['show'])):?>
                                    <?php $step = 0;?>
                                    <?php foreach($cp['actions'] as $action):?>
                                        <?php
                                        if ($step++ >= 10) break;
                                        $cp_action_detail = $action->getCpActionDetail()['title'];
                                        $cp_action_data = $action->getCpActionData(); ?>
                                        <?php if($action->type == CpAction::TYPE_ENGAGEMENT || $action->type == CpAction::TYPE_FACEBOOK_LIKE): ?>
                                            <?php $engagement_action_detail = $this->getAction()->getEngagementLogStatusCount($action->id); ?>
                                            <td class="jsEngagementLogHover">
                                                <a href="javascript:;" style="text-decoration: none"><?php assign($cp_action_detail); ?></a>
                                                <div class='jsEngagementLogData' style='display: none; background-color: #d8d8d8; padding: 5px;'>
                                                       FB未連携：<?php write_html(number_format($engagement_action_detail['unread'])); ?><br />
                                                    新規いいね！：<?php write_html(number_format($engagement_action_detail['liked'])); ?><br />
                                                    既存いいね！：<?php write_html(number_format($engagement_action_detail['prev_liked'])); ?><br />
                                                       スキップ：<?php write_html(number_format($engagement_action_detail['skip_like'])); ?><br />
                                                     いいね！率：<?php write_html(number_format($like_rate, 2)) ?>%
                                                </div>
                                            </td>
                                        <?php elseif($cp_action_data->text): ?>
                                            <td class="jsEngagementLogHover">
                                                <a href="javascript:;" style="text-decoration: none"><?php assign($cp_action_detail); ?></a>
                                                <div class='jsEngagementLogData' style='display: none; background-color: #d8d8d8; padding: 5px;'>
                                                    <?php write_html($this->toHalfContentDeeply($cp_action_data->text)); ?>
                                                </div>
                                            </td>
                                        <?php else: ?>
                                            <td><?php assign($cp_action_detail) ?></td>
                                        <?php endif; ?>
                                    <?php endforeach;?>
                                    <?php if ((10-count($cp['actions'])) > 0): ?>
                                        <?php for($i=0; $i<(10-count($cp['actions'])); $i++): ?>
                                            <td>-</td>
                                        <?php endfor; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>

                    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoManagerPager')->render(array(
                        'TotalCount' => $data['allCpsCount'],
                        'CurrentPage' => $this->params['p'],
                        'Count' => $data['limit'],
                    ))) ?>
                </div>
            </div>
        </div><!-- row -->
    </div><!-- container-fluid -->

<script src="<?php assign($this->setVersion('/manager/js/services/CampaignSelectAllService.js'))?>"></script>
<script src="<?php assign($this->setVersion('/manager/js/services/CampaignListService.js'))?>"></script>
<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>