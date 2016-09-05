<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

/**
 * リダイレクトログをダウンロードする機能
 * Class csv_redirector_log
 */
class csv_redirector_log extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    protected $ContainerName = 'csv_redirector_log';

    private $redirector;

    public function doThisFirst() {
        ini_set('max_execution_time', 3600);
    }

    public function validate() {
        if (!$this->GET['redirector_id']) {
            return false;
        }
        /** @var RedirectorService $redirector_service */
        $redirector_service = $this->getService('RedirectorService');
        $this->redirector = $redirector_service->getRedirectorById($this->GET['redirector_id']);

        if (!$this->redirector || $this->redirector->brand_id != $this->getBrand()->id) {
            return false;
        }

        return true;
    }

    public function doAction() {

        try {
            $db = new aafwDataBuilder();
            $condition = array(
                'brand_id' => $this->getBrand()->id,
                'redirector_id' => $this->redirector->id,
                '__NOFETCH__' => true
            );

            $rs = $db->getRedirectorLogByRedirectorId($condition);

            $is_empty_data = true;
            $data_csv['header'] = array('会員番号','リダイレクト日時','デバイス');

            while ($redirector_log = $db->fetch($rs)) {
                $data = array();
                $data[] = $redirector_log['relation_no'] ?: '-';
                $data[] = date('Y/m/d H:i',strtotime($redirector_log['login_date']));
                $data[] = $redirector_log['device'] == '1' ? 'PC': 'スマホ';

                $data_csv['list'][] = $data;
                $is_empty_data = false;
            }

            if ($is_empty_data) {
                return 'redirect: ' . Util::rewriteUrl('admin-settings', 'redirector_settings_form', array(), array('mid' => 'redirector-download-failed'));
            }

            // Export csv
            $csv = new CSVParser();
            $csv->setCSVFileName('redirect_log_' . $this->redirector->sign .'_'. (new DateTime())->format('YmdHis'));
            header("Content-type:" . $csv->getContentType());
            header($csv->getDisposition());

            print mb_convert_encoding($csv->out($data_csv, 1), 'Shift_JIS', "UTF-8");
            exit();

        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error('csv_redirector_log get error.' . $e);
            return 'redirect: ' . Util::rewriteUrl('admin-settings', 'redirector_settings_form', array(), array('mid' => 'redirector-download-error'));
        }
    }
}