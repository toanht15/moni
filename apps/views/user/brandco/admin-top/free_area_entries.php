<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array('brand' => $data['brand'],))) ?>
  <article class="modalInner-large">
    <header>
      <h1>フリーエリアリスト</h1>
    </header>
    <section class="addNew">
    <a href="<?php assign(Util::rewriteUrl('admin-top', 'edit_free_area_entry_form', array(0)))?>" class="iconAdd1">新規作成</a>
    </section>
    <section class="modalInner-cont">
      <section class="editContList">
        <ul>
        <?php foreach($data['freeAreaEntries'] as $entry):?>
        <?php
        $dateInfo = date_create($entry->updated_at);
        $last_update = date_format($dateInfo, 'Y年m月d日 H:i:s');
         ?>
        	<li class="<?php if ($entry->public_flg == 1) assign('contDisplay')?>" id="free_area<?php assign($entry->id)?>">
            <p class="postText">
              <a href="<?php assign(Util::rewriteUrl('admin-top', 'edit_free_area_entry_form', array($entry->id),array('p'=>$this->params['p'])))?>"><span class="supplement1"><?php assign($last_update)?></span><span><?php write_html($this->cutLongText($entry->body,40,'...'))?></span></a>
            </p>
            <p class="action" >
              <select name="" class="selectAction" data-url = '<?php assign(Util::rewriteUrl('admin-top', 'api_free_area_change_public_flag.json'))?>' data-callback = '<?php assign(Util::rewriteUrl('admin-top', 'free_area_entries', null,array('p'=>$this->params['p'])))?>' data-entry="<?php assign('entryId='.$entry->id.'&service_prefix='.$entry->getServicePrefix())?>">
                <option value="default" >操作</option>
                <option value="public"><?php if($entry->public_flg == '0') assign('使用'); else assign('非使用');?></option>
                <option value="<?php assign(Util::rewriteUrl('admin-top', 'edit_free_area_entry_form', array($entry->id),array('p'=>$this->params['p'])))?>">編集</option>
                <option value="delete">削除</option>
              </select>
            </p>
          </li>          
	<?php endforeach;?>
        </ul>
          <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoModalPager')->render(array(
                'TotalCount' => $data['freeAreaCount'],
                'CurrentPage' => $this->params['p'],
                'Count' => $data['limit'],
            ))) ?>
      </section>
    </section>
    <footer>
      <p class="btn2"><a href="#closeModalFrame" data-type="refreshTop">閉じる</a></p>
    </footer>
  </article>
<?php write_html($this->csrf_tag()); ?>
  <div class="modal2 jsModal">
    <section class="modalCont-small jsModalCont" id="jsModalCont">
      <h1>確認</h1>
      <p><span class="attention1">このエリアを削除しますか？</span></p>
      <p class="btnSet"><span class="btn2"><a href="#closeModal" class="middle1">キャンセル</a></span><span class="btn4"><a id="delete_area" href="#" data-url = '<?php assign(Util::rewriteUrl('admin-top', 'api_delete_free_area_entry.json'))?>' data-callback = '<?php assign(Util::rewriteUrl('admin-top', 'free_area_entries', null,array('p'=>$this->params['p'])))?>' class="middle1">削除する</a></span></p>
    </section>
  </div>

<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script'=>array('FreeAreaEntriesService')))) ?>