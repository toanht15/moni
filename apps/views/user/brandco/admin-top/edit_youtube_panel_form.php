<?php
    write_html($this->parseTemplate('BrandcoModalHeader.php', array('brand' => $data ['brand'])));
    $page_link = Util::rewriteUrl('sns', 'detail', array($data['brandSocialAccountId'], $data['entryId']));
?>

<form id="frmPanel" name="frmPanel"
		action="<?php assign(Util::rewriteUrl( 'admin-top', 'edit_panel' )); ?>"
		method="POST" enctype="multipart/form-data">
<article class="modalInner-large">
    <?php write_html( $this->formHidden( 'brandSocialAccountId', $data['brandSocialAccountId'])); ?>
    <?php write_html( $this->formHidden( 'entryId', $data['entryId'])); ?>
    <?php write_html( $this->formHidden( 'type', $data['type'])); ?>
    <?php write_html( $this->formHidden( 'from', $params['from']));?>
    <?php write_html($this->csrf_tag()); ?>
    <header class="innerYT-small">
        <h1>Youtubeパネル編集</h1>
    </header>
    <section class="modalInner-cont">
        <div class="panelContEdit">
            <form action="#">
                <table>
                    <tbody>
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
                            <th>SNSリンク</th>
                            <td><?php assign($this->cutLongText($data['entry']->link, 50)); ?><br>
                                <?php write_html($this->formCheckbox('target_type', array($this->getActionFormValue('target_type')), array(), array(YoutubeEntry::TARGET_TYPE_BLANK => '新しいウィンドウで開く'))) ?>
                            </td>
                        </tr>
                        <tr>
                            <th>パネルリンク</th>
                            <td><?php assign($page_link); ?></td>
                        </tr>
                        <tr>
                            <th colspan="2">投稿内容<small class="textLimit">（最大300文字）</small></th>
                        </tr>
                        <tr>
                            <td colspan="2">
                            <?php $disabled = $this->brand->isViewFullText() ? 'disabled' : '' ?>
                            <?php write_html( $this->formTextarea( 'panel_text', PHPParser::ACTION_FORM, array( 'class' =>'AWid200 AP3', 'maxlength'=>'300','cols'=>'40','rows'=>'4','id'=>'panel_text', $disabled => $disabled ))); ?>
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
            <section class="contBoxMain-yt">
                <div class="videoInner">
                    <iframe src="https://www.youtube.com/embed/<?php assign($data['entry']->object_id)?>?rel=0" frameborder="0" allowfullscreen></iframe>
                </div>
                <div class="contInner">
                    <div class="contWrap">
                        <p class="contText">
                            <span id='text_preview' style="display:<?php assign($data['entry']->panel_text ? '': 'none') ?>"><?php write_html($this->nl2brAndHtmlspecialchars($data['entry']->panel_text))?></span>
                        </p>

                        <p class="postType"><?php assign($data['pageName'])?></p>
                    </div>
                </div>
                <!-- /.contBoxMain--></section>

      <!-- /.panelContPreview --></div>
    </section>
    <footer>
        <p class="btnSet">
        <?php if($params['from'] == 'top'){
            $prev_page = '#closeModalFrame';
        } else {
            $prev_page = Util::rewriteUrl ( 'admin-top', 'edit_panel_list', array ($data['brandSocialAccountId']));
        }
        ?>
            <span class="btn2"><a class="test" href="<?php assign($prev_page) ?>">キャンセル</a></span>
            <span class="btn3"><a href="javascript:void();" id='submitButton'>保存</a></span>
        </p>
    </footer>
</article>
</form>
<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script'=>array('EditPanelService')))) ?>