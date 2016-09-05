<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class BrandcoHeader extends aafwWidgetBase {

    public function doService($params = array()) {
        $brand_page_settings = BrandInfoContainer::getInstance()->getBrandPageSetting();

        if (!$params['og']['url']) {
            $params['og']['url'] = $brand_page_settings->top_page_url ? $brand_page_settings->top_page_url : Util::rewriteUrl('', '');
        }

        if (!$params['og']['image']) {
            $params['og']['image'] = $brand_page_settings->og_image_url ? $brand_page_settings->og_image_url : $params['brand']->getProfileImage();
        }

        if (!$params['og']['site_name']) {
            $params['og']['site_name'] = $params['brand']->name;
        }

        if ($params['og']['title']) {
            $title = $params['og']['title'];
        } else if ($brand_page_settings->meta_title) {
            $title = $brand_page_settings->meta_title;
        } else {
            $title = $params['brand']->name;
        }

        $params['title'] = $title;
        $params['og']['title'] = $title;

        if ($params['og']['description'] === null || $params['og']['description'] === '') {
            if ($brand_page_settings->meta_description) {
                $params['og']['description'] = $brand_page_settings->meta_description;
            } else {
                $params['og']['description'] = '『' . $params['brand']->name . '』のブランドページです。SNSアカウントの記事や、どなたでも参加できるキャンペーン情報をお届けします。';
            }
        }

        if (!$params['keyword'] && $brand_page_settings->meta_keyword) {
            $params['keyword'] = $brand_page_settings->meta_keyword;
        }

        if (!$params['og']['type']) {
            $params['og']['type'] = 'article';
        }

        return $params;
    }
}