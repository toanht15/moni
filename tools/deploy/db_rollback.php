#!/usr/bin/php
<?php
/**
 * deploy.php mode path_to_define.php
 */
$cmd = 'ps ax | grep "' . basename(__FILE__) . '" | grep -v grep | wc -l';
$ret = shell_exec($cmd);
//同時起動防止
if ($ret > 1) {
    exit('多重プロセス警告\n');
}

require_once('GitDeploy.php');
$deploy = new GitDeploy();

$hasArg = $deploy->setArgument(getopt('b:m:r:'));
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

// デプロイ用ディレクトリをリリースするブランチで最新化する
$deploy->checkoutBranch($deploy->branch);
$deploy->pullBranch($deploy->branch);

// cd __FILE__
chdir(dirname(__FILE__));

// 設定ファイル群を反映
$deploy->renameFile($deploy->rename_file_list);

// dump-autoload
$deploy->dump_autoload();

$deploy->migrate_rollback();