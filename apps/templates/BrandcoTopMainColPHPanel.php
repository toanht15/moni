<?php $page_link = Util::rewriteUrl('photo', 'detail', array($data['panel']['entry']['id'])); ?>

<section
    class="jsPanel contBoxMain-photo <?php assign($data['panel']['entry']['size_class']) ?> <?php if ($data['panel']['panelType'] == BrandcoTopMainCol::PANEL_TYPE_TOP && $data['isLoginAdmin']) assign(' contFixed') ?>">
    <a href="<?php assign($page_link) ?>" class="contInner panelClick"
       data-link="<?php assign($page_link) ?>"
       data-entry_id="<?php assign($data['panel']['entry']['id']) ?>"
       data-entry="<?php assign(StreamService::STREAM_TYPE_PHOTO) ?>">
        <div class="contWrap">
            <?php if ($data['panel']['entry']["image_url"]): ?>
                <img src="<?php assign($data['panel']['entry']["image_url"]) ?>" alt="<?php assign($data['panel']['entry']["title"]); ?>">
            <?php endif; ?>
            <p class="contText">
                <span><?php write_html($this->nl2brAndHtmlspecialchars($data['panel']['entry']["title"])) ?></span>
            </p>
            <p class="postDate">
                <img src="<?php assign($data['panel']['entry']["user_profile_image_url"]) ?>" width="100" height="100"
                     onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';"
                     alt="">
                <?php assign(date('Y/m/d', strtotime($data['panel']['entry']['pub_date']))); ?>
            </p>
            <p class="postType"><?php assign($data['panel']['entry']['cp_title']); ?></p>
        </div>
        <!-- /.contInner --></a>

    <?php if ($data['isLoginAdmin']): ?>
        <ul class="editBox1">
            <li><a href="#editPHPanelForm"
                   data-option=<?php assign('/' . $data['panel']['entry']["id"] . '?from=top') ?> class="linkEdit
                   jsOpenModal">編集</a></li>
            <li><a href="javascript:void(0)" class="linkSize jsPanelSizing"
                   data-entry='<?php assign('stream=' . $data['panel']['streamName'] . '&entry_id=' . $data['panel']['entry']["id"]) ?>'>サイズ変更</a>
            </li>
            <li><a href="javascript:void(0)" class="linkFix jsFixed"
                   data-entry='<?php assign('entryId=' . $data['panel']['entry']["id"] . '&service_prefix=' . $data['panel']['entry']["service_prefix"] . '&brandId=' . $data['brand']->id) ?>'>
                    <?php if ($data['panel']['panelType'] == BrandcoTopMainCol::PANEL_TYPE_TOP) assign('優先表示を解除'); else assign('優先表示') ?></a>
            </li>
            <li>
                <a data-entry="<?php assign('entryId=' . $data['panel']['entry']["id"] . '&service_prefix=' . $data['panel']['entry']["service_prefix"] . '&brandId=' . $data['brand']->id) ?>"
                   href="javascript:void(0)" class="linkNonDisplay">非表示</a></li>
            <li class="panelLink"><a href="<?php assign($page_link); ?>" class="openNewWindow1"
                                     target="_blank">リンク先を開く</a></li>
        </ul>
    <?php endif; ?>
    <!-- /.contBoxMain--></section>
