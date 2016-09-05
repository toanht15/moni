<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<?php $service_factory = new aafwServiceFactory();
$code_auth_service = $service_factory->create('CodeAuthenticationService');
$cp_action_manager = $service_factory->create('CpCodeAuthActionManager');
$cp_flow_service = $service_factory->create('CpFlowService');
?>
<article>
    <h1 class="hd1">認証コード設定</h1>
    <p class="supplement1">ユーザーが入力した値を認証するためコードを登録できます。<br>商品パッケージやレシートに表示するコードを事前に登録し、ユーザーがキャンペーンページでコードを入力した際に正しいコードであるか認証します。</p>
    <section class="couponWrap" style="margin-top:30px;">
        <p><span class="btn3"><a href="<?php write_html(Util::rewriteUrl('admin-code-auth', 'create_code_auth')) ?>">新規作成</a></span></p>
        <!-- /.couponWrap --></section>
    <h2 class="hd2">認証コード一覧</h2>
    <section class="couponWrap">
        <table class="couponTable1">
            <thead>
            <tr>
                <th>認証コード名</th>
                <th>メモ</th>
                <th>上限数</th>
                <th>利用中のキャンペーンなど</th>
                <th>認証済みコード数</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data['code_auths'] as $code_auth): ?>
                <?php
                $code_auth_actions = $cp_action_manager->getCpConcreteActionByCodeAuthId($code_auth->id);
                $code_auth_action_count = $code_auth_actions ? $code_auth_actions->total() : 0;
                ?>
                <tr>
                    <th rowspan="<?php assign($code_auth_action_count) ?>"><a href="<?php write_html(Util::rewriteUrl('admin-code-auth', 'code_auth_codes', array($code_auth->id))) ?>"><?php assign($code_auth->name) ?></a></th>
                    <td rowspan="<?php assign($code_auth_action_count) ?>"><?php assign($code_auth->description) ?></td>
                    <td rowspan="<?php assign($code_auth_action_count) ?>">
                        <?php list($reserved_num, $code_auth_limit) = $code_auth_service->getCodeAuthStatisticByCodeAuthId($code_auth->id) ?>
                        <?php assign(number_format($code_auth_limit)) ?>
                    </td>
                    <?php if ($code_auth_action_count > 0): ?>
                        <?php
                        $first_action = $code_auth_actions->current();
                        $cp_action = $cp_flow_service->getCpActionById($first_action->cp_action_id);
                        $cp = $cp_action->getCp();
                        $code_auth_users = $cp_action_manager->getCodeAuthUsersByCpActionId($first_action->cp_action_id);
                        ?>
                        <?php if($cp): ?>
                            <td><a href="<?php assign(Util::rewriteUrl('admin-cp', 'edit_action', array($cp->id, $cp_flow_service->getFirstActionOfCp($cp->id)->id))) ?>" class="<?php assign($cp->type == Cp::TYPE_CAMPAIGN ? 'typeCampaign1' : 'typeMail1') ?>">
                                    <?php assign($cp->getTitle() . '(S' . $cp_action->getStepNo() . ')') ?></a></td>
                            <td><?php assign($code_auth_users ? $code_auth_users->total() : 0) ?></td>
                        <?php else: ?>
                            <td>削除済みキャンペーン</td>
                            <td><?php assign($code_auth_users ? $code_auth_users->total() : 0) ?></td>
                        <?php endif ?>
                    <?php else: ?>
                        <td>なし</td>
                        <td>0</td>
                    <?php endif ?>
                </tr>
                <?php if ($code_auth_action_count > 1): ?>
                    <?php foreach ($code_auth_actions as $code_auth_action): ?>
                        <?php if ($code_auth_action == $first_action) continue; ?>
                        <?php
                        $cp_action = $cp_flow_service->getCpActionById($code_auth_action->cp_action_id);
                        $cp = $cp_action->getCp();
                        $code_auth_users = $cp_action_manager->getCodeAuthUsersByCpActionId($code_auth_action->cp_action_id);
                        ?>
                        <?php if($cp): ?>
                            <tr>
                                <td><a href="<?php assign(Util::rewriteUrl('admin-cp', 'edit_action', array($cp->id, $cp_flow_service->getFirstActionOfCp($cp->id)->id))) ?>" class="<?php assign($cp->type == Cp::TYPE_CAMPAIGN ? 'typeCampaign1' : 'typeMail1') ?>">
                                        <?php assign($cp->getTitle() . '(S' . $cp_action->getStepNo() . ')') ?></a></td>
                                <td><?php assign($code_auth_users ? $code_auth_users->total() : 0) ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td>削除済みキャンペーン</td>
                                <td><?php assign($code_auth_users ? $code_auth_users->total() : 0) ?></td>
                            </tr>
                        <?php endif ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
            <!-- /.couponTable1 --></table>
        <!-- /.couponWrap --></section>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoModalPager')->render(array(
        'TotalCount' => $data['total_count'],
        'CurrentPage' => $this->params['p'],
        'Count' => $data['page_limited'],
    ))) ?>
</article>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
