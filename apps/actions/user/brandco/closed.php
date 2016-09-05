<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.entities.BrandContract');
AAFW::import('jp.aainc.classes.text.TextTemplate');

class closed extends BrandcoGETActionBase {
    public $NeedOption = array();
    public $ClosedModeAccess = true;
    /** @var TextTemplate text_tempalte */
    private $text_tempalte;
    private $replace;

    public function validate() {
        $this->Data['brand_contract'] = $this->getBrand()->getBrandContract()->toArray();
        $this->text_tempalte = new TextTemplate();

        if (!isset($this->GET['preview'])) {
            if (!$this->isPast($this->Data['brand_contract']['contract_end_date'])) {
                return '404';
            }
        }

        return true;
    }

    public function doAction() {
        if (isset($this->GET['preview']) && $this->GET['preview'] == BrandContract::SESSION_PREVIEW_MODE) {
            try {
                $redis = aafwRedisManager::getRedisInstance();
                $key = BrandContract::PREVIEW_PREFIX . ':' . $this->getBrand()->id . ':' . BrandContract::CLOSED_PAGE_PREVIEW_KEY;
                $page_content = json_decode($redis->get($key), true);

                $this->Data['brand_contract']['closed_title'] = $this->convertTag($page_content['closed_title']);
                $this->Data['brand_contract']['closed_description'] = $this->convertTag(html_entity_decode($page_content['closed_description']));
            } catch (Exception $e) {
                $logger = aafwLog4phpLogger::getDefaultLogger();
                $logger->error('closed@doAction Error: ' . $e);
            } finally {
                if ($redis) {
                    $redis->close();
                }
            }
        }else{
            // DBに値がない場合はデフォルト
            if (!$this->Data['brand_contract']['closed_title']) {
                $this->Data['brand_contract']['closed_title'] = $this->text_tempalte->loadContent('brand_contract_title', $this->getReplace());
            }else{
                $this->Data['brand_contract']['closed_title'] = $this->convertTag($this->Data['brand_contract']['closed_title']);
            }

            if (!$this->Data['brand_contract']['closed_description']) {
                $this->Data['brand_contract']['closed_description'] = $this->text_tempalte->loadContent('brand_contract_body', $this->getReplace());
            }else{
                $this->Data['brand_contract']['closed_description'] = $this->convertTag(html_entity_decode($this->Data['brand_contract']['closed_description']));
            }
        }
        return '/user/brandco/closed.php';
    }

    private function convertTag($page_content){
        return $this->text_tempalte->convertContent($page_content, $this->getReplace());
    }

    /**
     * @return mixed
     */
    private function getReplace() {
        return array(
            'BRAND_NAME' => $this->getBrand()->name,
            'CLOSE_START_DATE' => date('Y年m月d日', strtotime($this->Data['brand_contract']['contract_end_date'])),
            'CLOSE_END_DATE' => date('Y年m月d日', strtotime($this->Data['brand_contract']['display_end_date'])),
            'CLOSE_END_DATETIME' => date('Y年m月d日 H時i分', strtotime($this->Data['brand_contract']['display_end_date']))
        );
    }
}