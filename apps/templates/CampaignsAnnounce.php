<?php if($data["action_info"]["cp"]["join_limit_flg"] == Cp::JOIN_LIMIT_OFF && $data["action_info"]["cp"]["share_flg"] == Cp::FLAG_SHOW_VALUE): ?>
    <div class="campaignShare">
        <p>このキャンペーンを友達に知らせよう</p>
        <ul class="snsBtns-box">
            <li><div class="fb-like" data-href="<?php assign($data["action_info"]["cp"]["url"]) ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div></li
                ><li><a href="https://twitter.com/share" class="twitter-share-button" data-lang="ja" data-count="vertical">ツイート</a></li
                ><li class="line"><span><script type="text/javascript" src="//media.line.me/js/line-button.js?v=20140411" ></script><script type="text/javascript">new media_line_me.LineButton({"pc":false,"lang":"ja","type":"a"});</script></span></li
                ><li><a href="http://b.hatena.ne.jp/entry/<?php assign($data['pageStatus']['og']['url'])?>" class="hatena-bookmark-button" data-hatena-bookmark-title="<?php assign($data['pageStatus']['og']['title'])?>" data-hatena-bookmark-layout="simple-balloon" data-hatena-bookmark-lang="ja" title="このエントリーをはてなブックマークに追加"><img src="https://b.st-hatena.com/images/entry-button/button-only@2x.png" alt="このエントリーをはてなブックマークに追加" width="20" height="20" style="border: none;" /></a><script type="text/javascript" src="https://b.st-hatena.com/js/bookmark_button.js" charset="utf-8" async="async"></script></li
                ><li><div class="g-plusone" data-size="medium"></div></li>
            <!-- /.snsBtns --></ul>
        <!-- /.campaignShare --></div>
<?php endif; ?>
<ul class="campaignData">

    <?php if (!$data["action_info"]["cp"]["is_non_incentive"]): ?>
        <?php if($data["action_info"]["cp"]["show_winner_label"] == Cp::FLAG_SHOW_VALUE): ?>
            <li class="present"><span class="itemTitle">プレゼント</span><span class="itemData"><?php assign($data["action_info"]["cp"]["winner_label"]); ?></span></li>
        <?php else : ?>
            <li class="present"><span class="itemTitle">プレゼント</span><span class="itemData"><?php assign($data["action_info"]["cp"]["winner_count"]); ?>名様</span></li>
        <?php endif; ?>
            <li class="term"><span class="itemTitle">開催期間</span><span class="itemData"><?php assign($data["action_info"]["cp"]["start_datetime"]); ?> ～ <?php assign($data["action_info"]["cp"]["end_datetime"]); ?></span></li>
            <li class="result">
                <?php if ($data['action_info']['cp']['shipping_method'] == Cp::SHIPPING_METHOD_PRESENT || $data['cp']->selection_method == CpCreator::ANNOUNCE_LOTTERY): ?>
                    <span class="itemTitle">発表</span>
                <?php else: ?>
                    <span class="itemTitle">発表日</span>
                <?php endif; ?>
                    <span class="itemData">
                        <?php if ($data['action_info']['cp']['announce_display_label_use_flg'] == 1): ?>
                            <?php assign($data['action_info']['cp']['announce_display_label']) ?>
                        <?php elseif ($data["action_info"]["cp"]["shipping_method"] == Cp::SHIPPING_METHOD_PRESENT): ?>
                            賞品の発送をもって発表
                        <?php elseif ($data['cp']->selection_method == CpCreator::ANNOUNCE_LOTTERY): ?>
                            スピードくじの結果により即時
                        <?php else: ?>
                            <?php assign($data["action_info"]["cp"]["announce_date"]); ?>
                        <?php endif; ?>
                    </span>
            </li>
    <?php elseif (!$data['action_info']['cp']['is_permanent_cp']): ?>
        <li class="term"><span class="itemTitle">開催期間</span><span class="itemData"><?php assign($data["action_info"]["cp"]["start_datetime"]); ?> ～ <?php assign($data["action_info"]["cp"]["end_datetime"]); ?></span></li>
    <?php endif ?>
    <li class="sponsor"><span class="itemTitle">開催</span><span class="itemData"><?php assign($data["action_info"]["cp"]["sponsor"]); ?></span></li>
    <?php if($data["action_info"]["cp"]["show_recruitment_note"] == Cp::FLAG_SHOW_VALUE): ?>
        <li class="attention"><span class="itemTitle">注意事項</span><span class="itemData"><?php write_html($this->toHalfContent($data["action_info"]["cp"]["recruitment_note"], false)); ?></span></li>
    <?php endif; ?>

    <!-- /.campaignData --></ul>
    <p class="notAffiliated"><small>Not affiliated with Facebook, Inc.</small></p>

<?php // AUキャンペーン用 ?>
<?php if ($data['action_info']['cp']['is_au_campaign']): ?>
    <div style="padding:10px 9px 15px 9px;">
        <a href="https://tokuten.auone.jp/" target="_blank">
            <img src="https://s3-ap-northeast-1.amazonaws.com/parts.brandco.jp/image/brand/013d407166ec4fa56eb1e1f8cbe183b9/upload_file/4829929c3ab2151c3c6fad7e12b258007bcf06c2/titile_black.png" width="100%" alt="auスマートパスの日" >
        </a>
    </div>
<?php endif ?>
