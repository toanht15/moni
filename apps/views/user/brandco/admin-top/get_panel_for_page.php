<?php foreach ($data['panel_list'] as $panel):?>
	<?php if($panel['streamName'] == 'FacebookStream'):?>
        <?php write_html($this->parseTemplate('BrandcoTopMainColFBPanel.php', array(
			'panel' => $panel,
		    'brand'=>$data['brand'],
            'isLoginAdmin' => $data['isLoginAdmin'],
		)));/*Facebookパネル*/ ?>

	<?php elseif ($panel['streamName'] == 'TwitterStream'):?>
        <?php write_html($this->parseTemplate('BrandcoTopMainColTWPanel.php', array(
			'panel' => $panel,
		    'brand'=>$data['brand'],
            'isLoginAdmin' => $data['isLoginAdmin'],
		)));/*twitterパネル*/ ?>


    <?php elseif($panel['streamName'] == 'YoutubeStream'):?>
        <?php write_html($this->parseTemplate('BrandcoTopMainColYTPanel.php', array(
            'panel' => $panel,
            'brand'=>$data['brand'],
            'isLoginAdmin' => $data['isLoginAdmin'],
        )));/*Youtubeパネル*/ ?>

	<?php elseif($panel['streamName'] == 'LinkEntry'):?>
        <?php write_html($this->parseTemplate('BrandcoTopMainColLIPanel.php', array(
			'panel' => $panel,
		    'brand'=>$data['brand'],
            'isLoginAdmin' => $data['isLoginAdmin'],
		)));/*リンクパネル*/ ?>

    <?php elseif($panel['streamName'] == 'RssStream'):?>
        <?php write_html($this->parseTemplate('BrandcoTopMainColRSSPanel.php', array(
            'panel' => $panel,
            'brand'=>$data['brand'],
            'isLoginAdmin' => $data['isLoginAdmin'],
        )));/*RSSパネル*/ ?>

    <?php elseif($panel['streamName'] == 'InstagramStream'): ?>
        <?php write_html($this->parseTemplate('BrandcoTopMainColIGPanel.php', array(
            'panel' => $panel,
            'panelTag' => $i,
            'brand'=>$data['brand'],
            'isLoginAdmin' => $data['isLoginAdmin'],
        ))) ?>

    <?php elseif($panel['streamName'] == 'PhotoStream'): ?>
        <?php write_html($this->parseTemplate('BrandcoTopMainColPHPanel.php', array(
            'panel' => $panel,
            'panelTag' => $i,
            'brand'=>$data['brand'],
            'isLoginAdmin' => $data['isLoginAdmin'],
        ))) ?>

    <?php elseif($panel['streamName'] == 'PageStream'): ?>
        <?php write_html($this->parseTemplate('BrandcoTopMainColPGPanel.php', array(
            'panel'         => $panel,
            'panelTag'      => $i,
            'brand'         => $data['brand'],
            'isLoginAdmin'  => $data['isLoginAdmin'],
        )))/*ページパネル*/ ?>

    <?php endif; ?>

<?php endforeach;?>