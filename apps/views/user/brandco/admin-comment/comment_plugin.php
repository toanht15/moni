<?php write_html(aafwWidgets::getInstance()->loadWidget("BrandcoHeader")->render($data["pageStatus"])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget("BrandcoAccountHeader")->render($data["pageStatus"])) ?>

    <article>
        <h1 class="hd1">コメント機能設定</h1>
        <section class="commentPluginWrap">
            <div class="cf">
                <form method="POST" action="<?php write_html(Util::rewriteUrl('admin-comment', 'save_comment_plugin')) ?>" name="save_comment_plugin_form">
                    <?php write_html($this->csrf_tag()); ?>
                    <?php if ($this->getActionFormValue('id')): ?>
                        <?php write_html($this->formHidden('id', $this->getActionFormValue('id'))) ?>
                    <?php endif ?>

                    <dl class="commentPluginEdit">
                        <dt>
                            <label for="input1" class="require1">名前</label>
                            <span class="iconHelp">
                                <span class="text">ヘルプ</span>
                                <span class="textBalloon1">
                                    <span>プラグイン一覧でプラグインを識別できる名称を<br>入れてください。</span>
                                    <!-- /.textBalloon1 --></span>
                                <!-- /.iconHelp --></span>
                        </dt>
                        <dd>
                            <?php write_html($this->formText('title', PHPParser::ACTION_FORM, array('placeholder' => '管理用の名称（一覧などでの識別用）'))) ?>
                            <?php if ($this->ActionError && !$this->ActionError->isValid('title')): ?>
                                <p class="iconError1"><?php assign($this->ActionError->getMessage('title')) ?></p>
                            <?php endif; ?>
                        </dd>
                        <dt>ステータス
                            <span class="iconHelp">
                                <span class="text">ヘルプ</span>
                                    <span class="textBalloon1">
                                        <span>ある期間の前後に非表示化したい場合は非表示を選択してください。</span>
                                        <!-- /.textBalloon1 --></span>
                                <!-- /.iconHelp --></span>
                        </dt>
                        <dd>
                            <?php write_html($this->formRadio('status', PHPParser::ACTION_FORM, array('class' => 'jsCommentPluginStatus'), CommentPlugin::$comment_plugin_status_options)); ?>
                        </dd>
                        <dt>投稿時のシェア</dt>
                        <dd>
                            <?php write_html($this->formCheckbox('share_sns_list', PHPParser::ACTION_FORM, array('class' => 'jsShareSNS'), CommentPluginShareSetting::$comment_plugin_share_settings)); ?>
                            <p class="shareUrl">シェア用URL<span class="iconHelp"><span class="text">ヘルプ</span><span class="textBalloon1">
                                        <span>
                                            SNSにシェアされるURLを統一したい場合に指定して下さい。<br>
                                            スマホ用URLや広告用(パラメータ付き)URLなどがシェアされた場合、<br>
                                            SNS側で別々のURLとして判別されるのを防ぐため。<br>
                                        </span>
                                        <!-- /.textBalloon1 --></span>
                                    <!-- /.iconHelp --></span>
                                <?php write_html($this->formText('share_url', PHPParser::ACTION_FORM)) ?></p>
                            <?php if ($this->ActionError && !$this->ActionError->isValid('share_url')): ?>
                                <p class="iconError1"><?php assign($this->ActionError->getMessage('share_url')) ?></p>
                            <?php endif; ?>
                        </dd>
                        <!-- /.commentPluginEdit --></dl>

                    <div class="embedTag">
                        <p>埋め込みタグ</p>
                        <?php if (Util::isNullOrEmpty($data['comment_plugin']->plugin_code)): ?>
                            <textarea disabled="disabled" cols="30" rows="5">設定を作成・保存すると、埋め込み用のコードが表示されます。</textarea>
                        <?php else: ?>
                            <textarea class="jsPluginScript" readonly="" cols="30" rows="5"><?php assign($data['plugin_script']) ?></textarea>
                        <?php endif ?>
                        <!-- /.cf --></div>

                    <?php $has_text = !Util::isNullOrEmpty($this->getActionFormValue('free_text')) || !Util::isNullOrEmpty($this->getActionFormValue('footer_text')); ?>
                    <div class="freeArea jsSettingContWrap">
                        <div class="cf">
                            <dl class="commentPluginEdit">
                                <dt>フリーエリア
                                    <span class="iconHelp">
                                        <span class="text">ヘルプ</span>
                                    <span class="textBalloon1">
                                          <span>
                                          コメント機能の上下に自由に画像、テキストを<br>入れる事ができます。
                                          </span>
                                    <!-- /.textBalloon1 --></span>
                                    <!-- /.iconHelp --></span>
                                </dt>
                                <dd class="freeAreaAction">
                                    <p class="freeAreaSetting jsSettingContTile <?php if (!$has_text) assign('close') ?>">設定する</p>
                                    <p class="previewAction jsPreviewPlugin"><a href="javascript:void(0);">プレビュー</a></p>
                                </dd>
                            </dl>
                            <!-- /.cf --></div>
                        <div class="freeAreaEdit jsSettingContTarget" id="display" <?php if (!$has_text): ?>style="display: none;"<?php endif ?>
                             data-uploadurl='<?php assign(Util::rewriteUrl('admin-top', 'ckeditor_upload_file')) ?>'
                             data-listurl='<?php assign(Util::rewriteUrl('admin-blog', 'file_list', null, array('f_id' => BrandUploadFile::POPUP_FROM_STATIC_HTML_ENTRY))) ?>'>
                            <?php write_html($this->formTextArea('free_text', PHPParser::ACTION_FORM, array('cols' => '40', 'rows' => '4', 'width' => '960', 'height' => '580'))) ?>
                            <p><a href="javascript:void(0);" class="linkAdd" id="add_footer_text">コメントプラグインの下にフリーエリアを追加</a></p>
                            <?php write_html($this->formTextArea('footer_text', PHPParser::ACTION_FORM, array('cols' => '40', 'rows' => '4', 'width' => '960', 'height' => '580'))) ?>
                        </div>
                        <!-- /.freeArea --></div>
                </form>

            <p class="btnSet">
                <span class="btn2"><a href="<?php write_html(Util::rewriteUrl('admin-comment', 'plugin_list')) ?>">キャンセル</a></span>
                <span class="btn3"><a href="javascript:void(0);" id="submitPlugin"><?php assign($data['mode'] === 'create_mode' ? '作成' : '保存') ?></a></span>
            </p>
            <ul class="pager2">
                <li class="prev"><a href="<?php write_html(Util::rewriteUrl('admin-comment', 'plugin_list')) ?>" class="iconPrev1">プラグイン一覧へ</a></li>
                <!-- /.pager2 --></ul>
            <!-- /.commentPluginWrap --></section>
    </article>

<script type="text/javascript" src="<?php assign($this->setVersion('/js/zeroclipboard/ZeroClipboard.min.js')) ?>"></script>
<script type="text/javascript" src="<?php assign($this->setVersion('/ckeditor/ckeditor.js')) ?>"></script>

<?php $param = array_merge($data['pageStatus'], array('script' => array('admin-comment/CommentPluginService'))) ?>
<?php write_html($this->parseTemplate("BrandcoFooter.php", $param)) ?>