<?php if ($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX) ? 'disabled' : '' ?>
<?php endif; ?>

<section class="moduleEdit1">
    <section class="moduleCont1">

        <!-- input area -->

        <div class="moduleSettingWrap jsModuleContTarget">
            <dl class="moduleSettingList">
                <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable' => $disable))); ?>
                <?php write_html($this->parseTemplate('CpActionModuleImage.php', array('disable' => $disable))); ?>

                <section class="moduleCont1">
                    <h1 class="editText1 jsModuleContTile">テーマ<small class="textLimit">（最大<?php assign(CpValidator::MAX_TEXT_LENGTH)?>文字）</small></h1>
                    <div class="moduleSettingWrap jsModuleContTarget">
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('text')): ?>
                            <p class="iconError1"><?php assign ( $this->ActionError->getMessage('text') )?></p>
                        <?php endif; ?>
                        <p>
                            <?php write_html( $this->formTextArea( 'text', PHPParser::ACTION_FORM, array('maxlength'=>CpValidator::MAX_TEXT_LENGTH, 'cols'=>25, 'rows'=>10,'id'=>'jsTextArea',$data['disable']=>$data['disable']))); ?>
                            <!-- Campaign Status 1: STATUS_FIX, 2: DEFAULT -->
                            <a href="javascript:void(0);"
                               data-link="<?php assign(Util::rewriteUrl('admin-blog', 'file_list', array(), array('f_id' => BrandUploadFile::POPUP_FROM_INSTAGRAM_HASHTAG, 'stt' => ($data['disable'] ? 1 : 2)))) ?>"
                               class="openNewWindow1 jsFileUploaderPopup">ファイル管理から本文に画像URL挿入</a>
                            <br>
                            <a href="javascript:;"
                               class="openNewWindow1"
                               id="markdown_rule_popup"
                               data-link="<?php assign(Util::rewriteUrl('admin-cp', 'markdown_rule')); ?>" >
                                文字や画像の装飾について</a>
                        </p>
                        <!-- /.moduleSettingWrap --></div>
                    <!-- /.moduleCont1 --></section>

                <dt class="moduleSettingTitle jsModuleContTile jsDisconnected">アカウント未入力</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <p>
                        <label>
                            <input type="checkbox" class="jsSkip" value="1" name="skip_flg" <?php assign($this->getActionFormValue('skip_flg') ? 'checked' : '') ?> <?php assign($disable) ?> />スキップを許可
                        </label>
                        <p><small>※ユーザが投稿フローを経ずに次に進めます</small></p>
                        <p><small>※スキップ後の投稿はできません</small></p>
                    </p>
                <!-- /.moduleSettingDetail --></dd>

                <dt class="moduleSettingTitle jsModuleContTile jsConnected">アカウント入力済み</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">

                    <section class="moduleCont2">
                        <h1 class="editHashTag jsModuleContTile">ハッシュタグ</h1>
                        <p><small>※ブランド・キャンペーン固有のハッシュタグは一番最初に入力してください</small></p>
                        <div class="moduleSettingWrap jsModuleContTarget moduleHashtag">
                            <div class="entryHashTag jsEntryHashtag">
                                <p class="hash">#</p>
                                <p class="entry"><input type="text" class="jsHashtagEntry jsHashtagClick" placeholder="Hashtag" <?php assign($disable) ?> maxlength="<?php assign(CpInstagramHashtag::MAX_HAHTAAG_LENGTH) ?>"></p>
                            <!-- /.moduleHashtag --></div>
                            <div id="jsEntryError"></div>
                            <?php if ($this->ActionError && !$this->ActionError->isValid('hashtags')): ?>
                                <p class="iconError1"><?php assign($this->ActionError->getMessage('hashtags')) ?></p>
                            <?php endif; ?>
                            <?php if (!$disable): ?>
                                <p><a href="javascript:void(0);" class="linkAdd jsHashtagAdd jsHashtagClick">追加する</a></p>
                            <?php else: ?>
                                <p class="linkAdd jsHashtagAdd jsHashtagClick">追加する</p>
                            <?php endif; ?>
                            <ul class='jsHashtagList'>
                                <?php if ($data['cp_instagram_hashtag_action']->isExistsCpInstagramHashtags()): ?>
                                    <?php
                                    $service_factory = new aafwServiceFactory();
                                    /** CpInstagramHashtagService $cp_instagram_hashtag_service */
                                    $cp_instagram_hashtag_service = $service_factory->create('CpInstagramHashtagService');
                                    ?>
                                    <?php foreach($cp_instagram_hashtag_service->getCpInstagramHashtagsOrderById($data['cp_instagram_hashtag_action']->id) as $cp_hashtag): ?>
                                        <?php if (!$disable): ?>
                                            <li class="jsHashtag" data-hashtag="#<?php assign($cp_hashtag->hashtag) ?>">#<?php assign($cp_hashtag->hashtag); ?><a href="javascript:void(0);" class="iconBtnDelete jsHashtagDelete">削除する</a></li>
                                        <?php else: ?>
                                            <li class="jsHashtag" data-hashtag="#<?php assign($cp_hashtag->hashtag) ?>">#<?php assign($cp_hashtag->hashtag); ?><p href="javascript:void(0);" class="iconBtnDelete">削除する</p></li>
                                        <?php endif; ?>
                                        <?php write_html($this->formHidden('hashtags[]', $cp_hashtag->hashtag)) ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <!-- /.moduleCont2 --></section>

                    <section class="moduleCont2">
                        <h1 class="editRadio1 jsModuleContTile">検閲</h1>
                        <div class="moduleSettingWrap jsModuleContTarget">
                            <ul class="moduleSetting">
                                <li><?php write_html($this->formRadio('approval_flg', $this->getActionFormValue('approval_flg'), array($disable => $disable, 'class' => 'setting-radio jsApproval'), array('1' => 'あり'))) ?></li>
                                <li><?php write_html($this->formRadio('approval_flg', $this->getActionFormValue('approval_flg'), array($disable => $disable,'class' => 'setting-radio jsApproval'), array('0' => 'なし'))) ?></li>
                                <!-- /.moduleSetting --></ul>
                            <!-- /.moduleSettingWrap --></div>
                        <!-- /.moduleCont2 --></section>

                    <section class="moduleCont2">
                        <h1 class="editBtn1 jsModuleContTile">ボタン文言設定</h1>
                        <div class="moduleSettingWrap jsModuleContTarget">
                            <ul class="moduleSetting">
                                <li class="btn3Edit"><span>
                                        <?php write_html($this->formText('button_label_text', PHPParser::ACTION_FORM, array('maxlength' => '80', 'class' => 'jsBtnText', $disable => $disable))); ?>
                                        <?php if ( $this->ActionError && !$this->ActionError->isValid('button_label_text')): ?>
                                            <p class="iconError1"><?php assign($this->ActionError->getMessage('button_label_text')) ?></p>
                                        <?php endif; ?>
                                </span></li>
                            <!-- /.moduleSetting --></ul>
                            <p style="margin-top: 10px">
                                <label>
                                    <input type="checkbox" class="jsAutoload" value="1" name="autoload_flg" <?php assign($this->getActionFormValue('autoload_flg') ? 'checked' : '') ?> <?php assign($disable) ?> />自動ロード
                                </label>
                            </p>
                            <p><small>※自動的に次のステップが読み込まれます</small></p>
                        <!-- /.moduleSettingWrap --></div>
                    <!-- /.moduleCont2 --></section>
                <!-- /.moduleSettingDetail --></dd>
            <!-- /.moduleSettingList --></dl>
        <!-- /.moduleSettingWrap --></div>
    <!-- /.moduleCont1 --></section>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpActionModuleDeadLine')->render([
        'ActionForm'       => $data['ActionForm'],
        'ActionError'      => $data['ActionError'],
        'cp_action'        => $data['action'],
        'is_login_manager' => $data['pageStatus']['isLoginManager'],
        'disable'          => $disable,
    ])); ?>
<!-- /.moduleEdit1 --></section>


<!-- view area -->
<section class="modulePreview1">
    <header class="modulePreviewHeader">
        <p>スマートフォン<a href="javascript:void(0);" class="toggle_switch left jsModulePreviewSwitch">toggle_switch</a>PC</p>
    <!-- /.modulePreviewHeader --></header>

    <ul class="tablink1">
        <li class="jsDisconnectedTabTgt current"><span>アカウント未入力</span></li>
        <li class="jsConnectedTabTgt"><span>アカウント入力済み</span></li>
    <!-- /.tablink1 --></ul>

    <div class="displaySP jsModulePreviewArea">
        <section class="messageWrap">

            <section class="message_hashtag jsDisconnectedTgt" style="display: block">
                <h1 class="messageHd1">Instagramに投稿しよう！</h1>
                <p class="messageImg"><img src="" width="600" height="300" class="imagePreview"></p>
                <div class="module">
                    <p class="messageText jsTextPreview"></p>
                    <dl>
                        <dt>Instagramのユーザーネームを登録してください。<br>登録後、投稿用のハッシュタグが表示されます。</dt>
                        <dd>
                            <p class="entryAccount"><input type="text" placeholder="ユーザーネームを入力してください。"></p>
                            <p class="guide"><a href="#howUserNameRegister" class="guides jsOpenModal linkQuestion">ユーザーネームの確認方法</a></p>
                            <ul class="btnSet">
                                <li class="btn3"><a href="javascript:void(0)" class="large1">登録する</a></li>
                            </ul>
                            <span class="supplement1"><br>※ユーザーネーム登録後の変更はできません。</span>
                            <p class="messageSkip"><a href="javascript:void(0)"><small class="jsSkipTgt">登録せず次へ</small></a></p>
                        </dd>
                    <!-- /.module --></dl>
                </div>
                <div class="moduleItemUserImg">
                    <div class="moduleItemUserInner">
                        <h2 class="hd2">みんなの投稿写真</h2>
                        <ul>
                            <?php for($i=0; $i < InstagramHashtagUserPost::PERPAGE_CP_EVERYONE_POST_PC; $i++): ?><li>
                                <figure><a href="javascript:void(0)"><img class="jsPreviewInstagramUserPost"
                                                                          data-modal_id="#instagram_modal"
                                                                          data-modal_url="https://instagram.com/p/3taHKFhQW9/"
                                                                          src="<?php assign($this->setVersion('/img/dummy/02.jpg')); ?>"
                                                                          width="123"
                                                                          height="123" alt="">
                                        <span class="previwe jsPreviewInstagramUserPost"
                                              data-modal_id="#instagram_modal"
                                              data-modal_url="https://instagram.com/p/3taHKFhQW9/">拡大表示する</span></a></figure>
                                </li><?php endfor; ?>
                        </ul>
                        <!-- /.moduleItemUserInner --></div>
                </div>
            <!-- /.message --></section>

            <section class="message_hashtag jsConnectedTgt" style="display: none">
                <h1 class="messageHd1">Instagramに投稿しよう！</h1>
                <p class="messageImg"><img src="" width="600" height="300" class="imagePreview"></p>
                <div class="module">
                    <p class="messageText jsTextPreview"></p>
                    <p class="userId">あなたのユーザーネームは<span>USER NAME</span>です</p>
                    <p class="hashtagText jsHashtagTextTgt"></p>

                    <div class="instaAttention">
                        <p>Instagramアプリから上記のハッシュタグをつけて投稿してください。<br>
                            Instagramアカウントは公開状態にしてください。
                        </p>
                        <!-- /.instaAttention1 --></div>
                        <ul class="btnSet jsAutoloadTgt">
                        <li class="btn3"><a href="javascript:void(0)" class="middle1 jsBtnPreview"></a></li>
                    </ul>
                <!-- /.module --></div>

                <div class="moduleItemUserImg">
                    <div class="moduleItemUserInner">
                        <h2 class="hd2">みんなの投稿写真</h2>
                        <ul>
                            <?php for($i=0; $i < InstagramHashtagUserPost::PERPAGE_CP_EVERYONE_POST_PC; $i++): ?><li>
                                <figure><a href="javascript:void(0)"><img class="jsPreviewInstagramUserPost"
                                                                          data-modal_id="#instagram_modal"
                                                                          data-modal_url="https://instagram.com/p/3taHKFhQW9/"
                                                                          src="<?php assign($this->setVersion('/img/dummy/02.jpg')); ?>"
                                                                          width="123"
                                                                          height="123" alt="">
                                        <span class="previwe jsPreviewInstagramUserPost"
                                              data-modal_id="#instagram_modal"
                                              data-modal_url="https://instagram.com/p/3taHKFhQW9/">拡大表示する</span></a></figure>
                                </li><?php endfor; ?>
                        </ul>
                    <!-- /.moduleItemUserInner --></div>
                    <p class="supplement1">※Instagramのサービス状況により、全ての画像が表示されない場合があります。</p>
                    <ul class="btnSet jsAutoloadTgt">
                        <li class="btn3"><a href="javascript:void(0)" class="middle1 jsBtnPreview"></a></li>
                    </ul>
                <!-- /.moduleItemUserImg --></div>
            <!-- /.message_hashtag --></section>

        </section>
    </div>

<!-- /.modulePreview --></section>

<div class="modal1 jsModal" id="hashtagEveryOnePost" style="display:none;height: 2708px;">
    <section class="modalCont-medium jsModalCont" style="opacity: 1; top: 1863px;">
        <figure class="modalImgPreview">
            <figcaption class="title">Instagram投稿コメント</figcaption>
            <img src="<?php assign($this->setVersion('/img/dummy/02.jpg')); ?>" alt="" width="300">
            <!-- /.modalImgPreview --></figure>
        <p><a href="#closeModal" class="modalCloseBtn">キャンセル</a></p>
        <!-- /.modalCont-medium.jsModalCont --></section>
    <!-- /#modal1.modal1.jsModal --></div>

<div class="modal1 jsModal" id="howUserNameRegister" style="height: 24748px;">
    <section class="modalCont-medium jsModalCont" style="display: block; opacity: 1; top: 17641px;">
        <figure class="modalImgPreview">
            <figcaption class="title">Instagramアプリを起動して下記のアイコンをタップ。</figcaption>
            <img src="<?php assign($this->setVersion('/img/message/instagram_explanation01.png')); ?>" alt="img title">
            <!-- /.modalImgPreview --></figure>

        <figure class="modalImgPreview">
            <figcaption class="title">プロフィールから自分のアカウントを確認する。</figcaption>
            <img src="<?php assign($this->setVersion('/img/message/instagram_explanation02.png')); ?>" alt="img title">
            <!-- /.modalImgPreview --></figure>
        <p>
            <a href="#closeModal" class="modalCloseBtn">キャンセル</a>
        </p>
        <!-- /.modalCont-medium.jsModalCont --></section>
    <!-- /#modal1.modal1.jsModal --></div>