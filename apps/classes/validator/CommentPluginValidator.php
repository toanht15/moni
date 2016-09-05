<?php
AAFW::import('jp.aainc.classes.validator.BaseValidator');

class CommentPluginValidator extends BaseValidator {

    private $comment_plugin_id;
    private $comment_plugin;
    private $brand_id;

    private $service_factory;

    public function __construct($comment_plugin_id, $brand_id) {
        parent::__construct();

        $this->comment_plugin_id = $comment_plugin_id;
        $this->brand_id = $brand_id;

        $this->service_factory = new aafwServiceFactory();
    }

    public function validate() {
        if (!$this->isValidCommentPlugin()) {
            $this->errors['segment_id'][] = 'コメントプラグインが存在しません';
            return;
        }
    }

    /**
     * @return bool
     */
    public function isValidCommentPlugin() {
        if (trim($this->comment_plugin_id) === '') {
            return false;
        }

        $comment_plugin = $this->getCommentPlugin();

        if (!$comment_plugin->id) {
            return false;
        }

        if ($comment_plugin->brand_id != $this->brand_id) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isActiveCommentPlugin() {
        if ($this->getCommentPlugin()->status == CommentPlugin::COMMENT_PLUGIN_STATUS_PRIVATE) {
            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getCommentPlugin() {
        if (!$this->comment_plugin) {
            $comment_plugin_service = $this->service_factory->create('CommentPluginService');
            $this->comment_plugin = $comment_plugin_service->getCommentPluginById($this->comment_plugin_id);
        }

        return $this->comment_plugin;
    }
}
