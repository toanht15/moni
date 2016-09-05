<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class brandco_action_base_list extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;
    public $ManagerPageId    = Manager::MENU_FILE_LIST;
    private $pageLimited = 100;
    protected $p = 1;

    public function validate () {
        return true;
    }

    function doAction() {

        $list = $this->getFileList(AAFW_DIR.'/actions/user');

        $match_path = array();
        foreach($list as $path) {
            $result = array();

            $buffer = file_get_contents($path);
            if(strpos($buffer,'extends BrandcoGETActionBase')) {

                // クラス名
                $result['Class'] = preg_match('/class\s+(\w+)(.*)?\{/', $buffer, $matches) ? $result['Class'] = $matches[1] : '';

                // パス名
                $path = str_replace(AAFW_DIR.'/actions/user', '', $path);
                // /brandcoディレクトリ以外も可能性があるから念のためケア
                $result['Path'] = strpos($path, '/brandco', 0) === 0 ? $result['Path'] = str_replace('/brandco', '', $path) : '';

                // プロパティ名
                // require_onceしてproperty_existsで調べたいが、謎の落ち方をするので正規表現でチェックします。
                $result['NeedOption'] = preg_match('/\$NeedOption/', $buffer, $matches) ? $result['NeedOption'] = 'ok' : '';
                $match_path[] = $result;
            }
        }

        if($this->GET['p'] && $this->GET['p'] > 0 && $this->GET['p'] < (count($match_path) / $this->pageLimited) + 1) {
            $this->Data['p'] = $this->GET['p'];
        } else {
            $this->Data['p'] =  1;
        }

        $this->Data['data'] = $match_path;

        // ページング
        $this->Data['limit'] = $this->pageLimited;
        $this->Data['totalEntriesCount'] = count($match_path);

        return 'manager/dashboard/brandco_action_base_list.php';
    }

    function getFileList($dir) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

        $list = array();
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                $list[] = $fileinfo->getPathname();
            }
        }

        return $list;
    }
}
