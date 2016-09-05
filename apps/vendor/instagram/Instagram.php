<?php
class Instagram {

    const LEGAL_ACCESS_CODE = 200;
    const OAUTH_EXCEPTION_CODE = 400;

    const CSRF_SALT = 'brandco_csrf_token';

    const INSTAGRAM_AUTH_URL = 'https://instagram.com/oauth/authorize/';
    const INSTAGRAM_API_URL = 'https://api.instagram.com';
    const INSTAGRAM_EMBED_MEDIA_URL = 'http://api.instagram.com/publicapi/oembed';

    const INCOMING_STATUS_FOLLOWS = 'followed_by';
    const INCOMING_STATUS_REQUESTED = 'requested_by';
    const INCOMING_STATUS_BLOCKED = 'blocked_by_you';
    const INCOMING_STATUS_NONE = 'none';

    const OUTGOING_STATUS_FOLLOWS = 'follows';
    const OUTGOING_STATUS_REQUESTED = 'requested';
    const OUTGOING_STATUS_NONE = 'none';

    const EXCEPTION_ACCESS_DENIED = 'instagram_access_denied';

    private $client_id;
    private $client_secret;
    private $redirect_uri;
    private $scope;
    private $csrf_token;

    private $user_info;
    private $access_token;

    /**
     * @param $code
     */
    public function authenticate($code) {
        if (!$code) {
            throw new Exception('Invalid code');
        }

        $params = array(
            'client_id=' . $this->getClientId(),
            'client_secret=' . $this->getClientSecret(),
            'grant_type=authorization_code',
            'redirect_uri=' . urlencode($this->getRedirectUri()),
            'code=' . $code
        );
        $auth_url = self::INSTAGRAM_API_URL . '/oauth/access_token';

        $result = $this->executePOSTRequest($auth_url, $params);

        if (!$result || $result->code == self::OAUTH_EXCEPTION_CODE || $result->access_token == null) {
            throw new Exception('Request access token failed');
        }

        // Init basic information
        $this->setUserInfo($result->user);
        $this->setAccessToken($result->access_token);
    }

    /**
     * @param $media_url
     * @return mixed
     * @throws Exception
     */
    public function getEmbedMedia($media_url) {
        if (!$media_url) {
            throw new Exception('Invalid media');
        }

        $params = array(
            'url=' . $media_url
        );
        $embed_media_url = self::INSTAGRAM_EMBED_MEDIA_URL . '?' . $this->buildParams($params);

        return $this->executeGETRequest($embed_media_url);
    }

    /**
     * @param $user_id
     * @param $access_token
     * @param null $url_params
     * @return mixed
     * @throws Exception
     */
    public function getRecentMedia($user_id, $access_token, $url_params = null) {
        if (!$access_token) {
            throw new Exception('Invalid access token');
        }

        $params = array(
            'access_token=' . $access_token,
            $url_params
        );
        $get_recent_media_url = self::INSTAGRAM_API_URL . '/v1/users/' . $user_id . '/media/recent?' . $this->buildParams($params);

        return $this->executeGETRequest($get_recent_media_url);
    }

    /**
     * @param $media_id
     * @param $access_token
     * @return mixed
     * @throws Exception
     */
    public function getMediaInfo($media_id, $access_token) {
        if (!$access_token) {
            throw new Exception('Invalid access token');
        }

        $params = array(
            'access_token=' . $access_token
        );
        $get_media_info_url = self::INSTAGRAM_API_URL . '/v1/media/' . $media_id . '?' . $this->buildParams($params);

        return $this->executeGETRequest($get_media_info_url);
    }

    public function getTagMedia($name, $access_token, $option, $limit = 20) {
        if (!$name || !$access_token) throw new Exception('Invalid args');

        $params = array(
            'access_token=' . $access_token,
            'count=' . $limit
        );

        if ($option) {
            foreach ($option as $key => $value) {
                $params[] = $key . '=' . $value;
            }
        }

        $get_media_info_url = self::INSTAGRAM_API_URL . '/v1/tags/' . urlencode($name) . '/media/recent?' . $this->buildParams($params);
        
        return $this->executeGETRequest($get_media_info_url);
    }

    public function getTagInfo($name, $access_token) {
        if (!$name || !$access_token) throw new Exception('Invalid args');

        $params = array(
            'access_token=' . $access_token,
        );

        $get_media_info_url = self::INSTAGRAM_API_URL . '/v1/tags/' . urlencode($name) . '?' . $this->buildParams($params);
        return $this->executeGETRequest($get_media_info_url);
    }

    /**
     * @param $user_id
     * @param $access_token
     * @return mixed
     * @throws Exception
     */
    public function getAccountInfo($user_id, $access_token) {
        if (!$user_id) {
            throw new Exception('Invalid user id');
        }

        $params = array(
            'access_token='. $access_token
        );
        $get_user_info_url = self::INSTAGRAM_API_URL . '/v1/users/' . $user_id . '?' . $this->buildParams($params);

        return $this->executeGETRequest($get_user_info_url);
    }

    /**
     * @param $user_id
     * @param $access_token
     * @return mixed
     * @throws Exception
     */
    public function getRelationship($user_id, $access_token) {
        if (!$user_id) {
            throw new Exception('Invalid user id');
        }

        $params = array(
            'access_token='. $access_token
        );
        $get_relationship_url = self::INSTAGRAM_API_URL . '/v1/users/' . $user_id . '/relationship?' . $this->buildParams($params);

        return $this->executeGETRequest($get_relationship_url);
    }

    /**
     * @param $url
     * @param $params
     * @return mixed
     */
    public function executePOSTRequest($url, $params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->buildParams($params));
        $json_response = curl_exec($ch);
        curl_close($ch);

        return json_decode($json_response);
    }

    /**
     * @param $url
     * @return mixed
     */
    public function executeGETRequest($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        $json_response = curl_exec($ch);
        curl_close($ch);

        return json_decode($json_response);
    }

    /**
     * @param $params
     * @return string
     */
    public function buildParams($params) {
        return implode('&', $params);
    }

    /**
     * Return Instagram Authorization URL
     * @return string
     */
    public function buildAuthUrl() {
        $params = array(
            'response_type=code',
            'client_id=' . urlencode($this->getClientId()),
            'redirect_uri=' . urlencode($this->getRedirectUri()),
            'scope=' . $this->getScope(),
            'state=' . urlencode($this->getCsrfToken())
        );
        $params = implode('&', $params);

        return self::INSTAGRAM_AUTH_URL . '?' . $params;
    }

    /**
     * @return mixed
     */
    public function getUserInfo() {
        return $this->user_info;
    }

    /**
     * @param mixed $user_info
     */
    public function setUserInfo($user_info) {
        $this->user_info = $user_info;
    }

    /**
     * @return mixed
     */
    public function getAccessToken() {
        return $this->access_token;
    }

    /**
     * @param mixed $access_token
     */
    public function setAccessToken($access_token) {
        $this->access_token = $access_token;
    }

    /**
     * @param $token
     */
    public function setCsrfToken($token) {
        $this->csrf_token = hash('sha256', self::CSRF_SALT . $token);
    }

    /**
     * @return mixed
     */
    public function getCsrfToken() {
        return $this->csrf_token;
    }

    /**
     * @param $client_id
     */
    public function setClientId($client_id) {
        $this->client_id = $client_id;
    }

    /**
     * @return mixed
     */
    public function getClientId() {
        return $this->client_id;
    }

    /**
     * @param $client_secret
     */
    public function setClientSecret($client_secret) {
        $this->client_secret = $client_secret;
    }

    /**
     * @return mixed
     */
    public function getClientSecret() {
        return $this->client_secret;
    }

    /**
     * @param $redirect_uri
     */
    public function setRedirectUri($redirect_uri) {
        $this->redirect_uri = $redirect_uri;
    }

    /**
     * @return mixed
     */
    public function getRedirectUri() {
        return $this->redirect_uri;
    }

    /**
     * @param $scopes
     */
    public function setScope($scopes) {
        $this->scope = implode('+', $scopes);
    }

    /**
     * @return mixed
     */
    public function getScope() {
        return $this->scope;
    }
}