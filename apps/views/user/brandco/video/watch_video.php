<?php write_html($this->parseTemplate('BrandcoPopupHeader.php', $data['pageStatus'])); ?>

<article class="message_popupMovie">
    <?php write_html($this->formHidden('msg_id', $data['msg_id'])) ?>
    <p class="movieWrap">
        <video controls id="upload_url" style="width: 100%;" autoplay><source src="<?php assign($data['video_url']); ?>" type="video/mp4"></video>
        <!-- /.movieWrap --></p>

    <p class="movieSpeed">
        <span class="inner">再生速度</span>
        <?php write_html($this->formRadio('video_speed', '1.0', array('class' => 'jsVideoSpeedSelector'), $data['video_speed_list'])) ?>
    </p>

    <ul class="btnSet">
        <li class="btn1 jsCloseWindow"><span>閉じる</span></li>
        <!-- /.btnSet --></ul>
    <!-- /.message_popupMovie --></article>

<?php write_html($this->parseTemplate('BrandcoPopupFooter.php', array_merge($data['pageStatus'], array('script' => array('WatchVideoService'))))); ?>