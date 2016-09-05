<?php
$GLOBALS['THRIFT_AUTOLOAD'] = array();
$GLOBALS['AUTOLOAD_HOOKS'] = array();
spl_autoload_register(function ($class) {
    global $THRIFT_AUTOLOAD;
    $classl = strtolower($class);
    if (isset($THRIFT_AUTOLOAD[$classl])) {
        include_once $GLOBALS['THRIFT_ROOT'].'/packages/'.$THRIFT_AUTOLOAD[$classl];
    } else if (!empty($GLOBALS['AUTOLOAD_HOOKS'])) {
        foreach ($GLOBALS['AUTOLOAD_HOOKS'] as $hook) {
            $hook($class);
        }
    }
});
