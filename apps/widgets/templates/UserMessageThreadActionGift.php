<?php if ($data['message_info']['action_status']->status == CpUserActionStatus::JOIN):
    $disabled = 'disabled';
endif; ?>
<section class="message jsMessage" id="message_<?php assign($data['message_info']["message"]->id); ?>">
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="executeGiftActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_gift_action.json")); ?>" method="POST" enctype="multipart/form-data">
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_gift_action_id', $data['message_info']["concrete_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>
        <?php write_html($this->formHidden('brand_id', $data['pageStatus']['brand']->id));?>


        <?php if($data["message_info"]["concrete_action"]->image_url): ?>
            <p class="messageImg"><img src="<?php assign($data["message_info"]["concrete_action"]->image_url); ?>" alt="campaign img"></p>
        <?php endif; ?>
        <?php $message_text = $data['message_info']['concrete_action']->html_content ? $data['message_info']['concrete_action']->html_content : $this->toHalfContentDeeply($data['message_info']['concrete_action']->text); ?>
        <section class="messageText"><?php write_html($message_text); ?></section>
        <?php if ($data['message_info']['concrete_action']->card_required):?>
            <?php if (!isset($disabled)) :?>
                <link rel="stylesheet" href="<?php assign_js($this->setVersion('/web_font/mplus-2p-medium.ttf'))?>">
                <div class="messageGift jsMessageGift <?php assign(Util::isSmartPhone() ? 'SP' : 'PC');?>" id="giftModule1">
                    <div class="giftCarousel flexslider jsGiftcardSlider">
                        <ul class="slides" id="messageSlider">
                            <?php foreach($data['gift_card_upload'] as $element):?>
                                <li><img src="<?php assign($element->image_url)?>" alt="Card title" class="jsGiftCardCange"></li>
                            <?php endforeach;?>
                        </ul>
                        <!-- /.giftCarousel --></div>

                    <p class="iconError1 jsGiftCardInputError" style="display: none">入力してください</p>
                    <div class="giftCard jsGiftCard" id="giftCard1">
                        <p class="cardBackground jsCardBackground <?php assign($data['gift_message']->image_url != '' ? 'hasCard' : '');?>">
                            <img src="<?php assign($data['gift_message']->image_url != '' ? $data['gift_message']->image_url : $element->image_url)?>" alt="<?php assign($element->image_url)?>">
                            <!-- /.cardBackground --></p>

                        <p class="cardArea jsGiftCardMessage" id="giftCardMessage1">
                            <input type="text" class="cardAddressee jsCardAddressee" placeholder="あて先" value="<?php assign($data['gift_message']->receiver_text != '' ? $data['gift_message']->receiver_text : '')?>" style="left: <?php assign_js($data['gift_card_config']->to_x)?>px; top: <?php assign_js($data['gift_card_config']->to_y)?>px; font-size: <?php assign_js($data['gift_card_config']->to_text_size)?>px; width: <?php assign_js($data['gift_card_config']->to_size)?>px; color: <?php assign_js($data['gift_card_config']->text_color)?>;">
                            <textarea name="" id="" class="cardMessage jsCardMessage" style="line-height:<?php assign_js($data['gift_card_config']->content_text_size * 1.5)?>px; left: <?php assign_js($data['gift_card_config']->content_x)?>px; top: <?php assign_js($data['gift_card_config']->content_y)?>px; width: <?php assign_js($data['gift_card_config']->content_width)?>px; height: <?php assign_js($data['gift_card_config']->content_height)?>px; font-size: <?php assign_js($data['gift_card_config']->content_text_size)?>px; color: <?php assign_js($data['gift_card_config']->text_color)?>; resize: none"><?php write_html($data['gift_message']->content_text != '' ? $data['gift_message']->content_text : $data['gift_card_config']->content_default_text)?></textarea>
                            <input type="text" class="cardSender jsCardSender" placeholder="送り主" value="<?php assign($data['gift_message']->sender_text != '' ? $data['gift_message']->sender_text : '')?>" style="left: <?php assign_js($data['gift_card_config']->from_x)?>px; top: <?php assign_js($data['gift_card_config']->from_y)?>px; font-size: <?php assign_js($data['gift_card_config']->from_text_size)?>px; width: <?php assign_js($data['gift_card_config']->from_size)?>px; color: <?php assign_js($data['gift_card_config']->text_color)?>;">
                            <!-- /.cardArea --></p>
                        <!-- /.giftCard --></div>
                    <!-- /.messageGift --></div>
                <?php write_html($this->formHidden('image_generate_url', Util::rewriteUrl('messages', "api_execute_image_generate.json"))); ?>
                <ul class="btnSet" id="imageGenerateBtn">
                    <li class="btn3"><a href="javascript:void(0)" class="large1" id="cardGenerate"><?php assign($data["message_info"]["concrete_action"]->button_label_text); ?></a></li>
                    <!-- /.btnSet --></ul>
            <?php else:?>
                <p class="messageImg"><img src="<?php assign($data["gift_message"]->image_url); ?>" alt="gift card"></p>
            <?php endif;?>

            <div id="editGiftCard" class="messageGift jsMessageGift" style="display: <?php assign(isset($disabled) ? 'block' : 'none')?>;">

                <div class="editButton">
                    <p class="btn2"><a href="javascript:void(0)" class="small1 jsEditGiftCardBtn" style="display: <?php assign(isset($disabled) ? 'none' : 'block')?>">再編集</a></p>
                </div>

                <div class="cardUrl">
                    <p><strong>ギフトのURL</strong></p>
                    <p class="jsGreetingCardUrl"><?php assign(Util::rewriteUrl('gift', 'card', array($data['gift_message']->param_hash . ':' . $data['gift_message']->id), array()))?></p>
                </div>
                <!-- /.messageGift --></div>
        <?php else:?>
            <div class="messageGift jsMessageGift">
                <div class="cardUrl">
                    <p><strong>ギフトのURL</strong></p>
                    <p class="jsGreetingCardUrl"><?php assign(Util::rewriteUrl('gift', 'card', array($data['gift_message']->param_hash . ':' . $data['gift_message']->id), array()))?></p>
                </div>
                <!-- /.messageGift --></div>
        <?php endif;?>
        <ul class="btnSet jsSnsBtnList" id="snsBtn" style="display: <?php assign(isset($disabled) || (!isset($disabled) && !$data['message_info']['concrete_action']->card_required) ? 'block' : 'none')?>;">
            <?php if(isset($disabled)) :?>
                <?php if (Util::isSmartPhone()):?>
                    <li class="btnSnsLn1 jsLineSendingBtn"><span>LINE<br>で贈る</span></li>
                <?php else:?>
                    <li class="btnSnsFb1 jsFBSendingBtn"><span>Facebook<br>で贈る</span></li>
                <?php endif;?>
                <li class="btnMail1 jsMailSendingBtn"><span>メール<br>で贈る</span></li>
            <?php else :?>
                <?php if (Util::isSmartPhone()):?>
                    <li class="btnSnsLn1 jsLineSendingBtn"><a href="javascript:void(0)" class="arrow1"><span class="inner">LINE<br>で贈る</span></a></li>
                <?php else:?>
                    <li class="btnSnsFb1 jsFBSendingBtn"><a href="javascript:void(0)" class="arrow1"><span class="inner">Facebook<br>で贈る</span></a></li>
                <?php endif;?>
                <li class="btnMail1 jsMailSendingBtn"><a href="javascript:void(0)" class="arrow1"><span class="inner">メール<br>で贈る</span></a></li>
            <?php endif;?>
            <!-- /.btnSet --></ul>

    </form>
    <!-- /.message --></section>

    <?php //【Android低いバージョン】モーダルでグリーティングカードを設定する ?>
    <?php if (!isset($disabled)) :?>
        <div class="modal1 jsModal" id="modal1">
            <section class="modalCont-medium jsModalCont jsModalMessage" id="modal_message_<?php assign($data['message_info']["message"]->id); ?>">
                <dl class="modalGiftMessage jsModalGiftMessage">
                    <dt>宛名</dt>
                    <dd><input type="text" placeholder="あて先" value="<?php assign($data['gift_message']->receiver_text != '' ? $data['gift_message']->receiver_text : '')?>" class="cardAddressee jsCardAddressee"></dd>
                    <dt>メッセージ本文</dt>
                    <dd><textarea class="cardMessage jsCardMessage"><?php write_html($data['gift_message']->content_text != '' ? $data['gift_message']->content_text : $data['gift_card_config']->content_default_text)?></textarea></dd>
                    <dt>差出人</dt>
                    <dd><input type="text" placeholder="送り主" value="<?php assign($data['gift_message']->sender_text != '' ? $data['gift_message']->sender_text : '')?>" class="cardSender jsCardSender"></dd>
                    <!-- /.modalGiftMessage --></dl>

                <p class="btnSet"><span class="btn3"><a href="javascript:void(0)" class="middle1" id="setMessageAndroid">決定</a></span></p>
                <p>
                    <a href="javascript:void(0)" onclick="closeModal(1);" class="modalCloseBtn">キャンセル</a>
                </p>
                <!-- /.modalCont-medium.jsModalCont --></section>
            <!-- /#modal1.modal1.jsModal --></div>
    <?php endif;?>
<?php write_html($this->scriptTag("user/UserActionGiftService")); ?>