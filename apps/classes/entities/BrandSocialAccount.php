<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class BrandSocialAccount extends aafwEntityBase {

    const FB_EXPIRED_DATE = 60;

    protected $_Relations = array(
        'FacebookStreams' => array (
            'id' => 'brand_social_account_id',
        ),
        'TwitterStreams' => array (
            'id' => 'brand_social_account_id',
        ),
        'YoutubeStreams' => array (
            'id' => 'brand_social_account_id',
        ),
        'InstagramStreams' => array(
            'id' => 'brand_social_account_id'
        ),
        'Users' => array(
            'user_id' => 'id'
        ),
        'Brands' => array(
            'brand_id' => 'id'
        )
    );

    public function getUrl() {
        if ($this->social_app_id == SocialApps::PROVIDER_TWITTER) {
            $url = '//twitter.com/' . json_decode($this->store)->screen_name;
        } elseif ($this->social_app_id == SocialApps::PROVIDER_FACEBOOK) {
            $url = json_decode($this->store)->link;
        } elseif ($this->social_app_id == SocialApps::PROVIDER_GOOGLE) {
            $url = '//www.youtube.com/channel/' . json_decode($this->store)->channelId;
        } elseif ($this->social_app_id == SocialApps::PROVIDER_INSTAGRAM) {
            $url = '//instagram.com/' . $this->name;
        }

        return $url;
    }

    public function getName() {
        $name = '';
        if ($this->social_app_id == SocialApps::PROVIDER_INSTAGRAM) {
            $name = json_decode($this->store)->username;
        } else {
            $name = json_decode($this->store)->name;
        }

        return $name;
    }

    /**
     * @return string
     */
    public function getYoutubeChannelId() {
        $channelId = '';
        if ($this->social_app_id == SocialApps::PROVIDER_GOOGLE && $this->store) {
            $channelId = json_decode($this->store)->channelId;
        }
        return $channelId;
    }
    

}
