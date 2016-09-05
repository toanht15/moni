<?php

class MetaDataParser {

    public function getHtmlContent($url) {

        $htmlSource = file_get_contents($url);
        $htmlDom = new DOMDocument();
        $htmlDom->loadHTML(mb_convert_encoding($htmlSource, 'HTML-ENTITIES', 'UTF-8'));
        return $htmlDom;

    }

    public function getMetaData($htmlContent) {

        $targetData = array();
        $metaTags = $htmlContent->getElementsByTagName('meta');

        foreach($metaTags as $metaTag){

            if($metaTag->getAttribute('property')=='og:image'){
                $targetData['image'] = $metaTag->getAttribute('content');
            }

            if($metaTag->getAttribute('property')=='og:title'){
                $targetData['title'] = $metaTag->getAttribute('content');
            }

            if($metaTag->getAttribute('property')=='og:description'){
                $targetData['description'] = $metaTag->getAttribute('content');
            }

        }

        return $targetData;
    }
}
