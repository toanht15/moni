<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.entities.SegmentProvision');
AAFW::import('jp.aainc.classes.CpInfoContainer');

class SegmentProvisionConditionSelector extends aafwWidgetBase {

    public function doService($params = array()) {
        // Init default value of selector
        $cur_category = null;
        $cur_condition = null;
        $cur_sub_condition = null;

        /** @var SegmentCreateSqlService $create_sql_service */
        $create_sql_service = $this->getService('SegmentCreateSqlService');
        /** @var SegmentCreatorService $segment_creator_service */
        $segment_creator_service = $this->getService('SegmentCreatorService', array($params['brand_id']));

        if ($params['pre_condition_key'] && $params['pre_condition_key'] != -1) {
            $split_data = explode('/', $params['pre_condition_key']);
            $search_condition_key = $split_data[0];
            $search_condition_sub_key = $split_data[1];

            $params['is_fixed_condition'] = true;

            if (SegmentCreateSqlService::isCampaignCondition($search_condition_key)) {
                /** @var CpFlowService $cp_flow_service */
                $cp_flow_service = $this->getService('CpFlowService');
                $cp_action = CpInfoContainer::getInstance()->getCpActionById($search_condition_sub_key);
                $cp = $cp_flow_service->getCpByCpAction($cp_action);

                if ($cp->isCpTypeCampaign()) {
                    $cur_category = SegmentCreatorService::PROVISION_CATEGORY_CAMPAIGN;
                    $sub_category_type = SegmentCreatorService::PROVISION_SUB_CATEGORY_CAMPAIGN;
                } else {
                    $cur_category = SegmentCreatorService::PROVISION_CATEGORY_MESSAGE;
                    $sub_category_type = SegmentCreatorService::PROVISION_SUB_CATEGORY_MESSAGE;
                }

                if ($search_condition_key == SegmentCreateSqlService::SEARCH_PARTICIPATE_CONDITION) {
                    $cur_sub_condition = $search_condition_sub_key;
                } elseif ($search_condition_key == SegmentCreateSqlService::SEARCH_QUESTIONNAIRE) {
                    $cur_sub_condition = $search_condition_sub_key . '/' . $search_condition_key . '/' . $split_data[2];
                } else {
                    $cur_sub_condition = $search_condition_sub_key . '/' . $search_condition_key;
                }

                $params['sub_condition_list'] = $segment_creator_service->getSubCategoryConditionListByType($sub_category_type, $cp->id, $cur_sub_condition);
                $cur_condition = $cp->id;
            } else {
                $cur_category = SegmentCreatorService::getProvisionCategoryType($search_condition_key);

                if ($cur_category == SegmentCreatorService::PROVISION_CATEGORY_SNS_REACTION) {
                    $cur_sub_condition = $split_data[2];
                    $params['sub_condition_list'] = $segment_creator_service->getSubCategoryConditionListByType(SegmentCreatorService::PROVISION_SUB_CATEGORY_SNS_REACTION, $split_data[1], $cur_sub_condition);
                    $cur_condition = $create_sql_service->convertSocialAppIdToSocaialAccountAppId($split_data[1]);
                } else if ($cur_category == SegmentCreatorService::PROVISION_CATEGORY_USER_DATA) {
                    $cur_condition = $search_condition_key;
                } else if ($cur_category == SegmentCreatorService::PROVISION_CATEGORY_CUSTOM_PROFILE) {
                    if ($search_condition_sub_key) {
                        $cur_condition = $search_condition_sub_key;
                    } else {
                        $cur_condition = 0;
                    }
                } else {
                    $cur_condition = $search_condition_sub_key;
                }
            }

            // Fetching Condition Detail View
            $target_type = $split_data[0];
            if ($target_type == SegmentCreateSqlService::SEARCH_QUESTIONNAIRE) {
                $target_id = $split_data[1] . '/' . $split_data[2];
            } else {
                $target_id = $split_data[1];
            }
            $params['condition_view'] = $segment_creator_service->getConditionViewByType($target_type, $target_id);
            $params['condition_view_title'] = $create_sql_service->getConditionTitle($params['pre_condition_key'], $params['brand_id']);
        }

        // Fetching category list
        $params['category_list'] = SegmentCreatorService::getProvisionCategoryList($cur_category);

        $cur_category = $cur_category ? : SegmentCreatorService::PROVISION_CATEGORY_USER_DATA;
        $params['category_list'][$cur_category]['is_selected'] = true;

        $brand_service = $this->getService('BrandService');
        if (!$brand_service->isValidCustomAttributeDefinitions($params['brand_id'])) {
            unset($params['category_list'][SegmentCreatorService::PROVISION_CATEGORY_IMPORT_VALUE]);
        }

        // Fetching condition list
        $params['condition_list'] = $segment_creator_service->getCategoryConditionListByType($cur_category, $cur_condition);
        if (isset($cur_condition)) {
            $params['condition_list'][$cur_condition]['is_selected'] = true;
        }

        if ($cur_sub_condition) {
            $params['sub_condition_list'][$cur_sub_condition]['is_selected'] = true;
        }

        return $params;
    }
}
