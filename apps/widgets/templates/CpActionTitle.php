<h1 class="<?php assign($data['h1_class'])?>">
    <img src="<?php assign($data['cp']->image_url ?: $this->setVersion('/img/icon/iconNoImage2.png')) ?>" alt="campaign img" class="hdCampaignIcon"><span class="<?php assign($data['title_attributes']['class']) ?>">
        <?php assign($data['title_attributes']['label']) ?>
        <?php if ($data['should_announce']): ?>
            <span class="iconCaution">
            <span class="text">ヘルプ</span>
            <span class="textBalloon1">
                <?php if ($data['cp_status'] == Cp::CAMPAIGN_STATUS_WAIT_ANNOUNCE): ?>
                    <span>当選発表日です</span>
                <?php elseif ($data['cp_status'] == Cp::CAMPAIGN_STATUS_CLOSE): ?>
                    <span>当選発表日を過ぎています</span>
                <?php endif; ?>
                <!-- /.textBalloon1 --></span>
            <!-- /.iconCaution --></span>
        <?php endif; ?>
    </span><span class="hdInnerText"><?php assign($data['cp']->getTitle())?></span>

    <?php if ($cp_status == Cp::CAMPAIGN_STATUS_CLOSE && $data['enable_archive']): ?>
        <?php if ($data['cp']->isArchive()): ?>
            <a href="<?php write_html(Util::rewriteUrl('admin-cp', 'save_public_campaign_into_archive', array($data['cp']->id), array('p' => $_GET['p'], 'type' => $_GET['type']))) ?>" class="archiveOut"><span class="textBalloon1"><span>アーカイブから出す</span></span></a>
        <?php else: ?>
            <a href="javascript:void(0)" data-type="cp" data-url="<?php write_html(Util::rewriteUrl('admin-cp', 'save_public_campaign_into_archive', array($data['cp']->id), array('p' => $_GET['p'], 'type' => $_GET['type']))) ?>" class="trashbox"><span class="textBalloon1"><span>削除する</span></span></a>
        <?php endif; ?>
    <?php endif; ?>
</h1>
