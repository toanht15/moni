<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>
<?php write_html($this->parseTemplate('CandidatePreviewModal.php')) ?>
	<article>
		<section class="messageWrap">
			<section class="message">
				<h1 class="messageHd1">お客様の注文</h1>
				<div class="messageOrder">
					<div class="settlementInfo">
						<p class="productImg"><img
								src="<?php echo htmlspecialchars($data['order']['product']['image_url']) ?>"
								alt="<?php echo htmlspecialchars($data['order']['product']['title']) ?>"></p>


						<div class="settlementInfoInner">
							<?php write_html($this->csrf_tag());?>
							<?php
							$subTotal = 0;
							foreach ($data['order']['order_items'] as $key => $item){
							?>
							<h2 class="title"><?php echo htmlspecialchars($item['title']); ?></h2>
							<p class="price"><strong
									class="js-price"><?php echo number_format(htmlspecialchars($item['unit_price'])); ?></strong>円（税込）
							</p>
							<p class="number">数量
								<span><strong><?php echo number_format(htmlspecialchars($data['order']['order'][$item['id']])); ?></strong>
									点</span>
							</p>

						<?php
						$subTotal = $subTotal + ($data['order']['order'][$item['id']] * $item['unit_price']);
						} ?>
						<!-- /.settlementInfoInner --></div>


						<!-- /.settlementInfo --></div>
					<dl class="total">
						<dt>商品の小計：</dt>
						<dd><?php echo number_format($subTotal); ?>円</dd>
						<?php if($data['order']['product']['delivery_charge'] > 0 ):?>
						<dt>配送料・手数料：</dt>
						<dd><?php echo number_format(htmlspecialchars($data['order']['product']['delivery_charge'])); ?>
							円
						</dd>
						<?php endif;?>
						<dt>注文合計：</dt>
						<dd class="price"><strong><?php echo number_format($subTotal + $data['order']['product']['delivery_charge']); ?></strong>円</dd>
						<!-- /.total --></dl>
					<p class="orderDate">注文日：<?php echo date('Y年m月d日');?></p>

					<section>
						<h2 class="hd2">配送先情報</h2>
						<p>〒<?php echo $data['order']['zipCode1']; ?>-<?php echo $data['order']['zipCode2']; ?><br>
							<?php assign($data['order']['prefName']);?>
							<?php echo htmlspecialchars($data['order']['address1']); ?>
							<?php echo htmlspecialchars($data['order']['address2']); ?>
							<br>
							<?php echo htmlspecialchars($data['order']['address3']); ?>
							<br>
							電話番号：<?php echo htmlspecialchars($data['order']['telNo1']); ?>
							-<?php echo htmlspecialchars($data['order']['telNo2']); ?>
							-<?php echo htmlspecialchars($data['order']['telNo3']); ?></p>
						<p>名前：<?php assign($data['order']['lastName']);?><?php assign($data['order']['firstName']);?></p>
					</section>

					<section>
						<h2 class="hd2">お支払い方法</h2>
						<p><?php echo htmlspecialchars($data['order']['payTypeName']); ?></p>
						<?php
						if (isset($data['order']['cardNumber']) && $data['order']['cardNumber'] && $data['order']['payType'] == 0) {
							echo '下4桁 ' . htmlspecialchars(strrev(sprintf("%-04.4s",
									strrev($data['order']['cardNumber']))));
						}
						if (isset($data['order']['convenienceName']) && $data['order']['convenienceName'] &&  $data['order']['payType'] == 3) {
							echo htmlspecialchars($data['order']['convenienceName']);
						}

						?>
					</section>

					<div class="settlementBtnSet">
						<p class="btn3"><a href="javascript:void(0)" class="large1" id="js-submit-order">注文を確定する</a></p>
						<p class="cancel"><a href="<?php echo htmlspecialchars($data['order_edit_url']); ?>">注文内容を変更する</a></p>
						<!-- /.settlementBtnSet --></div>
					<p class="supplement1">※注文確定後のキャンセルはできません。<br>
						※配送先情報に誤りがある場合は商品をお届けできない可能性があります。<br>
						入力内容に間違いが無いかご確認をお願いいたします。</p>
					<!-- /.messageOrder --></div>
				<!-- /.message --></section>
			<!-- /.messageWrap --></section>

		<p class="pageTop"><a href="#top"><span>ページTOPへ</span></a></p>
	</article>

<script>
	var orderUrl = "<?php assign($data['order_settlement_url']);?>";
	var orderSending = false;
	var cpPageUrl = "<?php assign($data['cp_url']); ?>";

	$(function () {
		$('#js-submit-order').on('click', function(){
			order();
		});
	});

	function order() {
		if(orderSending == true)
		{
			//処理中のため処理なし。
			return ;
		}
		orderSending = true;

		Brandco.helper.brandcoBlockUI();
		$.ajax({
			type: "POST",
			url: orderUrl,
			data: {'csrf_token': $(':hidden[name="csrf_token"]').val() } ,
			dataType: "json",
			success: function (data) {
				if (data.status == 'ok') {
					viewSuccess(data);
				}
				else {
					viewError(data);
				}
				orderSending = false;
			},
			error: function () {
				Brandco.helper.brandcoUnblockUI();
				alert('送信に失敗しました。再度実行してください');
				orderSending = false;
			}
		});
	}

	function viewSuccess(data)
	{
		<?php if(! $data['isFinishedPaymentAction']) { ?>
		joinAction();
		<?php } else { ?>
		location.href = cpPageUrl;
		<?php } ?>
	}

	function viewError(data)
	{
		Brandco.helper.brandcoUnblockUI();
			if(data.error.exec){
				alert(data.error.exec);
			}else {
				alert('注文に失敗しました。');
			}
	}

	function joinAction()
	{
		<?php if($data['is_opening_flg']):?>
		  var Url = '<?php assign($data["join_url"])?>';
		  $('#js-cp-action').attr('action' ,  Url);
		  $('#js-cp-action').submit();
		<?php else:?>
		  $('#js-execute-payment-action').submit();
		<?php endif;?>

	}
</script>

<?php if(! $data['isFinishedPaymentAction']) { ?>
	<div>
		<form id="js-cp-action" class="openingCpActionForm" action="#" method="POST">
			<?php write_html($this->csrf_tag()); ?>
			<?php write_html($this->formHidden('cp_id', $data['cp_id'])); ?>
			<?php write_html($this->formHidden('cp_action_id',$data['cp_action_id'] )); ?>
			<?php write_html($this->formHidden('beginner_flg', $data['beginner_flg'])); ?>
		</form>
	</div>
<?php } ?>

<?php if(!$data['is_opening_flg']) { ?>
	<div>
		<form id="js-execute-payment-action" class="openingCpActionForm" action="<?php assign($data['join_url'])?>" method="POST">
			<?php write_html($this->csrf_tag()); ?>
			<?php write_html($this->formHidden('cp_user_id', $data['cp_user_id'])); ?>
			<?php write_html($this->formHidden('cp_action_id',$data['cp_action_id'] )); ?>
		</form>
	</div>
<?php } ?>

<?php write_html($this->scriptTag("user/UserActionPopularVoteService")); ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>