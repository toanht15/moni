<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array('brand' => $data['brand']))) ?>
<body class="modalInnerBody">
<article class="modalInner-large">

    <header>
        <h1>サイドメニュー編集</h1>
    </header>

    <form id="update_menu_form" action="<?php assign(Util::rewriteUrl('admin-top', 'update_side_menu')) ?>"
          method="post" enctype="multipart/form-data">
        <section class="menuAreaTitle">
            <span>メニュータイトル</span>
            <span>
                <label for="titleText">
                    <input type="radio" name="side_menu_title_type" onchange="MenusService.valueChanged();"
                           class="jsMenuTitle"
                           value="0" <?php if ($data['sideMenusType'] == Brand::menuSideTypeText) assign('checked="checked"'); ?>
                           id="titleText">タイトルテキスト</label>
                <?php if ($data['sideMenusType'] == Brand::menuSideTypeText): ?>
                    <input name="side_menu_title" oninput="MenusService.valueChanged();" type="text"
                           class="jsMenuTitleInput" maxlength="35" id="menuTitle"
                           value="<?php assign($data['sideMenusTitle']) ?>">
                    <small class="textLimit"></small><br>
                <?php else: ?>
                    <input name="side_menu_title" oninput="MenusService.valueChanged();" type="text"
                           class="jsMenuTitleInput" maxlength="35" id="menuTitle">
                    <small class="textLimit"></small><br>
                <?php endif; ?>
                <label for="titleImg">
                    <input type="radio" name="side_menu_title_type" onchange="MenusService.valueChanged();"
                           class="jsMenuTitle"
                           value="1" <?php if ($data['sideMenusType'] == Brand::menuSideTypeImage) assign('checked="checked"') ?>
                           id="titleImg">
                    ファイルアップロード</label>
                <input name="menuImage" onchange="MenusService.valueChanged();" type="file"
                       class="jsMenuTitleInput" <?php if ($data['sideMenusType'] == Brand::menuSideTypeText) assign('disabled="disabled"') ?>>
                <?php if ($this->ActionError && !$this->ActionError->isValid('menuImage')): ?>
                    <a class="attention1"><?php assign($this->ActionError->getMessage('menuImage')) ?></a>
                <?php elseif ($data['sideMenusType'] == Brand::menuSideTypeImage): ?>
                    <a href="<?php assign($data['sideMenusTitle']) ?>" target="_brank">設定されている画像を確認する。</a>
                <?php endif; ?>
            </span>
        </section>

        <section class="addNew">
            <?php if (count($data['sideMenus']) < $data['limit']): ?>
                <p class="btn3"><a href="<?php assign(Util::rewriteUrl('admin-top', 'create_side_menu_form')) ?>" class="middle1" id="iconAdd">新規メニュー追加</a></p>
            <?php else: ?>
                メニューの上限個数は20件です。
            <?php endif; ?>
        </section>

        <section class="modalInner-cont" data-menuslimit='<?php assign($data['limit']) ?>' data-createurl='<?php assign(Util::rewriteUrl('admin-top', 'create_side_menu_form')) ?>'
                 data-updateurl='<?php assign(Util::rewriteUrl('admin-top', 'update_side_menu', array(), array('redirect' => 'create_menu'))) ?>'>
        <section class="editMenuList">
            <?php write_html($this->csrf_tag()); ?>
            <?php write_html($this->formHidden('order', "", array("id" => "order"))); ?>
            <ul id="jsSortable">

                <?php foreach ($data['sideMenus'] as $menu): ?>
                    <?php $error = false; ?>
                    <?php if ($this->ActionError && (!$this->ActionError->isValid('link_' . $menu->id) || !$this->ActionError->isValid('title_' . $menu->id))): ?>
                        <?php $error = true; ?>
                    <?php endif; ?>
                    <li id="list_<?php assign($menu->id); ?>">
                        <p class="listNum"><?php assign($menu->list_order); ?></p>
                        <p class="menuTitle jsOpenActionArea_contrary">
                            <a href="javascript:void(0)"><?php assign($menu->name); ?></a>
                        </p>
                        <dl class="jsOpenActionArea" <?php if ($error) write_html('style="display: inline-block"') ?>>
                            <dt><label for="inputTitle1">タイトル</label></dt>
                            <dd><input type="text" name="title_<?php assign($menu->id); ?>"
                                       oninput="MenusService.valueChanged();"
                                       id="inputTitle_<?php assign($menu->id); ?>" value="<?php assign($menu->name); ?>"
                                       class="jsLinkTitle"></dd>
                            <?php if ($this->ActionError && !$this->ActionError->isValid('title_' . $menu->id)): ?>
                                <dt></dt>
                                <dd>
                                    <p class="attention1"><?php assign($this->ActionError->getMessage('title_' . $menu->id)) ?></p>
                                </dd>
                            <?php endif; ?>
                            <dt><label for="inputUrl1">URL</label></dt>
                            <dd><input type="url" name="link_<?php assign($menu->id); ?>"
                                       oninput="MenusService.valueChanged();" id="inputUrl<?php assign($menu->id); ?>"
                                       value="<?php assign($menu->link); ?>" class="jsLinkUrl"></dd>
                            <?php if ($this->ActionError && !$this->ActionError->isValid('link_' . $menu->id)): ?>
                                <dt></dt>
                                <dd>
                                    <p class="attention1"><?php assign($this->ActionError->getMessage('link_' . $menu->id)) ?></p>
                                </dd>
                            <?php endif; ?>

                        </dl>
                        <p class="action">表示
                            <?php if ($menu->hidden_flg === "0"): ?>
                                <a href="javascript:void(0)" data-menu_id="<?php assign($menu->id); ?>" class="switch on">
                                    <span class="switchInner" onclick="MenusService.valueChanged()">
                                        <span class="selectON">ON</span>
                                        <span class="selectOFF">OFF</span>
                                    </span>
                                </a>
                            <?php else: ?>
                                <a href="javascript:void(0)" data-menu_id="<?php assign($menu->id); ?>" class="switch off">
                                    <span class="switchInner" onclick="MenusService.valueChanged()">
                                        <span class="selectON">ON</span>
                                        <span class="selectOFF">OFF</span>
                                    </span>
                                </a>
                            <?php endif; ?>

                            <input id="hidden_flg_<?php assign($menu->id); ?>" name="hidden_flg_<?php assign($menu->id); ?>" value="<?php assign($menu->hidden_flg); ?>" type="hidden">
                            <span class="moreAction jsOpenActionArea" <?php if ($error) write_html('style="display: inline-block"') ?>>
                                <?php write_html($this->formCheckBox('is_blank_flg_' . $menu->id, $menu->is_blank_flg ? array('on') : '', array('onclick' => 'MenusService.valueChanged();'), array('on' => '別ウィンドウで開く'))); ?>
                                <span class="btn2"><a class="cmd-delete-menu small1" href="javascript:void(0)">削除</a></span></span>
                        </p>
                        <a href="javascript:void(0)" class="<?php if ($error) assign('closeAction'); else assign('openAction') ?> jsOpenAction">詳細</a></li>
                <?php endforeach; ?>
            </ul>
        </section>
        </section>
    </form>
    <footer>
        <p class="btnSet">
            <span class="btn2"><a href="#modal1" id="cancelChanges">キャンセル</a></span>
            <span class="btn3"><a href="javascript:void(0)" id="submit">保存</a></span>
        </p>
    </footer>
</article>

<div class="modal2 jsModal" id="modal1">
    <section class="modalCont-small jsModalCont">
        <p>キャンセルすると変更した内容が保存されません。よろしいですか？</p>
        <p class="btnSet"><span class="btn2"><a href="#closeModal" class="middle1">いいえ</a></span><span class="btn4"><a href="#closeModalFrame" class="middle1">はい</a></span></p>
    </section>
    <!-- /.modal2 -->
</div>

<div class="modal2 jsModal" id="modal2">
    <section class="modalCont-small jsModalCont">
        <p id="confirmMessage">変更を保存して新規メニューを追加します。</p>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModal" class="middle1">キャンセル</a></span>
            <span class="btn3"><a href="#" id="submitAndCreateMenu" class="middle1">はい</a></span>
        </p>
    </section>
    <!-- /.modal2 -->
</div>

<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script' => array('MenusService')))) ?>