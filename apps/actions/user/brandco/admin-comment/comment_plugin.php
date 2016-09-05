<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.validator.CommentPluginValidator');

class comment_plugin extends BrandcoGETActionBase {
    protected $ContainerName = 'comment_plugin';

    public $NeedOption = array(BrandOptions::OPTION_COMMENT);
    public $NeedAdminLogin = true;

    private $comment_plugin;

    public function beforeValidate() {
        $this->resetValidateError();
    }

    public function validate() {
        $comment_plugin_id = $this->GET['exts'][0];

        $comment_plugin_validator = new CommentPluginValidator($comment_plugin_id, $this->getBrand()->id);
        $comment_plugin_validator->validate();

        if (!$comment_plugin_validator->isValid()) {
            return false;
        }

        $this->comment_plugin = $comment_plugin_validator->getCommentPlugin();
        if ($this->comment_plugin->type == CommentPlugin::COMMENT_PLUGIN_TYPE_INTERNAL) {
            return false;
        }

        return true;
    }

    public function doAction() {
        /** @var CommentPluginService $comment_plugin_service */
        $comment_plugin_service = $this->getService('CommentPluginService');

        $this->comment_plugin->share_sns_list = $comment_plugin_service->getCommentPluginShareSnsList($this->comment_plugin->id);
        $this->assign('ActionForm', $this->comment_plugin->toArray());

        /** @var UserService $user_service */
        $user_service = $this->getService('UserService');
        $cur_user = $user_service->getUserPublicInfoByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);

        $this->Data['comment_plugin'] = $this->comment_plugin;
        $this->Data['comment_plugin']->from = array(
            'name' => $cur_user->name,
            'profile_img_url' => $cur_user->profile_image_url,
            'share_sns_list' => $this->Data['ActionForm']['share_sns_list']
        );

        if (!Util::isNullOrEmpty($this->comment_plugin->plugin_code)) {
            $php_parser = new PHPParser();
            $this->Data['plugin_script'] = $php_parser->parseTemplate('CommentPluginEmbedCodeTemplate.php', array('plugin_code' => $this->comment_plugin->plugin_code));
        }

        $this->Data['mode'] = 'edit_mode';
        return 'user/brandco/admin-comment/comment_plugin.php';
    }
}