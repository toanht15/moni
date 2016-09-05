<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class embed extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_COMMENT);
    public $NeedRedirect = true;

    public function validate () {
        $plugin_code = $this->GET['exts'][0];
        /** @var CommentPluginService $comment_plugin_service */
        $comment_plugin_service = $this->createService('CommentPluginService');

        if (!Util::isNullOrEmpty($plugin_code)) {
            $comment_plugin = $comment_plugin_service->getCommentPluginByCode($plugin_code);
            if ($comment_plugin->isLegalBrand($this->getBrand()->id) && $comment_plugin->isPublic()) {
                $this->Data['comment_plugin'] = $comment_plugin;
            }

            return true;
        }

        try {
            $cache_manager = new CacheManager(CacheManager::PREVIEW_PREFIX);
            $comment_plugin_data = $cache_manager->getCache(CacheManager::COMMENT_PLUGIN_PREVIEW_KEY, array($this->getBrand()->id));

            if (Util::isNullOrEmpty($comment_plugin_data)) {
                return false;
            }

            $this->Data['preview_mode'] = 'on';
            $this->Data['comment_plugin'] = $comment_plugin_service->createEmptyCommentPlugin();
            $this->Data['comment_plugin']->status = $comment_plugin_data['cp_status'];
            $this->Data['comment_plugin']->free_text = $comment_plugin_data['cp_free_text'];
            $this->Data['comment_plugin']->footer_text = $comment_plugin_data['cp_footer_text'];

            $php_parser = new PHPParser();
            /** @var UserService $user_service */
            $user_service = $this->getService('UserService');
            $cur_user = $user_service->getUserPublicInfoByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);

            $this->Data['comment_plugin']->from = array(
                'name' => $cur_user->name,
                'profile_img_url' => $cur_user->profile_image_url ?: $php_parser->setVersion('/img/base/imgUser1.jpg'),
                'share_sns_list' => $comment_plugin_data['cp_sns_list']
            );
        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
            return false;
        }

        return true;
    }

    function doAction() {
        return 'user/brandco/plugin/embed.php';
    }
}