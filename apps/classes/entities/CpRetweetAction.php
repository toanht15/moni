<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpRetweetAction extends aafwEntityBase {

    const NOT_POST_RETWEET = 0;              //リツイートボタンをまだ押さない
    const POST_RETWEET = 1;                  //ツイーター新連携
    const POSTED_RETWEET = 2;                //リツイートが成功
    const CONNECT_AND_POSTED_RETWEET = 3;    //ツイーターを連携してから、リツイートする

    const NOT_SKIP = 0;
    const SKIPPED = 1;
    protected $_Relations = array(
        'CpActions' => array(
            'cp_action_id' => 'id',
        ),
    );
}
