<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class save_comment_plugin extends BrandcoPOSTActionBase {
    protected $ContainerName = 'create_comment_plugin';

    protected $Form = array(
        'package' => 'admin-comment'
    );

    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    public $NeedOption = array(BrandOptions::OPTION_COMMENT);

    protected $ValidatorDefinition = array(
        'title' => array(
            'required' => true,
            'type' => 'str',
            'length' => 255
        ),
        'share_url' => array(
            'type' => 'str',
            'length' => 255,
            'validator' => array('URL')
        ),
        'free_text' => array(
            'type' => 'str'
        ),
        'footer_text' => array(
            'type' => 'str'
        )
    );

    private $comment_plugin_id;
    private $cur_action;

    public function doThisFirst() {
        $this->comment_plugin_id = $this->POST['id'];

        if ($this->isCreateMode()) {
            $this->cur_action = 'create_comment_plugin';
            $this->Form['action'] = $this->cur_action . '?mid=' . $this->getMessage(true);
        } else {
            $this->cur_action = 'comment_plugin';
            $this->ContainerName = 'comment_plugin';
            $this->Form['action'] = $this->cur_action . '/{id}?mid=' . $this->getMessage(true);
        }
    }

    public function validate() {
        if (!$this->isURL($this->POST['share_url'])) {
            return false;
        }

        return true;
    }

    function doAction() {
        /** @var CommentPluginService $comment_plugin_service */
        $comment_plugin_service = $this->getService('CommentPluginService');
        $cur_transaction = aafwEntityStoreFactory::create('CommentPlugins');

        try {
            $cur_transaction->begin();

            if (Util::isNullOrEmpty($this->comment_plugin_id)) {
                $comment_plugin = $comment_plugin_service->createEmptyCommentPlugin();
                $comment_plugin->type = CommentPlugin::COMMENT_PLUGIN_TYPE_EXTERNAL;
            } else {
                $comment_plugin = $comment_plugin_service->getCommentPluginById($this->comment_plugin_id);
            }

            $comment_plugin->title = $this->POST['title'];
            $comment_plugin->free_text = $this->POST['free_text'];
            $comment_plugin->footer_text = $this->POST['footer_text'];
            $comment_plugin->share_url = $this->POST['share_url'];
            $comment_plugin->brand_id = $this->getBrand()->id;
            $comment_plugin->status = $this->POST['status'];
            $comment_plugin->login_limit_flg = $this->POST['login_limit_flg'];

            $comment_plugin_service->updateCommentPlugin($comment_plugin);

            $comment_plugin_service->updateCommentPluginShareSettings($comment_plugin->id, $this->POST['share_sns_list']);
            $comment_plugin_service->updateCommentPluginAction($comment_plugin->id);

            if (Util::isNullOrEmpty($comment_plugin->plugin_code)) {
                $comment_plugin->plugin_code = $comment_plugin_service->generatePluginCode($this->getBrand()->id, $comment_plugin->id);
                $comment_plugin_service->updateCommentPlugin($comment_plugin);
            }

            $cur_transaction->commit();

            $this->Data['saved'] = 1;
        } catch (Exception $e) {
            $cur_transaction->rollback();

            $params = $this->isCreateMode() ? array() : array($this->comment_plugin_id);
            return 'redirect: ' . Util::rewriteUrl('admin-comment', $this->cur_action, $params, array('mid' => $this->getMessage(true)));
        }

        return 'redirect: ' . Util::rewriteUrl('admin-comment', 'comment_plugin', array($comment_plugin->id), array('mid' => $this->getMessage()));
    }

    /**
     * @param bool $failed
     * @return string
     */
    public function getMessage($failed = false) {
        if ($this->isCreateMode()) {
            return $failed ? 'action-creating-failed' : 'action-created';
        }

        return $failed ? 'action-saving-failed' : 'action-saved';
    }

    /**
     * @return bool
     */
    public function isCreateMode() {
        return Util::isNullOrEmpty($this->POST['id']);
    }
}
