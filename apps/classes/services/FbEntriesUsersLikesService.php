<?php

AAFW::import('jp.aainc.aafw.base.aafwServiceBase');

class FbEntriesUsersLikesService extends aafwServiceBase
{
    /** @var $fb_entries_users_likes */
    protected $fb_entries_users_likes;

    protected $fb_client;

    public function __construct()
    {
        $this->fb_entries_users_likes = $this->getModel('FbEntriesUsersLikes');
    }

    /**
     * Get All Users Likes
     * @return mixed
     */
    public function getAllUsersLikes()
    {
        return $this->fb_entries_users_likes->findAll();
    }

}