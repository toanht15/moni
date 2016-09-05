<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array('title' => 'Segmentデータダウンロード','managerAccount' => $this->managerAccount,))) ?>

<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

        <div class="col-md-10 col-md-offset-2 main">
            <h1 class="page-header">Segmentデータダウンロード</h1>
            <div class="well col-md-10 col-md-offset-0">

                <p class="required">Segment名</p>
                <p class="form-group"><?php write_html( $this->formSelect('cur_segment', PHPParser::ACTION_FORM, array('class' => 'jsCurrentActiveSegment'), $data['active_segments'])) ?></p>

                <p class="required">Provision名</p>
                <p class="form-group"><?php write_html( $this->formSelect('cur_provision', PHPParser::ACTION_FORM, array('class' => 'jsCurrentSegmentProvision'), $data['segment_provisions'])) ?></p>

                <p>対象日</p>
                <p class="form-group"><?php write_html( $this->formText('start_date', PHPParser::ACTION_FORM, array('maxlength'=>'10', 'class'=>'jsDate inputDate', 'placeholder'=>'年/月/日'))); ?></p>

                <span class="btn3">
                    <a href="javascript:void(0);" class="jsProvisionDataDownloadBtn" data-href="<?php assign(Util::rewriteUrl('segment', 'download_provision_data')) ?>">DOWNLOAD</a>
                    </span>

                </div>
        </div><!-- row -->
    </div><!-- container-fluid -->
<?php write_html($this->scriptTag('SegmentDataDownloadService'))?>

<link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css'))?>">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>
<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>
