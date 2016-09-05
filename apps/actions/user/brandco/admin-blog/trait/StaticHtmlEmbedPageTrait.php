<?php

trait StaticHtmlEmbedPageTrait {

    public function isInvalidLoginTypeInput(){
        if($this->publicFlg == StaticHtmlEmbedEntry::NOT_PUBLIC_PAGE && $this->isEmpty($this->loginTypes)){
            return true;
        }
        return false;
    }

    public function getUser(){
        /** @var UserService $user_service */
        $user_service = $this->createService('UserService');
        $user = $user_service->getUserByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);

        return $user;
    }

    public function createEntryData(){

        $data = array(
            'title' => $this->POST['title'],
            'body' => $this->POST['body'],
            'embed_flg' => StaticHtmlEntry::EMBED_PAGE,
            'public_date' => $this->POST['public_date'],
            'public_time_hh' => $this->POST['public_time_hh'],
            'public_time_mm' => $this->POST['public_time_mm'],
            'display' => $this->POST['display'],
            'write_type' => StaticHtmlEntries::WRITE_TYPE_BLOG
        );

        if($this->POST['entryId']){
            $data['entryId'] = $this->POST['entryId'];
        }

        if($this->POST['page_url']){
            $data['page_url'] = $this->POST['page_url'];
        }

        return $data;
    }
    
    public function saveEntry($brandId,$userId,$data){
        $entry = $this->staticHtmlEntryService->createStaticHtmlEntry($brandId, $userId, $data);
        return $entry;
    }

    public function saveEmbedEntry($staticHtmlEntry){
        $this->staticHtmlEntryService->deleteStaticHtmlEmbedEntries($staticHtmlEntry);
        $this->staticHtmlEntryService->createStaticHtmlEmbedEntries($staticHtmlEntry,$this->publicFlg);
    }

    public function saveEmbedLoginType($staticHtmlEntry){
        $this->staticHtmlEntryService->deleteStaticHtmlExternalPageLoginTypes($staticHtmlEntry);

        if(!Util::isNullOrEmpty($this->loginTypes)){
            $this->staticHtmlEntryService->createStaticHtmlExternalPageLoginTypes($staticHtmlEntry,$this->loginTypes);
        }
    }
}