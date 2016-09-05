<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array(
	'brand' => $data['brand'],
))) ?>
 <article class="modalInner-large">
    <header><h1>ブランドページトップに掲載するコンテンツを選択してください</h1></header>
    <section class="modalInner-cont">
      <section class="editBRANDCo jsMenuArea" id="jsEditBRANDCo">
        <h1>コンテンツ</h1>
        <ul>
            <li><a href="<?php assign(Util::rewriteUrl( 'admin-top', 'page_entries')); ?>"><img src="<?php assign($this->setVersion('/img/icon/iconEditPage.png'))?>" width="80" height="80" alt="ページ"><span>ページ</span></a></li>
            <li><a href="<?php assign(Util::rewriteUrl( 'admin-top', 'link_entries')); ?>"><img src="<?php assign($this->setVersion('/img/icon/iconEditLink.png'))?>" width="80" height="80" alt="リンク"><span>リンク</span></a></li>
            <li><a href="<?php assign(Util::rewriteUrl('admin-top', 'photo_entries')); ?>"><img src="<?php assign($this->setVersion('/img/icon/iconEditPhoto.png'))?>" width="80" height="80" alt="投稿フォト"><span>投稿フォト</span></a></li>
        </ul>
      </section>
      <section class="editSNS jsMenuArea" id="jsEditSNS">
          <h1>ソーシャルメディア</h1>
          <ul>
        <?php foreach($data['socialPanelKinds'] as $brand_social_account):?>
			<?php $name = $brand_social_account->cutLongText(json_decode($brand_social_account->store)->name, 7, '...');?>
			<?php if($brand_social_account->social_app_id == SocialApps::PROVIDER_FACEBOOK):?>
				<?php $stream = $brand_social_account->getFacebookStream(); ?>
			    <li class="fbAccount"><a href="<?php assign(Util::rewriteUrl('admin-top', 'edit_panel_list', array($brand_social_account->id)))?>"><img src="<?php assign($brand_social_account->picture_url)?>" alt=""><span><?php assign($name)?></span></a></li>
			<?php elseif($brand_social_account->social_app_id == SocialApps::PROVIDER_TWITTER):?>
				<?php $stream = $brand_social_account->getTwitterStream(); ?>
			   	<li class="twAccount"><a href="<?php assign(Util::rewriteUrl('admin-top', 'edit_panel_list', array($brand_social_account->id)))?>"><img src="<?php assign($brand_social_account->picture_url)?>" alt=""><span><?php assign($name)?></span></a></li>
			<?php elseif($brand_social_account->social_app_id == SocialApps::PROVIDER_GOOGLE):?>
				<?php $stream = $brand_social_account->getYoutubeStream(); ?>
			   	<li class="ytAccount"><a href="<?php assign(Util::rewriteUrl('admin-top', 'edit_panel_list', array($brand_social_account->id)))?>"><img src="<?php assign($brand_social_account->picture_url)?>" alt=""><span><?php assign($name)?></span></a></li>
            <?php elseif($brand_social_account->social_app_id == SocialApps::PROVIDER_INSTAGRAM):?>
                <?php $name = $brand_social_account->cutLongText($brand_social_account->name, 7, '...');?>
                <li class="igAccount"><a href="<?php assign(Util::rewriteUrl('admin-top', 'edit_panel_list', array($brand_social_account->id)))?>"><img src="<?php assign($brand_social_account->picture_url)?>" alt=""><span><?php assign($name)?></span></a></li>
			<?php endif;?>
		<?php endforeach;?>

        <?php if($data['rssPanelKinds']): ?>
            <?php foreach($data['rssPanelKinds'] as $rssStream): ?>
                <?php $name = $rssStream->cutLongText($rssStream->title, 7, '...'); ?>
                <li class="rssAccount"><a href="<?php assign(Util::rewriteUrl('admin-top', 'rss_entries', array($rssStream->id)))?>"><img src="<?php assign($rssStream->getStreamImage())?>" alt=""><span><?php assign($name)?></span></a></li>
            <?php endforeach; ?>
        <?php endif; ?>
          <li class="addAccount"><a href="#modal1" class="jsOpenModal"><span class="linkAdd">追加する</span></a></li>
        </ul>
      </section>
    </section>
    <footer>
      <p class="btn2"><a href="#closeModalFrame" data-type="refreshTop">閉じる</a></p>
    </footer>
  </article>
  <div class="modal2 jsModal" id="modal1">
    <section class="modalCont-small jsModalCont">
      <h1>連携するSNSを選択してください</h1>
      <ul class="linkSNSList">
        <?php if (Util::isDefaultBRANDCoDomain()): ?>
            <li><a href="<?php assign(Util::rewriteUrl( 'facebook', 'connect' )); ?>"><img src="<?php assign($this->setVersion('/img/sns/iconSnsFB3.png'))?>" width="50" height="50" alt=""></a></li>
            <li><a href="<?php assign(Util::rewriteUrl( 'instagram', 'connect' )); ?>"><img src="<?php assign($this->setVersion('/img/sns/iconSnsIG3.png'))?>" width="50" height="50" alt=""></a></li>
            <li><a href="<?php assign(Util::rewriteUrl( 'twitter', 'connect' )); ?>"><img src="<?php assign($this->setVersion('/img/sns/iconSnsTW3.png'))?> width="50" height="50" alt=""></a></li>
            <li><a href="<?php assign(Util::rewriteUrl( 'google', 'connect' )); ?>"><img src="<?php assign($this->setVersion('/img/sns/iconSnsYT3.png'))?>" width="50" height="50" alt=""></a></li>
        <?php endif; ?>
        <li><a href="#" id="rssButton"><img src="<?php assign($this->setVersion('/img/sns/iconSnsRss3.png'))?>" width="50" height="50" alt=""></a></li>
      </ul>
      <p class="btnSet"><span class="btn2"><a href="#closeModal" class="middle1">キャンセル</a></span></p>
    </section>
  </div>

    <div class="modal2 jsModal" id="modal2">
        <section class="modalCont-small jsModalCont">
            <h1>リンクを入力してください</h1>
            <input name="link" type="text">
            <?php write_html($this->csrf_tag()); ?>
            <p class="btnSet"><span class="btn2"><a href="#closeModal" class="middle1">キャンセル</a></span><span class="btn3"><a href="javascript:void(0)" id='aSubmit' data-checkurl = '<?php assign(Util::rewriteUrl('admin-top', 'api_check_rss_url.json'))?>'
                                                                                                                                                       data-addurl = '<?php assign(Util::rewriteUrl('admin-top', 'api_add_rss.json'))?>' data-callbackurl = '<?php assign(Util::rewriteUrl('admin-top','select_panel_kind')) ?>' class="middle1">取得</a></span></p>
        </section>
    </div>

<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script'=>array('SelectPanelKindService')))) ?>