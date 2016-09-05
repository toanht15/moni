<?php

class LoggerAppenderHipChat extends LoggerAppender {

    public function append(LoggerLoggingEvent $event) {
        try {
            $level = $event->getLevel();
            $hipChat = new HipChat(config('HipChat.Token'));
            $message = mb_substr($this->layout->format($event), 0, 1000);
            if($level->isGreaterOrEqual(LoggerLevel::getLevelError())) {
                $hipChat->sendRoomNotification(config('HipChat.Rooms'), $message, true, Hipchat::COLOR_RED);
            } else if ($level->isGreaterOrEqual(LoggerLevel::getLevelWarn())) {
                $hipChat->sendRoomNotification(config('HipChat.Rooms'), $message, false, Hipchat::COLOR_YELLOW);
            } else {
                $hipChat->sendRoomNotification(config('HipChat.Rooms'), $message, false, Hipchat::COLOR_GRAY);
            }
        } catch(Exception $e) {

        }
    }
}

/**
 * Library for interacting with the HipChat REST API.
 *
 * @see https://www.hipchat.com/docs/apiv2/
 */
class HipChat {
    const DEFAULT_TARGET = 'https://api.hipchat.com';

    const STATUS_BAD_RESPONSE = -1; // Not an HTTP response code
    const STATUS_OK = 200;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_UNAUTHORIZED = 401;
    const STATUS_FORBIDDEN = 403;
    const STATUS_NOT_FOUND = 404;
    const STATUS_NOT_ACCEPTABLE = 406;
    const STATUS_INTERNAL_SERVER_ERROR = 500;
    const STATUS_SERVICE_UNAVAILABLE = 503;
    /**
     * Colors for rooms/message
     */
    const COLOR_YELLOW = 'yellow';
    const COLOR_RED = 'red';
    const COLOR_GRAY = 'gray';
    const COLOR_GREEN = 'green';
    const COLOR_PURPLE = 'purple';
    const COLOR_RANDOM = 'random';
    /**
     * Formats for rooms/message
     */
    const FORMAT_HTML = 'html';
    const FORMAT_TEXT = 'text';
    /**
     * API versions
     */
    const VERSION_2 = 'v2';
    private $api_target;
    private $auth_token;
    private $verify_ssl = true;
    private $proxy;
    /**
     * Creates a new API interaction object.
     *
     * @param $auth_token string Your API token.
     * @param $api_target string API protocol and host. Change if you're using an API
     *                           proxy such as apigee.com.
     * @param $api_version string Version of API to use.
     */
    function __construct($auth_token, $api_target = self::DEFAULT_TARGET,
                         $api_version = self::VERSION_2) {
        $this->api_target = $api_target;
        $this->auth_token = $auth_token;
        $this->api_version = $api_version;
    }
    /////////////////////////////////////////////////////////////////////////////
    // Room functions
    /////////////////////////////////////////////////////////////////////////////
    /**
     * Send a message to a room
     *
     * @see http://api.hipchat.com/docs/api/method/rooms/message
     */
    public function sendRoomNotification($room_id, $message, $notify = false,
                                         $color = self::COLOR_YELLOW, $message_format = self::FORMAT_HTML) {
        $args = array(
            'color' => $color,
            'message' => $message,
            'notify' => (bool)$notify,
            'message_format' => $message_format
        );
        $response = $this->makeRequest('room/'.$room_id.'/notification', $args, 'POST');
        return $response;
    }
    /////////////////////////////////////////////////////////////////////////////
    // Helper functions
    /////////////////////////////////////////////////////////////////////////////
    /**
     * Performs a curl request
     *
     * @param $url        URL to hit.
     * @param $post_data  Data to send via POST. Leave null for GET request.
     *
     * @throws HipChat_Exception
     * @return string
     */
    public function curlRequest($url, $post_data = null) {
        if (is_array($post_data)) {
            $post_data = array_map(array($this, 'sanitize_curl_parameter'), $post_data);
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
        if (isset($this->proxy)) {
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        }
        if (is_array($post_data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($post_data)))
        );
        $response = curl_exec($ch);

        curl_close($ch);
        return $response;
    }
    /**
     * Sanitizes the given value as cURL parameter.
     *
     * The first value may not be a "@". PHP would treat this as a file upload
     *
     * @link http://www.php.net/manual/en/function.curl-setopt.php CURLOPT_POSTFIELDS
     *
     * @param string $value
     * @return string
     */
    private function sanitize_curl_parameter ($value) {
        if ((strlen($value) > 0) && ($value[0] === '@')) {
            return substr_replace($value, '&#64;', 0, 1);
        }
        return $value;
    }
    /**
     * Make an API request using curl
     *
     * @param string $api_method  Which API method to hit, like 'rooms/show'.
     * @param array  $args        Data to send.
     * @param string $http_method HTTP method (GET or POST).
     *
     * @throws HipChat_Exception
     * @return mixed
     */
    public function makeRequest($api_method, $args = array(), $http_method = 'GET') {
        $args['format'] = 'json';
        $url = "$this->api_target/$this->api_version/$api_method?auth_token=$this->auth_token";
        $post_data = null;
        // add args to url for GET
        if ($http_method == 'GET') {
            $url .= '?' . http_build_query($args);
        } else {
            $post_data = $args;
        }
        $response = $this->curlRequest($url, $post_data);
        // make sure response is valid json
        $response = json_decode($response);
        return $response;
    }
}