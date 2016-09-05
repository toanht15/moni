<?php
$service_factory = new aafwServiceFactory();
/** @var CpFlowService $cp_flow_service */
$cp_flow_service = $service_factory->create("CpFlowService");
$filter = array(
    'conditions' => array(
        'brand_id' => $data["brand_id"],
        'status' => array(Cp::STATUS_FIX, Cp::STATUS_CLOSE),
        'type' => Cp::TYPE_CAMPAIGN
    ),
    'order' => array(
        'name' => 'created_at',
        'direction' => 'desc'
    ),
);
$cps = $cp_flow_service->getCpsByFilter($filter);
$current_cp = null;
?>
<div class="customaudienceRefinement jsModuleContWrap">
    <div class="categoryLabel jsModuleContTile close">
        <p>キャンペーン参加状況</p>
        <p class="iconHelp">
            <span class="text"></span>
              <span class="textBalloon1">
                <span>
                  数値入力例<br>
                  <span class="label">50回</span><span class="sample"><span type="text" class="inputNum">50</span><span class="dash">〜</span><span type="text" class="inputNum">50</span></span><br>
                  <span class="label">50回〜100回</span><span class="sample"><span type="text" class="inputNum">50</span><span class="dash">〜</span><span type="text" class="inputNum">100</span></span>
                </span>
              <!-- /.textBalloon1 --></span>
            <!-- /.iconHelp --></p>
        <!-- /.categoryLabel --></div>
    <div class="refinementWrap jsModuleContTarget">
        <div class="refinementCampaign">
            <p class="campaignList" id="showCampaignList">
                <select name="campaignList" data-base-url="<?php
                if($data['audience_id']){
                    write_html(Util::rewriteUrl("admin-fan", "facebook_marketing_fan_target", array($data["audience_id"])));
                }else{
                    write_html(Util::rewriteUrl("admin-cp", "fan_list_download"));
                }
                ?>">
                    <option value="0">選択してください</option>
                    <?php foreach ($cps as $cp): ?>
                        <option value="<?php assign($cp->id) ?>" <?php if ($data["cp_id"] == $cp->id) { $current_cp = $cp; assign("selected"); } ?>><?php assign($cp->id.'-'.$cp->getTitle()) ?></option>
                    <?php endforeach; ?>
                </select>
                <!-- /.campaignList --></p>
            <?php if ($current_cp): ?>
                <?php $cp_groups = $cp_flow_service->getCpActionGroupsByCpId($current_cp->id) ?>
                <dl class="campaignStep">
                    <?php $action_order = 1 ?>
                    <?php foreach ($cp_groups as $cp_group): ?>
                        <?php $actions = $cp_flow_service->getCpActionsByCpActionGroupId($cp_group->id); ?>
                        <dt><?php assign($cp_group->getStepName()) ?></dt>

                        <?php foreach($actions as $action): ?>
                            <dd>
                                <?php $data["action"] = $action;
                                      $data["action_order"] = $action_order;
                                ?>
                                <?php write_html($this->parseTemplate("SearchBlockCampaignAction.php", $data)) ?>
                            </dd>
                            <?php $action_order++; ?>
                        <?php endforeach; ?>

                    <?php endforeach; ?>
                    <!-- /.campaignStep --></dl>
            <?php endif; ?>
            <!-- /.refinementCampaign --></div>
        <!-- /.refinementWrap --></div>
    <!-- /.customaudiencRefinement --></div>