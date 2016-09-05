<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.validator.user.SnsPageValidator');

class category extends BrandcoGETActionBase {

    public $NeedOption = array();

    public $NeedRedirect = true;

    public $brand_social_account_id;

    public function doThisFirst() {
        $this->brand_social_account_id = $this->GET['exts'][0];
    }

    public function validate() {
        $validator = new SnsPageValidator($this->brand_social_account_id, $this->getBrand()->id);
        $validator->validate();

        return $validator->isValid() ? true : '404';
    }

    public function doAction() {
        $brand_social_account_service = $this->createService('BrandSocialAccountService');

        $brand_social_account = $brand_social_account_service->getBrandSocialAccountById($this->brand_social_account_id);
        $this->Data['brand_social_account'] = $brand_social_account;
        $this->Data['page_data']['sns_name'] = SocialApps::getSocialMediaPageName($brand_social_account->social_app_id);

        $brand_social_account_name = $brand_social_account->social_app_id == SocialApps::PROVIDER_TWITTER ? $brand_social_account->name . '@' . $brand_social_account->screen_name : $brand_social_account->name;
        $tmp_og_title =  SocialApps::getSocialMediaPageOgTitle($brand_social_account->social_app_id) . '「' . $brand_social_account_name . '」';
        $this->Data['pageStatus']['og']['title'] = $tmp_og_title . ' / ' . $this->getBrand()->name ;
        $this->Data['pageStatus']['og']['image'] = $brand_social_account->picture_url;
        $this->Data['pageStatus']['og']['url'] = Util::rewriteUrl('sns', 'category', array($brand_social_account->id));

        if ($brand_social_account->social_app_id == SocialApps::PROVIDER_TWITTER) {
            $this->Data['pageStatus']['og']['description'] = $tmp_og_title . 'のツイート一覧';
        } elseif ($brand_social_account->social_app_id == SocialApps::PROVIDER_FACEBOOK) {
            $this->Data['pageStatus']['og']['description'] = $tmp_og_title . 'の投稿一覧';
        } elseif ($brand_social_account->social_app_id == SocialApps::PROVIDER_GOOGLE) {
            $this->Data['pageStatus']['og']['description'] = $tmp_og_title . 'の動画一覧';
        } elseif ($brand_social_account->social_app_id == SocialApps::PROVIDER_INSTAGRAM) {
            $this->Data['pageStatus']['og']['description'] = $tmp_og_title . 'のギャラリー';
        }

        return 'user/brandco/sns/category.php';
    }
}