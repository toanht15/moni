<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
class api_check_rss_url extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_check_rss_url';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function beforeValidate () {

    }

    public function validate () {
        if(!$this->url) {
            return false;
        }
        return true;
    }

    function doAction() {
        $service = $this->createService("RssStreamService");
        $rss = $service->fetch_rss($this->url);
        if($rss){
            $json_data = $this->createAjaxResponse("ok", array('url'=>$this->url));
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }else{
            $url_list = $this->feedSearch($this->url);
            if(count($url_list)){
                $parse_rss_url = parse_url($url_list[0]);
                // auto correct fetch url
                if(!$parse_rss_url['host']){
                    //for url as 'services/feeds/photos_public.gne?id=41841314@N02&lang=en-us&format=rss_200'
                    $parse_url = parse_url($this->url);
                    $json_data = $this->createAjaxResponse("ok", array('url'=>$parse_url['scheme'].'://'.$parse_url['host'].$url_list[0]));
                }else if(!$parse_rss_url['scheme']) {
                    //for url as 'www.abc.xyz/ssss/ssss?asd=aaaa' or '//www.aaa.bbb/sss?a=b'
                    $parse_url = parse_url($this->url);
                    $url = $parse_url['scheme'].'://'.$parse_rss_url['host'].$parse_rss_url["path"];
                    if($parse_rss_url['query']){
                        $url = $url.'?'.$parse_rss_url['query'];
                    }
                    $json_data = $this->createAjaxResponse("ok", array('url'=>$url));
                }else{
                    $json_data = $this->createAjaxResponse("ok", array('url'=>$url_list[0]));
                }
                $this->assign('json_data', $json_data);
                return 'dummy.php';
            }else{
                $json_data = $this->createAjaxResponse("ng");
                $this->assign('json_data', $json_data);
                return 'dummy.php';
            }
        }
    }
    function feedSearch($url) {

        if($html = @DOMDocument::loadHTML(file_get_contents($url))) {

            $xpath = new DOMXPath($html);
            $results = array();

            $feed_rss = $xpath->query("//link[@href][@type='application/rss+xml']/@href");
            foreach($feed_rss as $feed) {
                $results[] = $feed->nodeValue;
            }

            if(count($results) > 0){
                return $results;
            }

            $feed_atom = $xpath->query("//link[@href][@type='application/atom+xml']/@href");
            foreach($feed_atom as $feed) {
                $results[] = $feed->nodeValue;
            }

            return $results;

        }

        return array();

    }
}