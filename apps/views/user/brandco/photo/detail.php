<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>

<article class="mainCol">

    <div class="photoPageWrap">
        <section class="photoPage">
            <figure><img src="<?php assign($data['page_data']['photo_user']->photo_url); ?>"
                         alt="<?php assign($data['page_data']['photo_user']->photo_title); ?>"></figure>
            <div class="postWrap">
                <h1 class="postTitle">
                    <?php assign($data['page_data']['photo_user']->photo_title); ?></h1>

                <p class="postText">
                    <?php write_html($this->nl2brAndHtmlspecialchars($data['page_data']['photo_user']->photo_comment)); ?></p>
                <p class="postData">
                    <img src="<?php assign($data['page_data']['user']->profile_image_url); ?>" alt="" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';">
                    <span class="timeStamp">
                        <?php assign(date('Y/m/d', strtotime($data['page_data']['photo_user']->created_at))); ?></span></p>

                    <div class="snsWrap">
                        <ul class="snsBtns-btn">
                            <li><div class="fb-like" data-href="<?php assign(Util::getCurrentUrl()); ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div></li
                                ><li><a href="<?php assign(Util::getCurrentUrl()); ?>" class="twitter-share-button" data-lang="ja" data-count="none">ツイート</a></li
                                ><li><div class="g-plusone" data-size="medium" data-annotation="none"></div></li
                                >
                            <!-- /.snsBtns-btn --></ul>
                        <ul class="snsBtns-box">
                            <li><div class="fb-like" data-href="<?php assign(Util::getCurrentUrl()); ?>" data-layout="box_count" data-action="like" data-show-faces="false" data-share="false"></div></li
                                ><li><a href="<?php assign(Util::getCurrentUrl()); ?>" class="twitter-share-button" data-lang="ja" data-count="vertical">ツイート</a></li
                                ><li><div class="g-plusone" data-size="tall"></div></li
                                ><li class="line"><span><script type="text/javascript" src="//media.line.me/js/line-button.js?v=20140411" ></script><script type="text/javascript">new media_line_me.LineButton({"pc":false,"lang":"ja","type":"e"});</script></span></li>
                            <!-- /.snsBtns-box --></ul>
                        <!-- /.snsWrap --></div>

                <?php if ($data['page_data']['cp']->join_limit_flg != Cp::JOIN_LIMIT_ON && $data['page_data']['cp']->getStatus() == Cp::CAMPAIGN_STATUS_OPEN): ?>
                    <?php $cp_url = $data['page_data']['cp']->getReferenceUrl() ?>
                    <div class="relationCp">
                        <figure><a href="<?php assign($cp_url); ?>"><img src="<?php assign($data['page_data']['cp']->image_url); ?>" width="41" height="41" alt=""></a></figure>
                        <p><strong>キャンペーンに参加しませんか？</strong><br><a href="<?php assign($cp_url); ?>"><?php assign($data['page_data']['cp']->getTitle()); ?></a><br><span class="btn3"><a href="<?php assign($cp_url); ?>" class="small1">参加する</a></span></p>
                        <!-- /.relationCp --></div>
                <?php endif; ?>

                <!-- /.postWrap --></div>
            <!-- /.photoPage --></section>

            <ul class="pager3">
                <?php if ($data['page_data']['next_url']): ?>
                    <li><a href="<?php write_html($data['page_data']['next_url']); ?>" class="iconPrev1">前の記事</a></li>
                <?php endif; ?>
                <?php if ($data['page_data']['prev_url']): ?>
                    <li><a href="<?php write_html($data['page_data']['prev_url']); ?>" class="iconNext1">次の記事</a></li>
                <?php endif; ?>
                <!-- /.pager3 --></ul>

        <?php if ($data['page_data']['photo_entries'] && ($data['page_data']['photo_entries']->total() > 1)): ?>
            <div class="photoPostWrap">
                <h2>他のユーザの投稿</h2>

                <?php $li_count = 1; ?>
                <ul class="photoPostList">
                    <?php foreach($data['page_data']['photo_entries'] as $photo_entry): ?><?php if ($li_count >= (Util::isSmartPhone() ? PhotoStreamService::DETAIL_PAGE_LIMIT_SP: PhotoStreamService::DETAIL_PAGE_LIMIT_PC)) break; ?><?php if ($photo_entry->id == $data['photo_entry']->id) continue; ?><li class="jsPhotoPanel">
                        <?php $photo_user = $photo_entry->getPhotoUser(); ?>
                        <a href="<?php assign(Util::rewriteUrl('photo', 'detail', array($photo_entry->id))); ?>">
                            <img src="<?php assign($photo_user->getCroppedPhoto()); ?>" alt="<?php assign($photo_user->photo_title); ?>" onerror="this.src='<?php assign($photo_user->photo_url); ?>';">
                        </a>
                    </li><?php $li_count++ ?><?php endforeach; ?><!-- /.photoPostList --></ul>
                <!-- /.photoListWrap --></div>
        <?php endif; ?>

        <!-- /.photoPageWrap --></div>

    <?php if ($this->brand_contract->plan == BrandContract::PLAN_MANAGER_STANDARD && $this->brand->hasOption(BrandOptions::OPTION_TOP)): ?>
        <p class="pagePrev"><a href="<?php Util::rewriteUrl('', ''); ?>">一覧へ戻る</a></p>
    <?php endif; ?>

    <?php if ($data['page_data']['photo_entries']): ?>
        <nav class="bredlink1">
            <ul>
                <?php if ($this->brand_contract->plan == BrandContract::PLAN_MANAGER_STANDARD && $this->brand->hasOption(BrandOptions::OPTION_TOP)): ?>
                    <li class="home"><a href="<?php assign(Util::rewriteUrl('', '')); ?>">HOME</a></li>
                <?php endif; ?>
                <li><a href="<?php assign(Util::rewriteUrl('photo', 'cp_actions', array($data['page_data']['photo_user']->cp_action_id))); ?>">みんなの投稿</a></li>
                <li class="current"><span><?php assign($this->page_data['page_title']); ?></span></li>
            </ul>
            <!-- /.bredlink1 --></nav>
    <?php endif; ?>

    <?php if (!$data['photo_entry']->hidden_flg): ?>
        <nav class="bredlink1">
            <ul>
                <?php if ($this->brand_contract->plan == BrandContract::PLAN_MANAGER_STANDARD && $this->brand->hasOption(BrandOptions::OPTION_TOP)): ?>
                    <li class="home"><a href="<?php assign(Util::rewriteUrl('', '')); ?>">HOME</a></li>
                <?php endif; ?>
                <li><a href="<?php assign(Util::rewriteUrl('photo', 'post_list')); ?>">Photo Gallery</a></li>
                <li class="current"><span><?php assign($this->page_data['page_title']); ?></span></li>
            </ul>
            <!-- /.bredlink1 --></nav>
    <?php endif; ?>
    <p class="pageTop"><a href="#top"><span>ページTOPへ</span></a></p>
</article>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
