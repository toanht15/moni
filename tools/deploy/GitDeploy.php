<?php

class GitDeploy {
	/**
	 * リビジョン
	 */
	public $revision;

	/**
	 * デプロイ先
	 */
	public $mode;

	/**
	 * 対象ブランチ名
	 */
	public $branch;

	/**
	 * リリース用定義ファイル
	 * e.g. define.php
	 */
	public $define;

	/**
	 * ローカルディレクトリパス
	 */
	public $localDirectory;

	/**
	 * git repository
	 */
	public $repository;

	/**
	 * in define.php
	 */
	public $deploy_def;

	/**
	 * in define.php
	 */
	public $rename_file_list;

    /**
     * in define.php
     */
    public $migrate_cmd;

    public $migrate_rollback_cmd;

    public $composer_path;

    public $laravel_home;

    public $dump_autoload_cmd;

    public $gulp_command_list;

    public $whenever_def;

	/**
	 * in define.php
	 */
	public $exclude_file_list;

	/**
	 * array
	 * in define.php
	 */
	public $destinations;

    public $migrate_targets = array();

    public $update_crontab_targets = array();

	/**
	 * usageを表示
	 */
	public function showUsage() {
		$usage = <<<EOF

  usage:    php deploy.php -m デプロイモード -b ブランチ名 -d 定義ファイル

    -m    デプロイモード  product, staging, stg1, checking など. モードの指定は必須です
    -b    ブランチ名      master, release/HOGE など. 指定しない場合はmasterになります
    -r    ハッシュ名      特定のリビジョンをリリースする時に利用する. -bより優先されます
    -d    定義ファイル    define.php,指定しない場合はdefine.phpになります


EOF;

		echo $usage;
	}

	/**
	 * pullコマンドを実行
	 */
	public function pullBranch() {
		if ($this->revision) return null;

		$command = 'git pull';
		return $this->execute($command);
	}

	/**
	 * 引数を取得する
	 */
	public function setArgument($args) {
		// オプションが指定されていなかったりファイルが存在しなければエラー
		if (!isset($args['m']) || (isset($args['d']) && !file_exists($args['d']))) {
			return false;
		}

		$this->mode = $args['m'];
		$this->revision = isset($args['r']) ? $args['r'] : null;
		$this->branch = isset($args['b']) ? $args['b'] : 'master';
		if ($this->revision) {
			$this->branch = $this->revision;
		}
		$this->define = isset($args['d']) ? $args['d'] : 'define.php';
		return true;
	}

	/**
	 * Linuxコマンドを実行する
	 * @param $command String コマンド
	 * @param bool $throw Exceptionを投げるフラグ
	 * @throws Exception
	 * @return array
	 */
	public function execute($command, $throw = true) {
		echo("rsync command=" . $command);
		// echo $command . "\n";
		// $status = 0;
		// $output = '';
		$output = system($command, $status);

		// エラーがあった場合はExceptionを投げて止める
		if ($throw && $status !== 0) {
			throw new Exception();
		}
		return $output;
	}

	/**
	 * 定義ファイルを読み込み
	 */
	public function setDefineParams() {
		require_once($this->define);

		$this->deploy_def = Define::$deploy_def;
		if (!isset($this->deploy_def[$this->mode])) {
			return false;
		}

		$this->rename_file_list = Define::$rename_file_list;
		$this->exclude_file_list = Define::$exclude_file_list;
        $this->migrate_cmd = Define::$migrate_cmd;
        $this->migrate_rollback_cmd = Define::$migrate_rollback_cmd;
        $this->laravel_home = Define::$laravel_home;
        $this->composer_path = Define::$composer_path;
        $this->dump_autoload_cmd = Define::$dump_autoload_cmd;
        $this->gulp_command_list = Define::$gulp_command_list;
        $this->whenever_def = Define::$whenever_def;
		$this->localDirectory = dirname(__FILE__) . '/' . $this->deploy_def[$this->mode]['local_dir'];
		$this->repository = $this->deploy_def[$this->mode]['repository'];
		$this->destinations = $this->deploy_def[$this->mode]['destinations'];
        $this->migrate_targets = Define::$migrate_targets;
        $this->update_crontab_targets = Define::$update_crontab_targets;
		return true;
	}

	/**
	 * 設定ファイル群をリネーム
	 * dir/file.%モード% → dir/file
	 * @param $renameFileList array define.php -> rename_file_list
	 * @return bool
	 */
	public function renameFile($renameFileList) {
		foreach ($renameFileList as $file) {
			$full_path = $this->localDirectory . $file;

			$addFileMode = $this->mode;
			switch($this->mode){
				case 'checking':
					$addFileMode = 'product';
					break;
				case 'lbc':
					$addFileMode = 'product';
					break;
			}

			if (!file_exists($full_path . '.' . $addFileMode)) continue;
			$command = 'cp -f ' . $full_path . '.' . $addFileMode . ' ' . $full_path;

			$this->execute($command, false);
		}
		return true;
	}

    /**
     * db migrate
     */
    public function migrate() {
        $full_path_command = 'php ' . $this->localDirectory . $this->migrate_cmd;
        $this->execute($full_path_command);
    }

    /**
     * db migrate
     */
    public function migrate_rollback() {
        $full_path_command = 'php ' . $this->localDirectory . $this->migrate_rollback_cmd;
        $this->execute($full_path_command);
    }

    /**
     * composer.phar dump-autload
     */
    public function dump_autoload() {
        $full_path_command = 'php ' . $this->localDirectory . $this->composer_path . ' -d=' . $this->localDirectory . $this->laravel_home . ' ' . $this->dump_autoload_cmd;
        $this->execute($full_path_command);
    }

    /**
     * gulp
     */
    public function gulp() {
        foreach($this->gulp_command_list as $gulp_command) {
            $this->execute($gulp_command);
        }
    }

	/**
	 * rsyncに使うexcludeのコマンドオプションを生成
	 */
	public function makeExcludeOption($excludeFileList) {
		if (!is_array($excludeFileList) || !count($excludeFileList)) {
			return '';
		}

		$exclude = '';
		foreach ($excludeFileList as $file) {
			$exclude .= '--exclude \'' . $file . '\' ';
		}
		return $exclude;
	}

	/**
	 * rsyncコマンドを生成
	 * @param $exclude String 除外パターン
	 * @return bool
	 */
	public function rsync($exclude) {
		foreach ($this->destinations as $destination) {
			echo 'deploy to ' . $destination['host'] . "\n";
			$command = 'rsync -arzv --delete ' . $exclude;
			if ($destination['host']) {
				if ($destination['key'] == '') {
					$command .= "-e 'ssh -l " . $destination['user'] . "' ";
				} else {
					$command .= "-e 'ssh -l " . $destination['user'] . " -i " . $destination['key'] . "' ";
				}
			}

			if ($destination['health_check_dest_dir']) {
				$user = $destination['user'] ? $destination['user'] . '@' : '';
				$user_and_host = $user . $destination['host'] ? $destination['host'] . ':' : '';
				$health_check_command = $command . $this->localDirectory . 'docroot_health/ ' . $user_and_host . $destination['health_check_dest_dir'];
				$this->execute($health_check_command);
			}

			$command .= $this->localDirectory . ' ';
			$command .= $destination['user'] ? $destination['user'] . '@' : '';
			$command .= $destination['host'] ? $destination['host'] . ':' : '';
			$command .= $destination['dest_dir'];
			$this->execute($command);
		}
		return true;
	}

	/**
	 * 指定したブランチにチェックアウトする
	 * @param String ブランチ名
	 * @return array
	 */
	public function checkoutBranch($branch) {
		$this->updateRemote();

		$command = 'git checkout --force ' . $branch;
		return $this->execute($command);
	}

	/**
	 * リモートブランチリストをアップデートする
	 */
	public function updateRemote() {
		return $this->execute('git remote update');
	}

    public function updateCrontab() {
        foreach($this->whenever_def[$this->mode] as $dest){
            $this->execute('ssh ' . $dest . ' ~/.rbenv/shims/whenever -i -f /var/www/html/brandco/config/schedule.rb');
        }
    }

    public function isMigrationTarget() {
        if (!$this->mode) return false;
        return in_array($this->mode, $this->migrate_targets);
    }

    public function isUpdateCrontabTarget() {
        if (!$this->mode) return false;
        return in_array($this->mode, $this->update_crontab_targets);
    }

}
