<?php $page_link = Util::rewriteUrl('sns', 'detail', array($data['panel']['brandSocialAccountId'], $data['panel']['entry']['id'])); ?>

<section class="jsPanel contBoxMain-tw <?php assign($data['panel']['entry']['size_class'])?><?php if ($data['panel']['panelType'] == BrandcoTopMainCol::PANEL_TYPE_TOP && $data['isLoginAdmin']) assign(' contFixed')?>">
    <a href="<?php assign($page_link)?>" class="contInner panelClick"
       data-link="<?php assign($data['panel']['entry']["link"])?>"
       data-entry_id="<?php assign($data['panel']['entry']["id"])?>"
       data-entry="<?php assign(StreamService::STREAM_TYPE_TWITTER)?>"
       data-type="<?php assign(UserPanelClick::TOP_PANEL); ?>">
        <div class="contWrap">
            <p class="contText">
                <?php write_html($this->nl2brAndHtmlspecialchars($data['panel']['entry']["panel_text"]))?>
                <img <?php if(!$data['panel']['entry']["image_url"]) write_html('style="display:none"')?> src="<?php assign($data['panel']['entry']["image_url"]) ?>" alt="">
            </p>
            <!-- /.contWrap --></div>
    </a>

    <div class="nav">
        <ul class="twActions">
            <li><a href="//twitter.com/intent/follow?screen_name=<?php assign($data['panel']['screenName'])?>" class="twFollow">フォローする</a></li>
            <li><a href="//twitter.com/intent/tweet?in_reply_to=<?php assign($data['panel']['entry']["object_id"])?>" class="twReply" target="_blank">リプライ</a></li>
            <li><a href="//twitter.com/intent/retweet?tweet_id=<?php assign($data['panel']['entry']["object_id"])?>" class="twRetweet">リツイート</a></li>
            <li><a href="//twitter.com/intent/favorite?tweet_id=<?php assign($data['panel']['entry']["object_id"])?>" class="twFavo">お気に入り</a></li>
        </ul>
        <!-- /.nav --></div>

    <?php if($data['isLoginAdmin']):?>
        <ul class="editBox1">
            <li><a href="#editTWPanelForm" data-option=<?php assign('/'.$data['panel']['brandSocialAccountId'].'/'.$data['panel']['entry']["id"].'?from=top')?> class="linkEdit jsOpenModal">編集</a></li>
            <li><a href="javascript:void(0)" class="linkSize jsPanelSizing"
                   data-entry = '<?php assign('stream='.$data['panel']['streamName'].'&entry_id='.$data['panel']['entry']["id"]) ?>'>サイズ変更</a></li>
            <li><a href="javascript:void(0)" class="linkFix jsFixed"
                   data-entry='<?php assign('entryId='.$data['panel']['entry']["id"].'&brandSocialAccountId='.$data['panel']['brandSocialAccountId'].'&brandId='.$data['brand']->id)?>'
                    ><?php if($data['panel']['panelType'] == BrandcoTopMainCol::PANEL_TYPE_TOP) assign('優先表示を解除'); else assign('優先表示')?></a></li>
            <li><a data-entry="<?php assign('entryId='.$data['panel']['entry']["id"].'&brandSocialAccountId='.$data['panel']['brandSocialAccountId'].'&brandId='.$data['brand']->id)?>" href="javascript:void(0)" class="linkNonDisplay">非表示</a></li>
            <li class="panelLink"><a href="<?php assign($page_link); ?>" class="openNewWindow1" target="_blank">リンク先を開く</a></li>
        </ul>
    <?php endif;?>

    <footer>
        <a href="https://twitter.com/<?php assign($data['panel']['screenName']); ?>" target="_blank">
            <p class="postType">
                <img src="<?php assign($data['panel']['imageUrl'])?>" width="28" height="28" alt=""><span><span><?php assign($data['panel']['pageName'])?></span></span>
                <p class="timeLogo"><small><span class="iconTW2_2">Twitter</span></small></p>
            </p>
        </a>
    </footer>
    <!-- /.contBoxMain-tw--></section>
