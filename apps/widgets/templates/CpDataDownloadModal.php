<div class="modal1 jsModal" id="modal_download_modal_<?php assign($data['cp_id'])?>" data-cp_id="<?php assign($data['cp_id'])?>">
    <section class="modalCont-small jsModalCont jsDataDownloadModal">
        <h1>データダウンロード</h1>
        <p><span class="font-download">表示があるものは直接ダウンロードされます。</span></p>
        <ul>
            <?php if($data['cp']->status != Cp::STATUS_DEMO): ?>
                <li><a href="<?php write_html(Util::rewriteUrl('admin-cp', 'fan_list_download', array($data['cp_id']), $data['isFromPublicCp'] ? array('r' => true) : '')) ?>">参加者情報</a></li>
            <?php endif; ?>
            <li><a href="<?php assign(Util::rewriteUrl('admin-cp', 'csv_daily_cp_action_status', array($data['cp_id']), array('type' => 'read'))); ?>" class="download">日別既読件数</a></li>
            <li><a href="<?php assign(Util::rewriteUrl('admin-cp', 'csv_daily_cp_action_status', array($data['cp_id']), array('type' => 'finish'))); ?>" class="download">日別完了件数</a></li>
            <?php if($data['can_get_fid_report']): ?>
                <li><a href="<?php assign(Util::rewriteUrl('admin-cp', 'csv_fid_daily_report', array($data['cp_id']))); ?>" class="download">日別流入元</a></li>
            <?php endif; ?>
            <?php if ($data['first_gift_action_id']): ?>
                <li><a href="<?php assign(Util::rewriteUrl('admin-cp', 'csv_gift_campaign_fan', array($data['first_gift_action_id'], '0'))); ?>" class="download">ギフト受手一覧</a></li>
                <?php if ($data['is_gift_campaign_with_address']): ?>
                    <li><a href="<?php assign(Util::rewriteUrl('admin-cp', 'csv_gift_campaign_fan', array($data['first_gift_action_id'], '1'))); ?>" class="download">ギフト受信ユーザー配送先</a></li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if(count($data['fixed_target_actions']) > 0 && count($data['shipping_actions']) > 0): ?>
                <?php if(count($data['fixed_target_actions']) > 1): ?>
                    <?php if(Util::isAcceptRemote()): ?>
                        <li>
                            <span class="download labelModeClient">当選者配送先</span>
                            <select name="<?php assign('announce_dl_'.$data['cp']->id)?>" class="jsAnnounceDL">
                                <option value="" selected>選択して下さい</option>
                                <?php foreach($data['fixed_target_actions'] as $action_id => $action): ?>
                                    <option value="<?php assign($action_id); ?>"><?php assign('STEP '.$action['order_no'])?></option>
                                <?php endforeach; ?>
                            </select>
                        </li>
                    <?php else: ?>
                        <li>
                            <s class="download labelModeClient">当選者配送先</s>
                            <select name="<?php assign('announce_dl_'.$data['cp']->id)?>">
                                <option value="" selected>選択して下さい</option>
                                <?php foreach($data['fixed_target_actions'] as $action_id => $action): ?>
                                    <option value=""><?php assign('STEP '.$action['order_no'])?></option>
                                <?php endforeach; ?>
                            </select>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if(Util::isAcceptRemote() && !$data['pageStatus']['isAgent']): ?>
                        <?php $action_id = array_keys($data['fixed_target_actions'])[0]; ?>
                        <li><a href="javascript:void(0)" class="jsAnnounceDL download" data-action_id="<?php assign($action_id); ?>">当選者配送先</a></li>
                    <?php else: ?>
                        <li><s class="download">当選者配送先</s></li>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>

            <?php if(($data['cp']->status == Cp::STATUS_FIX || $data['cp']->status == Cp::STATUS_CLOSE) && date("Y-m-d", strtotime($data['cp']->start_date)) <= date("Y-m-d", strtotime("-1 day"))): ?>
                <li><a href="<?php assign(Util::rewriteUrl('admin-cp', 'csv_daily_campaign_report', array($data['cp']->id))); ?>" class="download">キャンペーン日次レポート</a>
                    <span class="glossary"><a href="javascript:void(0);" data-link="<?php assign(Util::rewriteUrl('admin-cp', 'daily_cp_report_description', array(), array('cp_id' => $data['cp_id']))); ?>" class="jsFileUploaderPopup">用語説明</a></span></li>
            <?php else: ?>
                <li><s class="download">キャンペーン日次レポート</s><span class="glossary"><a href="javascript:void(0);" data-link="<?php assign(Util::rewriteUrl('admin-cp', 'daily_cp_report_description', array('cp_id' => $data['cp_id']))); ?>" class="jsFileUploaderPopup">用語説明</a></span></li>
            <?php endif; ?>

        </ul>
        <p class="btnSet">
            <span class="btn2"><a href='javascript:Brandco.unit.closeModal("_download_modal_<?php assign($data['cp_id'])?>");' class="middle1">キャンセル</a></span>
        <!-- /.btnSet --></p>
    <!-- /.modalCont-small --></section>
<!-- /.modal1 --></div>
