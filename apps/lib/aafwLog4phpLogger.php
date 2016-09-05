<?php

include(dirname(__FILE__) . '/../vendor/log4php/Logger.php');
include(dirname(__FILE__) . '/../vendor/log4php/LoggerAutoloader.php');

$settings = aafwApplicationConfig::getInstance();
$config_file = DOC_CONFIG . DIRECTORY_SEPARATOR . $settings->Log4php['configFileName'];
Logger::configure($config_file);

class aafwLog4phpLogger {

	const LOGGER_TYPE_DEFAULT   = 'default';
    const LOGGER_TYPE_SQL       = 'sql';
    const LOGGER_TYPE_CV        = 'cv';
    const LOGGER_TYPE_HIPCHAT   = 'hipchat';

	public static function getLogger($name) {
		return Logger::getLogger($name);
	}

	public static function getDefaultLogger() {
		return self::getLogger(self::LOGGER_TYPE_DEFAULT);
	}

	public static function getSQLLogger() {
		return self::getLogger(self::LOGGER_TYPE_SQL);
	}

    public static function getCVLogger() {
        return self::getLogger(self::LOGGER_TYPE_CV);
    }

    public static function getHipChatLogger() {
        return self::getLogger(self::LOGGER_TYPE_HIPCHAT);
    }
}
