<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array ('brand' => $data ['brand']) ) )?>

    <article class="modalInner-large">
        <header class="innerRss">
            <h1>
                <?php if($data['stream']->image_url):?>
                <img
                    src="<?php assign($data['stream']->image_url)?>"
                    alt="" width="60" height="60">
                <?php endif;?>
                <a href="<?php assign($data['stream']->link)?>" target="_blank"><?php write_html($this->nl2brAndHtmlspecialchars($data['stream']->title))?></a>
            </h1>
            <a href="#" class="openLink jsOpenLink">設定</a>
            <section class="editAccount jsOpenLinkAera">
                <div class="getPostMethod" data-streamid = '<?php assign($data['stream']->id) ?>'>
                    <ul>
                        <li><label for="manual" class="getPostManual" data-url = '<?php assign(Util::rewriteUrl('admin-top', 'api_change_stream_hidden_flg.json'))?>'><input type="radio" name="getPostMethod"
                                                                                                          id="manual" value="" <?php if($data['stream']->entry_hidden_flg) assign('checked')?>>選択した投稿だけを掲載する（事前検閲）</label></li>
                        <li><label for="auto" class="getPostAuto" data-url = '<?php assign(Util::rewriteUrl('admin-top', 'api_change_stream_hidden_flg.json'))?>'><input type="radio" name="getPostMethod"
                                                                                                      id="auto" value="" <?php if(!$data['stream']->entry_hidden_flg) assign('checked')?>>最新投稿を自動で掲載する（事後検閲）</label>
                        <span class="autoItemNum jsRadioToggleTarget" <?php if(!$data['stream']->entry_hidden_flg) write_html('style="display: inline-block"')?>>
                        表示上限
                        <select name="display_panel_limit" id="display_panel_limit" data-url="<?php assign(Util::rewriteUrl('admin-top', 'api_change_display_limit.json'))?>">
                            <option value="0" <?php if(!$data['display_panel_limit']) assign('selected') ?>>無制限</option>
                            <?php for($i=1; $i<100; $i++): ?>
                                <option value="<?php assign($i)?>" <?php if($i==$data['stream']->display_panel_limit) assign('selected') ?>><?php assign($i)?></option>
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
                        連携したRSSを解除します。<br> <span class="btn4"><a href="javascript:void(0)"
                                                                 onclick="Brandco.helper.showConfirm('#modal1', '<?php assign('stream_prefix=RssStream&streamId='.$data['stream']->id)?>')">解除する</a></span>
                    </p>
                </div>
            </section>
        </header>
        <section class="modalInner-cont">
            <section <?php if($data['rssEntries']) write_html('class="editContList"') ?>>

                <?php if(!$data['rssEntries']): ?>
                    インストールしたソーシャルメディアの投稿を取得するには、「設定」を確認してから、取得ボタンをクリックしてください。
                    <p class="btnSet">
                        <span class="btn3"><a href="javascript:void();" id="loadPanel" data-url='<?php assign(Util::rewriteUrl('admin-top','api_get_rss_items.json',array($data['stream']->id))); ?>' data-callbackurl='<?php assign(Util::rewriteUrl('admin-top','rss_entries',array($data['stream']->id))) ?>'>取得</a></span>
                    </p>
                <?php endif; ?>
                <ul>

                    <?php foreach($data['rssEntries'] as $entry):?>

                        <li class="<?php if($entry->priority_flg == '1') assign('contFixed')?>" id="li<?php assign($entry->id)?>">
                            <p class="postText">

                                <a href="<?php assign(Util::rewriteUrl('admin-top','edit_rss_entry_form',array($entry->id))); ?>">
                                    <span class="supplement1"><?php $dateInfo = date_create($entry->pub_date); assign(date_format($dateInfo, 'Y年m月d日 H:i:s'));?></span>
                                    <?php if($entry->image_url):?><img src="<?php assign($entry->image_url) ?>" width="70" height="70" alt=""><?php endif;?>
                                    <span><?php write_html($this->nl2brAndHtmlspecialchars($entry->panel_text))?></span>
                                </a>
                            </p>
                            <p class="action">
                                表示
                                <?php if($entry->hidden_flg):?>
                                    <a href="#" class="switch off" id="switch<?php assign($entry->id)?>"
                                       data-url = '<?php assign(Util::rewriteUrl('admin-top', 'api_toggle_hidden_panel.json'))?>'
                                       data-entry='<?php assign(json_encode(array('entryId' => $entry->id,'service_prefix'=>$entry->getServicePrefix(), 'brandId'=>$data['brand']->id)))?>'
                                       data-priority='<?php assign($entry->priority_flg)?>'>
							<span class="switchInner"><span class="selectON">ON</span>
							<span class="selectOFF">OFF</span></span></a>
                                <?php else:?>
                                    <a href="#" class="switch on" id="switch<?php assign($entry->id)?>"
                                       data-url = '<?php assign(Util::rewriteUrl('admin-top', 'api_toggle_hidden_panel.json'))?>'
                                       data-entry='<?php assign(json_encode(array('entryId' => $entry->id,'service_prefix'=>$entry->getServicePrefix(), 'brandId'=>$data['brand']->id)))?>'
                                       data-priority='<?php assign($entry->priority_flg)?>'>
							<span class="switchInner"><span class="selectON">ON</span>
							<span class="selectOFF">OFF</span></span></a>
                                <?php endif;?>
                                <select class="prioritize" name="" id="select<?php assign($entry->id)?>"
                                        data-url = '<?php assign(Util::rewriteUrl('admin-top', 'api_toggle_priority_panel.json'))?>'
                                        data-entry='<?php assign(json_encode(array('entryId' => $entry->id, 'service_prefix'=>$entry->getServicePrefix(), 'brandId'=>$data['brand']->id)))?>'
                                        data-editurl = '<?php assign(Util::rewriteUrl('admin-top','edit_rss_entry_form',array($entry->id)));?>'
                                        data-entrytype = '<?php assign($entry->getType())?>'>

                                    <option value="default">操作</option>
                                    <?php if($entry->hidden_flg == 0):?>
                                        <option value="fixed"><?php if($entry->priority_flg == 0) assign('優先表示');else assign('優先表示を解除')?></option>
                                    <?php endif;?>
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
                <span class="attention1">この連携を解除しますか？</span>
            </p>
            <p class="btnSet">
                <span class="btn2"><a href="#closeModal" class="middle1">キャンセル</a></span><span
                    class="btn4"><a href="javascript:void(0)" id="delete_area" data-url = '<?php assign(Util::rewriteUrl('admin-top', 'api_disconnect_social_app.json'))?>'
                                                                               data-callbackurl = '<?php assign(Util::rewriteUrl('admin-top', 'select_panel_kind'))?>' class="middle1">解除する</a></span>
            </p>
        </section>
    </div>

<div class="modal2 jsModal" id="modal4">
    <section class="modalCont-small jsModalCont">
        <h1 id="ajaxMessage">情報を取得している時にエラーを発生しました。<br> 後で再取得してお願いします。</h1>
        <p class="btnSet">
            <span class="btn3"><a href="#closeModal" class="middle1">閉じる</a></span>
        </p>
    </section>
</div>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script'=>array('RssEntriesService')))) ?>
