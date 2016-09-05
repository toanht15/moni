<?php

class ErrorConstants {

    /**
     * @var array
     */
    public static $ERROR_CODE = [

        "BASE" => [
            "UNKNOWN"               => "ERROR_BASE_0001",
            "REQUIRE_LOGIN"         => "ERROR_BASE_0002",
            "ACCESS_VIOLATION"      => "ERROR_BASE_0003",
            "BAD_REQUEST"           => "ERROR_BASE_0004",
            "USER_NOT_FOUND"        => "ERROR_BASE_0005",
            "RESOURCE_NOT_FOUND"    => "ERROR_BASE_0006",
            "ENTITY_NOT_FOUND"      => "ERROR_BASE_0007",
        ],

        "AUTH" => [
            "AUTH_FAIL"             => "ERROR_AUTH_0001",
            "AUTH_TOKEN_NOT_FOUND"  => "ERROR_AUTH_0002",
            "AUTH_TOKEN_INVALID"    => "ERROR_AUTH_0003",
            "AUTH_TOKEN_EXPIRED"    => "ERROR_AUTH_0004",
            "INVALID_USER_ID"       => "ERROR_AUTH_0005",
            "LOGIN_ERROR"           => "ERROR_AUTH_0006",
        ],

        "HTTP" => [
            "HTTP_ERROR"            => "ERROR_HTTP_0001",
            "CURL_ERROR"            => "ERROR_HTTP_0002",
        ],

        "API" => [
            "API_ERROR"             => "ERROR_API_0001",
            "API_VALIDATION_ERROR"  => "ERROR_API_0002",
        ],


        "FACEBOOK_API" => [
            "FACEBOOK_API_ERROR"    => "ERROR_FACEBOOK_API_0001",
        ],

        "TWITTER_API" => [
            "TWITTER_API_ERROR"     => "ERROR_TWITTER_API_0001",
        ],
    ];
}
