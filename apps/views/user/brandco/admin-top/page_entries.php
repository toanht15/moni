<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array('brand' => $data['brand']))); ?>
    <article class="modalInner-large">
        <header>
            <h1>ページリスト</h1>
            <a href="#" class="openLink jsOpenLink">設定</a>
            <section class="editAccount jsOpenLinkAera">
                <div class="getPostMethod" data-stream_id="<?php assign($data['stream']->id); ?>">
                    <ul>
                        <li><label for="manual" class="getPostManual" data-url="<?php assign(Util::rewriteUrl('admin-top', 'api_change_stream_hidden_flg.json'))?>">
                                <input type="radio" name="getPostMethod" id="manual" value="" <?php if($data['stream']->panel_hidden_flg) assign('checked')?>>選択した投稿だけを掲載する（事前検閲）</label></li>
                        <li><label for="auto" class="getPostAuto" data-url="<?php assign(Util::rewriteUrl('admin-top', 'api_change_stream_hidden_flg.json'))?>">
                                <input type="radio" name="getPostMethod" id="auto" value="" <?php if(!$data['stream']->panel_hidden_flg) assign('checked')?>>最新投稿を自動で掲載する（事後検閲）</label>
                        <span class="autoItemNum jsRadioToggleTarget" <?php if(!$data['stream']->panel_hidden_flg) write_html('style="display: inline-block"')?>>
                        表示上限
                        <select name="display_panel_limit" id="display_panel_limit" data-url="<?php assign(Util::rewriteUrl('admin-top', 'api_change_display_limit.json'))?>">
                            <option value="0" <?php if(!$data['stream']->display_panel_limit) assign('selected') ?>>無制限</option>
                            <?php for($i=1; $i<100; $i++): ?>
                                <option value="<?php assign($i)?>" <?php if($i == $data['stream']->display_panel_limit) assign('selected') ?>><?php assign($i)?></option>
                            <?php endfor; ?>
                        </select>
                      </span>
                        </li>
                    </ul>
                    <p>
                        事前検閲の場合、投稿はすべて「非表示」が初期状態となります。
                    </p>
                </div>
            </section>
        </header>
        <section class="modalInner-cont">
            <section class="editContList">
                <ul>
                    <?php foreach ($data['page_entries'] as $page_entry): ?>
                        <?php
                        $switch_class = $page_entry->top_hidden_flg ? 'switch off' : 'switch on';
                        $entry_data = json_encode(array('entryId' => $page_entry->id, 'service_prefix' => $page_entry->getServicePrefix()));
                        $updated_at = date_create($page_entry->updated_at);
                        $pub_date = date_create($page_entry->pub_date);
                        ?>
                        <li class="<?php if($page_entry->priority_flg) assign('contFixed')?>" id="li<?php assign($page_entry->id)?>">
                            <p class="postText">
                                <a href="<?php assign(Util::rewriteUrl('admin-top', 'edit_page_entry_form', array($page_entry->id),array('p'=>$params['p'])))?>"><span
                                        class="supplement1"><?php assign(date_format($updated_at, 'Y年m月d日 H:i:s') . '（公開日：' . date_format($pub_date, 'Y年m月d日') . '）') ?></span>
                                    <img <?php if(!$page_entry->image_url) write_html('style="display: none"')?>
                                        src="<?php assign($page_entry->image_url)?>" width="70" height="70"
                                        alt=""><span><?php write_html($page_entry->getStaticHtmlEntry()->title . '<br>&nbsp;&nbsp;' . $this->cutLongText($page_entry->panel_text, 200))?></span></a>
                            </p>

                            <p class="action">
                                表示
                                <a href="#" class="<?php assign($switch_class); ?>"
                                   data-pre_public="<?php assign($page_entry->isPrePublicPage() ? 1 : 0) ?>"
                                   data-priority="<?php assign($page_entry->priority_flg != null ? $page_entry->priority_flg : 0); ?>"
                                   data-hidden_url='<?php assign(Util::rewriteUrl('admin-top', 'api_toggle_hidden_panel.json')) ?>'
                                   data-entry='<?php assign($entry_data); ?>'>
                                    <span class="switchInner"><span class="selectON">ON</span><span class="selectOFF">OFF</span></span></a>

                                <select class="prioritize"
                                        data-prioritize_url='<?php assign(Util::rewriteUrl('admin-top', 'api_toggle_priority_panel.json')); ?>'
                                        data-entry='<?php assign($entry_data); ?>'>
                                    <option value="default">操作</option>
                                    <?php if ($page_entry != null && $page_entry->top_hidden_flg == 0 && !$page_entry->isPrePublicPage()): ?>
                                        <option
                                            value="fixed"><?php assign($page_entry->priority_flg == 0 ? '優先表示' : '優先表示を解除'); ?></option>
                                    <?php endif; ?>
                                </select>
                            </p>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoModalPager')->render(array(
                    'TotalCount' => $data['total_entries_count'],
                    'CurrentPage' => $this->params['p'],
                    'Count' => $data['page_limited'],
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

<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script' => array('PageEntriesService')))); ?>