<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'CSV',
    'managerAccount' => $this->managerAccount,
))) ?>
<style>
    li {
        list-style-type: none;
        padding: 10px 3px;
    }
    li > ul > li:hover {background: #ececec;}
    .list {
        max-height: 250px;
        overflow: auto;
    }
    .list > li:hover {background: #ececec;}
</style>

<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

        <div class="col-md-10 col-md-offset-2 main">
            <ol class="breadcrumb">
                <li><a href="<?php assign(Util::rewriteUrl('dashboard', 'brand_list', array(), array(), '', true)); ?>">ブランド一覧</a></li>
                <li class="active">CSV</li>
            </ol>

            <h2>基本情報</h2>

            <ul>
                <li>
                    <a href="<?php assign(Util::rewriteUrl('dashboard', 'brand_kpi', array($this->brand->id), array(), '', true)); ?>">KPI</a>
                    <a href="<?php assign(Util::rewriteUrl('dashboard', 'csv_brand_kpi', array($this->brand->id), array(), '', true)); ?>"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></a>
                </li>
                <li>
                    <span>メッセージ開封率</span>
                    <a href="<?php assign(Util::rewriteUrl('dashboard', 'csv_brand_open_email_rate', array($this->brand->id), array(), '', true)); ?>"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></a>
                </li>
                <li>
                    <span>Tableau</span>
                    <ul>
                        <li>
                            <span>プロフィールアンケート態度変容</span>
                            <form method="POST" class="form-inline" action="<?php assign(Util::rewriteUrl('data_download', 'csv_attitude', array(), array(), '', true)); ?>">
                                <div class="form-group">
                                    <?php if ($this->ActionError && !$this->ActionError->isValid("attitudeChangeFrom")): ?>
                                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('attitudeChangeFrom') )?></p>
                                    <?php endif; ?>
                                    <label for="attitudeChangeFrom">FROM</label>
                                    <?php write_html($this->formText(
                                        'attitudeChangeFrom',
                                        PHPParser::ACTION_FORM,
                                        array("class" => "form-control jsDate inputDate", "id" => "attitudeChangeFrom", "placeholder" => "yyyy-mm-dd")
                                        ));?>
                                </div>
                                <div class="form-group">
                                    <?php if ($this->ActionError && !$this->ActionError->isValid("attitudeChangeTo")): ?>
                                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('attitudeChangeTo') )?></p>
                                    <?php endif; ?>
                                    <label for="attitudeChangeTo">TO</label>
                                    <?php write_html($this->formText(
                                        'attitudeChangeTo',
                                        PHPParser::ACTION_FORM,
                                        array("class" => "form-control jsDate inputDate", "id" => "attitudeChangeTo", "placeholder" => "yyyy-mm-dd")
                                        ));?>
                                </div>
                                <?php write_html($this->formHidden("brandId", $this->brandId));?>
                                <button type="submit" class="btn btn-primary">DL</button>
                            </form>
                            <small>FROMを指定しない場合はファンサイト開設初期を起点とし、TOを指定しない場合は現在日付までを終点としてデータを出力します。</small>
                        </li>
                    </ul>
                </li>
                <li>
                    デモグラ
                    <ul>
                        <li>性別分布<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></li>
                        <li>年齢分布（共通・男女）<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></li>
                        <li>連携ソーシャルアカウント<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></li>
                        <li>平均年齢（共通／男女）<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></li>
                    </ul>
                </li>
                <li>
                    プロフィールカラム
                    <ul>
                        <li>設問ごとの回答数<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></li>
                        <li>マルチアンサー<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></li>
                    </ul>
                </li>
            </ul>

            <hr>
            <p>以下の情報にアクセスするためには<a href="<?php assign($this->brand->getUrl())?>" target="_blank">ログイン</a>して利用してください。</p>
            <h2>キャンペーン</h2>

            <ul class="list">

<?php
$service_factory = new aafwServiceFactory();
/** @var CpFlowService $cp_flow_service */
$cp_flow_service = $service_factory->create('CpFlowService');
/** @var CpMessageDeliveryService $message_delivery_service */
$message_delivery_service = $service_factory->create('CpMessageDeliveryService');

foreach ($data['cps'] as $cp):
    if($cp->type != Cp::TYPE_CAMPAIGN) continue;

    switch ($cp->getStatus()) {
        case Cp::CAMPAIGN_STATUS_SCHEDULE: $label = "公開予約"; break;
        case Cp::CAMPAIGN_STATUS_OPEN: $label ="開催中" ; break;
        case Cp::CAMPAIGN_STATUS_WAIT_ANNOUNCE: $label = "当選発表待ち"; break;
        case Cp::CAMPAIGN_STATUS_CLOSE: $label = "終了"; break;
        default: $label="下書き"; break;
    }

    $cp_action_groups = $cp_flow_service->getCpActionGroupsByCpId($cp->id);
    $show_opened_email_csv = false;
    foreach ($cp_action_groups as $action_group) {
        $first_action = $cp_flow_service->getFirstActionInGroupByGroupId($action_group->id);
        if ($message_delivery_service->getDeliveredCpMessageDeliveryReservationByCpActionId($first_action->id)) {
            $show_opened_email_csv = true;
        }
        break;
    }
    ?>
                <li>
                    <span >
                    <span class="label label-info"><?php assign($label)?></span>&nbsp;<img src="<?php assign($cp->getIcon())?>" height="15" width="15" />&nbsp;<?php assign($cp->id)?>&nbsp;<a href="<?php assign($cp->getUrl())?>" target="_blank"><?php assign($cp->getTitle())?></a>
                    </span>

                    <div style="float: right;">
                        概要、
                        <a target="_brank" href="https://10ay.online.tableau.com/#/site/allied/views/_8/sheet0?id=<?php assign($cp->id) ?>">キャンペーンレポート(Tableau)</a>、
                        PV（日別）
                            <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                        、
                        流入元レポート（日別）<a href="<?php assign(Util::rewriteUrl('admin-cp', 'csv_fid_daily_report', array($cp->id), null, $this->brand->getUrl(), true)); ?>">
                            <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                        </a>、
                        既読件数（日別）<a href="<?php assign(Util::rewriteUrl('admin-cp', 'csv_daily_cp_action_status', array($cp->id), null, $this->brand->getUrl(), true)); ?>">
                            <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                        </a>、
                        リファラ上位
                            <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>、
                        メール開封率
                        <?php if ($show_opened_email_csv): ?>
                            <a href="<?php assign(Util::rewriteUrl('dashboard', 'csv_action_open_email_rate', array($cp->id), null, '', true)); ?>">
                                <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                            </a>
                        <?php else: ?>
                            <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php $i = 0;foreach($cp_flow_service->getCpActionsByCpId($cp->id) as $action):?>
                            <?php $cp_action_detail = $action->getCpActionDetail()['title'] ?>
                            <span class="label label-default">S<?php assign(++$i) ?>:
                                <?php if($action->type == CpAction::TYPE_ENGAGEMENT): ?>
                                    <?php assign($cp_action_detail . '(' . $action->getEngagementLogCount() . ')') ?>
                                <?php else: ?>
                                    <?php assign($cp_action_detail) ?>
                                <?php endif; ?>
                            </span>&nbsp;
                        <?php endforeach;?>
                    </div>
                </li>
<?php endforeach;?>
            </ul>

            <h2>メッセージ</h2>

            <ul class="list">

                <?php foreach ($data['cps'] as $cp): if($cp->type == Cp::TYPE_CAMPAIGN) continue; ?>
                <?php $cp_action_groups = $cp_flow_service->getCpActionGroupsByCpId($cp->id);
                    $show_opened_email_csv = false;
                    foreach ($cp_action_groups as $action_group) {
                        $first_action = $cp_flow_service->getFirstActionInGroupByGroupId($action_group->id);
                        if ($message_delivery_service->getDeliveredCpMessageDeliveryReservationByCpActionId($first_action->id)) {
                            $show_opened_email_csv = true;
                        }
                        break;
                    } ?>
                    <li>
                        <img src="<?php assign($cp->getIcon())?>" height="15" width="15" /><?php assign($cp->id)?> <a href="<?php assign($cp->getUrl())?>" target="_blank"><?php assign($cp->getTitle())?></a>
                    <?php if ($show_opened_email_csv): ?>
                        <span style="float: right;">
                            メール開封率<a href="<?php assign(Util::rewriteUrl('dashboard', 'csv_action_open_email_rate', array($cp->id), null, '', true)); ?>" class="glyphicon glyphicon-download-alt" aria-hidden="true"></a>
                        </span>
                    <?php else: ?>
                        <span style="float: right;">
                        メール開封率
                        <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                    </span>
                    <?php endif; ?>、
                    <span style="float: right;">
                        既読件数（日別）
                        <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                    </span>
                    </li>
                <?php endforeach;?>
            </ul>

            <h2>アンケート</h2>

            <ul class="list">

                <li>
                    DummyDummyDummyDummyDummy
                    <span style="float: right;">
                        設問ごとの回答数<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>、
                        マルチアンサー<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                    </span>
                </li>
                <li>
                    DummyDummyDummyDummyDummy
                    <span style="float: right;">
                        設問ごとの回答数<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>、
                        マルチアンサー<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                    </span>
                </li>
                <li>
                    DummyDummyDummyDummyDummy
                    <span style="float: right;">
                        設問ごとの回答数<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>、
                        マルチアンサー<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                    </span>
                </li>
            </ul>
        </div>

    </div><!-- row -->
</div><!-- container-fluid -->

<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>