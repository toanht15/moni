<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>

<?php write_html($this->parseTemplate('CandidatePreviewModal.php')) ?>

<article>
    <section class="messageWrap">
        <section class="campaign">
            <?php if ($data['cp_image_url']): ?>
                <p class="campaignImg"><img src="<?php assign($data['cp_image_url']); ?>" width="690" height="280" alt="img text"></p>
            <?php endif; ?>

            <?php if ($data['candidate']): ?>
                <div class="uploadCont">
                    <div class="votedItem">
                        <figure class="itemCont">
                            <figcaption class="itemTitle">「<?php assign($data['candidate']['title']); ?>」に投票しました！</figcaption>
                            <?php if ($data['cp_popular_vote_action']->file_type == CpPopularVoteAction::FILE_TYPE_IMAGE): ?>
                                <span class="contImg"><img src="<?php assign($data['candidate']['thumbnail_url']); ?>" alt="image title"></span>
                            <?php else: ?>
                                <p class="contMovie"><iframe src="<?php assign($data['candidate']['original_url']); ?>" frameborder="0" allowfullscreen=""></iframe></p>
                            <?php endif; ?>
                        </figure>
                        <p class="itemText"><?php assign($data['candidate']['description']); ?></p>
                        <!-- /.votedItem --></div>
                    <!-- /.uploadCont --></div>
            <?php endif; ?>

            <ul class="btnSet">
                <li class="btn3"><a href="<?php assign($data['cp_url']); ?>" class="large1">キャンペーンを見る</a></li>
                <!-- /.btnSet --></ul>
            <div class="messageRankingPreview">
                <section class="rankingTheme">
                    <?php write_html($data['cp_popular_vote_action']->html_content ? : $this->toHalfContentDeeply($data['cp_popular_vote_action']->text)); ?>
                </section>
                <p class="rankingLabel"><strong><?php assign(($data['cp_popular_vote_action']->show_ranking_flg == CpPopularVoteAction::RANKING_FLG) ? '現在の投票結果' : '投票候補一覧'); ?></strong></p>
                <ol class="messageRankingItem jsCandidateList" data-file_type="<?php assign($data['cp_popular_vote_action']->file_type); ?>">
                    <?php foreach ($data['candidates'] as $candidate): ?>
                    <li <?php if (strlen($candidate['class_name'])) write_html('class="' . $candidate['class_name'] .'"'); ?>>
                        <figure class="itemCont">
                            <figcaption class="itemTitle jsCandidateTitle"><?php assign($candidate['title']); ?></figcaption>
                            <span class="contImg"><img src="<?php assign($candidate['thumbnail_url']); ?>" alt="image title" class="jsCandidateImage" data-modal_content="<?php assign($candidate['original_url']); ?>"></span>
                        </figure>
                        <p class="itemText jsCandidateDescription"><?php assign($candidate['description']); ?></p>
                        <?php if ($data['cp_popular_vote_action']->file_type == CpPopularVoteAction::FILE_TYPE_IMAGE): ?>
                            <a href="javascript:void(0);" data-open_modal_type="CandidatePreview" class="imgPreview jsOpenModal">拡大表示する</a>
                        <?php else: ?>
                            <a href="javascript:void(0);" data-open_modal_type="CandidatePreview" class="moviePreview jsOpenModal">拡大表示する</a>
                        <?php endif; ?>
                        <!-- /.rank1st --></li>
                    <?php endforeach; ?>
                    <!-- /.messageRankingItem --></ol>
                <!-- /.messageRankingPreview --></div>
            <!-- /.campaign --></section>
        <!-- /.messageWrap --></section>
    <p class="pageTop" style="display: none;"><a href="#top"><span>ページTOPへ</span></a></p>
</article>

<?php write_html($this->scriptTag("user/UserActionPopularVoteService")); ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
