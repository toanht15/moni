<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');

/**
 * 1分毎に、comment_user_relationsのnoが割り当てないレーコドをセットする
 * 順番はcreate_dateの順に
 *
 */
class SetCommentsUsersNo extends BrandcoBatchBase {

    const LIMIT = 50; //メモリ溢れるので一度に50件ずつとる

    private $comment_plugin_service;
    private $comments_users_max_relation_no_service;
    private $comment_user_service;
    private $comments_users_max_relation_no_entity;

    public function __construct($argv = null) {
        parent::__construct($argv);

        /** @var CommentPluginService $comment_plugin_service */
        $this->comment_plugin_service = $this->service_factory->create('CommentPluginService');

        /** @var CommentsUsersMaxRelationNoService $comments_users_max_relation_no_service */
        $this->comments_users_max_relation_no_service = $this->service_factory->create('CommentsUsersMaxRelationNoService');

        /** @var CommentUserService $comment_user_service */
        $this->comment_user_service = $this->service_factory->create('CommentUserService');

        /** @var CommentsUsersMaxRelationNos comments_users_max_relation_no_entity */
        $this->comments_users_max_relation_no_entity = aafwEntityStoreFactory::create('CommentsUsersMaxRelationNos');
    }

    public function executeProcess() {

        $pluginCommentPage = 1;

        while(true) {

            $filter = $this->buildQueryCommentPluginFilter($pluginCommentPage);

            $commentPluginContainer = $this->comment_plugin_service->getCommentPluginList(array(),$filter);

            if(count($commentPluginContainer) == 0) {
                break;
            }

            foreach($commentPluginContainer as $commentPlugin) {
                $this->updateCommentUserRelationNo($commentPlugin);
            }

            $pluginCommentPage++;
        }
    }

    private function buildQueryCommentPluginFilter($page) {
        return array(
            'page' => $page,
            'count' => self::LIMIT
        );
    }

    private function buildQueryNewCommentUserRelationCondition($plugin) {
        return array(
            'brand_id' => $plugin->brand_id,
            'comment_plugin_ids' => $plugin->id,
            'status' => CommentUserRelation::COMMENT_USER_RELATION_STATUS_ALL,
            'get_new_record' => true
        );
    }

    private function buildQuerySavedCommentUserRelationNoCondition($plugin) {
        return array(
            'brand_id' => $plugin->brand_id,
            'comment_plugin_ids' => $plugin->id,
            'status' => CommentUserRelation::COMMENT_USER_RELATION_STATUS_ALL,
            'get_saved_no_record' => true
        );
    }

    private function updateCommentUserRelationNo($plugin) {

        $page_num = 1;

        $condition = $this->buildQueryNewCommentUserRelationCondition($plugin);
        $order = array(
            'name' => 'created_at',
            'direction' => 'asc'
        );

        while(true) {

            $pager = array(
                'page' => $page_num,
                'count' => self::LIMIT
            );

            $comments_users_relation_container = $this->comment_user_service->getCommentList($condition, $pager, $order);

            if(count($comments_users_relation_container) == 0) {
                break;
            }

            foreach($comments_users_relation_container as $comments_users_relation) {

                try {
                    $this->comments_users_max_relation_no_entity->begin();

                    // 現時点の会員番号の最大値を取得
                    $comments_users_max_relation_no = $this->comments_users_max_relation_no_service->getMaxNoByCommentPluginIdForUpdate($plugin->id);

                    if($comments_users_max_relation_no) {
                        $max_no = $comments_users_max_relation_no->max_no;
                    } else {
                        $max_no = $this->comment_user_service->countComment($this->buildQuerySavedCommentUserRelationNoCondition($plugin));
                        $comments_users_max_relation_no = aafwEntityStoreFactory::create('CommentsUsersMaxRelationNos');
                        $comments_users_max_relation_no->comment_plugin_id = $plugin->id;
                    }

                    $comments_users_relation->no = $max_no + 1;
                    $this->comment_user_service->updateCommentUserRelation($comments_users_relation);

                    $comments_users_max_relation_no->max_no = $max_no + 1;
                    $this->comments_users_max_relation_no_service->setMaxNo($comments_users_max_relation_no);

                    $this->comments_users_max_relation_no_entity->commit();
                } catch (Exception $e) {
                    $this->comments_users_max_relation_no_entity->rollback();
                    throw $e;
                }

            }

            $page_num++;
        }
    }
}