<?php write_html(aafwWidgets::getInstance()->loadWidget("BrandcoHeader")->render($data["pageStatus"])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget("BrandcoAccountHeader")->render($data["pageStatus"])) ?>

    <article>
        <h1 class="hd1">コメント機能</h1>
        <?php if (!Util::isNullOrEmpty($data['comment_plugin'])): ?>
            <h2 class="hd2"><?php assign($data['comment_plugin']->title) ?></h2>
        <?php endif ?>
        <section class="commentPluginWrap">
            <?php write_html($this->csrf_tag()); ?>
            <?php write_html($this->formHidden('comment_plugin_id', $data['comment_plugin']->id)) ?>

            <div class="itemsSortingDetail">
                <dl class="column">
                    <dt>公開ステータス</dt>
                    <dd>
                        <?php write_html($this->formRadio('status', $data['status'], array('class' => 'jsStatusSearchCondition'), CommentUserRelation::$comment_user_relation_status_options)) ?>
                        <span class="option jsDiscardFlgSearchCondition" style="display: none;"><label><?php write_html($this->formCheckBox('discard_flg', array(), array(), array(CommentUserRelation::DISCARD_FLG_ON => 'ユーザーによって削除された投稿'))) ?></label></span></dd>
                    <dt>期間</dt>
                    <dd>
                        <?php write_html($this->formText('from_date','',array('placeholder' => '年/月/日', 'class' => 'jsDate inputDate')))?>
                        <span class="dash">〜</span>
                        <?php write_html($this->formText('to_date','',array('placeholder' => '年/月/日', 'class' => 'jsDate inputDate')))?>
                    </dd>
                </dl>
                <dl class="column">
                    <dt>ニックネーム</dt>
                    <dd><?php write_html($this->formText('nickname','',array('placeholder' => 'ニックネームを入力')))?></dd>
                    <dt>投稿内容</dt>
                    <dd><?php write_html($this->formText('comment_content','',array('placeholder' => 'キーワード入力')))?></dd>
                </dl>
                <div class="detailSettingWrap jsSettingContWrap">
                    <p class="detailSetting jsSettingContTile close">詳細検索</p>

                    <dl class="detailInner jsSettingContTarget" style="display: none;">
                        <dt>会員No</dt>
                        <dd><?php write_html($this->formTextArea('bur_no', '', array('class' => 'pluralItems jsReplaceLbComma','placeholder' => 'No.'))) ?><small class="supplement1">※カンマ/改行区切りで複数指定可</small></dd>
                        <dt>SNSへのシェア</dt>
                        <dd><?php write_html($this->formRadio('sns_share', $data['sns_share'], array(), CommentUserRelation::$sns_share_options)) ?></dd>
                        <dt>メモ</dt>
                        <dd><?php write_html($this->formRadio('note_status', $data['note_status'], array(), CommentUserRelation::$note_status_options)) ?></dd>
                    </dl>
                </dl>
            <!-- /.detailInner --></div>

                <p class="btnSet"><span class="btn2"><a href="javascript:void(0);" class="small1 jsResetSearchCondition">リセット</a></span><span class="btn1"><a href="javascript:void(0)" class="small1 jsUpdateItemList">適用</a></span></p>
                <!-- /.itemsSortingDetail --></div>

            <div class="pager1 jsListPager">
                <p><strong>件数計算中...</strong></p>
            </div>

            <p class="batchAction">
                <span class="iconCheck3">選択中<span class="jsTargetItemCounter">0</span>件</span>
                <?php write_html($this->formRadio('cur_form_status', $data['cur_form_status'], array(), CommentUserRelation::$comment_user_relation_statuses)) ?>
                <span class="btn3"><a href="javascript:void(0);" class="small1 jsApprovalFormSubmit">適用</a></span>
                <!-- /.batchAction --></p>

            <table class="commentPluginTable1">
                <thead>
                <tr>
                    <th class="check"><input type="checkbox" class="jsItemCheckAll">
                        <!-- /.check --></th>
                    <th class="postData">投稿情報
                        <!-- /.postData --></th>
                    <th class="userData">会員情報
                        <!-- /.userData --></th>
                    <th class="action">いいね/返信
                        <!-- /.action --></th>
                    <th class="share">シェア（友達数）
                        <!-- /.share --></th>
                    <th class="postBody">投稿内容（全て<a href="javascript:void(0);" class="jsAllContentDisplay" data-type="show">開く</a>▼/<a href="javascript:void(0);" class="jsAllContentDisplay" data-type="hide">閉じる</a>▲）
                        <!-- /.postBody --></th>
                    <th class="status">公開ステータス
                        <!-- /.status --></th>
                </tr>
                </thead>
                <tbody class="jsCommentList">
                </tbody>
                <!-- /.commentPluginTable1 --></table>

            <div class="pager1 jsListPager">
                <p><strong>件数計算中...</strong></p>
            </div>

            <div class="commentPluginDownload">
                <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoItemCountList')->render(array('limit' => $data['page_limit']))) ?>
                <p class="btnDownload"><span class="btn1"><a href="javascript:void(0);" data-url="<?php assign(Util::rewriteUrl('admin-comment', 'download_comment_data')) ?>" class="large2 jsDataDownload">CSVダウンロード</a></span></p>
                <!-- /.commentPluginDownload --></div>

            <ul class="pager2">
                <li class="prev"><a href="<?php write_html(Util::rewriteUrl('admin-comment', 'plugin_list')) ?>" class="iconPrev1">プラグイン一覧へ</a></li>
                <!-- /.pager2 --></ul>
            <!-- /.commentPluginWrap --></section>
    </article>

    <div class="modal1 jsModal" id="note_modal">
        <section class="modalCont-medium jsModalCont jsNoteForm">
            <h1>メモを残せます。</h1>
            <p><?php write_html($this->formTextArea('note', "", array('cols' => 70, 'rows' => 5))) ?></p>
            <p class="btnSet">
                <span class="btn2"><a href="#closeModal">キャンセル</a></span>
                <span class="btn3"><a href="javascript:void(0);" class="jsSubmitNoteForm">保存</a></span>
            </p>
        </section>
        <!-- /.modal1 --></div>

<link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css'))?>">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>
<script src="<?php assign($this->setVersion('/js/ContainedStickyScroll/jquery-contained-sticky-scroll-min.js')) ?>"></script>

<?php $script = array('admin-comment/CommentListService') ?>
<?php $params = array_merge($data['pageStatus'], array('script' => $script)) ?>
<?php write_html($this->parseTemplate("BrandcoFooter.php", $params)); ?>