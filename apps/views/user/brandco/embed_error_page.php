<?php write_html($this->parseTemplate('EmbedIframeHeader.php', array(
    'brand' => $data['pageStatus']['brand'],
))) ?>
<?php if(!$data['staticHtmlEntry'] || $data['staticHtmlEntry']->hidden_flg):?>
    <div class="OwnedWrap">
        <section class="notFound">
            <h1>この内容は見ることができません</h1>
            <p>一時的に見れない状態にあるか、削除された可能性があります。</p>
            <p class="btnSet"><span class="btn1"><a href="javascript:void(0)" id="back_url">前に戻る</a></span></p>
        <!-- /.notFound --></section>
    <!-- /.OwnedWrap --></div>
<?php endif; ?>
<?php write_html($this->formHidden('page_url',$data['pageUrl']))?>
<?php write_html($this->formHidden('base_url',Util::getBaseUrl()))?>
<?php write_html($this->parseTemplate('EmbedIframeFooter.php', array('script'=>array('admin-blog/EmbedIframeControllService')))) ?>