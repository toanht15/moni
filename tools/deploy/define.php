<?php
/*
 * デプロイ元のリポジトリとデプロイ先のサーバの定義
 */
class Define {
    public static $deploy_def = array(
        'product' => array(
            'repository' => 'git@devmgr03.aainc.local:brandco.git',
            'local_dir' => 'br/product/',
            'destinations' => array(
                array(
                    'host' => 'vpc3-bc-web01',
                    'user' => 'allied',
                    'key' => '',
                    'dest_dir' => '/var/www/html/brandco',
                    'health_check_dest_dir' => '/var/www/html/check/docroot/health/'
                ),
                array(
                    'host' => 'vpc3-bc-web02',
                    'user' => 'allied',
                    'key' => '',
                    'dest_dir' => '/var/www/html/brandco',
                    'health_check_dest_dir' => '/var/www/html/check/docroot/health/'
                ),
                array(
                    'host' => 'bc-batch01',
                    'user' => 'allied',
                    'key' => '',
                    'dest_dir' => '/var/www/html/brandco',
                ),
            ),
        ),
        'staging' => array(
            'repository' => 'git@devmgr03.aainc.local:brandco.git',
            'local_dir' => 'br/staging/',
            'destinations' => array(
                array(
                    'host' => 'vpc3-stg-bc-web01',
                    'user' => 'allied',
                    'key' => '',
                    'dest_dir' => '/var/www/html/brandco',
                ),
                array(
                    'host' => 'bc-stg-db03',
                    'user' => 'allied',
                    'key' => '',
                    'dest_dir' => '/var/www/html/brandco',
                )
            ),
        ),
        'stg1' => array(
            'repository' => 'git@devmgr03.aainc.local:brandco.git',
            'local_dir' => 'br/stg1/',
            'destinations' => array(
                array(
                    'host' => 'vpc3-stg-bc-web01',
                    'user' => 'allied',
                    'key' => '',
                    'dest_dir' => '/var/www/html/brandco1',
                ),
                array(
                    'host' => 'bc-stg-db03',
                    'user' => 'allied',
                    'key' => '',
                    'dest_dir' => '/var/www/html/brandco1',
                )
            ),
        ),
        'dev-brandco' => array(
            'repository' => 'git@devmgr03.aainc.local:brandco.git',
            'local_dir' => 'br/dev-brandco/',
            'destinations' => array(
                array(
                    'host' => 'dev-brandco',
                    'user' => 'root',
                    'key' => '',
                    'dest_dir' => '/var/www/html/brandco',
                ),
                array(
                    'host' => 'vpc3-stg-bc-batch01',
                    'user' => 'allied',
                    'key' => '',
                    'dest_dir' => '/var/www/html/brandco',
                )
            ),
        ),
        'checking' => array(
            'repository' => 'git@devmgr03.aainc.local:brandco.git',
            'local_dir' => 'br/checking/',
            'destinations' => array(
                array(
                    'host' => 'vpc3-bc-web00',
                    'user' => 'allied',
                    'key' => '',
                    'dest_dir' => '/var/www/html/brandco',
                    'health_check_dest_dir' => '/var/www/html/check/docroot/health/',
                ),
            ),
        ),
        'verification' => array(
            'repository' => 'git@devmgr03.aainc.local:brandco.git',
            'local_dir' => 'br/verification/',
            'destinations' => array(
                array(
                    'host' => 'vpc3-bc-web00-verification',
                    'user' => 'allied',
                    'key' => '',
                    'dest_dir' => '/var/www/html/brandco',
                    'health_check_dest_dir' => '/var/www/html/check/docroot/health/',
                ),
            ),
        ),
        'lbc' => array(
            'repository' => 'git@devmgr03.aainc.local:brandco.git',
            'local_dir' => 'br/lbc/',
            'destinations' => array(
                array(
                    'host' => 'vpc3-lbc-web00',
                    'user' => 'allied',
                    'key' => '',
                    'dest_dir' => '/var/www/html/brandco',
                ),
            ),
        ),
    );

    public static $whenever_def = array(
        'staging' => array('bc-stg-db03'),
        'product' => array('bc-batch01'),
        'checking' => array(),
        'verification' => array(),
    );

    public static $migrate_targets = array('product', 'staging', 'stg1', 'dev-brandco', 'verification');

    public static $update_crontab_targets = array('product', 'staging');

    /*
     * renameするファイルを定義
     */
    public static $rename_file_list = array(
        'apps/config/app.yml',
        'apps/config/web.yml',
        'apps/config/facebook.yml',
        'apps/config/twitter.yml',
        'apps/config/google.yml',
        'apps/config/redis.yml',
        'apps/config/storage.yml',
        'apps/config/define.php',
        'apps/config/instagram.yml',
        'apps/config/gmo.yml',
        'tools/deploy/laravel/app/config/database.php',
        'apps/vendor/cores/MoniplaCoreSettings.php',
        'apps/vendor/gpay_client/src/conf/connector.properties'
    );

    /*
     * 実行するコマンドを定義
     */
    public static $migrate_cmd = 'tools/deploy/laravel/artisan migrate';

    public static $migrate_rollback_cmd = 'tools/deploy/laravel/artisan migrate:rollback';

    public static $composer_path = 'tools/deploy/composer.phar';

    public static $laravel_home = 'tools/deploy/laravel';

    public static $dump_autoload_cmd = 'dump-autoload';

    public static $gulp_command_list = array(
        'npm run-script jsConcat',
        'npm run-script jsMinify',
        'npm run-script jsGzip',
        'npm run-script cssGzip',
    );

    /*
     * rsync時に除外するファイルを定義
     */
    public static $exclude_file_list = array(
        '.idea',
        '.git',
        '.gitignore',
        'docs',
        'tools',
        'docroot_static/html/',
        'docroot_static/top/html/',
        'docroot_static/js/_admin_unit.js',
        'docroot_static/js/_unit.js',
        'docroot_static/js/_unit_sp.js',
        'node_modules'
    );

}

