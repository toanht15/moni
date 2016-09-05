<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.validator.user.SnsPageValidator');

class detail extends BrandcoGETActionBase {
    public $NeedRedirect = true;

    public $NeedOption = array();

    private $sns_entry_id;
    private $brand_social_account;
    private $brand_social_account_id;

    public function doThisFirst() {
        $this->brand_social_account_id = $this->GET['exts'][0];
        $this->sns_entry_id =  $this->GET['exts'][1];
    }

    public function validate() {
        $validator = new SnsPageValidator($this->brand_social_account_id, $this->getBrand()->id);
        $validator->validate();

        if ($this->getBrandSocialAccount()->social_app_id == SocialApps::PROVIDER_INSTAGRAM) {
            return '404';
        }

        return $validator->isValid() ? true : '404';
    }

    public function doAction() {
        $stream = $this->getSnsStream();
        $stream_service = $this->createService(get_class($stream) . 'Service');

        $sns_entry = $stream_service->getAvailableEntryById($this->sns_entry_id);
        if (!$sns_entry) return '403';

        $sns_stream = $stream_service->getStreamById($sns_entry->stream_id);

        $next_entry_id = $stream_service->getNextEntryId($this->sns_entry_id, $sns_stream->id);
        if ($next_entry_id) $this->Data['page_data']['next_url'] = Util::rewriteUrl('sns', 'detail', array($this->brand_social_account_id, $next_entry_id));

        $prev_entry_id = $stream_service->getPreviousEntryId($this->sns_entry_id, $sns_stream->id);
        if ($prev_entry_id) $this->Data['page_data']['prev_url'] = Util::rewriteUrl('sns', 'detail', array($this->brand_social_account_id, $prev_entry_id));

        $this->Data['sns_entry'] = $sns_entry;
        $this->Data['brand_social_account'] = $this->getBrandSocialAccount();
        $this->Data['page_data']['sns_type'] = SocialApps::getSocialMediaPageClassType($this->getBrandSocialAccount()->social_app_id);
        $this->Data['page_data']['sns_name'] = SocialApps::getSocialMediaPageName($this->getBrandSocialAccount()->social_app_id);

        $brand_social_account_name = $this->getBrandSocialAccount()->social_app_id == SocialApps::PROVIDER_TWITTER ? $this->getBrandSocialAccount()->name . '@' . $this->getBrandSocialAccount()->screen_name : $this->getBrandSocialAccount()->name;
        $this->Data['page_data']['page_name'] = $brand_social_account_name;

        if ($this->getBrandSocialAccount()->social_app_id == SocialApps::PROVIDER_GOOGLE) {
            $snippet = json_decode($sns_entry->extra_data)->snippet;
            $this->Data['page_data']['page_title'] = $snippet->title;
        } else {
            $this->Data['page_data']['page_title'] = $this->cutLongText($sns_entry->panel_text, 15);
        }

        $tmp_og_title =  SocialApps::getSocialMediaPageOgTitle($this->getBrandSocialAccount()->social_app_id) . '「' . $brand_social_account_name . '」';
        $this->Data['pageStatus']['og']['title'] = $tmp_og_title . '/' . $this->getBrand()->name;
        $this->Data['pageStatus']['og']['description'] = $this->cutLongText($sns_entry->panel_text, 100);
        $this->Data['pageStatus']['og']['image'] = trim($sns_entry->image_url) != null ? $sns_entry->image_url : $this->getBrandSocialAccount()->picture_url;
        $this->Data['pageStatus']['og']['url'] = Util::rewriteUrl('sns', 'detail', array($this->getBrandSocialAccount()->id, $sns_entry->id));

        return 'user/brandco/sns/detail.php';
    }

    public function getBrandSocialAccount() {
        if (!$this->brand_social_account) {
            $brand_social_account_service = $this->createService('BrandSocialAccountService');
            $this->brand_social_account = $brand_social_account_service->getBrandSocialAccountById($this->brand_social_account_id);
        }

        return $this->brand_social_account;
    }

    public function getSnsStream() {
        if ($this->getBrandSocialAccount()->social_app_id == SocialApps::PROVIDER_FACEBOOK) {
            $stream = $this->getBrandSocialAccount()->getFacebookStream();
        } elseif ($this->getBrandSocialAccount()->social_app_id == SocialApps::PROVIDER_TWITTER) {
            $stream = $this->getBrandSocialAccount()->getTwitterStream();
        } elseif ($this->getBrandSocialAccount()->social_app_id == SocialApps::PROVIDER_GOOGLE) {
            $stream = $this->getBrandSocialAccount()->getYoutubeStream();
        } elseif ($this->getBrandSocialAccount()->social_app_id == SocialApps::PROVIDER_INSTAGRAM) {
            $stream = $this->getBrandSocialAccount()->getInstagramStream();
        }

        return $stream;
    }
}