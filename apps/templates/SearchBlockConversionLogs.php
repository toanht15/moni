<?php
$service_factory = new aafwServiceFactory();
/** @var ConversionService $conversion_service */
$conversion_service = $service_factory->create('ConversionService');
$conversions = $conversion_service->getConversionsByBrandId($data['brand_id']);
?>
<div class="customaudienceRefinement jsModuleContWrap">
    <div class="categoryLabel jsModuleContTile close">
        <p>コンバージョン</p>
        <p class="iconHelp">
            <span class="text"></span>
              <span class="textBalloon1">
                <span>
                  数値入力例<br>
                  <span class="label">50回</span><span class="sample"><span type="text" class="inputNum">50</span><span class="dash">〜</span><span type="text" class="inputNum">50</span></span><br>
                  <span class="label">50回〜100回</span><span class="sample"><span type="text" class="inputNum">50</span><span class="dash">〜</span><span type="text" class="inputNum">100</span></span>
                </span>
              <!-- /.textBalloon1 --></span>
            <!-- /.iconHelp --></p>
        <!-- /.categoryLabel --></div>
    <div class="refinementWrap jsModuleContTarget">
        <div class="setting">
            <?php foreach ($conversions as $conversion): ?>
                <div class="refinementItem jsSearchInputBlock">
                    <p class="settingLabel"><?php assign($conversion->name) ?></p>
                    <form>
                        <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_PROFILE_CONVERSION.'/'.$conversion->id)) ?>
                        <?php $data["search_type"] = CpCreateSqlService::SEARCH_PROFILE_CONVERSION;
                        $data["unit_label"] = "回";
                        $data["extern_key"] = $conversion->id;
                        ?>
                        <?php write_html($this->parseTemplate("SearchRangeInputNum.php", $data)) ?>
                    </form>
                    <!-- /.refinementItem --></div>
            <?php endforeach; ?>
            <!-- /.setting --></div>
        <!-- /.refinementWrap --></div>
    <!-- /.customaudiencRefinement --></div>