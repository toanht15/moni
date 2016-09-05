<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.PhotoStreamService');
AAFW::import('jp.aainc.classes.entities.PhotoEntry');

class CpPhotoList extends aafwWidgetBase {
    const PAGE_LIMITED = 18;

    private $photo_order_kinds = array(
        1 => 'created_at',
        2 => 'cp_user_id'
    );

    private $photo_order_types = array(
        1 => 'asc',
        2 => 'desc'
    );

    public function doService($params = array()) {
        /** @var PhotoUserService $photo_user_service */
        $photo_user_service = $this->getService('PhotoUserService');
        /** @var ContentApiCodeService $api_code_service */
        $api_code_service = $this->getService('ContentApiCodeService');
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');

        $params['page'] = $params['page'] ? $params['page'] : 1;
        $params['limit'] = $params['limit'] ?: self::PAGE_LIMITED;
        $action_id = $params['action_id'];
        $brand_id = $params['brand_id'];
        $cp_id = $params['cp_id'];

        $params['photo_actions'] = $cp_flow_service->getCpActionsByCpIdAndActionType($cp_id, CpAction::TYPE_PHOTO);

        $cp_photo_action_service = $this->getService('CpPhotoActionService');
        $params['cur_photo_action'] = $cp_photo_action_service->getCpPhotoAction($params['action_id']);

        $search_params = array();
        if ($params['approval_status'] && $params['approval_status'] != 1) {
            $search_params['approval_status'] = $params['approval_status'] - 2;
        }

        $order = array(
            'name' => $this->photo_order_kinds[$params['order_kind']],
            'direction' => $this->photo_order_types[$params['order_type']]
        );

        $pager = array(
            'page' =>  $params['page'],
            'count' => $params['limit'],
        );

        //投稿一覧を取得
        $params['photo_posts'] = [];
        if (is_numeric($action_id) && is_numeric($brand_id)) {
            $params['photo_posts'] = $photo_user_service->getPhotoList($action_id, $brand_id,$search_params,$order,$pager);
        }
        $params['total_photo_count'] = $params['photo_posts']['pager']['count'];
        $params['page_limited'] = $params['limit'];
        $total_page = ceil($params['total_photo_count'] / $params['limit']);
        $params['page'] = Util::getCorrectPaging($params['page'], $total_page);
        if(empty($params['photo_posts']['list'])){
            $params['photo_posts'] = $photo_user_service->getPhotoList($action_id, $brand_id,$search_params,$order,$params['page']);
        }

        foreach ($params['photo_posts']['list'] as &$photo_post) {
            $photo_data = array();
            //approval_status_classの追加とapproval_statusの修正
            if ($photo_post->approval_status == 0) {
                $approval_status_class = 'label5';
                $approval_status = '未承認';
            } elseif ($photo_post->approval_status == 1) {
                $approval_status_class = 'label4';
                $approval_status = '承認';
            } else {
                $approval_status_class = 'label2';
                $approval_status = '非承認';
            }
            $photo_data['photo_post'] = $photo_post;
            $photo_data['approval_status_class'] = $approval_status_class;
            $photo_data['approval_status'] = $approval_status;

            //croppedPhotoのパスを取得
            $photo_data['photo_url'] = $photo_post->getCroppedPhoto();
            $params['photo_posts']['data'][] = $photo_data;
        }
        $api_code = $api_code_service->getApiCodeByCpIdAndCpActionType($params['cp_id'], CpAction::TYPE_PHOTO);
        $params['api_url'] = $api_code ? $api_code_service->getApiUrl($api_code->code, CpAction::TYPE_PHOTO) : '';
        $params['approved_photo_count'] = $photo_user_service->countApprovedPhotoEntriesByCpActionId($action_id);

        return $params;
    }
}