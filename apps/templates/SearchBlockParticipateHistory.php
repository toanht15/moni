<div class="customaudienceRefinement jsModuleContWrap">
    <div class="categoryLabel jsModuleContTile close">
        <p>コミュニケーション履歴</p>
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
            <div class="refinementItem jsSearchInputBlock">
                <p class="settingLabel">キャンペーン参加回数</p>
                <form>
                    <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_CP_ENTRY_COUNT)) ?>
                    <?php $data["search_type"] = CpCreateSqlService::SEARCH_CP_ENTRY_COUNT;
                    $data["unit_label"] = "回";
                    ?>
                    <?php write_html($this->parseTemplate("SearchRangeInputNum.php", $data)) ?>
                </form>
                <!-- /.refinementItem --></div>
            <div class="refinementItem jsSearchInputBlock">
                <p class="settingLabel">キャンペーン当選回数</p>
                <form>
                    <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_CP_ANNOUNCE_COUNT)) ?>
                    <?php $data["search_type"] = CpCreateSqlService::SEARCH_CP_ANNOUNCE_COUNT;
                    $data["unit_label"] = "回";
                    ?>
                    <?php write_html($this->parseTemplate("SearchRangeInputNum.php", $data)) ?>
                </form>
                <!-- /.refinementItem --></div>
            <div class="refinementItem jsSearchInputBlock">
                <p class="settingLabel">メッセージ受信数</p>
                <form>
                    <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_MESSAGE_DELIVERED_COUNT)) ?>
                    <?php $data["search_type"] = CpCreateSqlService::SEARCH_MESSAGE_DELIVERED_COUNT;
                    $data["unit_label"] = "回";
                    ?>
                    <?php write_html($this->parseTemplate("SearchRangeInputNum.php", $data)) ?>
                </form>
                <!-- /.refinementItem --></div>
            <div class="refinementItem jsSearchInputBlock">
                <p class="settingLabel">メッセージ開封数</p>
                <form>
                    <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_MESSAGE_READ_COUNT)) ?>
                    <?php $data["search_type"] = CpCreateSqlService::SEARCH_MESSAGE_READ_COUNT;
                    $data["unit_label"] = "回";
                    ?>
                    <?php write_html($this->parseTemplate("SearchRangeInputNum.php", $data)) ?>
                </form>
                <!-- /.refinementItem --></div>
            <div class="refinementItem jsSearchInputBlock">
                <p class="settingLabel">メッセージ閲覧率</p>
                <form>
                    <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO)) ?>
                    <?php $data["search_type"] = CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO;
                    $data["unit_label"] = "%";
                    ?>
                    <?php write_html($this->parseTemplate("SearchRangeInputNum.php", $data)) ?>
                </form>
                <!-- /.refinementItem --></div>
            <!-- /.setting --></div>
        <!-- /.refinementWrap --></div>
    <!-- /.customaudiencRefinement --></div>