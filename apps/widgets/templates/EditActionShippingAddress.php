<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX)? 'disabled' : '' ?>
<?php endif; ?>
    <section class="moduleEdit1">
        <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable'=>$disable))); ?>
        <?php write_html($this->parseTemplate('CpActionModuleImage.php', array('disable'=>$disable))); ?>
        <?php write_html($this->parseTemplate('CpActionModuleText.php', array('disable'=>$disable))); ?>
        <section class="moduleCont1">
            <h1 class="editCheck1 jsModuleContTile">取得する情報</h1>
            <div class="moduleSettingWrap jsModuleContTarget">
                <ul class="moduleSetting">
                    <li><label><?php write_html( $this->formCheckBox( 'name_required', array($this->getActionFormValue('name_required')), array($disable=>$disable), array('1' => '氏名'))); ?></label>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('name_required')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('name_required') )?></p>
                        <?php endif; ?></li>
                    <li><label><?php write_html( $this->formCheckBox( 'address_required', array($this->getActionFormValue('address_required')), array($disable=>$disable), array('1' => '住所'))); ?></label>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('address_required')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('address_required') )?></p>
                        <?php endif; ?></li>
                    <li><label><?php write_html( $this->formCheckBox( 'tel_required', array($this->getActionFormValue('tel_required')), array($disable=>$disable), array('1' => '電話番号'))); ?></label>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('tel_required')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('tel_required') )?></p>
                        <?php endif; ?></li>
                <!-- /.moduleSetting --></ul>
            <!-- /.moduleSettingWrap --></div>
        <!-- /.moduleCont1 --></section>
        <?php write_html($this->parseTemplate('CpActionModuleButton.php', array('disable'=>$disable))); ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('CpActionModuleDeadLine')->render([
            'ActionForm'       => $data['ActionForm'],
            'ActionError'      => $data['ActionError'],
            'cp_action'        => $data['action'],
            'is_login_manager' => $data['pageStatus']['isLoginManager'],
            'disable'          => $disable,
        ])); ?>
    <!-- /.moduleEdit1 --></section>

<section class="modulePreview1">
    <header class="modulePreviewHeader">
        <p>スマートフォン<a href="#" class="toggle_switch left jsModulePreviewSwitch">toggle_switch</a>PC</p>
        <!-- /.modulePreviewHeader --></header>

    <div class="displaySP jsModulePreviewArea">
        <section class="messageWrap">
            <section class="message">
                <p class="messageImg"><img src="" width="600" height="300" id="imagePreview"></p>

                <section class="messageText" id="textPreview"></section>

                <ul class="commonTableList1">
                    <li class="element_name">
                        <p class="title1">
                            <span class="require1">氏名（かな）</span>
                            <!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <label class="editName"><span>姓</span><input type="text" class="name"></label><label class="editName"><span>名</span><input type="text" class="name"></label>
                                </span>
                                <span class="editInput">
                                    <label class="editName"><span>せい</span><input type="text" class="name"></label><label class="editName"><span>めい</span><input type="text" class="name"></label>
                                <!-- /.editInput --></span>
                            <!-- /.itemEdit --></p>
                    </li>
                    <li class="element_address">
                        <p class="title1">
                            <span class="require1">郵便番号</span>
                            <!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <input type="text" class="inputNum">－<input type="text" class="inputNum">
                                    <a href="#">住所検索</a><span class="supplement1">※半角数字</span>
                                <!-- /.editInput --></span>
                            <!-- /.itemEdit --></p>
                    </li>
                    <li class="element_address">
                        <p class="title1">
                            <span class="require1">都道府県</span>
                            <!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <?php write_html($this->formSelect("pref", '13'/*東京*/, array(), $data['prefectures']));?>
                                    <!-- /.editInput --></span>
                            <!-- /.itemEdit --></p>
                    </li>
                    <li class="element_address">
                        <p class="title1">
                            <span class="require1">市区町村</span>
                            <!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <input type="text">
                                <!-- /.editInput --></span>
                            <!-- /.itemEdit --></p>
                    </li>
                    <li class="element_address">
                        <p class="title1">
                            <span class="require1">番地</span>
                            <!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <input type="text">
                                <!-- /.editInput --></span>
                            <!-- /.itemEdit --></p>
                    </li>
                    <li class="element_address">
                        <p class="title1">建物<!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <input type="text">
                                <!-- /.editInput --></span>
                            <!-- /.itemEdit --></p>
                    </li>
                    <li class="element_tel">
                        <p class="title1">
                            <span class="require1">電話番号</span>
                            <!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <input type="text" class="inputNum">－<input type="text" class="inputNum">－<input type="text" class="inputNum">
                                <!-- /.editInput --></span>
                            <span class="supplement1">※半角数字</span>
                            <!-- /.itemEdit --></p>
                    </li>
                    <!-- /.commonTableList1 --></ul>

                <div class="messageFooter">
                    <ul class="btnSet">
                        <li class="btn3"><a href="javascript:void(0)" class="large1" id="btnPreview"></a></li>
                    </ul>
                </div>
            </section>
        </section>
    </div>

    <!-- /.modulePreview --></section>
