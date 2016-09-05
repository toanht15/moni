<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>
<?php write_html($this->parseTemplate('CandidatePreviewModal.php')) ?>


<article>
	<section class="messageWrap">
		<section class="message">
			<h1 class="messageHd1">ERROR</h1>
			<div style="padding:12px;">
				お客様の注文内容を取得することができませんでした。<br>
				有効期限が切れたか、存在しません。
				お手数ですが再度ご注文をお願いします。
			<div class="settlementBtnSet">
				<p class="btn3">
				<a class="large" href="<?php echo $data['products_url']; ?>">注文画面へ戻る</a>
				</p>
			</div>
			</div>
		</section>
	</section>
</article>

<?php write_html($this->scriptTag("user/UserActionPopularVoteService")); ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>