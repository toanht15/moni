<?php

class StaticHtmlEntryTemplateValidator {

    private $validator;
    public function __construct() {
        $this->validator = new aafwValidator();
    }

    /**
     * @return array
     */
    public function isValid($templateJson) {
        $templates = json_decode($templateJson);

        if(count($templates) == 0 || $templates == null) {
            return false;
        }

        foreach( $templates as $template ) {
            $result = true;
            if (in_array($template->type, array_keys(StaticHtmlTemplate::$template_types)) == false) {
                return false;
            }else if($template->type == StaticHtmlTemplate::TEMPLATE_TYPE_IMAGE_SLIDER) {
                $result = $this->isValidImageSlider($template->template);
            }else if($template->type == StaticHtmlTemplate::TEMPLATE_TYPE_FLOAT_IMAGE) {
                $result = $this->isValidFloatImage($template->template);
            }else if($template->type == StaticHtmlTemplate::TEMPLATE_TYPE_FULL_IMAGE) {
                $result = $this->isValidFullImage($template->template);
            }else if($template->type == StaticHtmlTemplate::TEMPLATE_TYPE_TEXT) {
                $result = $this->isValidText($template->template);
            }else if($template->type == StaticHtmlTemplate::TEMPLATE_TYPE_INSTAGRAM) {
                $result = $this->isValidInstagram($template->template);
            }else if($template->type == StaticHtmlTemplate::TEMPLATE_TYPE_STAMP_RALLY) {
                $result = $this->isValidStampRally($template->template);
            }

            if($result == false) {
                return false;
            }
        }

        return true;
    }

    public function isValidImageSlider($template) {
        if(!$template->slider_pc_image_count) {
            return false;
        }else if(!is_int(intval($template->slider_pc_image_count))){
            return false;
        }else if($template->slider_pc_image_count > 10 || $template->slider_pc_image_count < 0) {
            return false;
        }

        if(!$template->slider_sp_image_count) {
            return false;
        }else if(!is_int(intval($template->slider_sp_image_count))){
            return false;
        }else if($template->slider_sp_image_count > 10 || $template->slider_sp_image_count < 0) {
            return false;
        }

        if(count($template->item_list) == 0) {
            return false;
        }

        foreach($template->item_list as $image) {
            if($image->image_url == "") {
                return false;
            }

            if($this->validator->inStrLen($image->caption, 20, true) == false) {
                return false;
            }

            if($this->validator->inStrLen($image->link, 255, true) == false) {
                return false;
            }

            if($this->validator->isURL($image->link) == false) {
                return false;
            }
        }
        return true;
    }

    public function isValidFloatImage($template) {
        if($template->image_url == "") {
            return false;
        }

        if(in_array($template->position_type, array(1, 2)) == false) {
            return false;
        }

        if($this->validator->inStrLen($template->caption, 20, true) == false) {
            return false;
        }

        if($this->validator->inStrLen($template->text, 20000, true) == false) {
            return false;
        }

        if($this->validator->inStrLen($template->link, 255, true) == false) {
            return false;
        }

        if($this->validator->isURL($template->link) == false) {
            return false;
        }

        return true;
    }

    public function isValidFullImage($template) {
        if($template->image_url == "") {
            return false;
        }

        if($this->validator->inStrLen($template->caption, 20, true) == false) {
            return false;
        }

        if($this->validator->inStrLen($template->link, 255, true) == false) {
            return false;
        }

        if($this->validator->isURL($template->link) == false) {
            return false;
        }

        return true;
    }

    public function isValidText($template) {
        if($template->text == "") {
            return false;
        }

        if($this->validator->inStrLen($template->text, 20000, true) == false) {
            return false;
        }
        return true;
    }

    public function isValidInstagram($template) {

        $apiUrls = json_decode($template->api_url);

        if(!$apiUrls){
            return false;
        }

        $notInputCount = 0;

        foreach($apiUrls as $url){

            if($this->validator->inStrLen($url, 250, true) == false) {
                return false;
            }

            if($this->validator->isURL($url) == false) {
                return false;
            }

            if(!$url){
                $notInputCount++;
            }
        }

        if($notInputCount == count($apiUrls)){
            return false;
        }

        return true;
    }

    public function isValidStampRally($template) {

        $campaignCount = $template->campaign_count;

        if($campaignCount == ""){
            return false;
        }

        if($this->validator->isNumeric($campaignCount) == false){
            return false;
        }

        if($template->stamp_status_joined_image == "" || $template->stamp_status_finished_image == "" || $template->stamp_status_coming_soon_image == ""){
            return false;
        }

        if($template->cp_ids && $campaignCount < count($template->cp_ids)){
            return false;
        }

        return true;
    }
}
