<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array('brand' => $data['brand'],))) ?>
<article class="modalInner-large">
    <header><h1>フォトパネル編集</h1></header>
    <section class="modalInner-cont">
        <div class="panelContEdit">
            <form id="frmEntry" name="frmPanel" action="<?php assign(Util::rewriteUrl( 'admin-top', 'edit_photo_entry' )); ?>" method="POST">
                <?php write_html( $this->formHidden( 'entry_id', $data['entry_id'])); ?>
                <?php write_html( $this->formHidden( 'from', $params['from']));?>
                <?php write_html($this->csrf_tag()); ?>
                <table>
                    <tbody>
                    <tr>
                        <th>ページタイトル</th>
                        <td>
                            <?php write_html( $this->formText( 'photo_title', $data['photo_user']->photo_title, array( 'class' =>'AWid200 AP3', 'maxlength'=>'50','id'=>'panel_title', 'disabled' => 'disabled'))); ?>
                            <small class="textLimit"></small>
                        </td>
                    </tr>
                    <tr>
                        <th>パーマリンク</th>
                        <td>
                            <?php write_html( $this->formText( 'link', Util::rewriteUrl('photo', 'detail', array($data['entry']->id)), array( 'class' =>'AWid200 AP3', 'maxlength'=>'255', 'disabled' => 'disabled'))); ?>
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
                        <input id="display" name="display" value="<?php assign((!$data['entry']->hidden_flg)? '0': '1'); ?>" type="hidden">
                    </tr>
                    <tr>
                        <th colspan="2">ページ内テキスト</th>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <?php write_html( $this->formTextarea( 'body', $data['photo_user']->photo_comment, array( 'class' =>'AWid200 AP3', 'maxlength'=>'300','cols'=>'40','rows'=>'4','id'=>'panel_text', 'disabled' => 'disabled' ))); ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="panelContPreview">
            <p class="previeTitle">パネルプレビュー</p>
            <section class="jsPanel contBoxMain-photo">
                <a href="#" class="contInner">
                    <div class="contWrap">
                        <img <?php if(!$data['photo_user']->photo_url) write_html('style="display:none"')?> src="<?php assign($data['photo_user']->photo_url) ?>">
                        <p class="contText">
                            <span><?php assign($data['photo_user']->photo_title); ?></span>
                        </p>
                        <p class="postDate">
                            <img src="<?php assign($data['user']->profile_image_url) ?>" width="20" height="20" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';" alt="<?php assign($data['user']->name) ?>">
                            <?php assign(date('Y/m/d', strtotime($data['entry']->pub_date))); ?>
                        </p>

                        <p class="postType"><?php assign($data['cp']->getTitle()); ?></p>
                    </div>
                    <!-- /.contInner --></a>
                <!-- /.contBoxMain-->
            </section>
        </div>
    </section>
    <footer>
        <p class="btnSet">
            <?php if($params['from'] == 'top'){
                $prev_page = '#closeModalFrame';
            } else {
                $prev_page = Util::rewriteUrl ( 'admin-top', 'photo_entries', array(), array('p'=>$params['p']));
            }
            ?>
            <span class="btn2"><a href="<?php assign($prev_page) ?>">キャンセル</a></span>
            <span class="btn3"><a href="javascript:void(0);" id="submitButton">保存</a></span></p>
    </footer>
</article>
<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script'=>array('EditPanelService')))) ?>