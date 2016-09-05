<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array('brand' => $data['brand'],))) ?>
<article class="modalInner-large">
    <header class="innerLI-small"><h1>RSSパネル編集</h1></header>
    <section class="modalInner-cont">
        <div class="panelContEdit">
            <form id="frmEntry" name="frmPanel"
                  action="<?php assign(Util::rewriteUrl( 'admin-top', 'edit_rss_entry' )); ?>"
                  method="POST" enctype="multipart/form-data">
                <?php write_html( $this->formHidden( 'entryId', $data['entryId'])); ?>
                <?php write_html( $this->formHidden( 'streamId', $data['entry']->getRssStream()->id)); ?>
                <?php write_html( $this->formHidden( 'from', $params['from']));?>
                <?php write_html($this->csrf_tag()); ?>
                <table>
                    <tbody>
                    <tr>
                        <th rowspan="2">カバー画像</th>
                        <td>
                            <label for="panelFileUpload"><input type="radio"name="panelImage" class="jsPanelImage" value="imageUpload"id="panelFileUpload">ファイルアップロード</label>
                            <input type="file"class="jsPanelImageInput" disabled="disabled" name="panel_image">
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('panel_image')): ?>
                                <p class="attention1"><?php assign ( $this->ActionError->getMessage('panel_image') )?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="panelImageURL"><input type="radio" name="panelImage" class="jsPanelImage" value="imageURL" checked="checked" id="panelImageURL">画像URL</label>
                            <?php write_html( $this->formText('image_url',PHPParser::ACTION_FORM, array( 'class' =>'AWid200 AP3', 'maxlength'=>'255' ,'id'=>'imageUrlInput'))); ?>
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('image_url')): ?>
                                <p class="attention1"><?php assign ( $this->ActionError->getMessage('image_url') )?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>パネルリンク</th>
                        <td>
                            <?php write_html( $this->formText( 'link', PHPParser::ACTION_FORM, array( 'class' =>'AWid200 AP3', 'maxlength'=>'255' ))); ?>
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('link')): ?>
                                <p class="attention1"><?php assign ( $this->ActionError->getMessage('link') )?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>表示</th>
                        <td>
                            <a href="javascript:void(0)" class="switch<?php if(!$data['entry']->hidden_flg):?> on<?php else:?> off<?php endif; ?>">
                                <span class="switchInner">
                                    <span class="selectON">ON</span>
                                    <span class="selectOFF">OFF</span>
                                </span>
                            </a>
                        </td>
                        <input id="display" name="display" value="<?php assign($data['entry']->hidden_flg); ?>" type="hidden">
                    </tr>
                    <tr>
                        <th colspan="2">ページ内テキスト<small class="textLimit">（最大300文字）</small></th>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <?php $disabled = $this->brand->isViewFullText() ? 'disabled' : '' ?>
                            <?php write_html( $this->formTextarea( 'panel_text', PHPParser::ACTION_FORM, array( 'class' =>'AWid200 AP3', 'maxlength'=>'300','cols'=>'40','rows'=>'4', 'id'=>'panel_text', $disabled => $disabled ))); ?>
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('panel_text')): ?>
                                <p class="attention1"><?php assign ( $this->ActionError->getMessage('panel_text') )?></p>
                            <?php endif; ?>
                            <?php if ($this->brand->isViewFullText()): ?>
                                <small>※ 全文表示モードの為編集できません</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="panelContPreview">
            <p class="previeTitle">パネルプレビュー</p>
            <section class="jsPanel contBoxMain">
                <div class="contInner">
                    <div class="contWrap">
                        <p class="contText">
                            <img id="image_preview" <?php if(!$data['entry']->image_url) write_html('style="display:none"')?> src="<?php assign($data['entry']->image_url) ?>"><span id='text_preview' style="display:<?php assign($data['entry']->panel_text ? '': 'none') ?>">
                            <?php write_html($this->nl2brAndHtmlspecialchars($data['entry']->panel_text))?></span>
                        </p>
                        <p class="postType" id='title_preview'><?php assign($data['entry']->getRssStream()->title)?></p>
                    </div>
                </div>
            <!-- /.contBoxMain--></section>
        </div>
    </section>
    <footer>
        <p class="btnSet">
            <?php if($params['from'] == 'top'){$prev_page = '#closeModalFrame';
            } else {$prev_page = Util::rewriteUrl ( 'admin-top', 'rss_entries', array($data['entry']->getRssStream()->id),array('p'=>$params['p']));} ?>
            <span class="btn2"><a href="<?php assign($prev_page) ?>">キャンセル</a></span>
            <span class="btn3"><a href="javascript:void();" id="submitButton">保存</a></span></p>
    </footer>
</article>
<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script'=>array('EditPanelService')))) ?>