<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class cp_actions extends BrandcoGETActionBase {
    public $NeedRedirect = true;
    public $NeedOption = array();

    private $cp_action_id;
    private $cp_action;

    public function doThisFirst() {
        $this->cp_action_id = $this->GET['exts'][0];
    }

    public function validate() {
        $photo_cp_validator = new CpDataManagerValidator($this->getBrand()->id, $this->cp_action_id, CpAction::TYPE_PHOTO);

        if (!$photo_cp_validator->validate()) {
            return '404';
        }
        $this->cp_action = $photo_cp_validator->getCpAction();

        return true;
    }

    public function doAction() {
        /** @var PhotoUserService $photo_user_service */
        $photo_user_service = $this->getService('PhotoUserService');

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService("CpFlowService");

        // 1ページ分の写真投稿一覧取得
        $photo_entries = $photo_user_service->getApprovedPhotoEntriesByActionId($this->cp_action_id);
        if (!$photo_entries) return 'redirect:' . Util::rewriteUrl('', '');

        // 全投稿数取得
        $total_count = $photo_user_service->countApprovedPhotoEntriesByCpActionId($this->cp_action_id);

        $this->Data['page_data']['page_title'] = '#' . $this->cp_action->getCpActionData()->title;
        $this->Data['page_data']['cp_action_id'] = $this->cp_action_id;
        $this->Data['page_data']['photo_entries'] = $photo_entries;
        $this->Data['page_data']['sp_panel_per_page'] = PanelServiceBase::DEFAULT_PAGE_COUNT_SP;
        $this->Data['page_data']['total_count'] = $total_count;
        $this->Data['page_data']['load_more_flg'] = $photo_entries->total() < $total_count;
        $this->Data['pageStatus']['og']['url'] = Util::rewriteUrl('photo', 'cp_action', array($this->cp_action_id));

        $cp = $cp_flow_service->getCpByCpAction($this->cp_action);
        if ($cp->status == Cp::STATUS_DEMO) {
            $this->Data["pageStatus"]["demo_info"]["is_demo_cp"] = true;
            $this->Data["pageStatus"]["demo_info"]["demo_cp_url"] = $cp->getDemoUrl();
            $this->Data["pageStatus"]["demo_info"]["cp_id"] = $this->Data["cp"]->id;
            $this->Data["pageStatus"]["demo_info"]["isHideClearButton"] = true;
            $this->Data["pageStatus"]["demo_info"]["isHideDemoUrl"] = true;
        }

        $this->Data['brand_contract'] = BrandInfoContainer::getInstance()->getBrandContract();

        return 'user/brandco/photo/cp_actions.php';
    }
}