<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class save_setting_attract extends BrandcoPOSTActionBase {
    protected $ContainerName = 'save_setting_attract';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => 'edit_setting_attract/{cp_id}',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array();

    public function validate() {
        $this->Data['brand'] = $this->getBrand();
        $validatorService = new CpValidator($this->Data['brand']->id);
        if (!$validatorService->isOwner($this->POST['cp_id'])) {
            $this->Validator->setError('auth', 'NOT_OWNER');
            return false;
        }

        $this->Data['status'] = $this->GET['exts'][0];
        if ($this->Data['status'] != Cp::SETTING_DRAFT && $this->Data['status'] != Cp::SETTING_FIX) {
            return false;
        }

        if (($this->POST['show_monipla_com_flg'] && $this->POST['show_monipla_com_flg'] != Cp::FLAG_SHOW_VALUE)
            || ($this->POST['show_top_page_flg'] && $this->POST['show_top_page_flg'] != Cp::FLAG_SHOW_VALUE)
            || ($this->POST['send_mail_flg'] && $this->POST['send_mail_flg'] != Cp::FLAG_SHOW_VALUE)) {
            return false;
        }

        return true;
    }

    function doAction() {

        /** @var CpFlowService $cp_service */
        $cp_service = $this->createService('CpFlowService');
        $cp = $cp_service->getCpById($this->POST['cp_id']);

        if($cp->join_limit_flg == Cp::JOIN_LIMIT_ON) {
            // 限定キャンペーンの時は露出をしない
            $cp->show_monipla_com_flg = $this->POST['show_monipla_com_flg'] = Cp::FLAG_HIDE_VALUE;
            $cp->share_flg            = $this->POST['share_flg']            = Cp::FLAG_HIDE_VALUE;
            $cp->show_top_page_flg    = $this->POST['show_top_page_flg']    = Cp::FLAG_HIDE_VALUE;
        }

        if ($this->POST['send_mail_flg']) {
            $cp->send_mail_flg = Cp::FLAG_SHOW_VALUE;
        } else if (!$cp->fix_attract_flg) {
            $cp->send_mail_flg = Cp::FLAG_HIDE_VALUE;
        }

        if ($this->POST['show_monipla_com_flg']) {
            $cp->show_monipla_com_flg = $this->POST['show_monipla_com_flg'];
        } else {
            $cp->show_monipla_com_flg = Cp::FLAG_HIDE_VALUE;
        }

        if ($this->POST['back_monipla_flg']) {
            $cp->back_monipla_flg = $this->POST['back_monipla_flg'];
        } else {
            $cp->back_monipla_flg = Cp::FLAG_HIDE_VALUE;
        }

        if ($this->POST['share_flg']) {
            $cp->share_flg = $this->POST['share_flg'];
        } else {
            $cp->share_flg = Cp::FLAG_HIDE_VALUE;
        }

        if ($this->POST['show_top_page_flg']) {
            $cp->show_top_page_flg = $this->POST['show_top_page_flg'];
        } else {
            $cp->show_top_page_flg = Cp::FLAG_HIDE_VALUE;
        }

        $cp->fix_attract_flg = $this->Data['status'];

        try {
            $cp_service->getCpModel()->begin();

            $cp_service->updateCp($cp);

            //リンクエントリーの表示
            /** @var LinkEntryService $link_service */
            $link_service = $this->createService('LinkEntryService');
            $link_entries = $link_service->getEntryByCpLink($cp->id, $this->brand->id, $this->brand->directory_name);
            /** @var TopPanelService $top_panel_service */
            $top_panel_service = $this->createService("TopPanelService");
            /** @var NormalPanelService $normal_panel_service */
            $normal_panel_service = $this->createService("NormalPanelService");
            if (!$this->POST['show_top_page_flg']) {
                $this->POST['show_top_page_flg'] = 0;
            }

            foreach ($link_entries as $link_entry) {

                if ($link_entry->priority_flg) {
                    $panel_service = $top_panel_service;
                } else {
                    $panel_service = $normal_panel_service;
                }

                if ($this->POST['show_top_page_flg'] == $link_entry->hidden_flg) {
                    if ($this->POST['show_top_page_flg']) {
                        $panel_service->addEntry($this->Data['brand'], $link_entry);
                    } else {
                        $panel_service->deleteEntry($this->Data['brand'], $link_entry);
                    }
                }
            }
            $cache_manager = new CacheManager();
            $cache_manager->deletePanelCache($this->brand->id);

            $cp_service->getCpModel()->commit();

        } catch (Exception $e) {
            $cp_service->getCpModel()->rollback();

            throw $e;
        }
        $this->Data['saved'] = 1;

        if ($this->Data['status'] == Cp::SETTING_DRAFT) {
            $url = Util::rewriteUrl('admin-cp', 'edit_setting_attract', array($this->POST['cp_id']), array('mid'=>'action-draft'));
        } else {
            $url = Util::rewriteUrl('admin-cp', 'edit_setting_attract', array($this->POST['cp_id']), array('mid'=>'action-saved'));
        }

        $return = 'redirect: ' . $url;

        return $return;
    }
}
