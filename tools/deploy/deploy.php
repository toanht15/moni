#!/usr/bin/php
<?php
/**
 * deploy.php mode path_to_define.php
 */
$cmd = 'ps ax | grep "' . basename(__FILE__) . '" | grep -v grep | wc -l';
$ret = shell_exec($cmd);
//同時起動防止
if ($ret > 2) {
    exit('多重プロセス警告\n');
}

// データベースの実行状況の確認。
// 10秒以上時間のかかっているクエリが存在する場合、強制的にデプロイを停止します。
$db = null;
$threshold_exceeded_queries = array();
const MAX_QUERY_TIME_THRESHOLD = 10;
try {
    $config = require_once dirname(__FILE__) . '/laravel/app/config/database.php';
    $mysql_config = $config['connections']['mysql'];
    echo 'Database config: ' . json_encode($mysql_config, JSON_PRETTY_PRINT);
    $db = mysql_connect($mysql_config['host'], $mysql_config['username'], $mysql_config['password'], true);
    mysql_select_db($mysql_config['database'], $db);
    mysql_query("use ps_helper", $db);
    $processlist = mysql_query("SELECT * FROM processlist WHERE command = 'Query' AND time > " . MAX_QUERY_TIME_THRESHOLD, $db);
    if ($processlist === false) {
        throw new Exception("クエリの実行に失敗しました!: SELECT * FROM processlist WHERE command = 'Query' AND time > " + MAX_QUERY_TIME_THRESHOLD);
    }
    while ($row = mysql_fetch_array($processlist)) {
        $threshold_exceeded_queries[] = $row;
    }
} catch (Exception $ex) {
  echo $ex->getMessage();
  exit(1);
} finally {
    if ($db !== null) {
        mysql_close($db);
    }
}
if (count($threshold_exceeded_queries) > 0) {
    echo('実行に時間のかかっているクエリが存在します。' . json_encode($threshold_exceeded_queries, JSON_PRETTY_PRINT));
    exit(1);
}
echo("Database check succeeded.");

require_once('GitDeploy.php');

$deploy = new GitDeploy();

$hasArg = $deploy->setArgument(getopt('d:b:m:r:'));
if (!$hasArg) {
    $deploy->showUsage();
    exit();
}

if (!$deploy->setDefineParams()) {
    exit("不正なモードです\n");
}

if (!$deploy->deploy_def[$deploy->mode]['repository']) {
    exit("リポジトリが定義されていません\n");
}

chdir($deploy->localDirectory);

try {

    // デプロイ用ディレクトリをリリースするブランチで最新化する
    $deploy->checkoutBranch($deploy->branch);

    $deploy->pullBranch($deploy->branch);

    // gulp実行
    $deploy->gulp();

    // cd __FILE__
    chdir(dirname(__FILE__));

    // 設定ファイル群を反映
    $deploy->renameFile($deploy->rename_file_list);

    if ($deploy->isMigrationTarget()) {
        // dump-autoload
        $deploy->dump_autoload();
        // DB migration
        $deploy->migrate();
    }

    // rsync
    $deploy->rsync($deploy->makeExcludeOption($deploy->exclude_file_list));

    if ($deploy->isUpdateCrontabTarget()) {
        $deploy->updateCrontab();
    }

}catch(Exception $e){
    var_dump($e);
    exit(1);
}

