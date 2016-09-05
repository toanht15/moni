<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

/**
 * キャンペーン開始、終了時メールを送信する
 * Class CpStatusAlertDelivery
 */
class CpStatusAlertDelivery extends BrandcoBatchBase {

    const MAIL_TEMPLATE_NAME = 'cp_status_alert';
    const ALLIED_TABLEAU_URL = 'https://10ay.online.tableau.com/#/site/allied/views';

    public function executeProcess() {
        $target_cps = $this->getTargetCps();

        //メール送信失敗のキャンペーン
        $send_mail_failed_cp_ids = array();

        foreach ($target_cps as $cp) {
            if (!$this->sendMail($cp)) {
                $send_mail_failed_cp_ids[] = $cp->id;
            }
        }

        if(count($send_mail_failed_cp_ids)) {
            $this->hipchat_logger->error('ERROR: CpStatusAlertDelivery send mail failed! cp_id = '.implode(',',$send_mail_failed_cp_ids));
        }
    }

    /**
     * メール送信の対象キャンペーンを取得する
     * 1日間以内の公開・終了のキャンペーン
     * @return mixed
     */
    private function getTargetCps() {
        //1日間以内の開催した(公開・終了)キャンペーンを取得する
        $begin_date = date('Y-m-d H:i:s', strtotime('-1 days'));

        $conditions = array(
            'start_date'  => $begin_date,
            'end_date'    => $begin_date
        );

        $order = array(
            'name'      => 'updated_at',
            'direction' => 'asc'
        );

        $db = aafwDataBuilder::newBuilder();

        return $db->getRecentPublicOrEndCampaigns($conditions, $order, array(), false, 'Cp');
    }

    /**
     * メールを作成し、送信する
     * @param $cp
     * @return bool
     */
    private function sendMail ($cp) {
        /** @var BrandService $brand_service */
        $brand_service = $this->service_factory->create('BrandService');
        /** @var InquiryMailService $inquiry_mail_service */
        $inquiry_mail_service = $this->service_factory->create('InquiryMailService');

        $brand = $brand_service->getBrandById($cp->brand_id);

        try{
            //ブランドの営業担当者・運用担当者を取得する
            $sales_manager = $inquiry_mail_service->getSalesManager($brand->id);
            $consultant_manager = $inquiry_mail_service->getConsultantsManager($brand->id);

            //メールの送信対象を取得する
            $to_addresses = array();

            if($sales_manager && $sales_manager->mail_address) {
                $to_addresses[] = $sales_manager->mail_address;
            }

            if($consultant_manager && $consultant_manager->mail_address) {
                $to_addresses[] = $consultant_manager->mail_address;
            }

            if(count($to_addresses) === 0) {
                throw new aafwException('send mail target is empty');
            }

            //Tableauレポートのメールアドレスをto_addressesに追加する
            $to_addresses[] = aafwApplicationConfig::getInstance()->query('Mail.Tableau');

            //メールのパラメーター
            $replace_params = array (
                'MAIL_TITLE'            => $cp->end_date <= date('Y-m-d H:i:s') ? "{$brand->name}様のキャンペーンが終了しました" : "{$brand->name}様がキャンペーンを開催しました",
                'CP_TITLE'              => $cp->getTitle(),
                'CP_URL'                => $cp->getUrl(),
                'CP_JOIN_LIMIT'         => $cp->join_limit_flg == Cp::JOIN_LIMIT_ON ? '限定' : '公開',
                'CP_START_DATE'         => $cp->start_date,
                'CP_END_DATE'           => $cp->end_date,
                'TABLEAU_REPORT_URL'    => $this->buildTableauReportUrl($cp->id)
            );

            $inquiry_mail_service->send(array_unique($to_addresses),self::MAIL_TEMPLATE_NAME, $replace_params);
        } catch (Exception $e) {
            $this->logger->error('CpStatusAlertDelivery#sendMail send mail failed! cp_id = '.$cp->id);
            $this->logger->error($e);
            return false;
        }

        return true;
    }

    /**
     * TableauレポートURLを作成する
     * @param $cp_id
     * @return string
     */
    private function buildTableauReportUrl ($cp_id) {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->service_factory->create('CpFlowService');
        $questionnaire_actions = $cp_flow_service->getCpActionsByCpIdAndActionType($cp_id, CpAction::TYPE_QUESTIONNAIRE);
        $facebook_like_actions = $cp_flow_service->getCpActionsByCpIdAndActionType($cp_id, CpAction::TYPE_FACEBOOK_LIKE);
        $twitter_follow_actions = $cp_flow_service->getCpActionsByCpIdAndActionType($cp_id, CpAction::TYPE_TWITTER_FOLLOW);

        $summary_url = self::ALLIED_TABLEAU_URL. '/_8/sheet0?id='.$cp_id;

        $tableau_report_url = <<<EOS
サマリー:
{$summary_url}
※Tableauレポートの描画までは2〜3日のタイムラグがあります


EOS;
        //アンケートモジュールを含む場合、アンケートレポートURLを追加する
        if (count($questionnaire_actions)) {
            $questionnaire_report_urls = '';

            foreach ($questionnaire_actions as $action) {
                $questionnaire_report_urls .= self::ALLIED_TABLEAU_URL.'/qtn_cp_action_id_'.$action->id.'/1'. PHP_EOL;
            }

            $tableau_report_url .= <<<EOS
アンケート:
{$questionnaire_report_urls}

EOS;
        }

        //Facebookいいねモジュールを含む場合、FacebookいいねレポートURLを追加する
        if (count($facebook_like_actions)) {
            $facebook_like_report_urls = '';

            foreach ($facebook_like_actions as $action) {
                $facebook_like_report_urls .= self::ALLIED_TABLEAU_URL.'/_8/Facebook?cp_action_id='.$action->id. PHP_EOL;
            }

            $tableau_report_url .= <<<EOS
Facebookいいね:
{$facebook_like_report_urls}

EOS;
        }

        //Twitterフォローモジュールを含む場合は、Twitterフォローレポートを追加する
        if (count($twitter_follow_actions)) {
            $twitter_follow_report_urls = '';

            foreach ($twitter_follow_actions as $action) {
                $twitter_follow_report_urls .= self::ALLIED_TABLEAU_URL.'/_8/Twitter?cp_action_id='.$action->id. PHP_EOL;
            }

            $tableau_report_url .= <<<EOS
Twitterフォロー:
{$twitter_follow_report_urls}

EOS;
        }

        return $tableau_report_url;
    }
}