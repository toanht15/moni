<?php

class aafwArtisan
{
    public static function showHelp()
    { ?>
        php AAFW.php artisan model:create table_name
        php AAFW.php artisan model:remove table_name
        php AAFW.php artisan page:create path1 page_name
        php AAFW.php artisan page:create path1 iframe:page_name
        php AAFW.php artisan page:remove path1 page_name
        php AAFW.php artisan cpaction:create action_name
        php AAFW.php artisan cpaction:remove action_name
    <?php }

    /**
     * 短い名前です
     **/
    public static function getShortName()
    {
        return 'artisan';
    }

    /**
     *
     **/
    public static function doService ( $argv ){
        $command = array_shift ( $argv );
        $action = explode(':', $command);
        switch ($action[0]) {
            case "model":
                $generator = new aafwModelGenerator();
                $generator->doService($argv, $action[1]);
                break;
            case "page":
                $generator = new aafwPageGenerator();
                $generator->doService($argv, $action[1]);
                break;
            case "cpaction":
                $generator = new aafwCpActionGenerator();
                $generator->doService($argv, $action[1]);
                break;
            default:
                self::showHelp();
                break;
        }
    }
}