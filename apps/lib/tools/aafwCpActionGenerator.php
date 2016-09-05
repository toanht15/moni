<?php

class aafwCpActionGenerator
{
    public static function showHelp()
    { ?>
        php AAFW.php artisan cpaction:create action_name
        php AAFW.php artisan cpaction:remove action_name
    <?php }

    /**
     * 短い名前です
     **/
    public static function getShortName()
    {
        return 'cpaction';
    }

    /**
     * @param $argv
     * @param string $method
     */
    public static function doService ( $argv , $method = aafwCommandLineTool::METHOD_CREATE){

        $class_name = $argv[0];
        $aafwObject = new aafwObject();
        $widget_class_name = $aafwObject->convertCamel($class_name);

        $widget_edit_class_path = AAFW::$AAFW_ROOT . '/widgets/classes/EditAction' . $widget_class_name . '.php';
        $widget_edit_template_path = AAFW::$AAFW_ROOT . '/widgets/templates/EditAction' . $widget_class_name . '.php';
        $widget_user_class_path = AAFW::$AAFW_ROOT . '/widgets/classes/UserMessageThreadAction' . $widget_class_name . '.php';
        $widget_user_template_path = AAFW::$AAFW_ROOT . '/widgets/templates/UserMessageThreadAction' . $widget_class_name . '.php';
        $action_manager_path = AAFW::$AAFW_ROOT . '/classes/brandco/cp/Cp' . $widget_class_name . 'ActionManager.php';
        $save_action_path = AAFW::$AAFW_ROOT . '/actions/user/brandco/admin-cp/save_action_' . $class_name . '.php';
        $script_path = AAFW::$AAFW_ROOT . '/../docroot_static/js/brandco/services/user/UserAction' . $widget_class_name . 'Service.js';

        if ($method == aafwCommandLineTool::METHOD_CREATE) {
            if (is_file($widget_edit_class_path)
            || is_file($widget_edit_template_path)
            || is_file($widget_user_class_path)
            || is_file($widget_user_template_path)
            || is_file($action_manager_path)
            || is_file($save_action_path)
            || is_file($script_path)) {
                echo $class_name . '存在しています。';
                return;
            }
            file_put_contents ($widget_edit_class_path, self::createWidgetClass('EditAction' . $widget_class_name));
            file_put_contents ($widget_edit_template_path, '');
            file_put_contents ($widget_user_class_path, self::createWidgetClass('UserMessageThreadAction' . $widget_class_name));
            file_put_contents ($widget_user_template_path, '<?php write_html($this->scriptTag("user/UserAction' . $widget_class_name . 'Service")); ?>');
            file_put_contents ($action_manager_path, self::createActionManager($widget_class_name));
            file_put_contents ($save_action_path, self::createSaveAction($class_name, $widget_class_name));
            file_put_contents ($script_path, '');

        } elseif ($method == aafwCommandLineTool::METHOD_REMOVE) {
            if (!is_file($widget_edit_class_path)
                || !is_file($widget_edit_template_path)
                || !is_file($widget_user_class_path)
                || !is_file($widget_user_template_path)
                || !is_file($action_manager_path)
                || !is_file($save_action_path)
                || !is_file($script_path)) {
                echo $class_name . '存在していません。';
                return;
            }
            unlink($widget_edit_class_path);
            unlink($widget_edit_template_path);
            unlink($widget_user_class_path);
            unlink($widget_user_template_path);
            unlink($action_manager_path);
            unlink($save_action_path);
            unlink($script_path);
        }
    }

    static function createWidgetClass($class_name) {
ob_start () ?>
<?php print '<?php' . "\n" ?>
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class <?php echo $class_name ?> extends aafwWidgetBase{

    public function doService( $params = array() ){

        return $params;
    }
}
<?php return ob_get_clean ();
    }

    static function createActionManager($class_name) {
ob_start () ?>
<?php print '<?php' . "\n" ?>
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

/**
* Class Cp<?php print $class_name ?>ActionManager
*/
class Cp<?php print $class_name ?>ActionManager extends aafwObject implements CpActionManager {
    use CpActionTrait;

}
<?php return ob_get_clean ();
    }

    static function createSaveAction($class_name, $widget_class_name) {
ob_start () ?>
<?php print '<?php' . "\n" ?>
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.Cp<?php print $widget_class_name ?>ActionManager');

class save_action_<?php print $class_name ?> extends BrandcoPOSTActionBase {
    protected $ContainerName = 'save_action_<?php print $class_name ?>';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}',
    );

    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
    );

    public function validate() {

        return true;
    }

    function doAction() {

        $this->Data['saved'] = 1;

        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
        $this->POST['callback'] = $this->POST['callback'].'?mid=action-saved';
        } else {
        $this->POST['callback'] = $this->POST['callback'].'?mid=action-draft';
        }

        $return = 'redirect: ' . $this->POST['callback'];

        return $return;
    }
}
<?php return ob_get_clean ();
    }
}