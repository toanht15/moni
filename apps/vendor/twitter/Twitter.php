<?php
require_once 'twitteroauth/twitteroauth.php';

class Twitter extends TwitterOAuth {
    const TW_API_INVALID_PAGE           = 34;
    const TW_API_RATE_LIMIT_EXCEEDED    = 88;
    const TW_API_INVALID_ID             = 144;
    const TW_API_NOT_AUTHORIZED         = 179;
    const TW_API_ALREADY_RETWEETED      = 327;

    private $skipping_error_codes = array(
        self::TW_API_ALREADY_RETWEETED
    );

	protected $accessToken =
	array('user_id', 'screen_name', 'oauth_token', 'oauth_token_secret');

	private static $Instances = null;
	private $user = null;
    private $api_result = null;

    private $logger;
    private $hipchat_logger;
    private $ConsumerKey;
    private $ConsumerSecret;

    const BRANDCO_MODE_ADMIN = 'admin';
    const BRANDCO_MODE_ADS = 'marketing';

    public function __construct($consumer_key, $consumer_secret, $oauth_token = null, $oauth_token_secret = null, $login_mode = self::BRANDCO_MODE_ADMIN) {
        parent::__construct($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
        $config = aafwApplicationConfig::getInstance();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();

        if($login_mode == self::BRANDCO_MODE_ADMIN) {
            $this->ConsumerKey = $config->query('@twitter.Admin.ConsumerKey');
            $this->ConsumerSecret = $config->query('@twitter.Admin.ConsumerSecret');
        } elseif($login_mode == self::BRANDCO_MODE_ADS) {
            $this->ConsumerKey = $config->query('@twitter.Ads.ConsumerKey');
            $this->ConsumerSecret = $config->query('@twitter.Ads.ConsumerSecret');
        }
    }

	public function getUser() {
		if ($this->user !== null) {
			return $this->user;
		} elseif($this->accessToken['user_id']) {
			$this->user = $this->accessToken['user_id'];
		} else{
			$ret = json_decode($this->checkCredentials());
			$this->user = $ret->id;
		}

		return $this->user;
	}

    public function getApiResult() {
        return json_decode($this->api_result);
    }

    public function setApiResult($api_result) {
        $this->api_result = $api_result;
    }

	public static function twRedirect($twUrl) {
		echo "<script type='text/javascript'>top.location.href = '$twUrl';</script>";
		exit();
	}

	public function twCheckLogin($nextUrl = null, $needRedirectFlg = true) {

		if ($_SESSION['tw_onetime_oauth_token']) {
            //authアクセストークン取得
			$onetime_twitter = new Twitter($this->ConsumerKey, $this->ConsumerSecret
				, $_SESSION['tw_onetime_oauth_token'], $_SESSION['tw_onetime_oauth_secret']);
			$token = $onetime_twitter->getAccessToken($_GET['oauth_verifier']);

			unset( $_SESSION['tw_onetime_oauth_token'] );
			unset( $_SESSION['tw_onetime_oauth_secret'] );

            //ユーザー取得
            $twitter = new Twitter($this->ConsumerKey, $this->ConsumerSecret
                , $token['oauth_token'], $token['oauth_token_secret']);
            if($twitter->getUser()) {
                // セッションに保存してあるトークンが有効なら
                return $twitter;
            }
        }

		$twitter = new Twitter($this->ConsumerKey, $this->ConsumerSecret);

		$twitter->unsetToken();// トークンをリセットする

		if ($needRedirectFlg) {

			if ($nextUrl) {
				$next = $nextUrl;
			} else {
				$next = (((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || '443' == $_SERVER['HTTP_X_FORWARDED_PORT']) ? 'https://' : 'http://');
				$next .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			}
			$token = $twitter->getRequestToken($next);
			$_SESSION['tw_onetime_oauth_token'] = $token['oauth_token'];
			$_SESSION['tw_onetime_oauth_secret'] = $token['oauth_token_secret'];
			$loginUrl = $twitter->getAuthorizeURL($token);
			self::twRedirect($loginUrl);
		} else {
			return $twitter;
		}
	}
	
	//override
	function oAuthRequest($url, $method, $parameters, $header = false, $sendSchedule = '') {
		try{
			if($sendSchedule == '') {
				if($header) {
					$ret = parent::oAuthRequestWithHeader($url, $method, $parameters);
				} else{
					$ret = parent::oAuthRequest($url, $method, $parameters);
				}

                $this->setApiResult($ret);
				if ( $this->http_code == '200' || $this->http_code == '304' ) {
					return $ret;
				}
			}

			$try_later_codes = array( '429', '500', '502', '503', '504');
			if ( in_array( $this->http_code, $try_later_codes ) || $sendSchedule != '' ) {
				//キューに入れる
				preg_match('/^[0-9]+/', $this->token->key, $match);
//				$twitter_queue = new twitter_queue();
//				$twitter_queue->twitter_id	= $match[0];
//				$twitter_queue->consumer_key	= $this->consumer->key;
//				$twitter_queue->consumer_secret	= $this->consumer->secret;
//				$twitter_queue->oauth_access_token	= $this->token->key;
//				$twitter_queue->oauth_access_token_secret	= $this->token->secret;
//				$twitter_queue->url		= $url;
//				$twitter_queue->method		= $method;
//				$twitter_queue->parameters	= serialize($parameters);
//				$twitter_queue->header		= $header;
//				$twitter_queue->send_schedule	= $sendSchedule ? $sendSchedule : '1970-01-01 00:00:00';
//				$twitter_queue->http_code	= $this->http_code;
//				$twitter_queue->save();
			}

            //既にリツイート済みを確認する
            $result = json_decode($ret);
            if (in_array($result->errors[0]->code, $this->skipping_error_codes)) return $ret;

            // TwitterAPIのコール失敗した際のログ
			$msg = 'request:' . "\n";
			$msg .= 'url='. $url . "\n";
			$msg .= 'method=' . $method . "\n";
			$msg .= 'header=' . $header . "\n";
			$msg .= 'parameters=' . var_export($parameters, true) . "\n";
			$msg .= 'token=' . $this->token . "\n";
			$msg .= 'response:' . "\n";
			$msg .= 'http_code:' . $this->http_code . "\n";
			$msg .= 'result:' . var_export($ret, true) . "\n";
			$this->logger->error($msg);

            if($result->errors[0]->code == 226) {
                $this->hipchat_logger->error($msg);
            }

			return false;
		}catch(Exception $e){
            $this->logger->error($e);
        }
			
	}

	public function getAccessToken($oauth_verifier = false) {
		try{
			if(!$this->accessToken['oauth_token']) {
				$this->accessToken = parent::getAccessToken($oauth_verifier);
			}
			return $this->accessToken;
		}catch(Exception $e){
            $this->logger->error($e);
        }
	}

	//フォロー
	public function postFollow($screen_name) {
		try{
			if(!$screen_name) return false;

			return $this->oAuthRequest("https://api.twitter.com/1.1/friendships/create.json","POST",array("screen_name"=>$screen_name));
		}catch(Exception $e){
            $this->logger->error($e);
            $this->hipchat_logger->error('api_execute_twitter_follow_action screen_name = ' . $screen_name . ' @Exception: ' . $e);
        }
	}

	//つぶやく
	public function postTweet($status) {
		try{
			if(!$status) return false;
			
			$ret = $this->oAuthRequest("https://api.twitter.com/1.1/statuses/update.json","POST",array("status"=>$status));
			if(!$ret && mb_strlen($status, 'utf8') > 140) {
				$status = mb_substr($status, 0, 139, 'utf-8');
				//同じ文字列をTweetするとAPIが動かないので空白を足す
				$status = ' '.$status;
				$ret = $this->postTweet($status);
			}

			return $ret;
		}catch(Exception $e){
            $this->logger->error($e);
        }
	}
    public function uploadMedia($image_url) {
        try {
            $ret = $this->oAuthRequest("https://upload.twitter.com/1.1/media/upload.json", "POST", array('media' => base64_encode(file_get_contents($image_url))));
            return $ret;
        } catch (Exception $e) {
            $this->logger->error($e);
        }
    }

	//つぶやく（画像付き）
	/*
	 * $status:tweet-text
	 * $image:file_path
	 */
	public function postTweetWithMedia($status, $media_ids) {
        if (!$status && empty($media_ids)) return false;
		try{
			if (empty($media_ids)) {
                return $this->oAuthRequest("https://api.twitter.com/1.1/statuses/update.json","POST",array("status"=>$status));
            } else {
                return $this->oAuthRequest("https://api.twitter.com/1.1/statuses/update.json","POST",array("status"=>$status,"media_ids"=>implode(',', $media_ids)));
            }
		}catch(Exception $e){
            $this->logger->error($e);
        }
	}

	//ダイレクトメッセージ
	public function postDirectMessage($text, $screen_name) {
		try{
			if(!$text || !$screen_name) return false;

			return $this->oAuthRequest("https://api.twitter.com/1.1/direct_messages/new.json","POST",array("text"=>$text,"screen_name"=>$screen_name), true);
		}catch(Exception $e){
            $this->logger->error($e);
        }
	}

	//リツイート
	public function postRetweet($id) {
		try{
			if(!$id) return false;
			return $this->oAuthRequest("https://api.twitter.com/1.1/statuses/retweet/".$id.".json","POST",null);
		}catch(Exception $e){
            $this->logger->error($e);
        }
	}

	//プロフィール
	public function setProfile($screen_name = null) {
		try{
			if(!$screen_name) $screen_name = self::$accessToken['screen_name'];
			if(!$screen_name) return false;

			//bigger - 73px by 73px, normal - 48px by 48px, mini - 24px by 24px
			$obj = json_decode($this->oAuthRequest("https://api.twitter.com/1.1/users/show.json?screen_name=".$screen_name,"GET",array()), true);

			$this->screen_name       = $screen_name;
			$this->name              = $obj['name'];
			$this->description       = $obj['description'];
			$this->friend_cnt        = $obj['friends_count'];
			$this->tweet_cnt         = $obj['statuses_count'];
			$this->follower_cnt      = $obj['followers_count'];
			$this->favorite_cnt      = $obj['favourites_count'];
			$this->retweet_count     = $obj['status']['retweet_count'];
			$this->profile_image_url = $obj['profile_image_url'];
			$this->profile_image_url_https = $obj['profile_image_url_https'];
			return true;
		}catch(Exception $e){
            $this->logger->error($e);
        }
	}
	
        public function __set( $key, $value ) {
            $this->$key = $value;
        }
        
        public function __get( $key ) {
            return $this->$key;
        }

	//プロフィール画像(API不要)
	public function getProfileImgUrl($screen_name = null) {
		try{
			if(!$screen_name) $screen_name = self::$accessToken['screen_name'];
			if(!$screen_name) return false;

			//bigger - 73px by 73px, normal - 48px by 48px, mini - 24px by 24px
			$req = $this->oAuthRequest("https://api.twitter.com/1.1/users/show.json?screen_name=".$screen_name,"GET",array());
			return json_decode($req);
		}catch(Exception $e){
            $this->logger->error($e);
        }
	}

	//タイムライン取得
	public function getTimeline($screen_name) {
		try{
			if(!$screen_name) return false;

			$req = $this->oAuthRequest("https://api.twitter.com/1.1/statuses/user_timeline.json","GET",array("screen_name"=>$screen_name));
			return json_decode($req);
		}catch(Exception $e){}
	}

	//フォロー一覧
	public function getFollowList($screen_name = null) {
		try{
			$req = $this->oAuthRequest("https://api.twitter.com/1.1/friends/ids.json","GET",array("screen_name"=>$screen_name));
			return json_decode($req);
		}catch(Exception $e){
            $this->logger->error($e);
        }
	}

    //ツイート内容
    public function getTweetContent($tweet_id = null) {
        if ($tweet_id == null) return null;
        try {
            $req = $this->oAuthRequest("https://api.twitter.com/1.1/statuses/show.json", "GET", array("id" => $tweet_id));
            return json_decode($req);
        } catch (Exception $e) {
            $this->logger->error($e);
        }
    }

    /**
     * 指定した2人のユーザーの関連情報を取得
     *
     * @param $source_id
     * @param $target_id
     * @return mixed
     */
    public function getFriendshipsShow($source_id, $target_id) {
        try{
            $req = $this->oAuthRequest(
                'https://api.twitter.com/1.1/friendships/show.json', 'GET', array(
                    'source_id' => $source_id,
                    'target_id' => $target_id,
            ));
            return json_decode($req);
        } catch (Exception $e) {
        }
    }

	/**
	 * リプライ一覧
	 * 注意事項：非公開アカウントからのメンションは件数にカウントされるものの空の配列が返ってくる
	 * @param $count
	 * @return bool|mixed
	 */
	public function getReply($count) {
		try{
			if(!$count) return false;

			$req = $this->oAuthRequest("https://api.twitter.com/1.1/statuses/mentions_timeline.json","GET",array("count"=>$count));
			return json_decode($req);
		}catch(Exception $e){
            $this->logger->error($e);
        }
	}

	//Twitter oEmbed
	public function getOEmbed($atts) {
		try{
			if(!$atts['id'] && !$atts['url']) return false;
			if(!$atts['lang']) $atts['lang'] = 'ja';

			$req = $this->oAuthRequest("https://api.twitter.com/1.1/statuses/oembed.json","GET",$atts);
			return json_decode($req);
		}catch(Exception $e){
            $this->logger->error($e);
        }
	}
	
	//API実行可能回数
	public function getRateLimitStatus() {
		try{
			$req = $this->oAuthRequest("https://api.twitter.com/1.1/application/rate_limit_status.json","GET",array());
			return json_decode($req);
		}catch(Exception $e){
            $this->logger->error($e);
        }
	}

	//API実行可能回数
	public function getSearch($que) {
		try{
			if(!$que) return false;

			$conditions = array();

			$conditions['q'] = urlencode($que);
			$conditions['result_type'] = 'recent';
			$conditions['count'] = 100;

			$req = $this->oAuthRequest("https://api.twitter.com/1.1/search/tweets.json","GET",$conditions);
			return json_decode($req);
		}catch(Exception $e){
            $this->logger->error($e);
        }
	}
	
	//check access token is expired
	public function checkCredentials() {
		try{
			return $this->oAuthRequest("https://api.twitter.com/1.1/account/verify_credentials.json","GET",array('skip_status' => 't'));

		}catch(Exception $e){
            $this->logger->error($e);
        }
	}

	//API実行可能回数
	public function destroyStatus($id) {
		try{
			if(!$id) return false;

			return $this->oAuthRequest("https://api.twitter.com/1.1/statuses/destroy/". $id .".json","POST",array());
		}catch(Exception $e){}
	}

	//API実行可能回数
	public function getUsersByUserIds($args) {
		try{
			if($args['screen_name'] && !$args['user_id']) return false;
			$req = $this->oAuthRequest("https://api.twitter.com/1.1/users/lookup.json?". http_build_query($args) ,"GET",array());
			return json_decode($req);
		}catch(Exception $e){
            $this->logger->error($e);
        }
	}

	private function unsetToken() {
		unset($this->token);
	}
	
}
