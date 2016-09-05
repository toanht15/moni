<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpMovieAction extends aafwEntityBase {
    CONST IS_YOUTUBE_SELECT = 1;
    CONST IS_YOUTUBE_ID = 2;
    CONST IS_UPLOADED = 3;

    CONST IS_UPLOADED_FILE = 0;
    CONST IS_UPLOADED_URL = 1;
    
    protected $_Relations = array(

        'CpAction' => array(
            'cp_action_id' => 'id',
        )
    );

    public function isOriginalVideo() {
        return $this->movie_type == self::IS_UPLOADED;
    }
}