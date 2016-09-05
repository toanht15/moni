<?php

$settings = aafwApplicationConfig::getInstance();

return array(

	'rootLogger' => array(
		'level' => 'INFO',
		'appenders' => array('app'),
	),

	'loggers' => array(

		'default' => array(
			'level' => $settings->Log4php['loggers']['app']['level'],
			'appenders' => array('app'),
			'additivity' => false,
		),

		'sql' => array(
			'level' => $settings->Log4php['loggers']['sql']['level'],
			'appenders' => array('sql'),
			'additivity' => false,
		),

        'cv' => array(
            'level' => $settings->Log4php['loggers']['cv']['level'],
            'appenders' => array('cv'),
            'additivity' => false,
        ),

        'hipchat' => array(
            'level' => $settings->Log4php['loggers']['hipchat']['level'],
            'appenders' => array('hipchat'),
            'additivity' => false,
        ),
	),

	'appenders' => array(

        'app' => array(
            'class' => 'LoggerAppenderDailyFile',
            'layout' => array(
                'class' => 'LoggerLayoutPattern',
                'params' => array(
                    'conversionPattern' => '[%s{SERVER_NAME}] %d{Y-m-d H:i:s} %level %M %L %m Request:[%request] %n',
                )
            ),
            'params' => array(
                'datePattern' => 'Y-m-d',
                'append' => true,
                'file' => $settings->Log4php['appenders']['app']['name'],
            )
        ),

        'sql' => array(
			'class' => 'LoggerAppenderDailyFile',
			'layout' => array(
				'class' => 'LoggerLayoutPattern',
				'params' => array(
					'conversionPattern' => '[%s{SERVER_NAME}] %d{Y-m-d H:i:s} %level %m %n',
				)
			),
			'params' => array(
				'datePattern' => 'Y-m-d',
				'append' => true,
				'file' => $settings->Log4php['appenders']['sql']['name'],
			)
		),

        'hipchat' => array(
            'class' => 'LoggerAppenderHipChat',
            'layout' => array(
                'class' => 'LoggerLayoutPattern',
                'params' => array(
                    'conversionPattern' => '[%s{SERVER_NAME}] %d{Y-m-d H:i:s} %level %M %L %m Request:[%request] %n',
                )
            ),
            'rootLogger' => array(
                'appenders' => array('default'),
            )
        ),

        'cv' => array(
            'class' => 'LoggerAppenderDailyFile',
            'layout' => array(
                'class' => 'LoggerLayoutPattern',
                'params' => array(
                    'conversionPattern' => '[%s{SERVER_NAME}] %d{Y-m-d H:i:s} %level %M %L %m Request:[%request] %n',
                )
            ),
            'params' => array(
                'datePattern' => 'Y-m-d',
                'append' => true,
                'file' => $settings->Log4php['appenders']['cv']['name'],
            )
        ),

    )
);
