<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array('brand' => $data['brand'],))) ?>
<article class="modalInner-large">
    <header class="innerLI-small"><h1>ページパネル編集</h1></header>
    <section class="modalInner-cont">
        <div class="panelContEdit">
            <form id="frmEntry" name="frmPanel" action="<?php assign(Util::rewriteUrl( 'admin-top', 'edit_page_entry' )); ?>" method="POST" enctype="multipart/form-data">
                <?php write_html( $this->formHidden( 'entry_id', $data['entry_id'])); ?>
                <?php write_html( $this->formHidden( 'from', $params['from']));?>
                <?php write_html($this->csrf_tag()); ?>
                <table>
                    <tbody>
                    <tr>
                        <th>ページタイトル</th>
                        <td>
                            <?php write_html( $this->formText( 'title', $data['static_html_entry']->title, array( 'class' =>'AWid200 AP3', 'maxlength'=>'60','id'=>'panel_title', 'disabled' => 'disabled'))); ?>
                            <small class="textLimit"></small>
                        </td>
                    </tr>
                    <th rowspan="2">パネル内画像</th>
                    <td>
                        <label for="panelFileUpload"><input type="radio" name="panelImage" class="jsPanelImage" value="imageUpload" id="panelFileUpload">ファイルアップロード</label>
                        <input type="file" class="jsPanelImageInput" disabled="disabled" name="panel_image">
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('panel_image')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('panel_image') )?></p>
                        <?php endif; ?>
                    </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="panelImageURL"><input type="radio" name="panelImage" class="jsPanelImage" value="imageURL" checked="checked" id="panelImageURL">画像URL</label>
                            <?php write_html( $this->formText( 'image_url', PHPParser::ACTION_FORM, array( 'class' =>'AWid200 AP3', 'maxlength'=>'255' ,'id'=>'imageUrlInput'))); ?>
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('image_url')): ?>
                                <p class="attention1"><?php assign ( $this->ActionError->getMessage('image_url') )?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>パーマリンク</th>
                        <td>
                            <?php write_html( $this->formText( 'link', $data['static_html_entry']->getUrl(), array( 'class' =>'AWid200 AP3', 'maxlength'=>'255', 'disabled' => 'disabled'))); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>公開日</th>
                        <td><?php $pub_date = date_create($data['entry']->pub_date);
                            assign(date_format($pub_date, 'Y年m月d日 H:i')); ?></td>
                    </tr>
                    <tr>
                        <th>表示</th>
                        <td>
                            <a href="javascript:void(0)" class="switch<?php if(!$data['entry']->top_hidden_flg):?> on<?php else:?> off<?php endif; ?>">
                                <span class="switchInner">
                                    <span class="selectON">ON</span>
                                    <span class="selectOFF">OFF</span>
                                </span>
                            </a>
                        </td>
                        <input id="display" name="display" value="<?php assign((!$data['entry']->top_hidden_flg)? '0': '1'); ?>" type="hidden">
                    </tr>
                    <tr>
                        <th colspan="2">ページ内テキスト</th>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <?php write_html( $this->formTextarea( 'panel_text', PHPParser::ACTION_FORM, array( 'class' =>'AWid200 AP3', 'maxlength'=>'300','cols'=>'40','rows'=>'4','id'=>'panel_text' ))); ?>
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('panel_text')): ?>
                                <p class="attention1"><?php assign ( $this->ActionError->getMessage('panel_text') )?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="panelContPreview">
            <p class="previeTitle">パネルプレビュー</p>
            <section class="jsPanel contBoxMain-cms">
                <div class="contInner">
                    <div class="contWrap">
                        <img id="image_preview" <?php if(!$data['entry']->image_url) write_html('style="display:none"')?> src="<?php assign($data['entry']->image_url) ?>" height="180">
                        <p class="contTitle" id='title_preview'><?php assign($data['static_html_entry']->title); ?></p>
                        <p class="contText">
                            <span id='text_preview' style="display:<?php assign($data['entry']->panel_text ? '': 'none') ?>"><?php write_html($this->nl2brAndHtmlspecialchars($data['entry']->panel_text))?></span>
                        </p>
                    </div>
                </div>
                <p class="postType"><?php assign($data['category']->name)?></p>
                <!-- /.contBoxMain--></section>
        </div>
    </section>
    <footer>
        <p class="btnSet">
            <?php if($params['from'] == 'top'){
                $prev_page = '#closeModalFrame';
            } else {
                $prev_page = Util::rewriteUrl ( 'admin-top', 'page_entries', array(),array('p'=>$params['p']));
            }
            ?>
            <span class="btn2"><a href="<?php assign($prev_page) ?>">キャンセル</a></span>
            <span class="btn3"><a href="javascript:void(0);" id="submitButton">保存</a></span></p>
    </footer>
</article>
<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script'=>array('EditPanelService')))) ?>