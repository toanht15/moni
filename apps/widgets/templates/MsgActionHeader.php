<h1 class="hd1">
    <img src="<?php assign($this->setVersion('/img/icon/iconMail2.png')); ?>" width="40" height="40" alt="campaign img" class="hdCampaignIcon"><span class="hdInnerText"><?php assign($data['cp']->getTitle())?></span>
    <?php if($data['enable_archive']):?>
        <?php if ($data['cp']->archive_flg == Cp::ARCHIVE_ON): ?>
            <a href="<?php write_html(Util::rewriteUrl('admin-cp', 'save_public_campaign_into_archive', array($data['cp']->id), array('p' => $_GET['p'], 'type' => $_GET['type']))) ?>" class="archiveOut"><span class="textBalloon1"><span>アーカイブから出す</span></span></a>
        <?php else: ?>
            <a href="javascript:void(0)" data-type="msg" data-url="<?php write_html(Util::rewriteUrl('admin-cp', 'save_public_campaign_into_archive', array($data['cp']->id), array('p' => $_GET['p'], 'type' => $_GET['type']))) ?>" class="trashbox"><span class="textBalloon1"><span>削除する</span></span></a>
        <?php endif; ?>
    <?php endif; ?>
</h1>
<?php if($data['user_list_page']): ?>
    <ul class="campaignAction">
        <ul class="action">
            <?php if($data['first_photo_action']): ?>
                <li>
                        <span class="font-download">
                            <select name="<?php assign('download_photo_'.$data['cp']->id)?>">
                                <option value="" selected>写真ダウンロード</option>
                                <option value="<?php assign(Util::rewriteUrl('admin-cp', 'download_photo_image_zip', array($data['cp']->id, $data['first_photo_action'])))?>">CSV+画像</option>
                                <option value="<?php assign(Util::rewriteUrl('admin-cp', 'download_user_photo_data_zip', array($data['cp']->id, $data['first_photo_action'])))?>">エクセル</option>
                            </select>
                        </span>
                </li>
            <?php endif; ?>

            <?php if($data['first_instagram_hashtag_action']): ?>
                <li>
                        <span class="font-download">
                            <select name="<?php assign('download_instagram_image'.$data['cp']->id)?>">
                                <option value="" selected>Instagram投稿ダウンロード</option>
                                <option value="<?php assign(Util::rewriteUrl('admin-cp', 'download_instagram_hashtag_post_image_zip', array($data['cp']->id, $data['first_instagram_hashtag_action']),array('file_type' => '1')))?>">CSV+画像</option>
                                <option value="<?php assign(Util::rewriteUrl('admin-cp', 'download_instagram_hashtag_post_image_zip', array($data['cp']->id, $data['first_instagram_hashtag_action']),array('file_type' => '2')))?>">エクセル</option>
                            </select>
                        </span>
                </li>
            <?php endif; ?>
        </ul>
    </ul>
<?php endif; ?>


<?php write_html(aafwWidgets::getInstance()->loadWidget('CpHeaderActionList')->render(
    array(
        'cp' => $data['cp'],
        'group_array' => $data['group_array'],
        'action' => $data['action'],
        'group' => $data['group'],
        'user_list_page' => $data['user_list_page'],
        'pageStatus' => $data['pageStatus'],
        'enable_archive' => $data['enable_archive'],
        'isHideDemoFunction' => $data['isHideDemoFunction'],
    )
)); ?>
