<?php

class aafwPageGenerator
{
    public static function showHelp()
    { ?>
        php AAFW.php artisan page:create page_name
        php AAFW.php artisan page:create iframe:page_name
        php AAFW.php artisan page:remove page_name
    <?php }

    /**
     * 短い名前です
     **/
    public static function getShortName()
    {
        return 'page';
    }

    /**
     * @param $argv
     * @param string $method
     */
    public static function doService ( $argv , $method = aafwCommandLineTool::METHOD_CREATE ){
        if ($argv[0] == "manager") {
            self::createManagerPage($argv);
        } else {
            if ($method == aafwCommandLineTool::METHOD_CREATE) {
                self::createUserPage($argv);
            } elseif ($method == aafwCommandLineTool::METHOD_REMOVE) {
                self::removeUserPage($argv);
            }
        }
    }

    static function createManagerPage($argv) {

    }

    static function createUserPage($argv) {
        $class_name = "";
        $path = "user/brandco";

        $action_fname = AAFW::$AAFW_ROOT . '/actions/' . $path;
        $view_fname = AAFW::$AAFW_ROOT . '/views/' . $path;

        $end = end($argv);
        $is_iframe = false;
        if (preg_match('/iframe:/', $end)){
            $end = explode(':', $end)[1];
            $is_iframe = true;
        }

        foreach ( $argv as $arg ) {
            if ($arg == $end || $arg == "iframe:" . $end) {
                $class_name = $end;
                break;
            }
            if ( !is_dir ( $action_fname . '/' . $arg) || !is_dir ( $view_fname . '/' . $arg)) {
                //confirm to create new folder
                print "フォルダー存在しません。\n";
                exit();
            }

            $action_fname = $action_fname . '/' . $arg;
            $view_fname = $view_fname . '/' . $arg;

            $path = $path . '/' . $arg;
        }

        $action_fname = $action_fname . '/' . $class_name . '.php';
        $view_fname = $view_fname . '/' . $class_name . '.php';

        if ( !is_file ( $action_fname ) && !is_file ( $view_fname )) {
            file_put_contents ( $action_fname, self::createActionClass ( $class_name, $path . '/' . $class_name . '.php' ) );
            file_put_contents ( $view_fname, self::createViewClass ( $is_iframe) );
        } else {
            print $class_name . 'ファイル名存在しています。' . "\n";
            exit();
        }
    }

    static function createActionClass ( $class_name , $path) {
        ob_start () ?>
<?php print '<?php' . "\n" ?>
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class <?php echo $class_name ?> extends BrandcoGETActionBase {
    protected $ContainerName = '<?php echo $class_name ?>';

    public function validate () {

    return true;
    }

    function doAction() {

    return '<?php echo $path ?>';
    }
}
        <?php return ob_get_clean ();
    }

    static function createViewClass ($is_iframe) {
        if (!$is_iframe) {
            $html = '<?php write_html(aafwWidgets::getInstance()->loadWidget("BrandcoHeader")->render($data["pageStatus"])) ?>';
            $html .= "\n" . '<?php write_html(aafwWidgets::getInstance()->loadWidget("BrandcoAccountHeader")->render($data["pageStatus"])) ?>';
            $html .= "\n" . '<article>' . "\n";
            $html .= "\n" . '</article>';
            $html .= "\n" . '<?php write_html($this->parseTemplate("BrandcoFooter.php", $data["pageStatus"])); ?>';
        } else {
            $html = '<?php write_html($this->parseTemplate("BrandcoModalHeader.php", array("brand" => $data["brand"]))) ?>';
            $html .= "\n" . '<article class="modalInner-large">' . "\n";
            $html .= "\n" . '</article>' . "\n" . '<?php write_html($this->parseTemplate("BrandcoModalFooter.php")) ?>';
        }
ob_start () ?>
<?php print $html ?>
<?php return ob_get_clean ();
    }

    static function removeUserPage($argv) {
        $action_fname = AAFW::$AAFW_ROOT . '/actions/user/brandco';
        $view_fname = AAFW::$AAFW_ROOT . '/views/user/brandco';
        foreach ( $argv as $arg ) {
            $action_fname = $action_fname . '/' . $arg;
            $view_fname = $view_fname . '/' . $arg;
        }
        $action_fname .= '.php';
        $view_fname .= '.php';
        if (is_file($action_fname) && is_file($view_fname)) {
            unlink($action_fname);
            unlink($view_fname);
        } else {
            echo $arg . '存在していません。';
            exit ();
        }
    }
}