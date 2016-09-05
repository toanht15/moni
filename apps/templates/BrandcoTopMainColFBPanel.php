<?php $page_link = Util::rewriteUrl('sns', 'detail', array($data['panel']['brandSocialAccountId'], $data['panel']['entry']['id'])); ?>

<?php if ($data['panel']['entry']["image_url"]): ?>
    <section class="jsPanel contBoxMain-fb <?php assign($data['panel']['entry']['size_class']) ?><?php if ($data['panel']['panelType'] == BrandcoTopMainCol::PANEL_TYPE_TOP && $data['isLoginAdmin']) assign(' contFixed') ?>">
    <?php if ($data['panel']['entry']['type'] == FacebookEntry::ENTRY_TYPE_VIDEO): ?>
        <div class="videoInner">
            <?php if ($data['panel']['entry']['status_type'] == FacebookEntry::STATUS_TYPE_ADDED_VIDEO): ?>
                <div class="fb-video" data-href="<?php assign($data['panel']['entry']["video_url"])?>" data-allowfullscreen="true"></div>
            <?php else: ?>
                <iframe src="<?php assign($data['panel']['entry']["video_url"])?>" frameborder="0" allowfullscreen></iframe>
            <?php endif; ?>
        </div>
    <?php endif ?>
    <a href="<?php assign($page_link) ?>" class="contInner panelClick"
       data-link="<?php assign($data['panel']['entry']["link"])?>"
       data-entry_id="<?php assign($data['panel']['entry']["id"])?>"
       data-entry="<?php assign(StreamService::STREAM_TYPE_FACEBOOK) ?>"
       data-type="<?php assign(UserPanelClick::TOP_PANEL); ?>">

        <div class="contWrap">
            <?php if ($data['panel']['entry']['type'] != FacebookEntry::ENTRY_TYPE_VIDEO): ?>
                <img src="<?php assign($data['panel']['entry']["image_url"]) ?>" alt="">
            <?php endif ?>
            <?php if ($data['panel']['entry']["panel_text"]): ?>
                <p class="contText">
                    <span><?php write_html($this->nl2brAndHtmlspecialchars($data['panel']['entry']["panel_text"])) ?></span>
                </p>
            <?php endif; ?>
            <p class="postType"><?php assign($data['panel']['pageName']) ?></p>
        </div>
    </a>
<?php else: ?>
    <section class="jsPanel contBoxMain-fb <?php assign($data['panel']['entry']['size_class']) ?><?php if ($data['panel']['panelType'] == BrandcoTopMainCol::PANEL_TYPE_TOP && $data['isLoginAdmin']) assign(' contFixed') ?>">
        <a href="<?php assign($page_link) ?>" class="contInner panelClick"
           data-link="<?php assign($data['panel']['entry']["link"])?>"
           data-entry="<?php assign(StreamService::STREAM_TYPE_FACEBOOK) ?>"
           data-entry_id="<?php assign($data['panel']['entry']['id']) ?>">
            <div class="contWrap">
                <p class="contText"><span><?php write_html($this->nl2brAndHtmlspecialchars($data['panel']['entry']["panel_text"])) ?></span></p>
                <p class="postType"><?php assign($data['panel']['pageName']) ?></p>
            </div>
        </a>
<?php endif; ?>
    <?php if ($data['isLoginAdmin']): ?>
        <ul class="editBox1">
            <li><a href="#editFBPanelForm"
                   data-option=<?php assign('/' . $data['panel']['brandSocialAccountId'] . '/' . $data['panel']['entry']["id"] . '?from=top') ?> class="linkEdit
                jsOpenModal">編集</a></li>
            <li><a href="javascript:void(0)" class="linkSize jsPanelSizing"
                   data-entry='<?php assign('stream=' . $data['panel']['streamName'] . '&entry_id=' . $data['panel']['entry']["id"]) ?>'>サイズ変更</a>
            </li>
            <li><a href="javascript:void(0)" class="linkFix jsFixed"
                   data-entry='<?php assign('entryId=' . $data['panel']['entry']["id"] . '&brandSocialAccountId=' . $data['panel']['brandSocialAccountId'] . '&brandId=' . $data['brand']->id) ?>'>
                    <?php if ($data['panel']['panelType'] == BrandcoTopMainCol::PANEL_TYPE_TOP) assign('優先表示を解除'); else assign('優先表示') ?></a>
            </li>
            <li>
                <a data-entry="<?php assign('entryId=' . $data['panel']['entry']["id"] . '&brandSocialAccountId=' . $data['panel']['brandSocialAccountId'] . '&brandId=' . $data['brand']->id) ?>"
                   href="javascript:void(0)" class="linkNonDisplay">非表示</a></li>
            <li class="panelLink"><a href="<?php assign($page_link); ?>" class="openNewWindow1" target="_blank">リンク先を開く</a></li>
        </ul>
    <?php endif; ?>
<!-- /.contBoxMain--></section>