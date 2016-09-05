<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array (
		'brand' => $data ['brand']
) ) )?>
<?php
if($data['stream']->getType() == StreamService::STREAM_TYPE_TWITTER):
    $inner_class = 'innerTW';
elseif($data['stream']->getType() == StreamService::STREAM_TYPE_FACEBOOK):
    $inner_class = 'innerFB';
elseif($data['stream']->getType() == StreamService::STREAM_TYPE_YOUTUBE):
    $inner_class = 'innerYT';
elseif($data['stream']->getType() == StreamService::STREAM_TYPE_INSTAGRAM):
    $inner_class = 'innerIG';
endif;
?>
<article class="modalInner-large">
	<header class="<?php assign($inner_class); ?>">
		<h1>
			<img src="<?php assign($data['image_url'])?>" alt="" width="60" height="60" id="sns_profile_image">パネル管理
		</h1>
		<a href="#" class="openLink jsOpenLink">設定</a>
		<section class="editAccount jsOpenLinkAera">
			<div class="getPostMethod" data-brand-social-account-id="<?php assign($data['brandSocialAccountId'])?>">
				<ul>
					<li><label for="manual" class="getPostManual" data-url="<?php assign(Util::rewriteUrl('admin-top', 'api_change_stream_hidden_flg.json'))?>"><input type="radio" name="getPostMethod"
							id="manual" value="" <?php if($data['stream']->entry_hidden_flg) assign('checked')?>>選択した投稿だけを掲載する（事前検閲）</label></li>
					<li><label for="auto" class="getPostAuto" data-url="<?php assign(Util::rewriteUrl('admin-top', 'api_change_stream_hidden_flg.json'))?>"><input type="radio" name="getPostMethod"
							id="auto" value="" <?php if(!$data['stream']->entry_hidden_flg) assign('checked')?>>最新投稿を自動で掲載する（事後検閲）</label>
                        <span class="autoItemNum jsRadioToggleTarget" <?php if(!$data['stream']->entry_hidden_flg) write_html('style="display: inline-block"')?>>
                        表示上限
                        <select name="display_panel_limit" id="display_panel_limit" data-url="<?php assign(Util::rewriteUrl('admin-top', 'api_change_display_limit.json'))?>">
                            <option value="0" <?php if(!$data['display_panel_limit']) assign('selected') ?>>無制限</option>
                            <?php for($i=1; $i<100; $i++): ?>
                            <option value="<?php assign($i)?>" <?php if($i==$data['display_panel_limit']) assign('selected') ?>><?php assign($i)?></option>
                            <?php endfor; ?>
                        </select>
                      </span>
                    </li>
				</ul>
				<p>
					事前検閲の場合、収集した投稿はすべて「非表示」が初期状態となります。<br>事後検閲の場合、設定した上限数に合わせて「表示」が初期状態となります。
				</p>
			</div>
            <div class="liftingAccount">
                <p>
                    アカウント情報を更新します。<br><span class="btn3"><a href="javascript:void(0)" class="middle1 jsGetProfile" data-sns_account_id="<?php assign($data['brandSocialAccountId']); ?>" data-url="<?php assign(Util::rewriteUrl('admin-top','api_get_brand_sns_account_profile.json'))?>">更新する</a></span>
                </p>
            </div>
			<div class="liftingAccount">
				<p>
					連携したSNSを解除します。<br> <span class="btn4"><a href="javascript:void(0)"
						onclick="Brandco.helper.showConfirm('#modal1', '<?php assign('brandSocialAccountId='.$data['brandSocialAccountId'])?>')" class="middle1">解除する</a></span>
				</p>
			</div>
		</section>
	</header>
	<section class="modalInner-cont">
		<section <?php if($data['entries']) write_html('class="editContList"')?>>
        <?php if(!$data['entries']): ?>
            インストールしたソーシャルメディアの投稿を取得するには、「設定」を確認してから、取得ボタンをクリックしてください。
            <?php
                $url='';
                if($data['stream']->getType() == StreamService::STREAM_TYPE_TWITTER){
                    $url = Util::rewriteUrl('admin-top','api_get_twitter_user_timeline.json',array($data['brandSocialAccountId']));
                }else if($data['stream']->getType() == StreamService::STREAM_TYPE_FACEBOOK){
                    $url = Util::rewriteUrl('admin-top','api_get_facebook_user_posts.json',array($data['brandSocialAccountId']));
                }else if($data['stream']->getType() == StreamService::STREAM_TYPE_YOUTUBE){
                    $url = Util::rewriteUrl('admin-top','api_get_youtube_videos.json',array($data['brandSocialAccountId']));
                } elseif ($data['stream']->getType() == StreamService::STREAM_TYPE_INSTAGRAM) {
                    $url = Util::rewriteUrl('admin-top', 'api_get_instagram_recent_media.json', array($data['brandSocialAccountId']));
                }
            ?>
            <p class="btnSet">
                <span class="btn3"><a href="javascript:void(0);" id="loadPanel" data-url="<?php assign($url) ?>" data-callbackurl="<?php assign(Util::rewriteUrl('admin-top','edit_panel_list',array($data['brandSocialAccountId']))) ?>" >取得</a></span>
            </p>
        <?php endif; ?>
		<ul>
        <?php foreach($data['entries'] as $entry):?>
          <li class="<?php if($entry->priority_flg == '1') assign('contFixed')?>" id="li<?php assign($entry->id)?>">
					<p class="postText">
							<?php
							if($entry->getStoreName() == 'FacebookEntries')
								$edit_href = Util::rewriteUrl( 'admin-top', 'edit_facebook_panel_form', array($data['brandSocialAccountId'], $entry->id) );
							elseif($entry->getStoreName() == 'TwitterEntries')
								$edit_href = Util::rewriteUrl( 'admin-top', 'edit_twitter_panel_form', array($data['brandSocialAccountId'], $entry->id) );
							elseif($entry->getStoreName() == 'YoutubeEntries')
								$edit_href = Util::rewriteUrl( 'admin-top', 'edit_youtube_panel_form', array($data['brandSocialAccountId'], $entry->id) );
                            elseif ($entry->getStoreName() == 'InstagramEntries')
                                $edit_href = Util::rewriteUrl( 'admin-top', 'edit_instagram_panel_form', array($data['brandSocialAccountId'], $entry->id) );
							?>
						<a href="<?php assign($edit_href) ?>">
							<span class="supplement1"><?php $dateInfo = date_create($entry->pub_date); assign(date_format($dateInfo, 'Y年m月d日 H:i:s'));?></span>
							<?php if($entry->image_url):?><img src="<?php assign($entry->image_url) ?>" width="70" height="70" alt=""><?php endif;?>
                            <?php if($data['stream']->getType() == StreamService::STREAM_TYPE_INSTAGRAM): ?>
                                <span><?php assign($entry->cutLongText(json_decode($entry->extra_data)->caption->text, 185, '...')); ?></span>
                            <?php elseif ($data['stream']->getType() == StreamService::STREAM_TYPE_TWITTER): ?>
                                <span><?php assign($entry->cutLongText(json_decode($entry->extra_data)->text, 185, '...')); ?></span>
                            <?php else: ?>
                                <span><?php write_html($this->nl2brAndHtmlspecialchars($entry->cutLongText($entry->panel_text, 185, '...')))?></span>
                            <?php endif; ?>
						</a>
					</p>
					<p class="action">
			  表示
			  <?php if($entry->hidden_flg):?>
			  <a href="#" class="switch off" id="switch<?php assign($entry->id)?>"
                            data-url = '<?php assign(Util::rewriteUrl('admin-top', 'api_toggle_hidden_panel.json'))?>'
							data-entry='<?php assign(json_encode(array('entryId' => $entry->id, 'brandSocialAccountId' => $data['brandSocialAccountId'], 'brandId'=>$data['brand']->id)))?>'
							data-priority='<?php assign($entry->priority_flg)?>'>
							<span class="switchInner"><span class="selectON">ON</span>
							<span class="selectOFF">OFF</span></span></a>
			  <?php else:?>
			  <a href="#" class="switch on" id="switch<?php assign($entry->id)?>"
                            data-url = '<?php assign(Util::rewriteUrl('admin-top', 'api_toggle_hidden_panel.json'))?>'
							data-entry='<?php assign(json_encode(array('entryId' => $entry->id, 'brandSocialAccountId' => $data['brandSocialAccountId'], 'brandId'=>$data['brand']->id)))?>'
							data-priority='<?php assign($entry->priority_flg)?>'>
							<span class="switchInner"><span class="selectON">ON</span>
							<span class="selectOFF">OFF</span></span></a>
			  <?php endif;?>
              <?php
                  $request_url = Util::rewriteUrl('admin-top', 'api_renew_facebook_entry.json');
                  if($entry->getType() == StreamService::STREAM_TYPE_TWITTER){
                    $request_url = Util::rewriteUrl('admin-top', 'api_renew_twitter_entry.json');
                  }else if($entry->getType() == StreamService::STREAM_TYPE_YOUTUBE){
                    $request_url = Util::rewriteUrl('admin-top','api_renew_youtube_entry.json');
                  } elseif ($entry->getType() == StreamService::STREAM_TYPE_INSTAGRAM) {
                      $request_url = Util::rewriteUrl('admin-top', 'api_renew_instagram_entry.json');
                  }
              ?>
			  <select class="prioritize" name="" id="select<?php assign($entry->id)?>"
                            data-url = '<?php assign(Util::rewriteUrl('admin-top', 'api_toggle_priority_panel.json'))?>'
							data-entry='<?php assign(json_encode(array('entryId' => $entry->id, 'brandSocialAccountId' => $data['brandSocialAccountId'], 'brandId'=>$data['brand']->id)))?>'
							data-editurl = '<?php assign($edit_href)?>'
							data-requesturl = '<?php assign($request_url)?>'
							data-callbackurl = '<?php assign(Util::rewriteUrl('admin-top', 'edit_panel_list',array($data['brandSocialAccountId']),array('p'=>$this->params['p'])))?>'>
							<option value="default">操作</option>
							<?php if($entry->hidden_flg == 0):?>
							<option value="fixed"><?php if($entry->priority_flg == 0) assign('優先表示');else assign('優先表示を解除')?></option>
							<?php endif;?>
							<option value="repossession">再取得</option>
							<option value="edit">編集</option>
						</select>
					</p>
				</li>
		  <?php endforeach;?>
		</ul>
			<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoModalPager')->render(array(
				'TotalCount' => $data['totalEntriesCount'],
				'CurrentPage' => $this->params['p'],
				'Count' => $data['pageLimited'],
			))) ?>
		</section>
	</section>
	<footer>
		<p class="btnSet">
			<span class="btn2"><a href="<?php assign(Util::rewriteUrl('admin-top', 'select_panel_kind'))?>">戻る</a></span>
			<span class="btn3"><a href="#closeModalFrame" data-type="refreshTop">完了</a></span>
		</p>
	</footer>
</article>
<?php write_html($this->csrf_tag()); ?>
<div class="modal2 jsModal" id="modal1">
	<section class="modalCont-small jsModalCont">
		<h1>確認</h1>
		<p>
		 <span class="attention1">このSNS連携を解除しますか？<br>※この操作は取り消せません。</span>
		</p>
		<p class="btnSet">
			<span class="btn2"><a href="#closeModal" class="middle1">キャンセル</a></span><span
				class="btn4"><a href="javascript:void(0)" id="delete_area" data-url="<?php assign(Util::rewriteUrl('admin-top', 'api_disconnect_social_app.json'))?>"
                                data-callbackurl='<?php assign(Util::rewriteUrl('admin-top', 'select_panel_kind'))?>' class="middle1">解除する</a></span>
		</p>
	</section>
</div>

<div class="modal2 jsModal" id="modal4">
    <section class="modalCont-small jsModalCont">
        <h1 id="ajaxMessage">情報を取得している時にエラーを発生しました。<p>エラーメッセージ:</p><p id="message"> 後で再取得してお願いします。</p></h1>
        <p class="btnSet">
            <span class="btn3"><a href="#closeModal" class="middle1">閉じる</a></span>
        </p>
    </section>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script'=>array('EditPanelListService')))) ?>
