<?php if($data['backFromSNSConnect']): /* SNS連携からの戻りの時はモーダルを開く */?>
    <script>
    jQuery(function($){
        <?php if($data['backFromSNSConnect'] == 'tw' || $data['backFromSNSConnect'] == 'gg' || $data['backFromSNSConnect'] == 'insta'):?>
        Brandco.unit.openModal('#selectPanelKind');

        <?php elseif($data['backFromSNSConnect'] == 'fb'):?>
        Brandco.unit.openModal('#connectFBPanelKind');

        <?php endif;?>
    });
</script>
<?php endif;?>

<section class="addPanel">
	<ul>
		<li><span class="addArea"><a href="#selectPanelKind" class="jsOpenModal"><span>追加する</span></a></span></li>
        <?php foreach($data['hiddenEntries'] as $entry):?>
            <?php if (get_class($entry) == 'TwitterEntry') {
                $mode = 'TW';
            } elseif (get_class($entry) == 'FacebookEntry') {
                $mode = 'FB';
            } elseif (get_class($entry) == 'YoutubeEntry') {
                $mode = 'YT';
            } elseif (get_class($entry) == 'RssEntry') {
                $mode = 'RSS';
            } elseif (get_class($entry) == 'InstagramEntry') {
                $mode = 'IG';
            } elseif (get_class($entry) == 'PhotoEntry') {
                $mode = 'PH';
            } elseif (get_class($entry) == 'PageEntry') {
                $mode = 'PG';
            } else {
                $mode = 'LI';
            }
            ?>
            <li>
                <?php if ($entry->isSocialEntry()):?>
                    <a href="#edit<?php assign($mode)?>PanelForm" data-option="<?php assign('/' . $entry->getBrandSocialAccount()->id . '/' . $entry->id.'?from=top')?>" class="jsPostPanel jsOpenModal">
                <?php else: ?>
                    <?php if ($mode == 'PH') {
                        $user = $entry->getPhotoUser();
                        $entry->panel_text = $user->photo_comment;
                    } elseif ($mode == 'LI') {
                        $entry->panel_text = $entry->body;
                    }
                    ?>
                    <a href="#edit<?php assign($mode)?>PanelForm" data-option="<?php assign('/' . $entry->id) ?>?from=top" class="jsPostPanel jsOpenModal">
                <?php endif;?>
                    <?php if ($mode == 'PH' && $user->photo_url): ?>
                        <img src="<?php assign($user->photo_url)?>" width="60" height="60" alt="">
                    <?php elseif($entry->image_url): ?>
                        <img src="<?php assign($entry->image_url)?>" width="60" height="60" alt="">
                    <?php endif; ?>
                    <?php write_html($this->nl2brAndHtmlspecialchars($this->cutLongText($entry->panel_text, 25, '...',false)))?>
                    <span class="postData jsPostData">
                        <span class="icon<?php if ($mode === 'RSS') assign('Rss'); else assign($mode)?>">
                        <?php write_html($this->nl2brAndHtmlspecialchars($this->cutLongText($entry->panel_text, 25, '...',false)))?>
                        </span>
                        <?php assign(date("Y/m/d H:i", strtotime($entry->pub_date)))?>
                    </span>
                </a>
            </li>
        <?php endforeach;?>
	</ul>
</section>