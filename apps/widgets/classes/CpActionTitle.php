<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class CpActionTitle extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');

        $params['cp_status'] = $params['cp']->getStatus();
        $params['is_announced'] = $cp_flow_service->isAnnounced($params['cp']);
        $params['title_attributes'] = $this->getCpActionTitleLabelAndClass($params['cp'], $params['is_announced']);
        $params['should_announce'] = $this->shouldAnnounce($params['cp'], $params['is_announced']);

        if ($params['cp']->isDemo()) {
            $params['h1_class'] = "hd1_demo";
        } else if ($params['cp']->isLimitCp()) {
            $params['h1_class'] = "hd1_unpublished";
        } else {
            $params['h1_class'] = "hd1";
        }

        return $params;
    }

    /**
     * 当選発表すべきかどうか
     * @return bool
     */
    public function shouldAnnounce($cp, $is_announced = false) {
        return !$cp->isNonIncentiveCp() && $cp->isFixed() && $cp->isOverAnnounceDate() && !$is_announced;
    }

    /**
     * @param Cp $cp
     * @return array
     */
    public function getCpActionTitleLabelAndClass(Cp $cp, $is_announced = false) {
        $cp_status = $cp->getStatus();

        $attributes = array('label' => '', 'class' => '');
        switch ($cp_status) {
            case Cp::CAMPAIGN_STATUS_SCHEDULE:
                $attributes = array('label' => '公開予約', 'class' => 'label4');
                break;
            case Cp::CAMPAIGN_STATUS_OPEN:
                $attributes = array('label' => '開催中', 'class' => 'label3');
                break;
            case Cp::CAMPAIGN_STATUS_WAIT_ANNOUNCE:
            case Cp::CAMPAIGN_STATUS_CLOSE:
                if (!$is_announced) {
                    $attributes = array('label' => '当選発表待ち', 'class' => 'label1');
                } else {
                    $attributes = array('label' => '終了', 'class' => 'label2');
                }
                break;
            case Cp::CAMPAIGN_STATUS_DEMO:
                $attributes = array('label' => 'デモ公開中', 'class' => 'label6');
                break;
            case Cp::CAMPAIGN_STATUS_CP_PAGE_CLOSED:
                $attributes = array('label' => 'クローズ', 'class' => 'label2');
                break;
            default:
                $attributes = array('label' => '下書き', 'class' => 'label5');
                break;
        }

        return $attributes;
    }
}
