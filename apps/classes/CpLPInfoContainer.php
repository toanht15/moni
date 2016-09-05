<?php

AAFW::import('jp.aainc.classes.CacheManager');
AAFW::import('jp.aainc.lib.container.aafwContainer');

class CpLPInfoContainer {

    const KEY_ENTRY_ACTION = 'ea';

    const KEY_ACTION_INFO = 'ai';

    const KEY_OG_INFO = 'oi';

    /** @var CacheManager cache_manager */
    private $cache_manager;

    public function __construct() {
        $this->cache_manager = new CacheManager();
    }

    public function getCpLPInfo($cp, $brand) {
        $value = $this->cache_manager->getCampaignLPInfo($cp->id);
        if (!$value) {
            /** @var  $cp_flow_service CpFlowService */
            $cp_flow_service = aafwServiceFactory::create('CpFlowService');
            $entry_action = $cp_flow_service->getFirstActionOfCp($cp->id);

            // エントリーアクション情報を取得する
            list($cp_action, $concrete_action) = $cp_flow_service->getEntryActionInfoByCpId($cp->id);
            $sns_store = aafwEntityStoreFactory::create('CpJoinLimitSnses');
            $join_snses = $sns_store->find(array("cp_id" => $cp->id));
            // アクション情報
            $action_info = [
                "cp" => [
                    "id" => $cp->id,
                    "created_at" => $cp->created_at,
                    "sponsor" => ($brand->enterprise_name) ? $brand->enterprise_name : $brand->name,
                    "can_entry" => $cp->canEntry(),
                    "url" => $cp->getUrl(true, $brand),
                    "shipping_method" => $cp->shipping_method,
                    "winner_count" => $cp->winner_count,
                    "show_winner_label" => $cp->show_winner_label,
                    "winner_label" => $cp->winner_label,
                    "show_recruitment_note" => $cp->show_recruitment_note,
                    "recruitment_note" => $cp->recruitment_note,
                    "join_limit_sns_flg" => $cp->join_limit_sns_flg,
                    "join_limit_flg" => $cp->join_limit_flg,
                    "share_flg" => $cp->share_flg,
                    "join_limit_sns" => $cp->getJoinLimitSns($join_snses),
                    "join_limit_sns_without_platform" => $cp->hasJoinLimitSnsWithoutPlatform($join_snses),
                    "extend_tag" => $cp->use_extend_tag ? $cp->extend_tag : "",
                    "start_date" => Util::getFormatDateString($cp->start_date),
                    "start_datetime"=> Util::getFormatDateTimeString($cp->start_date),
                    "end_date" => Util::getFormatDateString($cp->end_date),
                    "end_datetime" => Util::getFormatDateTimeString($cp->end_date),
                    "announce_date" => Util::getFormatDateString($cp->announce_date),
                    'is_au_campaign' => $cp->isAuCampaign(),
                    'au_login_url' => 'http://pass.auone.jp/gate/?nm=1&ru=' . urlencode(Util::rewriteUrl('my', 'signup', array(), array('cp_id' => $cp->id))),
                    'announce_display_label_use_flg' => $cp->announce_display_label_use_flg,
                    'announce_display_label' => $cp->announce_display_label,
                    'is_permanent_cp' => $cp->isPermanent(),
                    'is_non_incentive' => $cp->isNonIncentiveCp()
                ],

                "concrete_action" => [
                    "text" => $concrete_action->text,
                    "html_content" => $concrete_action->html_content,
                    "image_url" => $concrete_action->image_url,
                    "button_label_text" => $concrete_action->button_label_text
                ]
            ];

            $og_info = $cp->getOpenGraphInfo($brand);

            $value = array(self::KEY_ENTRY_ACTION => $entry_action, self::KEY_ACTION_INFO => $action_info, self::KEY_OG_INFO => $og_info);
            $this->cache_manager->setCampaignLPInfo($cp->id, $value);
        }

        // ブランドの情報は変わる恐れがあるので、その都度上書きする(キャッシュからとる前提なので、性能は悪化しない)
        $value[self::KEY_ACTION_INFO]['cp']['url'] = $cp->getUrl(true, $brand);
        $value[self::KEY_ACTION_INFO]['cp']['sponsor'] = ($brand->enterprise_name) ? $brand->enterprise_name : $brand->name;

        return $value;
    }

    public static function rewriteUrl($cp_id, $package, $action, $arrayParam = array(), $queryParam = array(), $base_url = '', $secure = false) {
        if ($cp_id == 6257) {
            return 'https://monipla.com/isehan/my/signup?cp_id=' . $cp_id;
        }

        return Util::rewriteUrl($package, $action, $arrayParam, $queryParam, $base_url, $secure);
    }
}