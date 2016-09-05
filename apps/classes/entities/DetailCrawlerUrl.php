<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class DetailCrawlerUrl extends aafwEntityBase {
    const TYPE_INTERNAL = 1;
    const TYPE_EXTERNAL = 2;

    const CRAWLER_TYPE_TWITTER = 1;
    const CRAWLER_TYPE_FACEBOOK = 2;
    const CRAWLER_TYPE_YOUTUBE = 3;
    const CRAWLER_TYPE_RSS = 4;
    const CRAWLER_TYPE_INSTAGRAM = 5;

    //data type facebook crawler
    const DATA_TYPE_LIKE = 1;
    const DATA_TYPE_COMMENT = 2;

    //data type twitter crawler
    const DATA_TYPE_RETWEET = 3;
    const DATA_TYPE_REPLY = 4;
    const DATA_TYPE_FOLLOW = 5;
}