<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class api_get_instagram_image_for_page extends BrandcoPOSTActionBase {

    public $NeedOption = array();

    protected $ContainerName = 'api_get_instagram_image_for_page';
    protected $AllowContent = array('JSON');
    public $CsrfProtect = true;

    private $numberImagePerPage;
    private $apiUrl;
    private $nextId;

    private $codes;

    public function doThisFirst() {

        $this->numberImagePerPage = $this->POST['number_image_per_page'];
        $this->apiUrl = $this->POST['api_url'];
        $this->nextId = $this->POST['next_id'] && is_numeric($this->POST['next_id']) ? $this->POST['next_id'] : 0;

    }

    public function validate() {

        if(!$this->numberImagePerPage || !is_numeric($this->numberImagePerPage)){
            return false;
        }

        if(!$this->apiUrl){
            return false;
        }

        $this->codes = $this->extractCodeFromApiUrl($this->apiUrl);
        if (!$this->codes) {
            return false;
        }

        return true;
    }

    public function getFormURL () {
        $json_data = $this->createAjaxResponse("ng");
        $this->assign('json_data', $json_data);

        return false;
    }

    function doAction() {
        $responseContent = $this->createResponseContent();
        $this->assign('json_data', $responseContent);
        return 'dummy.php';
    }

    private function createResponseContent(){

        $instagramData = $this->getCpInstagramData($this->codes);

        if($instagramData){

            $instagramPosts = $instagramData['posts'];

            $html = "";

            $parser = new PHPParser();

            foreach($instagramPosts as $post){

                $detailData = json_decode($post->detail_data);

                $html .= $parser->parseTemplate('InstagramPhotoWrap.php',array(
                    'instagram_post' => $detailData,
                ));
            }

            $responseContent = $this->createAjaxResponse("ok",$instagramData['pagination'], array(), $html);

        }else{
            $responseContent = $this->createAjaxResponse("ng");
        }

        return $responseContent;
    }

    private function getCpInstagramData($codes){

        $db = aafwDataBuilder::newBuilder();

        $param = array(
            'codes' => $codes,
            'max_id' => $this->nextId ? $this->nextId : null,
            'cp_action_type' => CpAction::TYPE_INSTAGRAM_HASHTAG,
            'BY_MAX_ID' => '__ON__',
        );
        
        $pager = array(
            'page' => 1,
            'count' => $this->numberImagePerPage + 1     // $instagramHashtagUserPosts_count = $page_limit + $next_min_user
        );

        $order = array(
            'name' => 'id',
            'direction' => 'desc'
        );

        $result = $db->getCpInstagramHashtagUserPostsByContentApiCodes($param, $order, $pager, true, 'InstagramHashtagUserPost');
        $instagramHashtagUserPosts = $result['list'];

        if (!$instagramHashtagUserPosts) {
            return null;
        }

        $pagination = array();
        if ($result['pager']['count'] >= $this->numberImagePerPage + 1) {
            // If next_min_user is available pop it from photo_users list
            $lastInstagramHashtagUserPost = array_pop($instagramHashtagUserPosts);

            $pagination = array(
                'next_id' => $lastInstagramHashtagUserPost->id,
            );
        }

        if($this->nextId){
            $param = array(
                'codes' => $codes,
                'min_id' => $this->nextId,
                'cp_action_type' => CpAction::TYPE_INSTAGRAM_HASHTAG,
                'BY_MIN_ID' => '__ON__'
            );

            $pager = array(
                'count' => $this->numberImagePerPage
            );

            $order = array(
                'name' => 'id',
                'direction' => 'asc'
            );

            $result = $db->getCpInstagramHashtagUserPostsByContentApiCodes($param, $order, $pager, true, 'InstagramHashtagUserPost');

            if($result['list']){
                $firstInstagramHashtagUserPost = array_pop($result['list']);
                $pagination['previous_id'] = $firstInstagramHashtagUserPost->id;
            }
        }

        return array(
            'posts' => $instagramHashtagUserPosts,
            'pagination' => $pagination
        );
    }

    private function extractCodeFromApiUrl($api_url){
        $codes = array();
        $apiUrls = json_decode($api_url);

        foreach($apiUrls as $url){
            $query = parse_url($url, PHP_URL_QUERY);
            parse_str($query, $params);
            if($params['code']){
                $codes[] = $params['code'];
            }
        }

        return $codes;
    }
}