<style>
    .pagePartsStampRallyCampaignPreview .pagePartsStampRallyCampaignPreviewBaloon li.stampJoined:after{
        background: url(<?php assign($data['cp_status_joined_image']) ?>) 0 0 no-repeat;
        background-size: cover;
    }
    .pagePartsStampRallyCampaignPreview .pagePartsStampRallyCampaignPreviewBaloon li.stampFinished:after{
        background: url(<?php assign($data['cp_status_finished_image']) ?>) 0 0 no-repeat;
        background-size: cover;
    }
</style>
<div class="pagePartsStampRallyCampaignList">
    <table class="itemTable" style="">
        <thead>
        <tr>

            <th class="snsAccount jsAreaToggleWrap">選択済み
                <a href="javascript:void(0)" class="<?php assign($data['search_condition'][StaticHtmlStampRallyService::SEARCH_BY_SELECTED_CP] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle" >キャンペーン<br>選択済み</a>
                <?php write_html($this->parseTemplate('SearchStampRallySelectedCp.php', array())) ?>
            </th>

            <th class="snsAccount jsAreaToggleWrap">ステータス
                <a href="javascript:void(0)" class="<?php assign($data['search_condition'][StaticHtmlStampRallyService::SEARCH_BY_CP_STATUS] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle" >絞り込む</a>
                <?php write_html($this->parseTemplate('SearchStampRallyCpStatus.php', array(
                ))) ?>
            </th>

            <th>キャンペーン名</th>

            <th class="snsAccount jsAreaToggleWrap">応募開始
                <a href="javascript:void(0)" class="<?php assign($data['search_condition'][StaticHtmlStampRallyService::SEARCH_BY_CP_OPEN_DATE] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle" >絞り込む</a>
                <?php write_html($this->parseTemplate('SearchStampRallyCpOpenDate.php', array(
                ))) ?>
            </th>

            <th class="snsAccount jsAreaToggleWrap">応募終了
                <a href="javascript:void(0)" class="<?php assign($data['search_condition'][StaticHtmlStampRallyService::SEARCH_BY_CP_FINISH_DATE] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle" >絞り込む</a>
                <?php write_html($this->parseTemplate('SearchStampRallyCpFinishDate.php', array(
                ))) ?>
            </th>

            <th>表示プレビュー</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($data['cp_list']['list'] as $cp): ?>
            <tr>
                <td><input type="checkbox" class="jsSelectCp" name="cp[]" data-cp_id="<?php assign($cp->id)?>" data-cp_status="<?php assign($cp->getStatus())?>"></td>
                <td class="<?php assign(StaticHtmlStampRallyService::$cp_status_labels[$cp->getStatus()]['class'])?>"><?php assign(StaticHtmlStampRallyService::$cp_status_labels[$cp->getStatus()]['label'])?></td>
                <td class="stampRallyTitle" title="<?php assign($cp->getTitle()) ?>"><?php assign($this->cutLongText($cp->getTitle(),15)); ?></td>
                <td><?php assign($data['staticHtmlStampRallyService']->convertDate($cp->start_date)); ?></td>
                <td><?php assign($data['staticHtmlStampRallyService']->convertDate($cp->end_date)); ?></td>
                <td>
                    <div class="pagePartsStampRallyCampaignPreview">
                        <p><img src="<?php assign($cp->getCpRectangleImage()) ?>"></p>
                        <div class="pagePartsStampRallyCampaignPreviewBaloon">
                            <ul>
                                <li>
                                    <img src="<?php assign($cp->getCpRectangleImage());?>">
                                </li>
                                <li class="stampJoined">
                                    <img src="<?php assign($cp->getCpRectangleImage());?>">
                                </li>
                                <li class="stampFinished">
                                    <img src="<?php assign($cp->getCpRectangleImage());?>">
                                </li>
                            </ul>
                            <p class="supplement1">※ページ全体の確定後、設定画像が反映されます</p>
                            <!-- /.pagePartsStampRallyCampaignPreviewBaloon --></div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <!-- /.itemTable --></table>
<!-- /.stampRallyCampaignList --></div>
<?php write_html($this->formHidden('current_check_cp_id', 0)) ?>