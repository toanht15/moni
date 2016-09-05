<?php
//Mailに関するテーブルを操作するクラス
class UserMailTrackingService extends aafwServiceBase {

    /** @var  UserMails $userMailStore */
    private $userMailStore;
    /** @var OpenUserMailTrackingLogs $openUserMailTrackingLogStore */
    private $openUserMailTrackingLogStore;
    /** @var EntryMails $entryMailStore  */
    private $entryMailStore;
    /** @var WelcomeMails $welcomeMailStore  */
    private $welcomeMailStore;
    /** @var  CpLostMails */
    private $cpLostMailStore;

    public function __construct() {
        $this->userMailStore                = $this->getModel('UserMails');
        $this->openUserMailTrackingLogStore = $this->getModel('OpenUserMailTrackingLogs');
        $this->entryMailStore               = $this->getModel('EntryMails');
        $this->welcomeMailStore             = $this->getModel('WelcomeMails');
        $this->cpLostMailStore              = $this->getModel('CpLostMails');
    }
    //***************************************************************************
    // user_mails
    //***************************************************************************
    /**
     * @param $user_mail_id
     * @return entity
     */
    public function findUserMail($user_mail_id) {
        $filter = array(
            'id' => $user_mail_id
        );
       
        return $this->userMailStore->findOne($filter);
    }

    /**
     * limit_date以前に送信したメールのID取得
     * @param $limit_date
     * @return array
     */
    public function findUserMailIdsBeforeLimitDate($limit_date) {
        $filter = array(
            'sent_at:<' => $limit_date
        );

        $user_mails = $this->userMailStore->find($filter);

        $user_mail_ids = array();
        foreach ($user_mails as $user_mail) {
            $user_mail_ids[] = $user_mail->id;
        }

        return $user_mail_ids;
    }
    
    /**
     * @param $user_id
     * @return mixed
     * @throws aafwException
     */
    public function createUserMail($user_id) {
        $userMail = $this->userMailStore->createEmptyObject();

        $userMail->user_id = $user_id;
        $userMail->sent_at = date('Y-m-d H:i:s');

        return $this->userMailStore->save($userMail);
    }

    /**
     * @param $user_mail
     */
    public function deletePhysicalUserMail($user_mail) {
        $this->userMailStore->deletePhysical($user_mail);
    }

    //***************************************************************************
    // welcome_mails
    //***************************************************************************
    /**
     * @param $user_mail_id
     * @return entity
     */
    public function findWelcomeMail($user_mail_id) {
        $filter = array(
            'user_mail_id' => $user_mail_id
        );
        
        return $this->welcomeMailStore->findOne($filter);
    }

    /**
     * @param $welcome_mail
     */
    public function deletePhysicalWelcomeMail($welcome_mail) {
        $this->welcomeMailStore->deletePhysical($welcome_mail);
    }

    /**
     * @param $user_mail_id
     * @param $brand_id
     * @param $cp_id
     * @return mixed
     * @throws aafwException
     */
    public function createWelcomeMail($user_mail_id, $brand_id, $cp_id) {
        $welcomeMail = $this->welcomeMailStore->createEmptyObject();

        $welcomeMail->user_mail_id = $user_mail_id;
        $welcomeMail->brand_id = $brand_id;
        $welcomeMail->cp_id = $cp_id;

        return $this->welcomeMailStore->save($welcomeMail);
    }

    /**
     * @param $user_mail_id
     * @return bool
     */
    public function isExistedWelcomeMail($user_mail_id) {
        $filter = array(
            'user_mail_id' => $user_mail_id
        );

        if ($this->welcomeMailStore->findOne($filter)) {
            return true;
        }

        return false;
    }

    //***************************************************************************
    // entry_mails
    //***************************************************************************
    /**
     * @param $user_mail_id
     * @return entity
     */
    public function findEntryMail($user_mail_id) {
        $filter = array(
            'user_mail_id' => $user_mail_id
        );
        
        return $this->entryMailStore->findOne($filter);
    }

    /**
     * @param $entry_mail
     */
    public function deletePhysicalEntryMail($entry_mail) {
        $this->entryMailStore->deletePhysical($entry_mail);
    }

    /**
     * @param $user_mail_id
     * @param $cp_id
     * @return mixed
     * @throws aafwException
     */
    public function createEntryMail($user_mail_id, $cp_id) {
        $entryMail = $this->entryMailStore->createEmptyObject();

        $entryMail->user_mail_id = $user_mail_id;
        $entryMail->cp_id = $cp_id;

        return $this->entryMailStore->save($entryMail);
    }

    /**
     * @param $user_mail_id
     * @return bool
     */
    public function isExistedEntryMail($user_mail_id) {
        $filter = array(
            'user_mail_id' => $user_mail_id
        );

        if ($this->entryMailStore->findOne($filter)) {
            return true;
        }

        return false;
    }

    //***************************************************************************
    // open_user_mail_tracking_logs
    //***************************************************************************
    /**
     * @param $user_mail_id
     * @return entity
     */
    public function findOpenUserMailTrackingLog($user_mail_id) {
        $filter = array(
            'user_mail_id' => $user_mail_id
        );
        
        return $this->openUserMailTrackingLogStore->findOne($filter);
    }

    /**
     * @param $user_mail_id
     * @return mixed
     */
    public function createOpenUserMailTrackingLogs($user_mail_id) {
        $open_user_mail_tracking_log = $this->openUserMailTrackingLogStore->createEmptyObject();

        $open_user_mail_tracking_log->user_mail_id = $user_mail_id;
        $open_user_mail_tracking_log->user_agent   = getenv('HTTP_USER_AGENT');
        $open_user_mail_tracking_log->remote_ip    = Util::getClientIP();
        $open_user_mail_tracking_log->referer_url  = getenv('HTTP_REFERER');
        $open_user_mail_tracking_log->device       = Util::isSmartPhone();
        $open_user_mail_tracking_log->language     = getenv('HTTP_ACCEPT_LANGUAGE');
        $open_user_mail_tracking_log->opened_at    = date('Y-m-d H:i:s');

        return $this->openUserMailTrackingLogStore->save($open_user_mail_tracking_log);
    }

    /**
     * @param $open_user_mail_tracking_log
     */
    public function deletePhysicalOpenUserMailTrackingLog($open_user_mail_tracking_log) {
        $this->openUserMailTrackingLogStore->deletePhysical($open_user_mail_tracking_log);
    }

    /**
     * @param $user_mail_id
     * @return bool
     */
    public function isExistedOpenUserMailTrackingLog($user_mail_id) {
        if ($this->findOpenUserMailTrackingLog($user_mail_id)) {
            return true;
        }
        return false;
    }

    //***************************************************************************
    // cp_lost_mails
    //***************************************************************************

    /**
     * @param $user_mail_id
     * @return entity
     */
    public function findCpLostMail($user_mail_id){
        $filter = array(
            'user_mail_id' => $user_mail_id
        );

        return $this->cpLostMailStore->findOne($filter);
    }

    /**
     * @param $cp_lost_mail
     */
    public function deletePhysicalCpLostMail($cp_lost_mail) {
        $this->cpLostMailStore->deletePhysical($cp_lost_mail);
    }

    /**
     * @param $user_mail_id
     * @param $cp_id
     * @return mixed
     * @throws aafwException
     */
    public function createCpLostMail($user_mail_id, $cp_id) {
        $cpLostMail = $this->cpLostMailStore->createEmptyObject();

        $cpLostMail->user_mail_id = $user_mail_id;
        $cpLostMail->cp_id        = $cp_id;

        return $this->cpLostMailStore->save($cpLostMail);
    }
}
