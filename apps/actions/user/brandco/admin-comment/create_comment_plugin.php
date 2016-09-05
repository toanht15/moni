<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class create_comment_plugin extends BrandcoGETActionBase {
    protected $ContainerName = 'create_comment_plugin';

    public $NeedOption = array(BrandOptions::OPTION_COMMENT);
    public $NeedAdminLogin = true;

    public function beforeValidate() {
        $this->resetValidateError();
    }

    public function validate() {
        return true;
    }

    public function doAction() {
        if (!$this->Data['ActionForm']) {
            $action_form['login_limit_flg'] = CommentPlugin::COMMENT_PLUGIN_LOGIN_LIMIT_FLG_ON;
            $action_form['status'] = CommentPlugin::COMMENT_PLUGIN_STATUS_PUBLIC;
            $action_form['share_sns_list'] = array(
                SocialAccount::SOCIAL_MEDIA_FACEBOOK,
                SocialAccount::SOCIAL_MEDIA_TWITTER
            );

            $this->assign("ActionForm", $action_form);
        }

        /** @var CommentPluginService $comment_plugin_service */
        $comment_plugin_service = $this->getService('CommentPluginService');
        $this->Data['comment_plugin'] = $comment_plugin_service->createEmptyCommentPlugin();
        $this->Data['comment_plugin']->status = $this->Data['ActionForm']['status'];

        /** @var UserService $user_service */
        $user_service = $this->getService('UserService');
        $cur_user = $user_service->getUserPublicInfoByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);

        $this->Data['comment_plugin']->from = array(
            'name' => $cur_user->name,
            'profile_img_url' => $cur_user->profile_image_url,
            'share_sns_list' => $this->Data['ActionForm']['share_sns_list']
        );

        $this->Data['mode'] = 'create_mode';
        return 'user/brandco/admin-comment/comment_plugin.php';
    }
}