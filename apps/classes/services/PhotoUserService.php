<?php
AAFW::import('jp.aainc.aafw.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');
AAFW::import('jp.aainc.classes.entities.PhotoUser');

class PhotoUserService extends aafwServiceBase {

    use CpActionTrait;

    /** @var PhotoUsers $photo_users */
    protected $photo_users;
    /** @var PhotoUserShares $photo_user_shares */
    protected $photo_user_shares;
    /** @var PhotoStreamService $photo_stream_service */
    protected $photo_stream_service;
    /** @var PhotoEntries $photo_entries */
    protected $photo_entries;

    public function __construct() {
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_photo_actions = $this->getModel("CpPhotoActions");
        $this->photo_entries = $this->getModel('PhotoEntries');
        $this->photo_users = $this->getModel("PhotoUsers");
        $this->photo_user_shares = $this->getModel("PhotoUserShares");

        $this->photo_stream_service = $this->getService('PhotoStreamService');

    }

    /**
     * @param $photo_user
     * @return mixed
     */
    public function updatePhotoUser($photo_user) {
        return $this->photo_users->save($photo_user);
    }

    /**
     * @param $params
     * @return int
     */
    public function isExistsPhotoUserByIds($params) {
        $filter = array(
            'cp_action_id' => $params['cp_action_id'],
            'cp_user_id' => $params['cp_user_id']
        );
        return $this->photo_users->isExists($filter);
    }

    /**
     * @param $params
     * @return mixed
     */
    public function createPhotoUser($params) {
        $photo_user = $this->photo_users->createEmptyObject();
        $photo_user->cp_action_id = $params['cp_action_id'];
        $photo_user->cp_user_id = $params['cp_user_id'];
        $photo_user->photo_url = $params['photo_url'];
        $photo_user->photo_title = $params['photo_title'];
        $photo_user->photo_comment = $params['photo_comment'];
        $photo_user->share_text = $params['share_text'];
        $photo_user->approval_status = $params['approval_status'] ? $params['approval_status'] : PhotoUser::APPROVAL_STATUS_DEFAULT;
        return $this->photo_users->save($photo_user);
    }

    /**
     * @param $photo_user_id
     * @return entity
     */
    public function getPhotoUserById($photo_user_id) {
        return $this->photo_users->findOne($photo_user_id);
    }

    /**
     * @param $cp_action_id
     * @param $cp_user_id
     * @return entity
     */
    public function getPhotoUserByIds($cp_action_id, $cp_user_id) {
        $filter = array(
            'cp_action_id' => $cp_action_id,
            'cp_user_id' => $cp_user_id
        );
        return $this->photo_users->findOne($filter);
    }

    /**
     * フォトアクションか
     * @param $cp_action_id
     * @return bool
     */
    public function isPhotoActionId($cp_action_id) {
        $cp_action = $this->getCpActionById($cp_action_id);
        return $cp_action->type == CpAction::TYPE_PHOTO;
    }

     /**
     * @param $cp_action_ids
     * @return aafwEntityContainer|array
     */
    public function getPhotoUsersByActionIds($cp_action_ids) {
        $filter = array(
            'cp_action_id' => $cp_action_ids
        );
        return $this->photo_users->find($filter);
    }

    /**
     * @param $cp_action_id
     * @param $approval_status
     * @return aafwEntityContainer|array
     */
    public function getPhotoUsersByCpActionIdAndApprovalStatus($cp_action_id, $approval_status) {
        if (!$cp_action_id || !$approval_status) return array();

        $filter = array(
            'cp_action_id' => $cp_action_id,
            'approval_status' => $approval_status
        );
        return $this->photo_users->find($filter);
    }

    /**
     * @param $cp_action_id
     * @param $cp_user_id
     * @return aafwEntityContainer|array
     */
    public function getPhotoUserByActionIdAndCpUserId($cp_action_id, $cp_user_id) {
        if (!$cp_action_id || !$cp_user_id) {
            return null;
        }
        $filter = array(
            'cp_action_id' => $cp_action_id,
            'cp_user_id' => $cp_user_id
        );
        return $this->photo_users->find($filter);
    }

    /**
     * 承認済みフォト投稿一覧取得
     * @param $cp_action_id
     * @param int $page
     * @param int $page_limit
     * @return aafwEntityContainer|array
     */
    public function getApprovedPhotoEntriesByActionId($cp_action_id, $page = 0, $page_limit = 0) {
        if (!$cp_action_id) return array();

        $filter = array(
            'conditions' => array(
                'pu.cp_action_id' => $cp_action_id,
                'pu.approval_status' => PhotoUser::APPROVAL_STATUS_APPROVE
            ),
            'join' => array(
                'type' => 'inner',
                'name' => 'photo_users',
                'alias' => 'pu',
                'key' => array(
                    'pu.id' => 'photo_entries.photo_user_id'
                )
            ),
            'pager' => array(
                'page' => $page ? $page : PhotoStreamService::DEFAULT_PAGE,
                'count' => $page_limit ? $page_limit : $this->photo_stream_service->getPageLimit()
            ),
            'order' => array(
                'name' => 'pu.created_at',
                'direction' => 'desc'
            )
        );

        return $this->photo_entries->find($filter);
    }

    /**
     * 承認済みフォト投稿数取得
     * @param $cp_action_id
     * @return mixed
     */
    public function countApprovedPhotoEntriesByCpActionId($cp_action_id) {
        if (!$cp_action_id) return 0;

        $filter = array(
            'conditions' => array(
                'pu.cp_action_id' => $cp_action_id,
                'pu.approval_status' => PhotoUser::APPROVAL_STATUS_APPROVE
            ),
            'join' => array(
                'type' => 'inner',
                'name' => 'photo_users',
                'alias' => 'pu',
                'key' => array(
                    'pu.id' => 'photo_entries.photo_user_id'
                )
            ),
        );
        return $this->photo_entries->count($filter);
    }

    /**
     * @param $cp_action_ids
     * @param int $approval_status
     * @return 件数
     */
    public function getPhotoUsersCountByActionIdsAndApprovalStatus($cp_action_ids, $approval_status = PhotoUser::APPROVAL_STATUS_DEFAULT) {
        $filter = array(
            'cp_action_id' => $cp_action_ids,
            'approval_status' => $approval_status
        );

        return $this->photo_users->count($filter);
    }

    /**
     * アクションIDのフォトユーザ数取得
     * 検閲ステータスにて絞り込み可能
     * @param $cp_action_ids
     * @param null $params
     * @return 件数
     */
    public function getPhotoUsersCountByActionIds($cp_action_ids, $params = null) {
        $filter = array(
            'cp_action_id' => $cp_action_ids
        );

        if (isset($params['approval_status'])) {
            $filter['approval_status'] = $params['approval_status'];
        }

        return $this->photo_users->count($filter);
    }

    /**
     * フォトユーザ一覧取得
     * ページングあり
     * @param $cp_action_ids
     * @param int $page
     * @param int $limit
     * @param null $order
     * @param null $params
     * @return aafwEntityContainer|array
     */
    public function getPhotoUsers($cp_action_ids, $page = 1, $limit = 20, $order = null, $params = null) {
        $filter = array(
            'conditions' => array(
                'cp_action_id' => $cp_action_ids
            ),
            'pager' => array(
                'page' => $page,
                'count' => $limit
            ),
            'order' => $order
        );

        if (isset($params['approval_status'])) {
            $filter['conditions']['approval_status'] = $params['approval_status'];
        }

        return $this->photo_users->find($filter);
    }

    /**
     * 指定IDより前のフォトユーザID取得
     * @param $photo_user_id
     * @param $cp_action_id
     * @param null $params
     * @return mixed
     */
    public function getPrevPhotoUserId($photo_user_id, $cp_action_id, $params = null) {
        $filter = array(
            'id:<' => $photo_user_id,
            'cp_action_id' => $cp_action_id
        );

        if (isset($params['approval_status'])) {
            $filter['approval_status'] = $params['approval_status'];
        }

        return $this->photo_users->getMax('id', $filter);
    }

    /**
     * 指定IDより後のフォトユーザID取得
     * @param $photo_user_id
     * @param $cp_action_id
     * @param null $params
     * @return mixed
     */
    public function getNextPhotoUserId($photo_user_id, $cp_action_id, $params = null) {
        $filter = array(
            'id:>' => $photo_user_id,
            'cp_action_id' => $cp_action_id
        );

        if (isset($params['approval_status'])) {
            $filter['approval_status'] = $params['approval_status'];
        }

        return $this->photo_users->getMin('id', $filter);
    }

    /**
     * @param $cp_action_id
     * @return entity
     */
    public function getCpPhotoActionByCpActionId($cp_action_id) {
        $filter = array('cp_action_id' => $cp_action_id);
        return $this->cp_photo_actions->findOne($filter);
    }

    public function updatePhotoUserShare($photo_user_share) {
        $this->photo_user_shares->save($photo_user_share);
    }

    public function createEmptyPhotoUserShare() {
        return $this->photo_user_shares->createEmptyObject();
    }

    /**
     * 投稿情報一覧取得
     * @param int $cp_action_id
     * @param int $brand_id
     * @return mixed
     */
    public function getPhotoList($cp_action_id, $brand_id, $approval_status, $order, $pager) {
        $condition = array();
        $condition['cp_action_id'] = $cp_action_id;
        $condition['brand_id'] = $brand_id;
        if(!empty($approval_status)){
            $condition['STATUS'] = '__ON__';
            $condition['approval_status'] = $approval_status;
        }
        if($order['name'] == "created_at" && $order['direction'] == "desc"){
            $condition['CREATED_AT_DESC'] = '__ON__';
        }
        if($order['name'] == "cp_user_id" && $order['direction'] == "desc"){
            $condition['CP_USER_ID_DESC'] = '__ON__';
        }
        $this->db = aafwDataBuilder::newBuilder();
        $photo_list = $this->db->getPhotoList($condition, null, $pager, true, 'PhotoUser');

        return $photo_list;
    }
}
