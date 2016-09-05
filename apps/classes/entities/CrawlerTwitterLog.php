<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CrawlerTwitterLog extends aafwEntityBase {

    //Twitter Entry / Twitter Stream Type
    const TYPE_INTERNAL = 1;
    const TYPE_EXTERNAL = 2;

    //Twitter crawler Type
    const CRAWLER_TYPE_REPLY = 1;
    const CRAWLER_TYPE_RETWEET = 2;
    const CRAWLER_TYPE_FOLLOW = 3;
}