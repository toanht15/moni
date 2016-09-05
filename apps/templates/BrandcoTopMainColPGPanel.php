<section class="jsPanel contBoxMain-cms <?php  assign($data['panel']['entry']['size_class'])?> <?php if ($data['panel']['panelType'] == BrandcoTopMainCol::PANEL_TYPE_TOP && $data['isLoginAdmin']) assign(' contFixed')?>">
    <?php if($data['brand']->isLimitedBrandPage() && $data['panel']['page_new_flg']): ?><p class="newLabel"><span>NEW</span></p><?php endif ?>
    <a href="<?php assign($data['panel']['entry']["link"])?>" class="contInner panelClick" <?php if ($data['panel']['entry']['target'] == LinkEntry::TARGET_BLANK) write_html('target="_blank"') ?>
       data-link="<?php assign($data['panel']['entry']["link"])?>"
       data-entry="<?php assign(StreamService::STREAM_TYPE_PAGE)?>"
       data-entry_id="<?php assign($data['panel']['entry']['id']) ?>">
        <div class="contWrap">
            <?php if($data['panel']['entry']["image_url"]):?><img src="<?php assign($data['panel']['entry']["image_url"])?>" alt=""><?php endif;?>
            <p class="contTitle"><?php assign($data['panel']['entry']["title"])?></p>
            <?php if($data['panel']['entry']["panel_text"]): ?>
                <p class="contText">
                    <span><?php write_html($this->nl2brAndHtmlspecialchars($data['panel']['entry']["panel_text"]))?></span>
                </p>
            <?php endif; ?>
            <p class="postType"><?php assign($data['panel']['category']['name'])?></p>
        </div>
    </a>
    <?php if($data['isLoginAdmin']):?>
    <ul class="editBox1">
        <li><a href="#editPGPanelForm" data-option=<?php assign('/'.$data['panel']['entry']["id"].'?from=top')?> class="linkEdit jsOpenModal">編集</a></li>
        <li><a href="javascript:void(0)" class="linkSize jsPanelSizing"
            data-entry = '<?php assign('stream='.$data['panel']['streamName'].'&entry_id='.$data['panel']['entry']["id"]) ?>'>サイズ変更</a></li>
        <li><a href="javascript:void(0)" class="linkFix jsFixed"
            data-entry='<?php assign('entryId='.$data['panel']['entry']["id"].'&service_prefix='.$data['panel']['entry']["service_prefix"].'&brandId='.$data['brand']->id)?>'>
        <?php if($data['panel']['panelType'] == BrandcoTopMainCol::PANEL_TYPE_TOP) assign('優先表示を解除'); else assign('優先表示')?></a></li>
        <li><a data-entry="<?php assign('entryId='.$data['panel']['entry']["id"].'&service_prefix='.$data['panel']['entry']["service_prefix"].'&brandId='.$data['brand']->id)?>" href="javascript:void(0)" class="linkNonDisplay">非表示</a></li>
        <li class="panelLink"><a href="<?php assign($data['panel']['entry']['link']); ?>" class="openNewWindow1" target="_blank">リンク先を開く</a></li>
    </ul>
    <?php endif;?>
<!-- /.contBoxMain--></section>
