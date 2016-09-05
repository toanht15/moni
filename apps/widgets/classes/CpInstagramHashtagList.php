<?php
AAFW::import('jp.aainc.widgets.base.AdminCpListBase');

class CpInstagramHashtagList extends AdminCpListBase {
    const PAGE_LIMITED = 18;

    protected $order_kinds = array(
        1 => 'created_at',
        2 => 'users.cp_user_id'
    );

    public function doSubService($params) {
        $params['page_limited'] = self::PAGE_LIMITED;

        $order = $this->getUserDataOrder($params);
        $search_params = $this->getSearchParams($params);

        $cp_flow_service = $this->getService('CpFlowService');
        $params['instagram_hashtag_actions'] = $cp_flow_service->getCpActionsByCpIdAndActionType($params['cp_id'], $this->getCurCpActionType());

        $cp_instagram_hashtag_action_service = $this->getService('CpInstagramHashtagActionService');
        $params['cp_instagram_hashtag_action'] = $cp_instagram_hashtag_action_service->getCpInstagramHashtagActionByCpActionId($params['action_id']);

        $instagram_hashtag_user_service = $this->getService('InstagramHashtagUserService');
        $params['total_instagram_hashtag_count'] = $instagram_hashtag_user_service->countInstagramHashtagUserByActionIds($params['action_id'], $search_params);

        $total_page = floor($params['total_instagram_hashtag_count'] / self::PAGE_LIMITED) + ($params['total_instagram_hashtag_count'] % self::PAGE_LIMITED > 0);
        $params['page'] = Util::getCorrectPaging($params['page'], $total_page);

        $params['instagram_hashtag_user_posts'] = $instagram_hashtag_user_service->getInstagramHashtagUserPosts($params['action_id'], $params['page'], self::PAGE_LIMITED, $order, $search_params);
        $params['approved_instagram_hashtag_count'] = $instagram_hashtag_user_service->countInstagramHashtagUserPostByActionIdsAndApprovalStatus($params['action_id'], array('approval_status' => InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE));

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
        $brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::HIDE_PERSONAL_INFO);
        $params['is_hide_personal_info'] = !Util::isNullOrEmpty($brand_global_setting) ? true : false;

        return $params;
    }

    public function getSearchParams($params) {
        $search_params = array();

        /**
         * Checkbox value {'1' => '全て', '2' => '未承認', '3' => '承認', '4' => '非承認'}
         * Approval value {'0' => '未承認', '1' => '承認', '2' => '非承認'}
         */
        if ($params['approval_status'] && $params['approval_status'] != 1) {
            $search_params['approval_status'] = $params['approval_status'] - 2;
        }

        /**
         * Checkbox value {'1' => '全て', '2' => '重複なし', '3' => '重複あり'}
         * Duplicate_flg value {'0' => '重複なし', '1' => '重複あり'}
         */
        if ($params['duplicate_flg'] && $params['duplicate_flg'] != 1) {
            $search_params['duplicate_flg'] = $params['duplicate_flg'] - 2;
        }

        /**
         * Checkbox value {'1' => '全て', '2' => '登録後投稿', '3' => '投稿後登録'}
         * Reverse_post_time_flg value {'0' => '正しい', '1' => '不正'}
         */
        if ($params['reverse_post_time_flg'] && $params['reverse_post_time_flg'] != 1) {
            $search_params['reverse_post_time_flg'] = $params['reverse_post_time_flg'] - 2;
        }

        return $search_params;
    }

    public function getCurCpActionType() {
        return CpAction::TYPE_INSTAGRAM_HASHTAG;
    }
}