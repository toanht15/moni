<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array('brand' => $data['brand']))); ?>
    <article class="modalInner-large">
        <header>
            <h1>ユーザー投稿フォトリスト</h1>
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
                        事前検閲の場合、収集した投稿はすべて「非表示」が初期状態となります。
                    </p>
                </div>
            </section>
        </header>
        <section class="modalInner-cont">
            <section class="editContList">
                <ul>
                    <?php foreach ($data['photo_entries'] as $photo_entry): ?>
                        <?php
                        $photo_user = $photo_entry->getPhotoUser();
                        $user = $photo_user->getCpUser()->getUser();
                        $entry_data = json_encode(array('entryId' => $photo_entry->id, 'service_prefix' => $photo_entry->getServicePrefix()));
                        $switch_class = $photo_entry->hidden_flg ? 'switch off' : 'switch on';
                        $entry_edit_url = Util::rewriteUrl('admin-top', 'edit_photo_entry_form', array($photo_entry->id), array('p' => $params['p']));
                        ?>

                        <li class="<?php if($photo_entry->priority_flg) assign('contFixed')?>">
                            <p class="postText">
                                <a href="<?php assign($entry_edit_url); ?>" class="jsEntryLink"
                                   data-default_url="<?php assign(Util::rewriteUrl('admin-top', 'edit_photo_entry_form')); ?>"
                                   data-page_no="<?php assign($params['p']); ?>">
                                    <span class="supplement1"><?php assign($photo_user->created_at); ?></span>
                                    <img src="<?php assign($photo_user->photo_url); ?>" width="70" height="70" alt="">
                            <span class="user">
                                <img src="<?php assign($user->profile_image_url); ?>"
                                     width="16" height="16" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';"
                                     alt="<?php assign($user->name); ?>"><?php assign($user->name); ?></span>
                                    <?php if ($photo_user->photo_title || $photo_user->photo_comment): ?>
                                        <span><?php if ($photo_user->photo_title): ?><?php assign($photo_user->photo_title); ?>
                                            <br><?php endif; ?><?php assign($photo_user->photo_comment); ?></span>
                                    <?php endif; ?>
                                </a>
                                <!-- /.postText --></p>

                            <p class="action">
                                表示
                                <a href="#" class="<?php assign($switch_class); ?>"
                                   data-priority="<?php assign($photo_entry->priority_flg != null ? $photo_entry->priority_flg : 0); ?>"
                                   data-hidden_url='<?php assign(Util::rewriteUrl('admin-top', 'api_toggle_hidden_panel.json')) ?>'
                                   data-entry='<?php assign($entry_data); ?>'>
                                    <span class="switchInner"><span class="selectON">ON</span><span class="selectOFF">OFF</span></span></a>

                                <select class="prioritize"
                                        data-prioritize_url='<?php assign(Util::rewriteUrl('admin-top', 'api_toggle_priority_panel.json')); ?>'
                                        data-entry='<?php assign($entry_data); ?>'>
                                    <option value="default">操作</option>
                                    <?php if ($photo_entry != null && $photo_entry->hidden_flg == 0): ?>
                                        <option
                                            value="fixed"><?php assign($photo_entry->priority_flg == 0 ? '優先表示' : '優先表示を解除'); ?></option>
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

<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script' => array('PhotoEntriesService')))); ?>