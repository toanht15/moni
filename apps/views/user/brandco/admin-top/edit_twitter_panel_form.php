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
		<header class="innerTW_small">
			<h1>Twitterパネル編集</h1>
		</header>
		<section class="modalInner-cont">
			<div class="panelContEdit">
				<form action="#">
					<table>
						<tbody>
                            <tr>
                                <th>SNSリンク</th>
                                <td><?php assign($this->cutLongText($data['entry']->link, 50)); ?></td>
                            </tr>
                            <tr>
                                <th>パネルリンク</th>
                                <td><?php assign($page_link); ?><br>
                                    <?php write_html($this->formCheckbox('target_type', array($this->getActionFormValue('target_type')), array(), array(TwitterEntry::TARGET_TYPE_BLANK => '新しいウィンドウで開く'))) ?>
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
								<th colspan="2">投稿内容<small class="textLimit">（最大300文字）</small></th>
							</tr>
							<tr>
                                <td colspan="2"><textarea cols="40" rows="4" disabled="disabled"><?php assign(json_decode($data['entry']->extra_data)->text); ?></textarea></td>
							</tr>
						</tbody>
					</table>
				</form>

			</div>
		<div class="panelContPreview">
        <p class="previeTitle">パネルプレビュー</p>

        <section class="contBoxMain-tw">
          <div class="contInner">
			  <div class="contWrap">
				  <p class="contText">
					  <label id='text_preview'>
						  <?php write_html($this->nl2brAndHtmlspecialchars(json_decode($data['entry']->extra_data)->text))?>
						  <img id="image_preview" <?php if(!$data['entry']->image_url) write_html('style="display:none"')?> src="<?php assign($data['entry']->image_url) ?>" alt="">
					  </label>
				  </p>
				  <!-- /.contWrap --></div>
		  </div>

			<div class="nav">
				<ul class="twActions">
					<li><a href="javascript:void(0);" class="twFollow">フォローする</a></li>
					<li><a href="javascript:void(0);" class="twReply">リプライ</a></li>
					<li><a href="javascript:void(0);" class="twRetweet">リツイート</a></li>
					<li><a href="javascript:void(0);" class="twFavo">お気に入り</a></li>
					<!-- /.twActions --></ul>
			</div>

          <footer>
			  <div>
				  <p class="postType"><img src="<?php assign($data['picture_url'])?>" width="28" height="28" alt=""><span><span><?php assign($data['twitterName'].'@'.$data['screenName'])?></span></span>
				  <p class="timeLogo"><small><span class="iconTW2_2">Twitter</span></small></p>
				  </p>
			  </div>
          </footer>
        <!-- /.contBoxMain-tw--></section>

      </div>
		</section>
		<footer>
			<p class="btnSet">
				<?php 
				if($params['from'] == 'top'){
					$prev_page = '#closeModalFrame';
				}
				else{
					$prev_page = Util::rewriteUrl ( 'admin-top', 'edit_panel_list', array ($data['brandSocialAccountId']));
				}
				?>
				<span class="btn2"><a href="<?php assign($prev_page) ?>">キャンセル</a></span>
				<span class="btn3"><a href="javascript:void()" id='submitButton' >保存</a></span>
			</p>
		</footer>
</article>
	</form>

<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script'=>array('EditPanelService')))) ?>
