<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class GreetingCardGenerateService extends aafwServiceBase {
    protected $greeting_card;

    public function __construct() {
    }

    public function makeCard($gift_card_config, $message_info) {
        try {
            $this->greeting_card = new ImageGenerator($message_info['image_url'], array('FontColor' => $gift_card_config->text_color));

            //宛名
            $this->greeting_card->setStringLine(array(
                'String' => $message_info['receiver_text'],
                'InputWidthSize' => $gift_card_config->to_size,
                'FontSize' => $gift_card_config->to_text_size,
                'Width' => $gift_card_config->to_x,
                'Height' => $gift_card_config->to_y,
                'InputHeightSize' => $message_info['receiver_height'],
            ));

            //本文
            $this->greeting_card->setStringArea(array(
                'String' => $message_info['content_text'],
                'FontSize' => $gift_card_config->content_text_size,
                'Width' => $gift_card_config->content_x,
                'Height' => $gift_card_config->content_y,
                'ContentWidth' => $message_info['content_width'],
                'ContentHeight' => $gift_card_config->content_height,
            ));

            //送り主
            $this->greeting_card->setStringLine(array(
                'String' => $message_info['sender_text'],
                'InputWidthSize' => $gift_card_config->from_size,
                'FontSize' => $gift_card_config->from_text_size,
                'Width' => $gift_card_config->from_x,
                'Height' => $gift_card_config->from_y,
                'InputHeightSize' => $message_info['sender_height'],
            ));
            $output = $this->greeting_card->toDraw();

        } catch (Exception $e) {
            $errors['error'] = $e->getMessage();
            return $errors;
        }

        if (!$output) {
            $errors['error'] = "画像合成が失敗しました。";
            return $errors;
        }
        return $output;

    }

}