<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array('brand' => $data['brand'],))) ?>
</form>
  <article class="modalInner-large">
    <header>
      <h1>サイドメニュー編集</h1>
    </header>
    <section class="modalInner-cont">
      <div class="pageContEdit">
        <form id="frmMenu" name="frmMenu" action="<?php assign(Util::rewriteUrl( 'admin-top', 'create_side_menu' )); ?>" method="POST">
		<?php write_html( $this->formHidden( 'menuId', $data['menuId'])); ?>
		<?php write_html($this->csrf_tag()); ?>
          <table>
            <tbody>
              <tr>
                <th>メニュー名</th>
                <td>
				<?php write_html( $this->formText( 'name', PHPParser::ACTION_FORM, array( 'class' =>'AWid200 AP3', 'maxlength'=>'35','id'=> 'menuName', 'style'=>'width:500px'))); ?>
                    <br><small class="textLimit"></small>
				<?php if ( $this->ActionError && !$this->ActionError->isValid('name')): ?>
					<p class="attention1"><?php assign ( $this->ActionError->getMessage('name') )?></p>
				<?php endif; ?>
                </td>
              </tr>
              <tr>
	              <th>別ウィンドウで開く</th>
	              <td>
	              	<?php if($data['sideMenu']->is_blank_flg):?>
	              	<?php write_html( $this->formCheckBox('is_blank_flg', array($this->getActionFormValue('is_blank_flg')), array('checked'=>'checked'), array('x' => ' 別ウィンドウで開く'))); ?>
	              	<?php else:?>
	              	<?php write_html( $this->formCheckBox('is_blank_flg', array($this->getActionFormValue('is_blank_flg')), array(), array('x' => ' 別ウィンドウで開く'))); ?>
	              	<?php endif;?>
	              </td>
              </tr>
              <tr>
                <th>リンク</th>
                <td>
				<?php write_html( $this->formText( 'link', PHPParser::ACTION_FORM, array( 'class' =>'AWid200 AP3', 'maxlength'=>'255', 'style'=>'width:500px'))); ?>
				<?php if ( $this->ActionError && !$this->ActionError->isValid('link')): ?>
					<p class="attention1"><?php assign ( $this->ActionError->getMessage('link') )?></p>
				<?php endif; ?></td>
              </tr>
              <tr>
                <th>ステータス</th>
                <td>
                  <select name="display" id="display">
                    <option value="1" <?php if($this->getActionFormValue('display') == 1) assign('selected="selected"')?>>非表示</option>
                    <option value="0" <?php if($this->getActionFormValue('display') == 0) assign('selected="selected"')?>>表示</option>
                  </select>
                </td>
              </tr>
            </tbody>
          </table>
        </form>
      </div>
    </section>
    <footer>
      <p class="btnSet"><span class="btn2"><a href="<?php assign(Util::rewriteUrl( 'admin-top', 'side_menus' ));?>">キャンセル</a></span><span class="btn3"><a href="javascript:void(0)" id="submit">保存</a></span></p>
    </footer>
  </article>

<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script'=>array('CreateMenuService')))) ?>