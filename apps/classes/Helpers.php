<?php
/**
 * Created by IntelliJ IDEA.
 * User: kanebako
 * Date: 2014/05/28
 * Time: 午後5:27
 *
 * 使用頻度の高い関数のWrapper
 */

/**
 * Get config on yml. default app.yml
 * @param $value @ymlFilename.foo.bar or foo.bar
 * @return mixed
 */
function config($value) {
    return aafwApplicationConfig::getInstance()->query($value);
}