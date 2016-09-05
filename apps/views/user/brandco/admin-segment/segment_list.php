<?php write_html(aafwWidgets::getInstance()->loadWidget("BrandcoHeader")->render($data["pageStatus"])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget("BrandcoAccountHeader")->render($data["pageStatus"])) ?>
    <article>
        <h1 class="hd1">セグメント一覧</h1>
        <?php write_html($this->csrf_tag()); ?>
        <ul class="segmentMakeWrap">
            <li class="btn1">
                <a href="<?php assign(Util::rewriteUrl('admin-segment', 'create_conditional_segment')) ?>" class="large2">条件セグメントの作成
                    <span class="iconHelp">
                        <span class="textBalloon1">
                            <span>
                              条件に基づいたセグメントを作成できます。
                            </span>
                      <!-- /.textBalloon1 --></span>
                    <!-- /.iconHelp --></span>
                </a>
            </li>
            <li class="btn1">
                <a href="<?php assign(Util::rewriteUrl('admin-segment', 'create_segment_group')) ?>" class="large2">セグメントグループの作成
                    <span class="iconHelp">
                      <span class="textBalloon1">
                        <span>
                          条件に基づいたセグメントを<br>複数作成しグループ化できます。<br>作成したセグメントに序列を付け<br>ユーザーを重複なく割り振れます。
                        </span>
                      <!-- /.textBalloon1 --></span>
                    <!-- /.iconHelp --></span>
                </a>
            </li>
        </ul>
        <section class="segmentCont">
            <p class="actionWrap">チェックしたセグメントに<span class="btn3"><a href="#actionSet" class="small1 jsOpenSegmentActionModal">アクション</a></span></p>
            <div class="segmentWrap">
                <ul class="segmentTypeList">
                    <li class="segmentAll jsSSelector" data-segment_type="<?php assign(Segment::TYPE_ALL_SEGMENT) ?>">
                        <span class="current">すべてのセグメント
                            <p><strong id="total_segment_counter"><?php assign($data['count_list']['total']['count']) ?></strong>/<?php assign($data['count_list']['total']['limit']) ?></p>
                        </span></li>
                    <li class="segmentTerms jsSSelector" data-segment_type="<?php assign(Segment::TYPE_CONDITIONAL_SEGMENT) ?>">
                        <a>条件セグメント
                            <p><strong id="segment_counter_<?php assign(Segment::TYPE_CONDITIONAL_SEGMENT) ?>"><?php assign($data['count_list'][Segment::TYPE_CONDITIONAL_SEGMENT]['count']) ?></strong>/<?php assign($data['count_list'][Segment::TYPE_CONDITIONAL_SEGMENT]['limit']) ?></p></a></li>
                    <li class="segmentGroup jsSSelector" data-segment_type="<?php assign(Segment::TYPE_SEGMENT_GROUP) ?>">
                        <a>セグメントグループ
                            <p><strong id="segment_counter_<?php assign(Segment::TYPE_SEGMENT_GROUP) ?>"><?php assign($data['count_list'][Segment::TYPE_SEGMENT_GROUP]['count']) ?></strong>/<?php assign($data['count_list'][Segment::TYPE_SEGMENT_GROUP]['limit']) ?></p></a></li>
                    <!-- /.segmentTypeList --></ul>

                <?php write_html(aafwWidgets::getInstance()->loadWidget('SegmentContainerList')->render($data['default_data'])) ?>
                <!-- /.segmentWrap --></div>
            <p class="actionWrap">チェックしたセグメントに<span class="btn3"><a href="#actionSet" class="small1 jsOpenSegmentActionModal">アクション</a></span></p>

            <!-- /.segmentCont --></section>
    </article>
<?php write_html($this->parseTemplate("segment/SegmentArchiveConfirmModal.php")); ?>
<?php write_html($this->parseTemplate("segment/SegmentActionSelectorModal.php", array('can_use_ads_action' => $data['can_use_ads_action']))); ?>
<?php write_html($this->parseTemplate("segment/SegmentActionSelectorAlertModal.php")); ?>
<?php if($data['can_use_ads_action']): ?>
    <?php write_html($this->parseTemplate("segment/SegmentAdsActionModal.php")); ?>
    <?php write_html($this->parseTemplate('ads/AddAdsAccountModal.php', array('callback_url' => Util::rewriteUrl('admin-segment','segment_list')))) ?>
<?php endif; ?>

<?php $script = array('admin-segment/SegmentListService') ?>
<?php $params = array_merge($data['pageStatus'], array('script' => $script)) ?>

<?php if($this->params['showModal'] == 'ads_action'): ?>
    <script>
        jQuery(function($){
            var target_data = $('.jsSContainerList input').serialize();
            SegmentListService.loadSegmentAdsAction(target_data);
            Brandco.unit.openModal('#segmentAdsActionModal');
        });
    </script>
<?php endif;?>

<?php write_html($this->parseTemplate("BrandcoFooter.php", $params)); ?>