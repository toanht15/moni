<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array('brand' => $data['brand'],))) ?>
<body class="modalInnerBody">
	<article class="modalInner-large">

		<header>
			<h1>グローバルメニュー編集</h1>
		</header>

			<section class="addNew">
                <p class="btn3"><a href="<?php  assign(Util::rewriteUrl('admin-top', 'create_global_menu_form'))?>" class="middle1" id="iconAdd">新規メニュー追加</a></p>
                <p class="iconHelp">
                    <span class="text">ヘルプ</span>
                    <span class="textBalloon1">
                      <span>
                        ドラッグ&ドロップで<br>表示順が変えられます
                      </span>
                    <!-- /.textBalloon1 --></span>
                <!-- /.iconHelp --></p>
			</section>

			<section class="modalInner-cont" data-updateurl = '<?php assign(Util::rewriteUrl('admin-top', 'update_global_menu', array(), array('redirect'=>'create_menu')))?>'
                                             data-createurl = '<?php assign(Util::rewriteUrl('admin-top', 'create_global_menu_form'))?>' >
				<section class="editMenuList">

					<form id="update_menu_form" action="<?php assign(Util::rewriteUrl('admin-top', 'update_global_menu'))?>" method="post">
						<?php write_html($this->csrf_tag()); ?>
						<?php write_html($this->formHidden('order', "", array("id" => "order"))); ?>
						<ul id="jsSortable">

							<?php foreach($data['globalMenus'] as $menu):?>
                                <?php $error = false; ?>
                            <?php if ($this->ActionError && (!$this->ActionError->isValid('link_'.$menu->id) || !$this->ActionError->isValid('title_'.$menu->id))): ?>
                                    <?php $error = true; ?>
                            <?php endif; ?>
							<li id="list_<?php assign($menu->id); ?>">
								<p class="listNum"><?php assign($menu->list_order); ?></p>
                                     <p class="menuTitle jsOpenActionArea_contrary">
									<a href="javascript:void(0)"><?php assign($menu->name); ?></a>
								</p>
                                   <dl class="jsOpenActionArea" <?php if($error) write_html('style="display: inline-block"') ?>>
									<dt><label for="inputTitle1">タイトル</label></dt>
									<dd><input type="text" name="title_<?php assign($menu->id); ?>" oninput="MenusService.valueChanged()" id="inputTitle_<?php assign($menu->id); ?>" value="<?php assign($menu->name); ?>" class="jsLinkTitle"></dd>
                                    <?php if ($this->ActionError && !$this->ActionError->isValid('title_'.$menu->id)): ?>
                                        <dt></dt><dd><p class="attention1"><?php assign ( $this->ActionError->getMessage('title_'.$menu->id) )?></p></dd>
                                    <?php endif; ?>
									<dt><label for="inputUrl1">URL</label></dt>
									<dd><input type="url" name="link_<?php assign($menu->id); ?>" oninput="MenusService.valueChanged()" id="inputUrl<?php assign($menu->id); ?>" value="<?php assign($menu->link); ?>" class="jsLinkUrl"></dd>
                                    <?php if ($this->ActionError && !$this->ActionError->isValid('link_'.$menu->id)): ?>
                                        <dt></dt><dd><p class="attention1"><?php assign ( $this->ActionError->getMessage('link_'.$menu->id) )?></p></dd>
                                    <?php endif; ?>

								</dl>
								<p class="action">表示
									<?php if($menu->hidden_flg === "0"): ?>
										<a href="javascript:void(0)" data-menu_id="<?php assign($menu->id); ?>" onclick="MenusService.valueChanged()" class="switch on"><span class="switchInner"><span class="selectON">ON</span><span class="selectOFF">OFF</span></span></a>
									<?php else: ?>
										<a href="javascript:void(0)" data-menu_id="<?php assign($menu->id); ?>" onclick="MenusService.valueChanged()" class="switch off"><span class="switchInner"><span class="selectON">ON</span><span class="selectOFF">OFF</span></span></a>
									<?php endif; ?>

									<input id="hidden_flg_<?php assign($menu->id); ?>" name="hidden_flg_<?php assign($menu->id); ?>" value="<?php assign($menu->hidden_flg); ?>" type="hidden">

                                                                        <span class="moreAction jsOpenActionArea" <?php if($error) write_html('style="display: inline-block"') ?>>
                                        <?php write_html( $this->formCheckBox('is_blank_flg_'.$menu->id, $menu->is_blank_flg?array('on'):'', array('onclick'=>'MenusService.valueChanged();'), array('on' => '別ウィンドウで開く'))); ?>

										<span class="btn2"><a class="cmd-delete-menu small1" href="javascript:void(0)">削除</a></span>
									</span>
								</p>
								<a href="javascript:void(0)" class="<?php if($error) assign('closeAction'); else assign('openAction') ?> jsOpenAction">詳細</a>
							</li>

							<?php endforeach; ?>
						</ul>
					</form>

				</section>
			</section>

			<footer>
				<p class="btnSet"><span class="btn2"><a href="#modal1" id="cancelChanges">キャンセル</a></span><span class="btn3"><a href="javascript:void(0)" id="submit" >保存</a></span></p>
			</footer>
	</article>

<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script'=>array('MenusService')))) ?>