<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>

<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>
<?php write_html($this->parseTemplate('CandidatePreviewModal.php')) ?>

	<form class="h-adr" id="js-order-form" onSubmit="return false;">
		<input type="hidden" name="cp_id" value="<?php echo $data['product']['detail']['cp_id'] ?>">
		<input type="hidden" name="product_id" value="<?php echo $data['product']['detail']['id'] ?>">
		<?php write_html($this->csrf_tag());?>
		<article>
			<section class="messageWrap">
				<section class="message">
					<h1 class="messageHd1">お客様の注文</h1>
					<div class="messageOrder">
						<div class="settlementInfo">
							<p class="productImg"><img
									src="<?php echo htmlspecialchars($data['product']['detail']['image_url']); ?>"
									alt="<?php echo htmlspecialchars($data['product']['detail']['title']) ?>"></p>

							<div class="settlementInfoInner js-product__<?php echo htmlspecialchars($item['id']); ?>">
								<?php foreach ($data['product']['items'] as $item) { ?>
									<h2 class="title"><?php echo htmlspecialchars($item['title']); ?></h2>
									<p class="price"><strong
											class="js-price"><?php echo number_format(htmlspecialchars($item['unit_price'])); ?></strong>円（税込）
									</p>
									<span class="js-error__selectCount hidden"></span>
									<?php
									//購入可能だった場合
									if ($item['stock_limited'] == 0 || ($item['stock_limited'] == 1 && $item['stock'] > 0)) {
										$canBuy = true;
										?>
										<!-- itemごと -->
										<p class="number" style="margin-bottom: 10px;">数量
											<select class="js_product_count" data-id="<?php echo $item['id']; ?>"
											        name="order[<?php echo htmlspecialchars($item['id']); ?>]">
												<?php
												$count = 10;
												if ($item['stock_limited'] == 1) {
													$count = $item['stock'] <= 10 ? $item['stock'] : 10 ;
												}
												for ($i = 1; $i <= $count; $i++) {
													echo '<option value="' . $i . '"';
													if(isset($data['order_count'][$item['id']]) && $data['order_count'][$item['id']] == $i){
														echo ' selected="selected"';
													}
													elseif (isset($data['order']['order'][$item['id']]) && $data['order']['order'][$item['id']] == $i) {
														echo ' selected="selected"';
													}
													echo '>' . $i . '</option>';
												}
												?>
											</select>
										</p>
									<?php } else {
										$canBuy = false;
										echo '<p>販売を終了しました</p>';
									}
									?>
								<?php } ?>

								<!-- -->
								<!-- /.settlementInfoInner --></div>
							<!-- /.settlementInfo --></div>

						<dl class="total">
							<dt>商品の小計：</dt>
							<dd><span class="js-subtotal"></span>円</dd>
							<?php if ($data['product']['detail']['delivery_charge'] > 0) { ?>
								<dt>送料・手数料：</dt>
								<dd>
									<span><?php echo number_format(htmlspecialchars($data['product']['detail']['delivery_charge'])); ?></span>円
								</dd>
							<?php } ?>
							<span
								class="hidden js-delivery_charge"><?php $data['product']['detail']['delivery_charge']; ?></span>
							<dt>注文合計：</dt>
							<dd class="price"><strong class="js-total">-</strong>円</dd>
							<!-- /.total --></dl>


						<section>
							<h2 class="hd2">配送先情報</h2>

							<?php if (! $data['isLogin']) { ?>
								<p class="loginRecommend">
									<span class="info">「<a href="<?php assign(Util::rewriteUrl('products','login',array($data['product']['detail']['id'])))?>">ログイン</a>」をすると、ユーザー登録済の方は以下の入力を省略できる可能性があります。</span>
									<!-- /.loginRecommend --></p>
							<?php } ?>


							<ul class="commonTableList1">
								<li>
									<p class="title1">
										<span class="require1">氏名（かな）</span>
										<!-- /.title1 --></p>
									<p class="itemEdit">
										<span class="js-error__lastName hidden"></span>
										<span class="js-error__firstName hidden"></span>
										<span class="js-error__lastNameKana hidden"></span>
										<span class="js-error__firstNameKana hidden"></span>
										<span class="editInput">
											<label class="editName"><span>姓</span><input type="text" class="name"
											                                             name="lastName"
											                                             value="<?php echo $data['address']['lastName']; ?>"></label><label
												class="editName"><span>名</span><input type="text" class="name"
											                                          name="firstName"
											                                          value="<?php echo $data['address']['firstName']; ?>"></label>
										</span>
										<span class="editInput">
											<label class="editName"><span>せい</span><input type="text" class="name"
											                                              name="lastNameKana"
											                                              value="<?php echo $data['address']['lastNameKana']; ?>"></label><label
												class="editName"><span>めい</span><input type="text" class="name"
											                                           name="firstNameKana"
											                                           value="<?php echo $data['address']['firstNameKana']; ?>"></label>
											<!-- /.editInput --></span>
										<!-- /.itemEdit --></p>
								</li>
								<li>
									<p class="title1">
										<span class="require1">郵便番号</span>
										<span class="supplement1">※半角数字</span>
										<!-- /.title1 --></p>
									<p class="itemEdit">
										<span class="p-country-name" style="display:none;">Japan</span>
										<span class="js-error__zipCode1 hidden"></span>
										<span class="js-error__zipCode2 hidden"></span>
										<span class="editInput">
											<input type="text" size="3" autocorrect="off" autocapitalize="off"
											       class="inputNum p-postal-code"
											       name="zipCode1"
											       value="<?php echo $data['address']['zipCode1'] ?>">－<input
												type="text" size="4" class="inputNum p-postal-code" name="zipCode2"
												value="<?php echo $data['address']['zipCode2'] ?>"
												onkeyup="AjaxZip3.zip2addr('zipCode1','zipCode2','prefId','address1','address2')">
											<a href="javascript:;" onclick="AjaxZip3.zip2addr('zipCode1','zipCode2','prefId','address1','address2')">住所検索</a>
											<!-- /.editInput --></span>
										<!-- /.itemEdit --></p>
								</li>
								<li>
									<p class="title1">
										<span class="require1">都道府県</span>
										<!-- /.title1 --></p>
									<p class="itemEdit">
										<span class="js-error__prefId hidden"></span>
										<span class="editInput">
											<select name="prefId" id="js-region-select" class="p-region">
												<?php
												foreach ($data['prefList'] as $key => $item) {
													?>
													<option value="<?php echo $key; ?>" <?php
													if ($data['address']['prefId'] == $key ) {
														echo 'selected="selected"';
													} ?>>
															<?php echo htmlspecialchars($item); ?>
														</option>
													<?php
												}
												?>
											</select>
											<!-- /.editInput --></span>
										<!-- /.itemEdit --></p>

								</li>
								<li>
									<p class="title1">
										<span class="require1">市区町村</span>
										<!-- /.title1 --></p>
									<p class="itemEdit">
										<span class="js-error__address1 hidden"></span>
                  <span class="editInput">
                    <input type="text" class="p-locality p-street-address" name="address1"
                           value="<?php echo $data['address']['address1']; ?>">
	                  <!-- /.editInput --></span>
										<!-- /.itemEdit --></p>
								</li>
								<li>
									<p class="title1">
										<span class="require1">番地</span>
										<!-- /.title1 --></p>
									<p class="itemEdit">
										<span class="js-error__address2 hidden"></span>
                  <span class="editInput">
	                  <input type="text" autocorrect="off" autocapitalize="off"  name="address2"
	                         value="<?php echo $data['address']['address2']; ?>">
	                  <!-- /.editInput --></span>

										<span class="supplement1">※番地、マンション名等の入れ忘れのないようお願いいたします。</span>
										<!-- /.itemEdit --></p>
								</li>
								<li>
									<p class="title1">
										建物
										<!-- /.title1 --></p>
									<p class="itemEdit">
										<span class="js-error__address3 hidden"></span>
                  <span class="editInput">
                    <input type="text" class="p-extended-address" name="address3"
                           value="<?php echo $data['address']['address3']; ?>">
	                  <!-- /.editInput --></span>
										<!-- /.itemEdit --></p>
								</li>
								<li>
									<p class="title1">
										<span class="require1">電話番号</span>
										<!-- /.title1 --></p>
									<p class="itemEdit">
                  <span class="editInput">
	                  <span class="js-error__telNo1 hidden"></span>
	                  <span class="js-error__telNo2 hidden"></span>
	                  <span class="js-error__telNo3 hidden"></span>
                    <input type="tel"
                           class="inputNum"
                           name="telNo1"
                           value="<?php echo $data['address']['telNo1']; ?>"
                    >－<input type="tel"
                             class="inputNum"
                             name="telNo2"
                             value="<?php echo $data['address']['telNo2']; ?>"
	                  >－<input type="tel"
                               class="inputNum"
                               name="telNo3"
                               value="<?php echo $data['address']['telNo3']; ?>">
	                  <!-- /.editInput --></span>
										<!-- /.itemEdit --></p>
								</li>
								<!-- /.commonTableList1 --></ul>
						</section>


						<section>
							<h2 class="hd2">お支払い方法</h2>
							<span class="js-error__payType hidden"></span>
							<ul class="acMenu jsAcMenuWrap">
								<li>
								<span class="cheackTitle"><input type="radio" id="radio01" class="customRadio jsAcMenu"
								                                 name="payType" value="0" <?php
									if (isset($data['payType']) && $data['payType'] == 0) {
										echo 'checked="checked"';
									} ?>><label for="radio01">クレジットカード決済<span><img
												src="<?php assign($this->setVersion('/img/message/cardType.png')); ?>" alt=""
												class="cradTypeImg"></span></label></span>
									<div class="settlementMethod jsAcMenuTarget"
									     style="display: <?php if (isset($data['payType']) && $data['payType'] == 0) {
										     echo 'block';
									     } else {
										     echo 'none';
									     } ?>;">
										<span class="js-error__cardName hidden"></span>
										<span class="js-error__cardNumber  hidden"></span>
										<p class="cradName">
											<span>カード名義人（半角英字）</span><input name="cardName" type="text"
											                                  value="<?php if (isset($data['cardName'])) {
												                                  echo htmlspecialchars($data['cardName']);
											                                  } ?>">
											<!-- /.cradName --></p>
										<p class="cradNumber">
											<span>カード番号（半角数字）</span><input name="cardNumber"
											                         value="<?php if (isset($data['cardNumber'])) {
												                         echo htmlspecialchars($data['cardNumber']);
											                         } ?>" type="tel">
											<!-- /.cradNumber --></p>
										<p class="cradTerm">
											<span class="js-error__cardExpirationMonth hidden"></span>
											<span class="js-error__cardExpirationYear hidden"></span>
											<span class="js-error__securityCode hidden"></span>
											<span>有効期限</span>
											<select name="cardExpirationMonth">
												<option value=""></option>
												<?php
												for ($i = 1; $i <= 12; $i++) {
													echo '<option value="'.str_pad($i, 2, 0, STR_PAD_LEFT).'"';
													if (isset($data['order']['cardExpirationMonth']) && $data['order']['cardExpirationMonth'] == $i) {
														echo ' selected="selected"';
													}
													echo '>' . str_pad($i, 2, 0, STR_PAD_LEFT) . '</option>';
												}
												?>
											</select>
											<select name="cardExpirationYear">
												<option value=""></option>
												<?php
												for ($i = 0; $i < 20; $i++) {
													$year = date('Y') + $i;
													$yearValue = date('y') + $i;
													?>
													<option value="<?php echo $yearValue; ?>" <?php
														if(isset($data['order']['cardExpirationYear']) && $data['order']['cardExpirationYear'] == $yearValue)
													{ ?> selected = "selected" <?php } ?> ><?php echo $year; ?></option>
													<?php
												}
												?>
											</select>
											<!-- /.cradTerm --></p>
										<p class="securityCode">
											<span>セキュリティ・コード</span>
											<input type="text" autocorrect="off" autocapitalize="off" class="inputNum"
											       name="securityCode" maxlength="4">
											<span><a href="#modal1" class="jsOpenModal">セキュリティ・コードについて</a></span>
											<!-- /.securityCode --></p>
										<!-- /#js.method --></div>
								</li>
								<li>
								<span class="cheackTitle"><input type="radio" id="radio02" class="customRadio jsAcMenu"
								                                 name="payType"
								                                 value="3" <?php if (isset($data['payType']) && $data['payType'] == 3) {
										echo 'checked="checked"';
									} ?> ><label
										for="radio02">コンビニ支払い</label></span>
									<div class="acMenuIneer jsAcMenuTarget"
									     style="display: <?php if (isset($data['payType']) && $data['payType'] == 3) {
										     echo 'block';
									     } else {
										     echo 'none';
									     } ?>;">
										<p>代金のお支払い後に商品が発送されます。なお、お支払いの際に必要となるお支払い番号はメールにて送りますのでご確認ください。</p>
										<p><a target="_blank" href="https://www.gmo-pg.com/service/convenience_store/various_user3/">コンビニでのお支払い方法</a></p>
										<p class="selectConveni"><strong>お支払いをするコンビニを選んでください。</strong>
										<span class="js-error__convenienceCode hidden"></span>
											<select name="convenienceCode" id="">
												<?php
												foreach ($data['convenienceStoreList'] as $key => $item) {
													echo '<option value=' . $key;
													if (isset($data['convenienceCode']) && $data['convenienceCode'] == $key) {
														echo ' selected="selected"';
													}
													echo '>' . $item . '</option>';
												} ?>
											</select>
											<!-- /.selectConveni --></p>
									</div>
								</li>
								<!-- <li>
									<span class="cheackTitle"><input type="radio" id="radio03" class="customRadio jsAcMenu"
																	 name="payType" value="18"><label for="radio03">楽天</label></span>
									<div class="acMenuIneer jsAcMenuTarget" style="display: none;">
										<p></p>
									</div>
								</li>-->
							</ul>
						</section>
						<div class="settlementBtnSet">
							<p class="btn3">
								<?php if ($canBuy):?>
								<a href="javascript:void(0)" onclick="checkSubmit()" class="large1"><?php assign($data['isLogin'] ? "確認画面へ" : "登録し次に進む")?></a>
							<?php else:?>
									<span class="large1">確認画面へ</span>
							<?php endif;?>
							</p>
							<p class="cancel"><a href="<?php assign($data['cancelUrl'])?>">キャンセル</a></p>
							<p class="supplement1">※未ログインの方、会員登録がお済みで無い方は、次の画面でログイン・会員登録が必要になります。</p>
							<!-- /.settlementBtnSet --></div>
						<!-- /.messageOrder --></div>
					<!-- /.message --></section>
				<!-- /.messageWrap --></section>

			<p class="pageTop"><a href="#top"><span>ページTOPへ</span></a></p>
		</article>
	</form>
	<div class="modal1 jsModal" id="modal1">
		<section class="modalCont-medium jsModalCont">
			<div class="modalsettlement">
				<h1>セキュリティ・コードについて</h1>
				<p>カード裏面、サイン欄右端の数字</p>
				<p><img src="<?php assign($this->setVersion('/img/message/imgSecurityCode.png')); ?>" alt="img title"></p>
				<p>※カードによっては表面に記載がある場合もあります。</p>
				<!-- /.modalImgPreview --></div>
			<p>
				<a href="#closeModal" class="modalCloseBtn">キャンセル</a>
			</p>
			<!-- /.modalCont-small.jsModalCont --></section>
		<!-- /#modal1.modal1.jsModal --></div>
	<!-- postcode -->
	<script src="<?php assign(Util::getHttpProtocol()); ?>://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
	<script type="application/json" id="dataJson"><?php echo json_encode($data['product']['items']); ?></script>
	<script>
		var deliveryCharge = <?php echo number_format($data['product']['detail']['delivery_charge']); ?>;
		var subTotal = 0;
		var total = 0;
		var order = [];
		var productItemsJson = $("#dataJson").html();
		var productItems = $.parseJSON(productItemsJson);
		var orderUrl = '<?php assign($data["orderUrl"])?>';
		$(function () {
			$('.jsAcMenu').on('change', function () {
				var target = $(this).parents('li').find('.jsAcMenuTarget');
				$('.jsAcMenuTarget').not(target).slideUp(200);
				target.slideDown(200);
			});

			//計算
			$('.js_product_count').on('change', function () {
				var dataId = $(this).attr('data-id');
				var itemCount = $(this).val();
				setOrder(dataId, itemCount);
			});
			$('.h-adr').on('submit', function () {
				return false;
			});
			//初期設定
			init();
		});

		function init() {
			var item = $('.js_product_count');
			item.each(function () {
				if ($(this).val() == 0) {
					$(this).val(1);
					setOrder($(this).attr('data-id'), 1);
				} else {
					setOrder($(this).attr('data-id'), $(this).val());
				}
			});
		}

		/**
		 * 注文の設定
		 */
		function setOrder(dataId, itemCount) {
			order[dataId] = itemCount;
			aggregate();
			setSubTotal();
			setTotal();
		}
		/**
		 *集計
		 */
		function aggregate() {
			subTotal = 0;
			order.some(function (number, id) {
				var unitPrice = 0;
				productItems.some(function (v) {
					if (id == v.id) {
						unitPrice = v.unit_price;
						return;
					}
				});
				console.log(unitPrice);
				subTotal = subTotal + (unitPrice * number);
			});
		}

		/**
		 * 小計の表示
		 */
		function setSubTotal() {
			$('.js-subtotal').html(numberFormat(subTotal));
		}

		/**
		 * 合計表示
		 */
		function setTotal() {
			total = subTotal + deliveryCharge;
			$('.js-total').html(numberFormat(total));
		}

		/**
		 * number format
		 * @param num
		 * @returns {string}
		 */
		function numberFormat(num) {
			return String(num).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,');
		}

		/**
		 * 送信チェックと実行
		 * @returns {boolean}
		 */
		function checkSubmit() {
			postApi();
			return false;
		}

		/**
		 * 仮保存
		 */
		function postApi() {
			$.ajax({
				type: "POST",
				url: orderUrl,
				data: $('#js-order-form').serializeArray(),
				dataType: "json",
				success: function (data) {
					if (data.status == 'ok') {
						viewComfirm(data);
					}
					else {
						setErrorView(data);
					}
				},
				error: function () {
					alert('送信に失敗しました。再度実行してください');
				}
			});
		}
		/**
		 * 確認画面表示
		 * @param data
		 */
		function viewComfirm(data) {
			location.href = data.redirect_url;
		}

		/**
		 * エラー画面表示
		 * @param data
		 */
		function setErrorView(data) {
			var errors = data.error;
			var checkList = [
				'selectCount',
				'lastName',
				'firstName',
				'lastNameKana',
				'firstNameKana',
				'zipCode1',
				'zipCode2',
				'prefId',
				'address1',
				'address2',
				'telNo1',
				'telNo2',
				'telNo3',
				'payType',
				'cardName',
				'cardNumber',
				'cardExpirationMonth',
				'cardExpirationYear',
				'securityCode'
			];

			for (i = 0; i < checkList.length; i++) {
				var checkName = checkList[i];
				$('#js-order-form .js-error__' + checkName).html('');
				$('.js-error__' + checkName).addClass('hidden');
				$('#js-order-form [name=' + checkName + ']').removeClass('errFocus');
				$.each(errors, function (i, v) {
					if (i == checkName) {
						$('.js-error__' + checkName).removeClass('hidden');
						$('#js-order-form [name=' + checkName + ']').addClass('errFocus');
						if (
							(checkName == 'zipCode2' && typeof(errors.zipCode1) != "undefined")
							|| (checkName == 'telNo2' && (typeof(errors.telNo1) != "undefined"))
							|| (checkName == 'telNo3' && (typeof(errors.telNo1) != "undefined" || typeof(errors.telNo2) != "undefined"))
						) {
							$('#js-order-form .js-error__' + checkName).html('');
						}
						else {
							$('#js-order-form .js-error__' + checkName).html(
								'<span class="iconError1">' + v + '</span>'
							);
						}
					}
				});
			}
		}
	</script>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>