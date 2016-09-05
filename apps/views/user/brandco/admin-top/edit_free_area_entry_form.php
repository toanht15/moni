<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array(
	'brand' => $data['brand'],
))) ?>

<article class="modalInner-large">
	<header><h1>フリーエリアエントリー</h1></header>
	<section class="modalInner-cont">
		<div class="pageContEdit">
			<form id="frmEntry" name="frmEntry"
				  action="<?php assign(Util::rewriteUrl( 'admin-top', 'edit_free_area_entry',null,array('p'=>$this->params['p']))); ?>"
				  method="POST">
				<?php write_html( $this->formHidden( 'entryId', $data['entryId'])); ?>
				<?php write_html($this->csrf_tag()); ?>
				<table>
					<tbody>
					<tr>
						<th colspan="2">コンテンツ<span class="btn1"><a href="javascript:void(0);" class="small1" id="preview" data-uploadurl="<?php assign(Util::rewriteUrl ( 'admin-top', 'ckeditor_upload_file')) ?>"
																   　　　　　										　				data-listfileurl="<?php assign(Util::rewriteUrl ( 'admin-blog', 'file_list', null, array('f_id' => BrandUploadFile::POPUP_FROM_STATIC_HTML_ENTRY))) ?>">プレビュー</a></span></th>
					</tr>
					<tr>
						<td colspan="2">
							<?php write_html( $this->formTextarea( 'body', PHPParser::ACTION_FORM, array( 'cols' => '40', 'rows' => '4' ))); ?>
							<?php if ( $this->ActionError && !$this->ActionError->isValid('body')): ?>
								<br><span class="attention1"><?php assign ( $this->ActionError->getMessage('body') )?></span>
							<?php endif; ?>
						</td>
					</tr>
					</tbody>
				</table>
			</form>
		</div>
	</section>
	<footer>
		<p class="btnSet"><span class="btn2"><a href="<?php assign(Util::rewriteUrl( 'admin-top', 'free_area_entries',null,array('p'=>'1'))); ?>">キャンセル</a></span><span class="btn3"><a href="javascript:void(0)" id="submitEntry">保存</a></span></p>
	</footer>
</article>

<script type="text/javascript" src="<?php assign($this->setVersion('/ckeditor/ckeditor.js'))?>"></script>

<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script'=>array('EditFreeAreaEntryService')))) ?>