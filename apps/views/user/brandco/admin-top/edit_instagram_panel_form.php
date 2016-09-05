<?php
    write_html($this->parseTemplate('BrandcoModalHeader.php', array('brand' => $data ['brand'])));
    $page_link = Util::rewriteUrl('sns', 'detail', array($data['brandSocialAccountId'], $data['entryId']));
?>
    <form id="frmPanel" name="frmPanel" action="<?php assign(Util::rewriteUrl('admin-top', 'edit_panel')); ?>" method="POST" enctype="multipart/form-data">
        <article class="modalInner-large">
            <?php write_html($this->formHidden('brandSocialAccountId', $data['brandSocialAccountId'])); ?>
            <?php write_html($this->formHidden('entryId', $data['entryId'])); ?>
            <?php write_html($this->formHidden('type', $data['type'])); ?>
            <?php write_html($this->formHidden('from', $params['from'])); ?>
            <?php write_html($this->csrf_tag()); ?>

            <header class="innerIG-small">
                <h1>Instagramパネル編集</h1>
            </header>
            <section class="modalInner-cont">
                <div class="panelContEdit">
                    <form action="#">
                        <table>
                            <tbody>
                            <tr>
                                <th>SNSリンク</th>
                                <td><?php assign($this->cutLongText($data['entry']->link, 50)); ?><br>
                                </td>
                            </tr>
                            <tr>
                                <th>表示</th>
                                <td>
                                    <a href="javascript:void(0)" class="switch<?php if (!$data['entry']->hidden_flg): ?> on<?php else: ?> off<?php endif; ?>">
                                            <span class="switchInner">
                                                <span class="selectON">ON</span>
                                                <span class="selectOFF">OFF</span>
                                            </span>
                                    </a>
                                </td>
                                <input id="display" name="display" value="<?php assign($data['entry']->hidden_flg); ?>" type="hidden">
                            </tr>
                            <tr>
                                <th colspan="2">投稿内容<small class="textLimit">（最大300文字）</small></th>
                            </tr>
                            <tr>
                                <td colspan="2"><textarea cols="40" rows="4" disabled="disabled"><?php assign(json_decode($data['entry']->extra_data)->caption->text); ?></textarea></td>
                            </tr>
                            <tr>
                                <th colspan="2">コメント<small class="textLimit">（最大300文字）</small></th>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <?php write_html($this->formTextarea('panel_comment', PHPParser::ACTION_FORM, array('class' => 'AWid200 AP3', 'maxlength'=>'300', 'cols' => '40', 'rows' => '4', 'id' => 'panel_comment'))); ?>
                                    <?php if ($this->ActionError && !$this->ActionError->isValid('panel_comment')): ?>
                                        <p class="attention1"><?php assign($this->ActionError->getMessage('panel_comment')) ?></p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="panelContPreview">
                    <p class="previeTitle">パネルプレビュー</p>
                    <section class="contBoxMain-ig">
                        <div class="contInner">
                            <div class="contWrap">
                                <img id="image_preview" <?php if (!$data['entry']->image_url) write_html('style="display:none"') ?> src="<?php assign($data['entry']->image_url) ?>">
                                <p class="contText">
                                    <span id='text_preview' style="display:<?php assign(json_decode($data['entry']->extra_data)->caption->text ? '' : 'none') ?>">
                                        <?php assign(json_decode($data['entry']->extra_data)->caption->text); ?>
                                    </span>
                                </p>
                                <p class="panelComment" style="display:<?php assign($data['entry']->panel_comment ? '' : 'none'); ?>">
                                <span id="comment_preview">
                                    <?php write_html($this->nl2brAndHtmlspecialchars($data['entry']->panel_comment)); ?>
                                </span>
                                </p>
                                <p id='title_preview' class="postType"><?php assign($data['pageName']) ?></p>
                            </div>
                        </div>
                        <!-- /.contBoxMain-->
                    </section>
                </div>
            </section>
            <footer>
                <p class="btnSet">
                    <?php if ($params['from'] == 'top') {
                        $prev_page = '#closeModalFrame';
                    } else {
                        $prev_page = Util::rewriteUrl('admin-top', 'edit_panel_list', array($data['brandSocialAccountId']));
                    }
                    ?>
                    <span class="btn2"><a href="<?php assign($prev_page) ?>" id="cancelButton">キャンセル</a></span>
                    <span class="btn3"><a href="javascript:void(0);" id="submitButton">保存</a></span>
                </p>
            </footer>
        </article>
    </form>
<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script' => array('EditPanelService')))) ?>