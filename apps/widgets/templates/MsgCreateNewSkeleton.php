<div class="makeStepTypeCont1">
    <section class="skeletonWrap">
        <form id="newSkeletonForm" name="newSkeletonForm" action="<?php assign(Util::rewriteUrl( 'admin-cp', 'save_setting_skeleton')); ?>" method="POST">
            <?php write_html($this->csrf_tag()); ?>
            <?php write_html($this->formHidden('groupCount', '', array("id" => "newSkeletonGroupCount"))) ?>
            <?php write_html($this->formHidden('cps_type', Cp::TYPE_MESSAGE)) ?>
            <?php write_html($this->formHidden('skeleton_type', Cp::SKELETON_NEW)) ?>
        </form>
        <p class="skeltonDetail">
            <span class="iconHelp">
            <span class="text">ヘルプ</span>
            <span class="textBalloon1">
                <span>
                  繋がっているSTEPでは、ユーザー自らの操作で次に進めます<br>
                  離れているSTEPでは、次に進むためには管理者の操作が必要です。
            </span>
            <!-- /.textBalloon1 --></span>
            <!-- /.iconHelp --></span>
            <span class="actionWrap">
                <span class="btn3"><a href="javascript:void(0)" class="middle1 newSkeletonSubmitButton">次へ</a></span>
            </span>
        </p>
        <div class="stepListEdit">
            <div class="stepListWrap">
                <ul class="stepList newSkeletonTag">
                    <li class="stepDetail_require newSkeletonGroup">
                        <h1>STEP1</h1>
                        <ul class="moduleList moduleList_message">
                            <li class="moduleDetail1 moduleDetail1Drag" data-action-type="<?php assign(CpAction::TYPE_MESSAGE) ?>">
                                <span class="addModuleL" style="display: none">追加する</span>
                                <span class="moduleIcon">
                                    <img src="<?php assign($this->setVersion('/img/module/mail1.png'))?>" height="33" width="33" alt="メッセージ">
                                    <span class="textBalloon1"><span>メッセージ</span></span>
                                </span>
                                <span class="addModuleR" style="display: none">追加する</span>
                            </li>
                        <!-- /.moduleList --></ul>
                    <!-- /.stepDetail_require --></li>
                    <li class="stepDetail_require">
                        <h1>STEP N</h1>
                        <ul class="moduleList">
                            <li class="addModuleDetail1"><span>追加する</span></li>
                        <!-- /.moduleList --></ul>
                    <!-- /.stepDetail_require --></li>
                <!-- /.stepList --></ul>
            <!-- /.stepListWrap --></div>
            <div class="deleteModule">
                <p class="">削除する</p>
            <!-- /.deleteModule --></div>
            <p class="supplement1">アイコンをドラッグ&ドロップすることでフローをカスタマイズできます。</p>
        <!-- /.stepListEdit --></div>
    <!-- /.stepListWrap --></section>

    <ul class="selectModuleList">
        <?php foreach($data['CpActionDetail'] as $key=>$value): ?>
            <li class="moduleDetail2" data-action-type="<?php assign($key) ?>"><span class="hdModuleIcon"><img src="<?php assign($this->setVersion('/img/module/'.$value['icon']))?>" width="55" height="55" alt="<?php assign($value['title']) ?>"></span><span class="moduleName"><?php assign($value['title']) ?></span></li>
        <?php endforeach; ?>
    </ul>
    <!-- 分岐どうしよう -->

<!-- /.makeStepTypeCont --></div>
