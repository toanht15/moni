<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
class BrandcoLoginOuterHeader extends aafwWidgetBase {
    public function doService( $params = array() ){

        if(Util::isBaseUrl()) {
            $service = ' / モニプラ';
        } else{
            $service = '';
        }
        if(!$params['og']['url']) {
            $params['og']['url'] = Util::rewriteUrl('', '');
        }
        if(!$params['og']['image']) {
            $params['og']['image'] = $params['brand']->getProfileImage();
        }
        if(!$params['og']['site_name']) {
            $params['og']['site_name'] = $params['brand']->name . $service;
        }

        if($params['og']['title']) {
            $title = $params['og']['title'];
        } else{
            $title = $params['brand']->name;
        }
        $params['title'] = $title . $service;
        $params['og']['title'] = $title. $service;

        if(!$params['og']['description']) {
            $params['og']['description'] = '『'.$params['brand']->name.'』のブランドページです。SNSアカウントの記事や、どなたでも参加できるキャンペーン情報をお届けします。';
        }
        if(!$params['og']['type']) {
            $params['og']['type'] = 'article';
        }

        return $params;
    }
}
