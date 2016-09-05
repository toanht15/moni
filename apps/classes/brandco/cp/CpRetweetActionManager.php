<?php
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

use Michelf\Markdown;
/**
* Class CpRetweetActionManager
*/
class CpRetweetActionManager extends aafwObject implements CpActionManager {
    use CpActionTrait;

    /** @var  CpTweetActions $cp_concrete_actions */
    protected $cp_concrete_actions;
    protected $brand_social_accounts;
    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions               = $this->getModel('CpActions');
        $this->cp_concrete_actions      = $this->getModel('CpRetweetActions');
        $this->brand_social_accounts    = $this->getModel('BrandSocialAccounts');
        $this->logger                   = aafwLog4phpLogger::getDefaultLogger();
    }
    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpActions($cp_action_id) {
        $cp_action = $this->getCpActionById($cp_action_id);
        if ($cp_action === null) {
            $cp_concrete_action = null;
        } else {
            $cp_concrete_action = $this->getCpConcreteActionByCpAction($cp_action);
        }
        return array($cp_action, $cp_concrete_action);
    }

    /**
     * @param $cp_action_group_id
     * @param $type
     * @param $status
     * @param $order_no
     * @return mixed
     */
    public function createCpActions($cp_action_group_id, $type, $status, $order_no) {
        $cp_action          = $this->createCpAction($cp_action_group_id, $type, $status, $order_no);
        $cp_concrete_action = $this->createConcreteAction($cp_action);
        return array($cp_action, $cp_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function deleteCpActions(CpAction $cp_action) {
        $this->deleteConcreteAction($cp_action);
        $this->deleteCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed
     */
    public function updateCpActions(CpAction $cp_action, $data) {
        $this->updateCpAction($cp_action);
        $this->updateConcreteAction($cp_action, $data);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function getConcreteAction(CpAction $cp_action) {
        return $this->getCpConcreteActionByCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action) {
        $cp_concrete_action = $this->cp_concrete_actions->createEmptyObject();
        $cp_concrete_action->cp_action_id       = $cp_action->id;
        $cp_concrete_action->title              = 'リツイート';
        $cp_concrete_action->button_label_text  = 'リツイート';
        $this->cp_concrete_actions->save($cp_concrete_action);
        return $cp_concrete_action;
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $cp_concrete_action = $this->getCpConcreteActionByCpAction($cp_action);
        $cp_concrete_action->del_flg                    = 0;
        $cp_concrete_action->title                      = $data['title'];
        $cp_concrete_action->image_url                  = $data['image_url'];
        $cp_concrete_action->text                       = $data['text'];
        $cp_concrete_action->html_content               = Markdown::defaultTransform($data['text']);
        $cp_concrete_action->tweet_url                  = $data['tweet_url'];
        $cp_concrete_action->twitter_name               = $data['twitter_name'];
        $cp_concrete_action->twitter_screen_name        = $data['twitter_screen_name'];
        $cp_concrete_action->twitter_profile_image_url  = $data['twitter_profile_image_url'];
        $cp_concrete_action->tweet_id                   = $data['tweet_id'];
        $cp_concrete_action->tweet_text                 = $data['tweet_text'];
        $cp_concrete_action->tweet_has_photo            = $data['tweet_has_photo'];
        $cp_concrete_action->tweet_date                 = $data['tweet_date'];
        $cp_concrete_action->skip_flg                   = $data['skip_flg'];
        $this->cp_concrete_actions->save($cp_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function deleteConcreteAction(CpAction $cp_action) {
        $cp_concrete_action = $this->getCpConcreteActionByCpAction($cp_action);
        $cp_concrete_action->del_flg = 1;
        $this->cp_concrete_actions->save($cp_concrete_action);
    }

    /**
     * cp_concrete_action取得
     * @param CpAction $cp_action
     * @return entity
     */
    public function getCpConcreteActionByCpAction(CpAction $cp_action) {
        return $this->cp_concrete_actions->findOne(array('cp_action_id' => $cp_action->id));
    }

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $cp_concrete_action             = $this->getConcreteAction($old_cp_action);
        $new_concrete_action            = $this->cp_concrete_actions->createEmptyObject();
        $new_concrete_action->cp_action_id              = $new_cp_action_id;
        $new_concrete_action->title                     = $cp_concrete_action->title;
        $new_concrete_action->image_url                 = $cp_concrete_action->image_url;
        $new_concrete_action->text                      = $cp_concrete_action->text;
        $new_concrete_action->html_content              = Markdown::defaultTransform($cp_concrete_action->text);
        $new_concrete_action->tweet_url                 = $cp_concrete_action->tweet_url;
        $new_concrete_action->twitter_name              = $cp_concrete_action->twitter_name;
        $new_concrete_action->twitter_screen_name       = $cp_concrete_action->twitter_screen_name;
        $new_concrete_action->twitter_profile_image_url = $cp_concrete_action->twitter_profile_image_url;
        $new_concrete_action->tweet_id                  = $cp_concrete_action->tweet_id;
        $new_concrete_action->tweet_text                = $cp_concrete_action->tweet_text;
        $new_concrete_action->tweet_has_photo           = $cp_concrete_action->tweet_has_photo;
        $new_concrete_action->tweet_date                = $cp_concrete_action->tweet_date;
        $new_concrete_action->skip_flg                  = $cp_concrete_action->skip_flg;
        $this->cp_concrete_actions->save($new_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @param bool $with_concrete_actions
     * @return mixed|void
     * @throws Exception
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {
        if ($with_concrete_actions) {
            //TODO delete concrete action
        }
        $cp_retweet_action = $this->getConcreteAction($cp_action);
        if (!$cp_action || !$cp_retweet_action) {
            throw new Exception("CpRetweetActionManager#deletePhysicalRelatedCpActionData null cp_retweet_action");
        }
        /** @var RetweetMessageService $retweet_message_service */
        $retweet_message_service = $this->getService('RetweetMessageService');
        $retweet_message_service->deletePhysicalRetweetMessagesByCpRetweetActionId($cp_retweet_action->id);
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
        $cp_retweet_action = $this->getConcreteAction($cp_action);
        if (!$cp_action || !$cp_retweet_action) {
            throw new Exception("CpRetweetActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_retweet_action");
        }
        /** @var RetweetMessageService $retweet_message_service */
        $retweet_message_service = $this->getService('RetweetMessageService');
        $retweet_message_service->deletePhysicalRetweetMessagesByCpRetweetActionIdAndCpUserId($cp_retweet_action->id, $cp_user->id);
    }
}
