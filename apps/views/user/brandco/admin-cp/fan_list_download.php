<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<article>
    <?php if($data['is_cp_data_download_mode']): ?>
        <?php write_html($this->parseTemplate('ActionHeader.php',array(
            'cp_id' => $data['cp_id'],
            'action_id' => $data['action_id'],
            'user_list_page' => true,
            'pageStatus' => $data['pageStatus'],
            'enable_archive' => false,
            'isHideDemoFunction' => true
        ))); ?>
    <?php else: ?>
        <h1 class="hd1">ファンデータダウンロード</h1>
    <?php endif;?>

    <?php if ($data['is_cp_data_download_mode'] || $data['is_brand_download_mode_with_cp_id']): ?>
        <?php write_html($this->formHidden('search_url', $url = Util::rewriteUrl('admin-cp', 'api_search_fan.json'))) ?>
        <?php write_html($this->formHidden('cp_id', $data['cp_id'])) ?>
        <?php if ($params['openCpTab']) write_html($this->formHidden('open_cp_tab', $params['openCpTab'])) ?>
        <?php if ($data['is_cp_data_download_mode']) write_html($this->formHidden('is_cp_data_download_mode', 1)) ?>
    <?php else: ?>
        <?php write_html($this->formHidden('search_url', Util::rewriteUrl('admin-fan', 'api_search_brand_fan.json'))) ?>
    <?php endif; ?>

    <div class="customaudienceWrap">
        <form name="download_fan_list_zip" method="GET" action="<?php assign(Util::rewriteUrl('admin-cp', 'download_fan_list_zip', array($data['cp_id']), array())) ?>">
            <?php write_html($this->csrf_tag()); ?>
        <div class="customaudienceDetail" id="searchInputList">
            <p class="customaudienceRefinementClear"><span class="label">絞り込み条件</span><span class="btn2"><a href="javascript:void(0)" class="small1" id="clear_button">全解除</a></span></p>

            <?php write_html($this->parseTemplate('SearchBlockUserProfile.php', $data['search_condition'])) ?>

            <?php write_html($this->parseTemplate('SearchBlockProfileQuestionnaire.php', $data["search_condition"])) ?>

            <?php write_html($this->parseTemplate('SearchBlockConversionLogs.php', $data['search_condition'])) ?>

            <?php write_html($this->parseTemplate('SearchBlockSocialAccount.php', $data['search_condition'])) ?>

            <?php write_html($this->parseTemplate('SearchBlockParticipateHistory.php', $data['search_condition'])) ?>

            <?php write_html($this->parseTemplate('SearchBlockCampaign.php', $data['search_condition'])) ?>

            <section class="backPage">
                <?php if($data['is_cp_data_download_mode']): ?>
                    <p><a href="<?php write_html(Util::rewriteUrl('admin-cp', 'show_user_list', array($data['cp_id'], $data['first_cp_action']->id))) ?>" class="iconPrev1">キャンペーン参加者一覧</a></p>
                <?php else: ?>
                    <p><a href="<?php write_html(Util::rewriteUrl('','')) ?>" class="iconPrev1">ホームページ</a></p>
                <?php endif; ?>
            <!-- /.backPage --></section>
        <!-- /.customaudienceDetail --></div>

            <div class="customaudiencePreview" id="customaudiencePreview">
                <dl class="selectedStatus">
                    <dt>現在のユーザー</dt>
                    <dd class="selectedUser"><strong id="totalCount"></strong>件</dd>
                    <!-- /.selectedStatus --></dl>
                <p>ダウンロード可能なデータ</p>
                <p><label><input type="checkbox" id="selectFileListSelectAll" checked="checked">全選択</label></p>
                <ul class="downloadData" id="fileListSelector">
                    <li>
                        <label>
                            <input type="checkbox" name="file_selector" value="<?php assign(FanListDownloadService::TYPE_PROFILE) ?>" checked="checked">プロフィール情報
                            <small class="supplement1"> (<?php assign($data['dl_date'] . '_' . FanListDownloadService::$download_file_name[FanListDownloadService::TYPE_PROFILE]) ?>.csv)</small>
                        </label>
                    </li>
                    <?php if($data['is_cp_data_download_mode'] || $data['is_brand_download_mode_with_cp_id']): ?>
                        <?php foreach ($data['download_file_list'] as $cp_action_id => $file): ?>
                            <?php if ($file['type'] == CpAction::TYPE_ANNOUNCE): ?>
                                <?php if ($data['can_use_aaid_hash_tag']): ?>
                                    <li style="display:none" class="jsWinnerListFile<?php assign($cp_action_id) ?>">
                                        <label>
                                            <input type="checkbox" name="file_selector" disabled="disabled" value="<?php assign($cp_action_id) ?>" id="jsDownloadWinnerList<?php assign($cp_action_id) ?>"><?php assign($file['file_info']) ?>
                                            <small class="supplement1"> <?php assign($file['file_name']) ?></small>
                                        </label>
                                    </li>
                                <?php endif; ?>
                            <?php else: ?>
                                <li>
                                    <label>
                                        <input type="checkbox" name="file_selector" value="<?php assign($cp_action_id) ?>" checked="checked"><?php assign($file['file_info']) ?>
                                        <small class="supplement1"> <?php assign($file['file_name']) ?></small>
                                    </label>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <!-- /.downloadData --></ul>
                <p class="btnSet">
                    <span class="btn3"><a href="javascript:void(0)" data-url="<?php write_html(Util::rewriteUrl("admin-cp", "download_fan_list_zip", array($data['cp_id']))) ?>" data-params="" id="submit_download_button">ダウンロード</a></span>
                    <span class="iconError1" id="download_attention" style="display: none">再度ダウンロードが必要な場合、完了後にページを再読み込みして下さい。</span>
                </p>
            <!-- /.customaudiencePreview --></div>
        </form>

    <!-- /.customaudienceWrap --></div>

</article>

<div class="modal2 jsModal" id="modal2">
    <section class="modalCont-small jsModalCont" id="jsModalCont">
        <h1>確認</h1>
        <p><span class="attention1">全ての絞り込み条件を解除しますか？</span></p>
        <p class="btnSet">
        <span class="btn2">
            <a href="#closeModal" class="middle1">キャンセル</a>
        </span>
        <span class="btn4"><a id="" href="
        <?php if($data['is_cp_data_download_mode']): ?>
            <?php write_html(Util::rewriteUrl('admin-cp', 'fan_list_download', array($data['cp_id']), array('r' => true))) ?>
        <?php else: ?>
            <?php write_html(Util::rewriteUrl('admin-cp', 'fan_list_download',array(), array('r' => true))) ?>
        <?php endif; ?>
        " class="middle1">はい</a>
        </span></p>
    </section>
</div>
<?php if($data['is_cp_data_download_mode'] || $data['is_brand_download_mode_with_cp_id']): ?>
    <?php write_html($this->parseTemplate('MessageDeliveryConfirmBox.php', array(
        'reservation' => null,
        'cp_id' => $data['cp_id'],
        'pageStatus' => $data['pageStatus'],
    ))) ?>
    <?php write_html($this->parseTemplate('CpDownloadList.php', array(
        'brand_id' => $data['brand']->id,
        'cp_id' => $data['cp_id'],
        'pageStatus' => $data['pageStatus'],
    ))) ?>
<?php endif; ?>

<link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css'))?>">
<?php write_html($this->formHidden('personal_pc', $data['personal_pc'] ? 'personal_pc' : '')) ?>
<?php if(!$data['personal_pc']): ?>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>
<?php endif; ?>

<script src="<?php assign($this->setVersion('/js/ContainedStickyScroll/jquery-contained-sticky-scroll-min.js')) ?>"></script>

<?php $script = array('admin-cp/FanListDownloadService', 'admin-cp/CpMenuService'); ?>
<?php $param = array_merge($data['pageStatus'], array('script' => $script)) ?>
<?php write_html($this->parseTemplate("BrandcoFooter.php", $param)); ?>