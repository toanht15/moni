<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array('brand' => $data['brand'])))?>

<article class="modalInner-large">
    <header class="innerIG">
        <h1><img src="<?php assign($data['image_url'])?>" alt="" width="60" height="60">パネル管理</h1>
    </header>
    <section class="modalInner-cont">
        <section <?php if($data['entries']) write_html('class="editContList"')?>>
            <?php if(!$data['entries']): ?>
                インストールしたソーシャルメディアの投稿を取得するには、「設定」を確認してから、取得ボタンをクリックしてください。
                <p class="btnSet">
                    <span class="btn3"><a href="javascript:void(0);" id="loadPanel" data-url="<?php assign(Util::rewriteUrl('admin-top', 'api_get_instagram_recent_media.json', array($data['brandSocialAccountId']))) ?>" data-callbackurl="<?php assign(Util::rewriteUrl('admin-cp', 'select_instagram_entry', array(),array('tgt_act_id'=>$data['brandSocialAccountId'],'action_id'=>$data['cp_action_id']))) ?>" >取得</a></span>
                </p>
            <?php endif; ?>
            <ul id="igEntryList">
                <?php foreach($data['entries'] as $entry):?>
                    <li class="<?php if($entry->priority_flg == '1') assign('contFixed')?>" id="li<?php assign($entry->id)?>">
                        <p class="postText">
                                <span class="supplement1"><?php $dateInfo = date_create($entry->pub_date); assign(date_format($dateInfo, 'Y年m月d日 H:i:s'));?></span>
                                <?php if($entry->image_url):?><img src="<?php assign($entry->image_url) ?>" id="entryImg_<?php assign($entry->id) ?>" data-link="<?php assign($entry->link) ?>" width="70" height="70" alt=""><?php endif;?>
                                <span><?php assign($entry->cutLongText(json_decode($entry->extra_data)->caption->text, 185, '...')); ?></span>
                        </p>
                        <p class="action">
                            <label><input type="radio" name="entry_id" id="<?php assign($entry->id) ?>" value="<?php assign($entry->id) ?>">表示</label>
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
            <span class="btn2"><a href="#closeModalFrame">戻る</a></span>
            <span class="btn3"><a href="#closeModalFrame" id="selectIgEntry">選択</a></span>
        </p>
    </footer>
</article>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<?php write_html($this->csrf_tag()); ?>
<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script'=>array('SelectInstagramEntryService')))) ?>
