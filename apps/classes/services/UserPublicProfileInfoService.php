<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class UserPublicProfileInfoService extends aafwServiceBase {

    protected $user_public_profile_infos;

    public function __construct() {
        $this->user_public_profile_infos = $this->getModel("UserPublicProfileInfos");
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function getPublicProfileInfo($user_id) {
        $filter = array(
            'user_id' => $user_id
        );

        return $this->user_public_profile_infos->findOne($filter);
    }

    /**
     * @param $user_id
     * @param $nickname
     * @return mixed
     */
    public function createPublicProfileInfo($user_id, $nickname) {
        $nickname = trim($nickname); // strip space from head n tail, accept ideographic space
        $public_profile_info = $this->getPublicProfileInfo($user_id);

        if (Util::isNullOrEmpty($public_profile_info)) {
            $public_profile_info = $this->user_public_profile_infos->createEmptyObject();

            $public_profile_info->user_id = $user_id;
            $public_profile_info->nickname = $nickname;

            $this->user_public_profile_infos->save($public_profile_info);
        } elseif (strcmp($nickname, $public_profile_info->nickname) !== 0) {
            $public_profile_info->nickname = $nickname;

            $this->user_public_profile_infos->save($public_profile_info);
        }

        return $public_profile_info;
    }
}