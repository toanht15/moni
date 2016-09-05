<?php
AAFW::import('jp.aainc.aafw.base.aafwServiceBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class InstagramHashtagUserService extends aafwServiceBase {

    /** @var InstagramHashtagUsers $instagram_hashtag_users */
    private $instagram_hashtag_users;

    /** @var InstagramHashtagUserPosts instagram_hashtag_user_posts */
    private $instagram_hashtag_user_posts;

    /** @var CpInstagramHashtagEntries cp_instagram_hashtag_entries */
    private $cp_instagram_hashtag_entries;

    public function __construct() {
        $this->instagram_hashtag_users = $this->getModel("InstagramHashtagUsers");
        $this->instagram_hashtag_user_posts = $this->getModel("InstagramHashtagUserPosts");
        $this->cp_instagram_hashtag_entries = $this->getModel('CpInstagramHashtagEntries');

        $this->data_builder = new aafwDataBuilder();
    }

    public function createEmptyObject() {
        return $this->instagram_hashtag_users->createEmptyObject();
    }

    public function getInstagramHashtagUserById($instagram_hashtag_user_id) {
        if (!$instagram_hashtag_user_id) return null;
        return $this->instagram_hashtag_users->findOne($instagram_hashtag_user_id);
    }

    public function saveInstagramHashtagUser(InstagramHashtagUser $instagram_hashtag_user) {
        return $this->instagram_hashtag_users->save($instagram_hashtag_user);
    }

    public function getInstagramHashtagUsersByCpActionId($cp_action_id) {
        if (!$cp_action_id) return array();

        $filter = array(
            'cp_action_id' => $cp_action_id
        );
        return $this->instagram_hashtag_users->find($filter);
    }

    public function getInstagramHashtagUserByCpActionIdAndCpUserId($cp_action_id, $cp_user_id) {
        if (!$cp_action_id || !$cp_user_id) return array();

        $filter = array(
            'cp_action_id' => $cp_action_id,
            'cp_user_id' => $cp_user_id
        );
        return $this->instagram_hashtag_users->findOne($filter);
    }

    public function deletePhysicalByInstagramHashtagUsers($instagram_hashtag_users, $with_related_entity = false) {
        if (!$instagram_hashtag_users) return;

        foreach ($instagram_hashtag_users as $instagram_hashtag_user) {
            if ($with_related_entity) {
                $this->instagram_hashtag_user_posts->deletePhysicalByInstagramHashtagUser($instagram_hashtag_user, true);
            }
            $this->instagram_hashtag_users->deletePhysical($instagram_hashtag_user);
        }
    }

    public function deletePhysicalByInstagramHashtagUser($instagram_hashtag_user, $with_related_entity = false) {
        if (!$instagram_hashtag_user) return;

        if ($with_related_entity) {
            $this->instagram_hashtag_user_posts->deletePhysicalByInstagramHashtagUser($instagram_hashtag_user, true);
        }
        $this->instagram_hashtag_users->deletePhysical($instagram_hashtag_user);
    }

    public function executeDuplicateInstagramHashtagUserByCpActionId($cp_action_id) {
        if (!$cp_action_id) return;

        $data_builder = aafwDataBuilder::newBuilder();
        $sql = 'UPDATE ';
        $sql .= 'instagram_hashtag_users users, ';
        $sql .= '(select * from instagram_hashtag_users where cp_action_id = ' . $cp_action_id . ' group by instagram_user_name having count(*) >= 2) tmp ';
        $sql .= 'SET ';
        $sql .= 'users.duplicate_flg = 1 ';
        $sql .= 'WHERE ';
        $sql .= 'users.instagram_user_name = tmp.instagram_user_name AND ';
        $sql .= 'users.cp_action_id = ' . $cp_action_id;
        $data_builder->executeUpdate($sql);
    }

    public function getInstagramHashtagUsersByCpActionIdAndInstagramUserName($cp_action_id, $instagram_user_name) {
        if (!$cp_action_id || $instagram_user_name === null) return array();

        $filter = array(
            'cp_action_id' => $cp_action_id,
            'instagram_user_name' => $instagram_user_name
        );
        return $this->instagram_hashtag_users->find($filter);
    }

    public function countInstagramHashtagUserByCpActionId($cp_action_id) {
        if (!$cp_action_id) return 0;

        $filter = array(
            'cp_action_id' => $cp_action_id
        );

        return $this->instagram_hashtag_users->count($filter);
    }

    /*****************************
     *
     * InstagramHashtagUserPosts
     *
     *****************************/

    public function getRandomInstagramHashtagUserPostsByCpActionId($cp_action_id, $limit = 20) {
        if (!$cp_action_id || !$limit) return array();

        $conditions = array(
            'cp_action_id' => $cp_action_id,
            'limit' => $limit
        );

        return $this->data_builder->getRandomInstagramHashtagUserPosts($conditions);
    }

    public function countInstagramHashtagUserPostByActionIdsAndApprovalStatus($cp_action_ids, $approval_status = InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT) {
        if (!$cp_action_ids) return array();

        $filter = array(
            'conditions' => array(
                'approval_status' => $approval_status,
                'users.cp_action_id' => $cp_action_ids,
            ),
            'join' => array(
                'type' => 'inner',
                'name' => 'instagram_hashtag_users',
                'alias' => 'users',
                'key' => array(
                    'instagram_hashtag_user_posts.instagram_hashtag_user_id' => 'users.id',
                ),
            )
        );
        return $this->instagram_hashtag_user_posts->count($filter);
    }

    public function countInstagramHashtagUserPostByActionId($cp_action_id) {
        if (!$cp_action_id) return array();

        $filter = array(
            'conditions' => array(
                'users.cp_action_id' => $cp_action_id,
            ),
            'join' => array(
                'type' => 'inner',
                'name' => 'instagram_hashtag_users',
                'alias' => 'users',
                'key' => array(
                    'instagram_hashtag_user_posts.instagram_hashtag_user_id' => 'users.id',
                ),
            )
        );
        return $this->instagram_hashtag_user_posts->count($filter);
    }

    public function countInstagramHashtagUserByActionIds($cp_action_ids, $params = null) {
        if (!$cp_action_ids) return array();

        $filter = array(
            'conditions' => array(
                'users.cp_action_id' => $cp_action_ids,
            ),
            'join' => array(
                'type' => 'inner',
                'name' => 'instagram_hashtag_users',
                'alias' => 'users',
                'key' => array(
                    'instagram_hashtag_user_posts.instagram_hashtag_user_id' => 'users.id',
                ),
            )
        );


        if (isset($params['duplicate_flg'])) {
            $filter['conditions']['users.duplicate_flg'][] = $params['duplicate_flg'];
        }

        if (isset($params['approval_status'])) {
            $filter['conditions']['approval_status'][] = $params['approval_status'];
        }

        if (isset($params['reverse_post_time_flg'])) {
            $filter['conditions']['reverse_post_time_flg'][] = $params['reverse_post_time_flg'];
        }

        return $this->instagram_hashtag_user_posts->count($filter);
    }

    public function getInstagramHashtagUserPosts($cp_action_ids, $page = 1, $limit = 20, $order = null, $params = null) {
        if (!$cp_action_ids) return array();

        $filter = array(
            'conditions' => array(
                'users.cp_action_id' => $cp_action_ids,
            ),
            'join' => array(
                'type' => 'inner',
                'name' => 'instagram_hashtag_users',
                'alias' => 'users',
                'key' => array(
                    'instagram_hashtag_user_posts.instagram_hashtag_user_id' => 'users.id',
                ),
            ),
            'pager' => array(
                'page' => $page,
                'count' => $limit
            ),
            'order' => $order
        );

        if (isset($params['duplicate_flg'])) {
            $filter['conditions']['users.duplicate_flg'][] = $params['duplicate_flg'];
        }

        if (isset($params['approval_status'])) {
            $filter['conditions']['approval_status'][] = $params['approval_status'];
        }

        if (isset($params['reverse_post_time_flg'])) {
            $filter['conditions']['reverse_post_time_flg'][] = $params['reverse_post_time_flg'];
        }

        if (isset($params['min_id'])) {
            $filter['conditions']['id:>'] = $params['min_id'];
        }

        return $this->instagram_hashtag_user_posts->find($filter);
    }

    public function getInstagramHashtagUserPostsByCpActionId($cp_action_id) {
        if (!$cp_action_id) return array();

        $filter = array(
            'conditions' => array(
                'users.cp_action_id' => $cp_action_id,
            ),
            'join' => array(
                'type' => 'inner',
                'name' => 'instagram_hashtag_users',
                'alias' => 'users',
                'key' => array(
                    'instagram_hashtag_user_posts.instagram_hashtag_user_id' => 'users.id',
                ),
            ),
        );

        return $this->instagram_hashtag_user_posts->find($filter);
    }

    public function getInstagramHashtagUserPostsByCpActionIdAndCpUserId($cp_action_id, $cp_user_id) {
        if (!$cp_action_id || !$cp_user_id) return array();

        $filter = array(
            'conditions' => array(
                'users.cp_action_id' => $cp_action_id,
                'users.cp_user_id' => $cp_user_id,
            ),
            'join' => array(
                'type' => 'inner',
                'name' => 'instagram_hashtag_users',
                'alias' => 'users',
                'key' => array(
                    'instagram_hashtag_user_posts.instagram_hashtag_user_id' => 'users.id',
                ),
            ),
        );

        return $this->instagram_hashtag_user_posts->find($filter);
    }

    public function getInstagramHashtagUserPostById($instagram_hashtag_user_post_id) {
        if (!$instagram_hashtag_user_post_id) return array();

        $filter = array(
            'id' => $instagram_hashtag_user_post_id
        );
        return $this->instagram_hashtag_user_posts->findOne($filter);
    }

    public function saveInstagramHashtagUserPost(InstagramHashtagUserPost $instagram_hashtag_user_post) {
        return $this->instagram_hashtag_user_posts->save($instagram_hashtag_user_post);
    }

    public function getPrevInstagramHashtagUserPostId($instagram_hashtag_user_post_id, $cp_action_id, $params = null) {
        if (!$instagram_hashtag_user_post_id || !$cp_action_id) return '';

        $conditions = array(
            'instagram_hashtag_user_post_id' => $instagram_hashtag_user_post_id,
            'cp_action_id' => $cp_action_id
        );

        if (isset($params['approval_status'])) {
            $conditions['approval_status'] = $params['approval_status'];
        }

        $result = $this->data_builder->getPrevInstagramHashtagUserPostId($conditions);

        return $result[0]['MAX(p.id)'];
    }

    public function getNextInstagramHashtagUserPostId($instagram_hashtag_user_post_id, $cp_action_id, $params = null) {
        if (!$instagram_hashtag_user_post_id || !$cp_action_id) return '';

        $conditions = array(
            'instagram_hashtag_user_post_id' => $instagram_hashtag_user_post_id,
            'cp_action_id' => $cp_action_id
        );

        if (isset($params['approval_status'])) {
            $conditions['approval_status'] = $params['approval_status'];
        }

        $result = $this->data_builder->getNextInstagramHashtagUserPostId($conditions);

        return $result[0]['MIN(p.id)'];
    }

    public function getInstagramHashtagUserPostList($instagram_hashtag_user, $is_csv_download = false, $brand_id = false) {
        if (!$instagram_hashtag_user) return array();

        $string_list = array();

        $photo_url_list = array();
        $post_text_list = array();
        $reverse_post_time = array();
        $post_date_time = array();
        $approval_status = array();

        $string_list['user_name'] = $instagram_hashtag_user->instagram_user_name;

        if ($instagram_hashtag_user->isExistsInstagramHashtagUserPosts()) {

            if ($is_csv_download) {
                $i = 1;
                $cp_user_service = $this->getService('CpUserService');
                $cp_user = $cp_user_service->getCpUserById($instagram_hashtag_user->cp_user_id);

                $brands_users_relation_service = $this->getService('BrandsUsersRelationService');

                if (!$brand_id) return array();
                $brands_users_relation = $brands_users_relation_service->getBrandsUsersRelation($brand_id, $cp_user->user_id);

                foreach ($instagram_hashtag_user->getInstagramHashtagUserPosts() as $instagram_hashtag_user_post) {
                    $extension = pathinfo($instagram_hashtag_user_post->standard_resolution, PATHINFO_EXTENSION);
                    $photo_url_list[] = $brands_users_relation->no . '(' . $i . ').' . $extension;
                    $post_text_list[] = json_decode($instagram_hashtag_user_post->detail_data)->caption->text;
                    $reverse_post_time[] = $instagram_hashtag_user_post->getReversePostTimeStatus();
                    $post_date_time[] = date('Y/m/d H:i', json_decode($instagram_hashtag_user_post->detail_data)->created_time);
                    $approval_status[] = $instagram_hashtag_user_post->getApprovalStatus();
                    $i++;
                }

            } else {
                foreach ($instagram_hashtag_user->getInstagramHashtagUserPosts() as $instagram_hashtag_user_post) {
                    $photo_url_list[] = $instagram_hashtag_user_post->standard_resolution;
                    $post_text_list[] = json_decode($instagram_hashtag_user_post->detail_data)->caption->text;
                    $reverse_post_time[] = $instagram_hashtag_user_post->getReversePostTimeStatus();
                    $post_date_time[] = date('Y/m/d H:i', json_decode($instagram_hashtag_user_post->detail_data)->created_time);
                    $approval_status[] = $instagram_hashtag_user_post->getApprovalStatus();
                }
            }

            $string_list['photo_url'] = implode(',', $photo_url_list);
            $string_list['post_text'] = implode(',', $post_text_list);
            $string_list['reverse_post_time'] = implode(',', $reverse_post_time);
            $string_list['post_date_time'] = implode(',', $post_date_time);
            $string_list['approval_status'] = implode(',', $approval_status);
        }

        return $string_list;
    }

    public function getInstagramHashtagUserPostsByCpActionIdAndObjectId($cp_action_id, $object_id) {
        if (!$cp_action_id || !$object_id) return array();

        $filter = array(
            'conditions' => array(
                'users.cp_action_id' => $cp_action_id,
                'object_id' => $object_id
            ),
            'join' => array(
                'type' => 'inner',
                'name' => 'instagram_hashtag_users',
                'alias' => 'users',
                'key' => array(
                    'instagram_hashtag_user_posts.instagram_hashtag_user_id' => 'users.id',
                ),
            ),
        );
        return $this->instagram_hashtag_user_posts->find($filter);
    }
}
