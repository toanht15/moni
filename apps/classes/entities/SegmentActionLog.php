<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class SegmentActionLog extends aafwEntityBase {

    //Action Type
    const TYPE_ACTION_MESSAGE = 1;
    const TYPE_ACTION_CAMPAIGN = 2;
    const TYPE_ACTION_ADS = 3;
    const TYPE_ACTION_DOWNLOAD = 4;

}