<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array('brand' => $data['brand'],))) ?>
<article class="modalInner-large">
	<header class="innerLI-small">
		<h1>リンクリスト</h1>
	</header>
	<section class="addNew">
		<a
			href="<?php  assign(Util::rewriteUrl('admin-top', 'edit_link_entry_form', array(0)))?>"
			class="iconAdd1">新規リンク追加</a>
	</section>
	<section class="modalInner-cont">
		<section class="editContList">
			<ul>
        <?php foreach($data['linkEntries'] as $entry):?>
          <li class="<?php if($entry->priority_flg) assign('contFixed')?>" id="li<?php assign($entry->id)?>">
					<p class="postText">
						<a
							href="<?php assign(Util::rewriteUrl('admin-top', 'edit_link_entry_form', array($entry->id),array('p'=>$params['p'])))?>"><span
							class="supplement1"><?php $dateInfo = date_create($entry->updated_at); assign(date_format($dateInfo, 'Y年m月d日 H:i:s'));?></span>
                                                    <img <?php if(!$entry->image_url) write_html('style="display: none"')?>
							src="<?php assign($entry->image_url)?>" width="70" height="70"
							alt=""><span><?php write_html($this->nl2brAndHtmlspecialchars($entry->title).'<br>&nbsp;&nbsp;'.$this->nl2brAndHtmlspecialchars($entry->body))?></span></a>
					</p>
					<p class="action">
					              表示
            <?php if($entry->hidden_flg):?>
              <a href="#" class="switch off"  id="switch<?php assign($entry->id)?>"
                            data-hiddenurl = '<?php assign(Util::rewriteUrl('admin-top', 'api_toggle_hidden_panel.json'))?>'
							data-entry='<?php assign(json_encode(array('entryId' => $entry->id,'service_prefix'=>$entry->getServicePrefix(), 'brandId'=>$data['brand']->id)))?>'
              				data-priority='<?php assign($entry->priority_flg)?>'><span class="switchInner"><span
								class="selectON">ON</span><span class="selectOFF">OFF</span></span></a>
              <?php else:?>
              <a href="#" class="switch on"  id="switch<?php assign($entry->id)?>"
                            data-hiddenurl = '<?php assign(Util::rewriteUrl('admin-top', 'api_toggle_hidden_panel.json'))?>'
							data-entry='<?php assign(json_encode(array('entryId' => $entry->id,'service_prefix'=>$entry->getServicePrefix(), 'brandId'=>$data['brand']->id)))?>'
              				data-priority='<?php assign($entry->priority_flg)?>'><span class="switchInner"><span class="selectON">ON</span><span
								class="selectOFF">OFF</span></span></a>
              <?php endif;?>
              <select class="prioritize" id="select<?php assign($entry->id)?>" data-prioritizeurl = '<?php assign(Util::rewriteUrl('admin-top', 'api_toggle_priority_panel.json'))?>'
							data-entry='<?php assign(json_encode(array('entryId' => $entry->id, 'service_prefix'=>$entry->getServicePrefix(), 'brandId'=>$data['brand']->id)))?>'
							data-editurl = '<?php assign(Util::rewriteUrl('admin-top', 'edit_link_entry_form', array($entry->id),array('p'=>$params['p'])))?>'
							>
							<option value="default">操作</option>
							<?php if($entry->hidden_flg == 0):?>
							<option value="fixed" ><?php if($entry->priority_flg == 0) assign('優先表示'); else assign('優先表示を解除')?></option>
							<?php endif;?>
							<option
								value="edit">編集</option>
							<option
								value="delete">削除</option>
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
			<span class="attention1">このリンクを削除しますか？</span>
		</p>
		<p class="btnSet">
			<span class="btn2"><a href="#closeModal" class="middle1">キャンセル</a></span><span
				class="btn4"><a id="delete_area" href="#" data-url = '<?php assign(Util::rewriteUrl('admin-top', 'api_delete_link_entry.json'))?>' data-callback = '<?php assign(Util::rewriteUrl('admin-top', 'link_entries', null,array('p'=>$this->params['p'])))?>' class="middle1">削除する</a></span>
		</p>
	</section>
</div>

<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script'=>array('LinkEntriesService')))) ?>