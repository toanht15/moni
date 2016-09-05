<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.clients.UtilityApiClient');

class ReplaceTagService extends aafwServiceBase{

    const TYPE_ALLIED_ID        = '<#ALLIED_ID>';  // Allied ID
    const TYPE_ANNOUNCE_TAG     = '%ALLIED_ID%';   // Announce module tag

    private $replace_tag_array =[
        self::TYPE_ALLIED_ID,
        self::TYPE_ANNOUNCE_TAG
    ];

    private $client_ids = array(
        self::TYPE_ALLIED_ID        => UtilityApiClient::REPLACE_TAG,
        self::TYPE_ANNOUNCE_TAG     => UtilityApiClient::REPLACE_ANNOUNCE_TAG
    );

    public function getTag($tag,$parameters=array()){
        foreach ($parameters as $key=>$value) {
            if (in_array($key, $this->replace_tag_array) && strpos($tag, $key) !== FALSE) {
                return $this->getReplaceAlliedId($tag, $key, $value);
            }
        }
        return $tag;
    }

    private function getReplaceAlliedId($tag, $parameters_key, $parameters_value){

        try {
            $hash_token = $this->getMerazomaToken($parameters_value, $this->client_ids[$parameters_key]);
            $replace_tag = str_replace($parameters_key, $hash_token, $tag);
        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error("UserMessageThreadActionJoinFinish#doService() Exception :" . $e);
            return $tag;
        }

        return $replace_tag;
    }

    private function getMerazomaToken($platform_user_id, $client_id){

        AAFW::import('jp.aainc.classes.clients.UtilityApiClient');

        $token = UtilityApiClient::getInstance()->getUserToken($client_id, $platform_user_id);
        return $token;
    }
}