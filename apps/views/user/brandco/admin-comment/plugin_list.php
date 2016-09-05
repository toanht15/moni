<?php write_html(aafwWidgets::getInstance()->loadWidget("BrandcoHeader")->render($data["pageStatus"])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget("BrandcoAccountHeader")->render($data["pageStatus"])) ?>

    <article>
        <h1 class="hd1">コメント機能</h1>
        <section class="commentPluginWrap">
            <h2 class="hd2">コメントプラグイン新規作成</h2>
            <p class="btn1"><a href="<?php assign(Util::rewriteUrl('admin-blog','create_static_html_entry_form')) ?>" class="middle2">モニプラに作成
                    <span class="iconHelp"><span class="textBalloon1"><span>モニプラのページに埋め込みで作成します。</span>
                            <!-- /.textBalloon1 --></span>
                        <!-- /.iconHelp --></span></a></p>
            <p class="btn1"><a href="<?php assign(Util::rewriteUrl('admin-comment', 'create_comment_plugin')) ?>" class="middle2">外部に作成
                    <span class="iconHelp"><span class="textBalloon1"><span>任意に指定した外部に作成します。</span>
                            <!-- /.textBalloon1 --></span>
                        <!-- /.iconHelp --></span></a></p>
            <!-- /.commentPluginWrap --></section>

        <h2 class="hd2">コメントプラグイン一覧<span class="subLink">（<a href="<?php write_html(Util::rewriteUrl('admin-comment', 'comment_list')) ?>">コメントをまとめて見る</a>）</span></h2>
        <section class="commentPluginWrap">
            <div class="itemsSorting">
                <p class="itemType">
                    <?php write_html($this->formRadio('type', $data['type'], array('class' => 'jsUpdateItemList'), CommentPlugin::$comment_plugin_type_options)) ?>
                    <!-- /.itemType --></p>

                <p class="itemOrder">
                    並べ替え
                    <?php write_html($this->formSelect('order_type', $data['order_type'], array('class' => 'jsApplySearchCondition'), CommentPlugin::$order_type_label)) ?>
                    <!-- /.itemOrder --></p>
                <!-- /.itemsSorting --></div>

            <div class="pager1 jsListPager">
                <p><strong>件数計算中...</strong></p>
            </div>

            <table class="commentPlugin1">
                <thead>
                <tr>
                    <th>プラグイン名</th>
                    <th>コメント数</th>
                    <th>シェア数（リーチ数<span class="iconHelp"><span class="textBalloon1"><span>シェアしたユーザの友達数の合計</span>
                            <!-- /.textBalloon1 --></span>
                        <!-- /.iconHelp --></span>）
                    </th>
                    <th>設定</th>
                    <th>設置場所</th>
                </tr>
                </thead>
                <tbody class="jsCommentPluginList">
                    </tbody>
                <!-- /.commentPlugin1 --></table>

            <div class="pager1 jsListPager">
                <p><strong>件数計算中...</strong></p>
            </div>

            <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoItemCountList')->render(array('limit' => $data['page_limit']))) ?>
        </section>
    </article>

<?php $script = array('admin-comment/CommentPluginListService') ?>
<?php $params = array_merge($data['pageStatus'], array('script' => $script)) ?>
<?php write_html($this->parseTemplate("BrandcoFooter.php", $params)); ?>