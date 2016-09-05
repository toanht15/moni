<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');

class WithdrawFan extends BrandcoBatchBase{

    function executeProcess() {
        /** @var BrandsUsersRelationService $brands_users_relation_service */
        $brands_users_relation_service = $this->service_factory->create('BrandsUsersRelationService');
        $brands_users_relations = $brands_users_relation_service->getWithdrawFanUserRelationsWithoutBrandId();
        foreach($brands_users_relations as $brand_user_relation) {
            try {
                $brands_users_relation_service->brands_users_relations->begin();

                $this->deleteShippingAddressUser($brand_user_relation);
                $this->deleteUserInfo($brand_user_relation);
                $this->deleteCpUserActionMessageAndStatusAndDemoData($brand_user_relation);
                $brands_users_relation_service->setDelInfoFlgByBrandUserRelation($brand_user_relation);

                $brands_users_relation_service->brands_users_relations->commit();
            } catch (Exception $e) {
                $brands_users_relation_service->brands_users_relations->rollback();
                aafwLog4phpLogger::getHipChatLogger()->error("WithdrawFan has failed!", $e);
                throw $e;
            }
        }
    }

    private function deleteShippingAddressUser($brand_user_relation) {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->service_factory->create('CpFlowService');
        $cps = $cp_flow_service->getCpsNotDraftByBrandId($brand_user_relation->brand_id);
        /** @var CpUserService $cp_user_service */
        $cp_user_service = $this->service_factory->create('CpUserService');
        /** @var ShippingAddressUserService $shipping_address_user_service */
        $shipping_address_user_service = $this->service_factory->create('ShippingAddressUserService');
        $shipping_address_manager = new CpShippingAddressActionManager();

        foreach ($cps as $cp) {
            if (!$cp->isHasAction(CpAction::TYPE_SHIPPING_ADDRESS)) {
                continue;
            }
            $cp_user = $cp_user_service->getCpUserByCpIdAndUserId($cp->id, $brand_user_relation->user_id);

            if (!$cp_user) {
                continue;
            }

            $shipping_actions = $cp_flow_service->getCpActionsByCpIdAndActionType($cp->id, CpAction::TYPE_SHIPPING_ADDRESS);
            foreach ($shipping_actions as $cp_action) {
                $shipping_address_action = $shipping_address_manager->getConcreteAction($cp_action);
                $shipping_address_user_service->deleteShippingAddressUserByCpUserIdAndShippingAddressActionId($cp_user->id, $shipping_address_action->id);
            }
        }
    }

    private function deleteUserInfo($brand_user_relation) {
        /** @var BrandsUsersRelationService $brands_users_relation_service */
        $brands_users_relation_service = $this->service_factory->create('BrandsUsersRelationService');
        $brands_relations = $brands_users_relation_service->getAllRelationsByUserId($brand_user_relation->user_id);

        if (!$brands_relations) {

            //個人情報削除
            /** @var ShippingAddressService $shipping_address_service */
            $shipping_address_service = $this->service_factory->create('ShippingAddressService');
            $shipping_address_service->deleteShippingAddressByUserId($brand_user_relation->user_id);

            /** @var SocialAccountService $social_account_service */
            $social_account_service = $this->service_factory->create('SocialAccountService');
            $social_account_service->deleteSocialAccountsByUserId($brand_user_relation->user_id);

            /** @var  UserService $user_service*/
            $user_service = $this->service_factory->create('UserService');
            $user = $user_service->getUserByBrandcoUserId($brand_user_relation->user_id);
            if($user) {
                $user->name = '';
                $user->mail_address = '';
                $user_service->updateUser($user);
            }
        }
    }

    private function deleteProfileQuestionAnswer($brand_user_relation) {
        /** @var ProfileQuestionnaireService $profile_question_service */
        $profile_question_service = $this->service_factory->create('ProfileQuestionnaireService');
        $profile_question_service->deleteProfileQuestionnaireAnswerByBrandRelationId($brand_user_relation->id);
    }

    private function deleteCpUserActionMessageAndStatusAndDemoData($brand_user_relation) {
        /** @var CpUserService $cp_user_service */
        $cp_user_service = $this->service_factory->create('CpUserService');
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->service_factory->create('CpFlowService');
        /** @var CpUserActionStatusService $cp_user_action_status_service */
        $cp_user_action_status_service = $this->service_factory->create('CpUserActionStatusService');

        $cps = $cp_flow_service->getCpsNotDraftByBrandId($brand_user_relation->brand_id);

        foreach ($cps as $cp) {
            $cp_user = $cp_user_service->getCpUserByCpIdAndUserId($cp->id, $brand_user_relation->user_id);

            if (!$cp_user) {
                continue;
            }
            if ($cp->status == Cp::STATUS_DEMO) {
                $cp_flow_service->resetDemoUserDataByCpUser($cp_user);
                continue;
            }
            $cp_user_action_status_service->deleteCpUserActionMessageByCpUser($cp_user->id);
            $cp_user_action_status_service->deleteCpUserActionStatusByCpUser($cp_user->id);
        }
    }

}