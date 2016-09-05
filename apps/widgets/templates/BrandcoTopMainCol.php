<?php $i=1;?>
<div id="jsTopSortable" class="jsPanelWrap">
<?php $isFirstOfNormalPanel = 1;?>
<?php foreach ($data['panel_list'] as $panel):?>

	<?php if($isFirstOfNormalPanel):?>
	<?php if($panel['panelType'] == BrandcoTopMainCol::PANEL_TYPE_NORMAL):?>
		</div>
        <div id="jsNormalSortable" class="jsPanelWrap">
    <?php $isFirstOfNormalPanel=0;?>
	<?php endif;?>		
	<?php endif;?>

	<?php if($panel['streamName'] == 'FacebookStream'):?>
        <?php write_html($this->parseTemplate('BrandcoTopMainColFBPanel.php', array(
			'panel' => $panel,
		    'panelTag' => $i,
		    'brand'=>$data['brand'],
            'isLoginAdmin' => $data['isLoginAdmin'],
		)))/*Facebookパネル*/ ?>

	<?php elseif ($panel['streamName'] == 'TwitterStream'):?>
        <?php write_html($this->parseTemplate('BrandcoTopMainColTWPanel.php', array(
			'panel' => $panel,
		    'panelTag' => $i,
		    'brand'=>$data['brand'],
            'isLoginAdmin' => $data['isLoginAdmin'],
		)))/*twitterパネル*/ ?>

	<?php elseif($panel['streamName'] == 'LinkEntry'):?>
        <?php write_html($this->parseTemplate('BrandcoTopMainColLIPanel.php', array(
			'panel' => $panel,
		    'panelTag' => $i,
		    'brand'=>$data['brand'],
            'isLoginAdmin' => $data['isLoginAdmin'],
		)))/*Linkパネル*/ ?>

	<?php elseif($panel['streamName'] == 'YoutubeStream'):?>
        <?php write_html($this->parseTemplate('BrandcoTopMainColYTPanel.php', array(
			'panel' => $panel,
		    'panelTag' => $i,
		    'brand'=>$data['brand'],
            'isLoginAdmin' => $data['isLoginAdmin'],
		)))/*Youtubeパネル*/ ?>

    <?php elseif($panel['streamName'] == 'RssStream'):?>
        <?php write_html($this->parseTemplate('BrandcoTopMainColRSSPanel.php', array(
            'panel' => $panel,
            'panelTag' => $i,
            'brand'=>$data['brand'],
            'isLoginAdmin' => $data['isLoginAdmin'],
        )))/*RSSパネル*/ ?>

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

    <?php elseif($panel['streamName'] == 'PageStream'):?>
        <?php write_html($this->parseTemplate('BrandcoTopMainColPGPanel.php', array(
            'panel'         => $panel,
            'panelTag'      => $i,
            'brand'         => $data['brand'],
            'isLoginAdmin'  => $data['isLoginAdmin'],
        )))/*ページパネル*/ ?>

	<?php endif; ?>
	<?php $i++;?>
<?php endforeach;?>
<?php if(Util::isSmartPhone()):?>
<p class="jsPanel loading" id="more_loading" style="display:none">
<img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" alt="loading">
<!-- /.loading --></p>
<p class="jsPanel morePanels" id="more_area">
<span class="btn3"><a href="javascript:void(0)" class="small1" id="more_link">more</a></span>
<?php
if ($_GET['preview']) {
    $query_params = array(
        'preview' => $_GET['preview'],
        'sp_mode' => 'on'
    );
}
$query_params['p'] = '';
?>
<input type="hidden" id="more_ajax_url" value="<?php assign(Util::rewriteUrl('admin-top', 'get_panel_for_page', null, $query_params))?>" />
<input type="hidden" id="sp_page_per_count" value="<?php assign($data['sp_page_per_count']) ?>" />
<input type="hidden" id="total_count" value="<?php assign($data['total_count']) ?>" />
</p>
<?php endif;?>
</div>
<?php if($data['isLoginAdmin']):?>
    <p id="globalUrl" data-priorityurl = '<?php assign(Util::rewriteUrl('admin-top', 'api_toggle_priority_panel.json'))?>'
                      data-chagedisplayurl = '<?php assign(Util::rewriteUrl('admin-top', 'api_change_display_panel.json'))?>'
                      data-hiddenurl = '<?php assign(Util::rewriteUrl('admin-top', 'api_toggle_hidden_panel.json'))?>'
                      data-dragurl = '<?php assign(Util::rewriteUrl('admin-top', 'api_drag_panel.json'))?>'></p>
<?php if(count($data['panel_list'])>0):?>
  <div class="modal2 jsModal">
    <section class="modalCont-small jsModalCont" id="jsModalCont">
      <h1>確認</h1>
      <p><span class="attention1">このパネルを非表示にしますか？</span></p>
      <p class="btnSet"><span class="btn2"><a href="#closeModal" class="middle1">キャンセル</a></span><span class="btn4"><a id="delete_area_top" href="javascript:void(0)" class="middle1">非表示にする</a></span></p>
    </section>
  </div>
<?php endif;?>
<?php write_html($this->csrf_tag()); ?>
<?php endif;?>
